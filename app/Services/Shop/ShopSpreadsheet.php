<?php

namespace App\Services\Shop;

use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;

/** Import et export du catalogue, export des ventes, au format Excel (.xlsx). Une ligne du catalogue = une déclinaison. */
class ShopSpreadsheet
{
    public const CATALOG_COLUMNS = [
        'reference_produit', 'nom', 'categorie', 'resume', 'description', 'publie', 'seuil_alerte',
        'declinaison', 'sku', 'prix', 'prix_promo', 'stock',
    ];

    public function __construct(private StockService $stock)
    {
    }

    public function exportCatalog(): string
    {
        $rows = [];

        Product::query()->with(['category.parent', 'variants'])->orderBy('name')->each(function (Product $product) use (&$rows) {
            foreach ($product->variants as $variant) {
                $rows[] = [
                    $product->reference, $product->name, $product->category ? ($product->category->parent ? $product->category->parent->name.' > ' : '').$product->category->name : '',
                    (string) $product->summary, (string) $product->description, $product->is_published ? 'oui' : 'non', $product->low_stock_threshold,
                    (string) $variant->name, $variant->sku, $variant->price, $variant->sale_price ?? '', $variant->stock,
                ];
            }
        });

        return $this->write(self::CATALOG_COLUMNS, $rows);
    }

    public function exportSales(?string $from, ?string $to): string
    {
        $rows = [];

        Order::query()
            ->with('items')
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->orderBy('created_at')
            ->each(function (Order $order) use (&$rows) {
                foreach ($order->items as $item) {
                    $rows[] = [
                        $order->created_at->format('d/m/Y H:i'), $order->number, $order->customerName(), $order->email, $order->phone,
                        $order->statusLabel(), $order->paymentStatusLabel(), $order->paymentMethodLabel(),
                        $item->product_name, (string) $item->variant_name, $item->sku, $item->quantity, $item->unit_price, $item->line_total,
                        $order->discount, $order->total, $order->refunded_amount,
                    ];
                }
            });

        return $this->write([
            'date', 'commande', 'client', 'e-mail', 'telephone', 'statut', 'paiement', 'mode de paiement',
            'produit', 'declinaison', 'sku', 'quantite', 'prix unitaire', 'total ligne', 'reduction commande', 'total commande', 'rembourse',
        ], $rows);
    }

    /**
     * @return array{created: int, updated: int, errors: array<int, string>}
     */
    public function importCatalog(UploadedFile $file, User $user): array
    {
        $rows = $this->read($file);
        $header = array_map(fn ($h) => Str::slug((string) $h, '_'), array_shift($rows) ?? []);
        $missing = array_diff(['reference_produit', 'nom', 'sku', 'prix', 'stock'], $header);

        if ($missing) {
            return ['created' => 0, 'updated' => 0, 'errors' => [1 => 'Colonnes manquantes : '.implode(', ', $missing).'.']];
        }

        $created = $updated = 0;
        $errors = [];

        foreach ($rows as $index => $values) {
            $line = $index + 2;
            $data = array_combine($header, array_pad(array_slice($values, 0, count($header)), count($header), null));

            if (collect($data)->filter(fn ($v) => trim((string) $v) !== '')->isEmpty()) {
                continue;
            }

            $problem = match (true) {
                trim((string) $data['reference_produit']) === '' => 'référence produit vide',
                trim((string) $data['nom']) === '' => 'nom vide',
                trim((string) $data['sku']) === '' => 'SKU vide',
                ! is_numeric($data['prix']) || (int) $data['prix'] < 0 => 'prix invalide',
                ! is_numeric($data['stock']) => 'stock invalide',
                ($data['prix_promo'] ?? '') !== '' && $data['prix_promo'] !== null && ! is_numeric($data['prix_promo']) => 'prix promo invalide',
                default => null,
            };

            if ($problem) {
                $errors[$line] = $problem;
                continue;
            }

            $sameSku = ProductVariant::query()->where('sku', trim($data['sku']))->with('product')->first();

            if ($sameSku && $sameSku->product?->reference !== trim($data['reference_produit'])) {
                $errors[$line] = 'SKU déjà utilisé par un autre produit';
                continue;
            }

            DB::transaction(function () use ($data, $user, &$created, &$updated) {
                $product = Product::withTrashed()->firstOrNew(['reference' => trim($data['reference_produit'])]);
                $isNew = ! $product->exists;

                $product->fill([
                    'name' => trim($data['nom']),
                    'category_id' => $this->category((string) ($data['categorie'] ?? ''))?->id ?? $product->category_id,
                    'summary' => $data['resume'] ?? $product->summary,
                    'description' => $data['description'] ?? $product->description,
                    'is_published' => in_array(mb_strtolower(trim((string) ($data['publie'] ?? ''))), ['oui', '1', 'yes', 'vrai'], true),
                    'low_stock_threshold' => is_numeric($data['seuil_alerte'] ?? null) ? (int) $data['seuil_alerte'] : ($product->low_stock_threshold ?? 5),
                ]);
                $product->slug ??= $this->uniqueSlug($product->name);
                $product->deleted_at = null;
                $product->save();

                $variant = $product->variants()->firstOrNew(['sku' => trim($data['sku'])]);
                $variantIsNew = ! $variant->exists;
                $variant->fill([
                    'name' => trim((string) ($data['declinaison'] ?? '')) ?: null,
                    'price' => (int) $data['prix'],
                    'sale_price' => is_numeric($data['prix_promo'] ?? null) ? (int) $data['prix_promo'] : null,
                ]);

                if ($variantIsNew) {
                    $variant->stock = 0;
                    $variant->sort_order = (int) $product->variants()->max('sort_order') + 1;
                }

                $variant->save();

                $difference = (int) $data['stock'] - $variant->stock;
                if ($difference !== 0) {
                    $this->stock->move($variant, $difference, 'import', null, $user, 'Import du catalogue');
                }

                $isNew ? $created++ : $updated++;
            });
        }

        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }

    private function category(string $path): ?ProductCategory
    {
        $names = array_values(array_filter(array_map('trim', preg_split('/\s*[>›]\s*/u', $path))));

        if (! $names) {
            return null;
        }

        $parent = null;

        foreach (array_slice($names, 0, 2) as $name) {
            $parent = ProductCategory::query()->firstOrCreate(
                ['name' => $name, 'parent_id' => $parent?->id],
                ['slug' => $this->uniqueCategorySlug($name)]
            );
        }

        return $parent;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produit';
        $slug = $base;
        $i = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function uniqueCategorySlug(string $name): string
    {
        $base = Str::slug($name) ?: 'categorie';
        $slug = $base;
        $i = 2;

        while (ProductCategory::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function write(array $header, array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'agf').'.xlsx';
        $writer = new Writer();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValuesWithStyle($header, (new Style())->withFontBold(true)));

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return $path;
    }

    private function read(UploadedFile $file): array
    {
        $rows = [];

        if (in_array(strtolower($file->getClientOriginalExtension()), ['csv', 'txt'], true)) {
            $handle = fopen($file->getRealPath(), 'r');
            $first = fgets($handle);
            $delimiter = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
            rewind($handle);

            while (($values = fgetcsv($handle, 0, $delimiter, '"', '\\')) !== false) {
                $rows[] = array_map(fn ($v) => is_string($v) ? trim(preg_replace('/^\xEF\xBB\xBF/', '', $v)) : $v, $values);
            }

            fclose($handle);

            return $rows;
        }

        $reader = new Reader();
        $reader->open($file->getRealPath());

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($v) => $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v, $row->toArray());
            }
            break;
        }

        $reader->close();

        return $rows;
    }
}
