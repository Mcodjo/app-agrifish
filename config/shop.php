<?php

return [

    // « fedapay » en production. « simulation » affiche une page de paiement de test (hors production uniquement).
    'payment_driver' => env('SHOP_PAYMENT_DRIVER', 'simulation'),

    'currency' => 'XOF',

    'cart_cookie' => 'agrifish_panier',

    'cart_lifetime_days' => 30,

    'max_quantity_per_line' => 999,

    'image_max_kb' => 4096,

    'max_images' => 8,

];
