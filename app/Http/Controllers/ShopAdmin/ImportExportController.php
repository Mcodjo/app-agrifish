<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Services\Shop\ShopSpreadsheet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExportController extends Controller
{
    public function __construct(private ShopSpreadsheet $sheets)
    {
    }

    public function index(): View
    {
        return view('shop-admin.import-export', ['columns' => ShopSpreadsheet::CATALOG_COLUMNS]);
    }

    public function exportCatalog(): BinaryFileResponse
    {
        return response()->download($this->sheets->exportCatalog(), 'catalogue-agrifish-'.now()->format('Y-m-d').'.xlsx')->deleteFileAfterSend();
    }

    public function exportSales(Request $request): BinaryFileResponse
    {
        $validated = $request->validate(['du' => ['nullable', 'date'], 'au' => ['nullable', 'date', 'after_or_equal:du']]);

        return response()->download(
            $this->sheets->exportSales($validated['du'] ?? null, $validated['au'] ?? null),
            'ventes-agrifish-'.now()->format('Y-m-d').'.xlsx'
        )->deleteFileAfterSend();
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:10240']]);

        $result = $this->sheets->importCatalog($request->file('file'), $request->user());

        return back()->with('import_result', $result);
    }
}
