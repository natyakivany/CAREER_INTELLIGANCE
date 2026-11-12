<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Occupation;
use App\Models\StudentProfile;
use App\Services\SkillGapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknologi Informasi',
                'semester' => 'Semester 7',
                'target_field' => 'it',
                'ipk' => 3.50,
            ]
        );

        $occupations = Occupation::orderBy('field')->orderBy('title')->get();
        $portfolioCompleteness = app(SkillGapService::class)->calculatePortfolioCompleteness($user);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'occupations' => $occupations,
            'portfolioCompleteness' => $portfolioCompleteness,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update User core fields
        $user->fill($request->only(['name', 'email', 'nim']));

        // Fresh graduate: clear NIM/NISN since they no longer have active student IDs
        $semester = $request->input('semester', '');
        if (in_array($semester, ['Alumni / Fresh Graduate PT', 'Lulusan SMK/SMA (Siap Kerja)'])) {
            $user->nim = null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update or create Student Profile
        $profileData = $request->only([
            'prodi',
            'institution_name',
            'jurusan',
            'semester',
            'phone',
            'target_field',
            'bio',
            'ipk',
        ]);

        $targetField = $request->input('target_field', 'it');

        if ($request->has('target_occupation_ids')) {
            $occIds = array_values(array_filter(array_map('intval', (array) $request->input('target_occupation_ids', []))));

            // Filter occupation IDs to only include those matching the selected target_field
            if ($targetField === 'it' || $targetField === 'elektro') {
                $occIds = Occupation::whereIn('id', $occIds)
                    ->where('field', $targetField)
                    ->pluck('id')
                    ->toArray();
            }

            $profileData['target_occupation_ids'] = $occIds;
            $profileData['target_occupation_id'] = $occIds[0] ?? null;
        } elseif ($request->filled('target_occupation_id')) {
            $occId = (int) $request->input('target_occupation_id');
            $profileData['target_occupation_id'] = $occId;
            $profileData['target_occupation_ids'] = [$occId];
        } else {
            // No occupation IDs submitted — clear old selections
            $profileData['target_occupation_ids'] = [];
            $profileData['target_occupation_id'] = null;
        }

        // Handle avatar upload / remove
        $studentProfile = $user->studentProfile()->first();
        if ($request->boolean('remove_avatar')) {
            if ($studentProfile && $studentProfile->avatar && Storage::disk('public')->exists($studentProfile->avatar)) {
                Storage::disk('public')->delete($studentProfile->avatar);
            }
            $profileData['avatar'] = null;
        } elseif ($request->hasFile('avatar')) {
            if ($studentProfile && $studentProfile->avatar && Storage::disk('public')->exists($studentProfile->avatar)) {
                Storage::disk('public')->delete($studentProfile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $avatarPath;
        }

        $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        app(SkillGapService::class)->calculateEmployabilityScore($user);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Recommend occupations based on student profile (AI-based heuristics).
     */
    public function recommendOccupations(Request $request)
    {
        $jurusan = $request->input('jurusan', '');
        $prodi = $request->input('prodi',
            'institution_name', '');
        $semester = $request->input('semester', '');
        $targetField = $request->input('target_field', 'all');

        $isSmk = str_contains(strtolower($semester), 'smk');

        $query = Occupation::query();
        if ($targetField !== 'all') {
            $query->where('field', $targetField);
        }

        $allOccupations = $query->get();
        $recommendedIds = [];

        foreach ($allOccupations as $occ) {
            $title = strtolower($occ->title);
            $category = strtolower($occ->category ?? '');

            $score = 0;
            // 1. Matching by Education Level (SMK vs Mahasiswa)
            if ($isSmk) {
                if (str_contains($title, 'junior') || str_contains($title, 'technician') || str_contains($title, 'support') || str_contains($title, 'operator') || str_contains($title, 'admin')) {
                    $score += 5;
                }
            } else {
                if (str_contains($title, 'engineer') || str_contains($title, 'architect') || str_contains($title, 'scientist') || str_contains($title, 'manager') || str_contains($title, 'senior') || str_contains($title, 'analyst') || str_contains($title, 'developer')) {
                    $score += 5;
                }
            }

            // 2. Matching by Major / Specialization
            $prodiLower = strtolower($prodi);
            $jurusanLower = strtolower($jurusan);
            $combinedMajor = $prodiLower.' '.$jurusanLower;

            if (str_contains($combinedMajor, 'perangkat lunak') || str_contains($combinedMajor, 'rpl') || str_contains($combinedMajor, 'informatika') || str_contains($combinedMajor, 'komputer')) {
                if (str_contains($title, 'software') || str_contains($title, 'developer') || str_contains($title, 'programmer') || str_contains($title, 'web') || str_contains($title, 'backend') || str_contains($title, 'frontend') || str_contains($category, 'software')) {
                    $score += 10;
                }
            }
            if (str_contains($combinedMajor, 'jaringan') || str_contains($combinedMajor, 'tkj') || str_contains($combinedMajor, 'telekomunikasi')) {
                if (str_contains($title, 'network') || str_contains($title, 'cloud') || str_contains($title, 'security') || str_contains($title, 'sysadmin') || str_contains($title, 'devops') || str_contains($category, 'infrastructure')) {
                    $score += 10;
                }
            }
            if (str_contains($combinedMajor, 'multimedia') || str_contains($combinedMajor, 'animasi') || str_contains($combinedMajor, 'desain') || str_contains($combinedMajor, 'visual')) {
                if (str_contains($title, 'ui/ux') || str_contains($title, 'designer') || str_contains($title, 'animator') || str_contains($title, 'graphic')) {
                    $score += 10;
                }
            }
            if (str_contains($combinedMajor, 'otomasi') || str_contains($combinedMajor, 'elektronika') || str_contains($combinedMajor, 'mekatronika') || str_contains($combinedMajor, 'listrik') || str_contains($combinedMajor, 'elektro')) {
                if (str_contains($title, 'hardware') || str_contains($title, 'iot') || str_contains($title, 'embedded') || str_contains($title, 'electrical') || str_contains($title, 'automation') || str_contains($title, 'robotics') || str_contains($category, 'hardware')) {
                    $score += 10;
                }
            }
            if (str_contains($combinedMajor, 'data') || str_contains($combinedMajor, 'informasi')) {
                if (str_contains($title, 'data') || str_contains($title, 'machine learning') || str_contains($title, 'ai') || str_contains($category, 'data')) {
                    $score += 5;
                }
            }

            // Add slight randomness to break ties and simulate AI non-determinism
            $score += rand(0, 3);

            if ($score > 3) {
                $recommendedIds[] = ['id' => $occ->id, 'score' => $score];
            }
        }

        usort($recommendedIds, fn ($a, $b) => $b['score'] <=> $a['score']);
        $topIds = array_column(array_slice($recommendedIds, 0, 5), 'id');

        if (empty($topIds) && $allOccupations->count() > 0) {
            $topIds = $allOccupations->random(min(4, $allOccupations->count()))->pluck('id')->toArray();
        }

        return response()->json([
            'success' => true,
            'recommended_ids' => $topIds,
        ]);
    }
}
