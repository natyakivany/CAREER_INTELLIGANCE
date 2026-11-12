<?php

use App\Models\StudentProfile;

$profiles = StudentProfile::with('user')->get();
foreach ($profiles as $p) {
    echo ($p->user ? $p->user->name : 'No User').' : '.$p->employability_score."\n";
}
