<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$updates = [
    'admin@atelier.app' => 'admin',
    'artist@atelier.app' => 'gote',
    'buyer@atelier.app' => 'buyer',
    'piyotm@atelier.app' => 'piyotm',
];

foreach ($updates as $email => $username) {
    $u = App\Models\User::where('email', $email)->first();
    if ($u) {
        $u->username = $username;
        $u->save();
    }
}

App\Models\User::firstOrCreate(
    ['email' => 'd1f1s@atelier.app'],
    [
        'name' => 'D1F1S',
        'username' => 'D1F1S',
        'password' => bcrypt('password'),
        'role' => App\Enums\UserRole::Artist,
        'active_profile' => 'artist'
    ]
);

echo "Users updated and D1F1S created.\n";
