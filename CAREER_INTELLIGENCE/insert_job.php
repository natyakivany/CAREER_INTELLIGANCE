<?php

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$u = User::where('name', 'nazwa')->first();
if ($u) {
    JobApplication::firstOrCreate(
        ['user_id' => $u->id, 'job_listing_id' => 57],
        [
            'match_score' => 0,
            'status' => 'Menunggu Review',
            'is_invited' => true,
            'notes' => 'Diundang oleh Mitra Industri.',
        ]
    );
    echo 'Created successfully for user_id: '.$u->id;
} else {
    echo 'User not found';
}
