<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\StudentProfile;
use App\Services\SkillGapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the user's portfolios.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $profile = StudentProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknologi Informasi',
                'semester' => 'Semester 7',
                'target_field' => 'it',
            ]
        );

        $query = $user->portfolios()->latest();

        if ($request->filled('field')) {
            $query->where('field', $request->field);
        }

        $portfolios = $query->get();
        $completeness = app(SkillGapService::class)->calculatePortfolioCompleteness($user);

        return view('portfolio', [
            'portfolios' => $portfolios,
            'profile' => $profile,
            'totalCount' => $user->portfolios()->count(),
            'itCount' => $user->portfolios()->where('field', 'it')->count(),
            'elektroCount' => $user->portfolios()->where('field', 'elektro')->count(),
            'completeness' => $completeness,
            'portfolioCompleteness' => $completeness,
        ]);
    }

    /**
     * Store a newly created portfolio in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'field' => ['required', 'in:it,elektro'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'tech_stack' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'completion_date' => ['nullable', 'date'],
        ]);

        $request->user()->portfolios()->create($validated);

        app(SkillGapService::class)->calculateEmployabilityScore($request->user());

        return redirect()->route('portfolio')->with('status', 'Proyek portofolio berhasil ditambahkan!');
    }

    /**
     * Remove the specified portfolio from storage.
     */
    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $portfolio->delete();

        app(SkillGapService::class)->calculateEmployabilityScore(Auth::user());

        return redirect()->route('portfolio')->with('status', 'Portofolio berhasil dihapus.');
    }
}
