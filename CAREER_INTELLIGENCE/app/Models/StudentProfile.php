<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudentProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'ipk' => 'decimal:2',
            'employability_score' => 'integer',
            'cv_custom_skills' => 'array',
            'cv_experience' => 'array',
            'target_occupation_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetOccupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class, 'target_occupation_id');
    }

    /**
     * Get all target occupations selected by the student.
     *
     * @return Collection<int, Occupation>
     */
    public function getTargetOccupationsAttribute(): Collection
    {
        $ids = $this->target_occupation_ids ?? ($this->target_occupation_id ? [$this->target_occupation_id] : []);
        if (empty($ids)) {
            return new Collection;
        }

        return Occupation::whereIn('id', $ids)->get();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return null;
    }

    public function getCleanPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62'.$clean;
        }

        return $clean;
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $clean = $this->clean_phone;

        return $clean ? "https://wa.me/{$clean}" : null;
    }

    public function getWhatsappLinkWithMessage(?string $message = null): ?string
    {
        $clean = $this->clean_phone;
        if (! $clean) {
            return null;
        }

        if ($message) {
            return "https://wa.me/{$clean}?text=".urlencode($message);
        }

        return "https://wa.me/{$clean}";
    }

    /**
     * Check if the student's ATS CV has been filled/created.
     */
    public function isCvFilled(): bool
    {
        return ! empty($this->cv_summary) || ! empty($this->cv_headline);
    }

    public const SMK_PRODIS = [
        'Rekayasa Perangkat Lunak (RPL)',
        'Teknik Komputer & Jaringan (TKJ)',
        'Multimedia / Desain Komunikasi Visual',
        'Pengembangan Gim & Animasi',
        'Teknik Elektronika Industri (TEI)',
        'Teknik Audio Video (TAV)',
        'Teknik Otomasi Industri (TOI)',
    ];

    /**
     * Determine education level ('SMK' or 'Mahasiswa').
     */
    public function getTingkatAttribute(): string
    {
        $semester = (string) ($this->semester ?? '');
        $jurusan = (string) ($this->jurusan ?? '');
        $prodi = (string) ($this->prodi ?? '');
        $institution = (string) ($this->institution_name ?? '');

        if (str_contains($semester, 'SMK') || str_contains($semester, 'SMA')) {
            return 'SMK';
        }

        if (str_contains($jurusan, 'SMK')) {
            return 'SMK';
        }

        if (in_array($prodi, self::SMK_PRODIS, true)) {
            return 'SMK';
        }

        if (stripos($institution, 'SMK') !== false && ! str_contains($jurusan, 'Perguruan Tinggi') && ! str_contains($semester, 'Semester') && ! str_contains($semester, 'PT')) {
            return 'SMK';
        }

        return 'Mahasiswa';
    }

    public function getTingkatLabelAttribute(): string
    {
        return $this->tingkat === 'SMK' ? 'Siswa / Lulusan SMK' : 'Mahasiswa (Perguruan Tinggi)';
    }

    public function isSmk(): bool
    {
        return $this->tingkat === 'SMK';
    }

    public function isMahasiswa(): bool
    {
        return $this->tingkat === 'Mahasiswa';
    }

    /**
     * Scope a query to only include SMK students.
     */
    public function scopeSmk($query)
    {
        return $query->where(function ($q) {
            $q->where('semester', 'LIKE', '%SMK%')
                ->orWhere('semester', 'LIKE', '%SMA%')
                ->orWhere('jurusan', 'LIKE', '%SMK%')
                ->orWhereIn('prodi', self::SMK_PRODIS);
        });
    }

    /**
     * Scope a query to only include higher education (Mahasiswa) students.
     */
    public function scopeMahasiswa($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sq) {
                $sq->whereNull('semester')
                    ->orWhere(function ($ssq) {
                        $ssq->where('semester', 'NOT LIKE', '%SMK%')
                            ->where('semester', 'NOT LIKE', '%SMA%');
                    });
            })
                ->where(function ($sq) {
                    $sq->whereNull('jurusan')
                        ->orWhere('jurusan', 'NOT LIKE', '%SMK%');
                })
                ->where(function ($sq) {
                    $sq->whereNull('prodi')
                        ->orWhereNotIn('prodi', self::SMK_PRODIS);
                });
        });
    }
}
