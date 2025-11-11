<?php

return [
    // Enable staged upgrade toggles to migrate safely
    'enabled' => env('UPGRADE_ENABLED', false),

    // Phase indicator: prepare, framework, admin, payments, assets, test, complete
    'phase' => env('UPGRADE_PHASE', 'prepare'),

    // Whitelist payment providers during transition (comma-separated)
    // Example: "paypal,stripe"
    'payment_whitelist' => array_filter(array_map('trim', explode(',', env('PAYMENT_WHITELIST', 'paypal,stripe')))),
];