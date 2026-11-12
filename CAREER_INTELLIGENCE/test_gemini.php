<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$key = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');

$models = ['antigravity-preview-09-2026', 'antigravity-preview-latest', 'deep-research-preview-04-2026', 'veo-3.1-generate-preview', 'gemini-embedding-001', 'aqa'];

foreach ($models as $model) {
    echo "Testing $model...\n";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=".$key;
    $response = Http::withoutVerifying()->post($url, [
        'contents' => [['parts' => [['text' => 'hello']]]],
    ]);
    if ($response->successful()) {
        echo "SUCCESS for $model\n";
    } else {
        echo "FAILED for $model: ".$response->body()."\n";
    }
}
