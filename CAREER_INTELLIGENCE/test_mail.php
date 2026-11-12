<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

try {
    Mail::raw('Test email dari script', function ($msg) {
        $msg->to('natyakivany09@gmail.com')->subject('Test Script');
    });
    echo "SUCCESS\n";
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage()."\n";
}
