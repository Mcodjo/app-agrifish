<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $status = $request->query('status');

        $signatures = Signature::query()
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            }))
            ->when($status === 'verified', fn ($query) => $query->whereNotNull('verified_at'))
            ->when($status === 'pending', fn ($query) => $query->whereNull('verified_at'))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.index', [
            'signatures' => $signatures,
            'q' => $q,
            'status' => $status,
            'stats' => [
                'total' => Signature::count(),
                'verified' => Signature::whereNotNull('verified_at')->count(),
                'pending' => Signature::whereNull('verified_at')->count(),
                'today' => Signature::whereDate('created_at', today())->count(),
            ],
            'profiles' => Signature::counted()
                ->selectRaw('profile, count(*) as total')->groupBy('profile')
                ->orderByDesc('total')->get(),
            'cities' => Signature::counted()
                ->selectRaw('city, count(*) as total')->groupBy('city')
                ->orderByDesc('total')->limit(10)->get(),
        ]);
    }

    public function destroy(Signature $signature): RedirectResponse
    {
        $signature->delete();

        return back()->with('status', 'Signature supprimée.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'signatures-petition-porto-novo-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM pour Excel
            fputcsv($out, ['N°', 'Prénom', 'Nom', 'E-mail', 'Commune', 'Profil', 'Commentaire', 'Nom public', 'Accepte les nouvelles', 'Confirmée le', 'Signée le'], ';');

            $n = 0;
            Signature::counted()->orderBy('created_at')->chunk(500, function ($rows) use ($out, &$n) {
                foreach ($rows as $s) {
                    fputcsv($out, [
                        ++$n,
                        $s->first_name,
                        $s->last_name,
                        $s->email,
                        $s->city,
                        $s->profileLabel(),
                        $s->comment,
                        $s->show_name ? 'oui' : 'non',
                        $s->accepts_updates ? 'oui' : 'non',
                        optional($s->verified_at)->format('d/m/Y H:i'),
                        $s->created_at->format('d/m/Y H:i'),
                    ], ';');
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
