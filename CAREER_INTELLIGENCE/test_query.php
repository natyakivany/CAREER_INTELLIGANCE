<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$q = User::where('role', 'mahasiswa')
    ->whereHas('studentProfile', function ($query) {
        $query->where('prodi', 'like', '%Teknik Komputer%');
    });

echo 'Teknik Komputer count: '.$q->count()."\n";

$q2 = User::where('role', 'mahasiswa')
    ->whereHas('studentProfile', function ($query) {
        $query->where('prodi', 'like', '%Teknik Rekayasa Perangkat Lunak%');
    });

echo 'RPL count: '.$q2->count()."\n";
