<?php

namespace Database\Seeders;

use App\Models\Shop\Coupon;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\ProductCategory;
use App\Models\Shop\ProductVariant;
use App\Models\Shop\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/** Catalogue et commandes de démonstration de la boutique (environnements local et testing). */
class DemoShopSeeder extends Seeder
{
    public function run(): void
    {
        $category = function (string $name, ?ProductCategory $parent = null, ?string $description = null): ProductCategory {
            return ProductCategory::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'parent_id' => $parent?->id, 'description' => $description]
            );
        };

        $aquaculture = $category('Aquaculture', null, 'Aliments et matériel pour l’élevage de poissons en étang et en bac.');
        $feed = $category('Aliments poisson', $aquaculture);
        $gear = $category('Matériel d’élevage', $aquaculture);
        $agriculture = $category('Agriculture', null, 'Semences, fertilisation et irrigation pour le maraîchage et les grandes cultures.');
        $seeds = $category('Semences', $agriculture);
        $soil = $category('Fertilisation', $agriculture);
        $water = $category('Irrigation', $agriculture);

        $products = [
            ['AQ-GRAN', 'Aliment flottant tilapia 3 mm', $feed, 'aliment-granules', 'Granulés flottants à 32 % de protéines pour le grossissement du tilapia.',
                "Aliment complet extrudé pour tilapia en phase de grossissement.\n\nTaux de protéines : 32 %. Granulés flottants de 3 mm, adaptés aux poissons de 50 à 250 g.\nRation conseillée : 3 à 5 % de la biomasse par jour, en 2 ou 3 repas.",
                [['Sac de 10 kg', 'AQ-GRAN-10', 9500, null, 40], ['Sac de 25 kg', 'AQ-GRAN-25', 22000, 19800, 18]], true],
            ['AQ-FILET', 'Filet de pêche pour étang', $gear, 'filet-peche', 'Filet senne en nylon traité, avec plombs et flotteurs, pour la récolte en étang.',
                "Filet senne monté, maille de 20 mm, en nylon traité anti-UV.\n\nLivré avec ralingue plombée et ralingue à flotteurs.",
                [['Longueur 15 m', 'AQ-FILET-15', 38000, null, 6], ['Longueur 30 m', 'AQ-FILET-30', 69000, null, 3]], false],
            ['AQ-FLOT', 'Flotteurs pour filets (lot de 50)', $gear, 'flotteurs', 'Flotteurs en plastique pour remplacer ou monter une ralingue.',
                'Lot de 50 flotteurs ovales en plastique, diamètre de passage 8 mm.',
                [[null, 'AQ-FLOT-50', 7500, null, 2]], false],
            ['AG-SEM-MAR', 'Semences maraîchères tropicales', $seeds, 'semences-maraicheres', 'Sachets de semences adaptées aux conditions tropicales : tomate, piment, gombo.',
                "Semences sélectionnées pour leur résistance à la chaleur.\n\nChaque sachet indique la période de semis, l’espacement et le cycle.",
                [['Tomate (5 g)', 'AG-SEM-TOM', 3500, null, 60], ['Piment (5 g)', 'AG-SEM-PIM', 3000, null, 45], ['Gombo (25 g)', 'AG-SEM-GOM', 2500, null, 0]], false],
            ['AG-MAIS', 'Semences de maïs à cycle court', $seeds, 'mais-semences', 'Variété de maïs à cycle de 90 jours, adaptée aux saisons des pluies courtes.',
                'Semences certifiées, taux de germination supérieur à 90 %. Densité conseillée : 20 à 25 kg par hectare.',
                [['Sac de 5 kg', 'AG-MAIS-5', 8500, null, 25], ['Sac de 25 kg', 'AG-MAIS-25', 39000, null, 8]], false],
            ['AG-COMP', 'Compost organique criblé', $soil, 'compost', 'Amendement organique pour améliorer la structure et la fertilité des sols.',
                'Compost mûr, criblé, issu de déchets verts. Apport conseillé : 1 à 2 kg par m² avant plantation.',
                [['Sac de 25 kg', 'AG-COMP-25', 6000, null, 70]], false],
            ['AG-GAG', 'Kit d’irrigation goutte-à-goutte', $water, 'goutte-a-goutte', 'Kit complet pour irriguer une planche maraîchère avec peu d’eau.',
                "Kit comprenant gaine goutteuse, raccords, filtre et vanne.\n\nÉconomise jusqu’à 50 % d’eau par rapport à l’arrosage manuel.",
                [['Kit 100 m²', 'AG-GAG-100', 45000, null, 10], ['Kit 500 m²', 'AG-GAG-500', 165000, 149000, 4]], true],
            ['AG-ARRO', 'Arrosoir galvanisé 10 litres', $water, 'arrosoir', 'Arrosoir robuste en acier galvanisé, avec pomme démontable.',
                'Capacité 10 litres. Acier galvanisé, poignée renforcée.',
                [[null, 'AG-ARRO-10', 9000, null, 15]], false],
        ];

        $manager = User::query()->where('role', 'manager')->first();

        foreach ($products as [$reference, $name, $cat, $image, $summary, $description, $variants, $onSale]) {
            $product = Product::withTrashed()->updateOrCreate(['reference' => $reference], [
                'name' => $name,
                'slug' => Str::slug($name),
                'category_id' => $cat->id,
                'summary' => $summary,
                'description' => $description,
                'is_published' => true,
                'low_stock_threshold' => 5,
                'sale_starts_at' => $onSale ? now()->subDays(3) : null,
                'sale_ends_at' => $onSale ? now()->addDays(20) : null,
                'deleted_at' => null,
            ]);

            $product->images()->delete();
            $product->images()->create(['path' => 'images/boutique/'.$image.'.webp', 'sort_order' => 0]);

            foreach ($variants as $i => [$label, $sku, $price, $salePrice, $stock]) {
                $variant = ProductVariant::query()->updateOrCreate(['sku' => $sku], [
                    'product_id' => $product->id, 'name' => $label, 'price' => $price, 'sale_price' => $salePrice, 'stock' => $stock, 'sort_order' => $i,
                ]);

                if (! $variant->movements()->exists()) {
                    StockMovement::create(['product_variant_id' => $variant->id, 'user_id' => $manager?->id, 'quantity' => $stock, 'stock_after' => $stock, 'type' => 'restock', 'note' => 'Stock initial']);
                }
            }
        }

        Coupon::query()->updateOrCreate(['code' => 'BIENVENUE10'], ['type' => 'percent', 'value' => 10, 'min_amount' => 20000, 'is_active' => true, 'ends_at' => now()->addMonths(2)]);

        $this->orders();
    }

    private function orders(): void
    {
        $client = User::query()->where('email', 'client@agrifish.africa')->first();

        if (! $client || Order::query()->where('email', $client->email)->exists()) {
            return;
        }

        $make = function (array $lines, string $status, string $method, string $paymentStatus, int $daysAgo) use ($client): Order {
            $subtotal = collect($lines)->sum(fn ($l) => $l[0]->currentPrice() * $l[1]);
            $order = Order::create([
                'number' => Order::generateNumber(), 'access_token' => Str::random(40), 'user_id' => $client->id, 'email' => $client->email,
                'first_name' => 'Mariam', 'last_name' => 'Ouattara', 'phone' => $client->phone ?? '+2250102030405', 'city' => 'Bouaké', 'district' => 'Commerce',
                'status' => $status, 'payment_method' => $method, 'payment_status' => $paymentStatus,
                'subtotal' => $subtotal, 'total' => $subtotal,
                'paid_at' => $paymentStatus === 'paid' ? now()->subDays($daysAgo) : null,
                'completed_at' => $status === 'completed' ? now()->subDays(max(0, $daysAgo - 2)) : null,
            ]);
            $order->forceFill(['created_at' => now()->subDays($daysAgo), 'updated_at' => now()->subDays($daysAgo)])->save();

            foreach ($lines as [$variant, $quantity]) {
                $order->items()->create([
                    'product_id' => $variant->product_id, 'product_variant_id' => $variant->id, 'product_name' => $variant->product->name,
                    'variant_name' => $variant->name, 'sku' => $variant->sku, 'unit_price' => $variant->currentPrice(), 'quantity' => $quantity,
                    'line_total' => $variant->currentPrice() * $quantity,
                ]);
            }

            $order->record('created', $client, ['to_status' => $method === 'online' ? 'pending_payment' : 'new']);

            if ($paymentStatus === 'paid' && $method === 'online') {
                $order->payments()->create(['provider' => 'simulation', 'reference' => 'TEST-'.Str::upper(Str::random(8)), 'amount' => $subtotal, 'status' => 'approved']);
                $order->record('paid', null, ['details' => 'Paiement de test']);
            }

            return $order;
        };

        $variant = fn (string $sku) => ProductVariant::query()->with('product')->where('sku', $sku)->firstOrFail();

        $make([[$variant('AQ-GRAN-25'), 2], [$variant('AQ-FLOT-50'), 1]], 'completed', 'online', 'paid', 12);
        $make([[$variant('AG-GAG-100'), 1], [$variant('AG-SEM-TOM'), 3]], 'paid', 'online', 'paid', 1);
        $make([[$variant('AG-COMP-25'), 4]], 'new', 'on_delivery', 'to_collect', 0);
    }
}
