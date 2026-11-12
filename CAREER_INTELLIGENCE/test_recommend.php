<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$req = Request::create('/profile/recommend-occupations', 'POST', [
    'jurusan' => 'SMK - Teknologi Informasi & Komunikasi (RPL/TKJ)',
    'prodi' => 'Teknik Komputer & Jaringan (TKJ)',
    'semester' => 'Siswa SMK/SMA Kelas 12',
    'target_field' => 'it',
]);

$ctrl = new ProfileController;
echo json_encode($ctrl->recommendOccupations($req)->getData());
