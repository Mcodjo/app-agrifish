<?php

namespace App\Support;

use App\Models\ServiceRequest;
use App\Models\Shop\Order;
use App\Models\Shop\ProductReview;
use App\Models\User;

/** Menu latéral de l'espace connecté, selon le rôle. */
class EspaceNavigation
{
    /**
     * @return array<int, array{title: ?string, items: array<int, array{label: string, url: string, active: bool, icon: string, badge?: int}>}>
     */
    public static function for(User $user): array
    {
        $item = fn (string $label, string $route, string $icon, string|array|null $pattern = null, ?int $badge = null) => [
            'label' => $label,
            'url' => route($route),
            'active' => request()->routeIs($pattern ?? $route),
            'icon' => $icon,
            'badge' => $badge ?: null,
        ];

        $shopItems = fn () => [
            $item('Tableau de bord', 'shop-admin.dashboard', 'chart', 'shop-admin.dashboard'),
            $item('Commandes', 'shop-admin.orders.index', 'receipt', 'shop-admin.orders.*', Order::query()->whereIn('status', ['new', 'paid'])->count()),
            $item('Produits', 'shop-admin.products.index', 'cube', 'shop-admin.products.*'),
            $item('Catégories', 'shop-admin.categories.index', 'folder', 'shop-admin.categories.*'),
            $item('Codes promo', 'shop-admin.coupons.index', 'tag', 'shop-admin.coupons.*'),
            $item('Avis clients', 'shop-admin.reviews.index', 'star', 'shop-admin.reviews.*', ProductReview::query()->where('status', 'pending')->count()),
            $item('Import et export', 'shop-admin.import-export', 'arrows', 'shop-admin.import-export'),
        ];

        $account = ['title' => 'Mon compte', 'items' => [
            $item('Mon profil', 'profile.edit', 'user'),
        ]];

        return match (true) {
            $user->isAdmin() => [
                ['title' => null, 'items' => [$item('Vue d’ensemble', 'dashboard', 'home')]],
                ['title' => 'Conseil', 'items' => [
                    $item('Demandes', 'admin.requests.index', 'inbox', 'admin.requests.*', ServiceRequest::query()->where('status', 'new')->count()),
                    $item('Projets', 'projects.index', 'briefcase', ['projects.index', 'projects.show']),
                ]],
                ['title' => 'Boutique', 'items' => $shopItems()],
                ['title' => 'Administration', 'items' => [
                    $item('Utilisateurs', 'admin.users.index', 'users', 'admin.users.*'),
                    $item('Réglages', 'admin.settings.edit', 'cog', 'admin.settings.*'),
                ]],
                $account,
            ],
            $user->isManager() => [
                ['title' => 'Boutique', 'items' => $shopItems()],
                $account,
            ],
            $user->isExpert() => [
                ['title' => null, 'items' => [
                    $item('Vue d’ensemble', 'dashboard', 'home'),
                    $item('Projets assignés', 'projects.index', 'briefcase', ['projects.index', 'projects.show']),
                ]],
                $account,
            ],
            default => [
                ['title' => null, 'items' => [
                    $item('Vue d’ensemble', 'dashboard', 'home'),
                    $item('Mes projets', 'projects.index', 'briefcase', ['projects.index', 'projects.show']),
                    $item('Nouvelle demande', 'requests.create', 'plus'),
                ]],
                ['title' => 'Boutique', 'items' => [
                    $item('Mes commandes', 'account.orders.index', 'receipt', 'account.orders.*'),
                    $item('Mes adresses', 'account.addresses.index', 'pin', 'account.addresses.*'),
                    $item('Aller à la boutique', 'shop.index', 'bag'),
                ]],
                $account,
            ],
        };
    }

    /** Tracés d'icônes (24 × 24, trait). */
    public const ICONS = [
        'home' => 'M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5Z',
        'briefcase' => 'M9 6V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V6m-11 0h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Zm-1 6h18',
        'plus' => 'M12 5v14M5 12h14',
        'inbox' => 'M3 13h5l1.5 3h5L16 13h5M5 5h14l2 8v6a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-6l2-8Z',
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6m-6 4h6m-6 4h3',
        'cube' => 'm12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Zm0 0v18m8-13.5-8 4.5-8-4.5',
        'folder' => 'M3 6.5A1.5 1.5 0 0 1 4.5 5H9l2 2.5h8.5A1.5 1.5 0 0 1 21 9v9.5a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.5v-12Z',
        'tag' => 'M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9-9-9Zm5-4.5h.01',
        'star' => 'm12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8L3.5 9.7l5.9-.9L12 3.5Z',
        'arrows' => 'M7 4v16m0 0-3-3m3 3 3-3m7 3V4m0 0-3 3m3-3 3 3',
        'users' => 'M16 19v-1a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v1m20 0v-1a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z',
        'cog' => 'M10.3 4.3a1.7 1.7 0 0 1 3.4 0 1.7 1.7 0 0 0 2.6 1.1 1.7 1.7 0 0 1 2.4 2.4 1.7 1.7 0 0 0 1 2.5 1.7 1.7 0 0 1 0 3.4 1.7 1.7 0 0 0-1 2.6 1.7 1.7 0 0 1-2.4 2.4 1.7 1.7 0 0 0-2.6 1 1.7 1.7 0 0 1-3.4 0 1.7 1.7 0 0 0-2.5-1 1.7 1.7 0 0 1-2.4-2.4 1.7 1.7 0 0 0-1.1-2.6 1.7 1.7 0 0 1 0-3.4 1.7 1.7 0 0 0 1.1-2.5 1.7 1.7 0 0 1 2.4-2.4 1.7 1.7 0 0 0 2.5-1.1ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
        'user' => 'M20 21v-1a5 5 0 0 0-5-5H9a5 5 0 0 0-5 5v1M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z',
        'chart' => 'M4 20V10m6 10V4m6 16v-7m4 7H2',
        'pin' => 'M12 21s-7-6.1-7-11a7 7 0 1 1 14 0c0 4.9-7 11-7 11Zm0-8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z',
        'bag' => 'M6 7h12l1 13H5L6 7Zm3 0V6a3 3 0 0 1 6 0v1',
    ];
}
