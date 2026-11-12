<?php

use App\Mail\MitraWelcomeMail;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    $user = User::where('email', 'natyakivany09@gmail.com')->first();
    if ($user) {
        Mail::to($user->email)->send(new MitraWelcomeMail($user, 'password123'));
        echo "SUCCESS - Sent MitraWelcomeMail\n";
    } else {
        echo "User not found\n";
    }
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}
