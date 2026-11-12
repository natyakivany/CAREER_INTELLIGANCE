<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Occupation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAssessmentService
{
    protected ?string $apiKey;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->model = config('services.gemini.model') ?: env('GEMINI_MODEL', 'antigravity-preview-latest');
    }

    /**
     * Generate dynamic assessment questions via Google Gemini API.
     * Fallback to rich, diverse internal question pool if offline or API key not set.
     */
    public function generateQuestions(string $field, ?StudentProfile $profile = null, ?Collection $portfolios = null, int $count = 10, array $excludeQuestionTexts = []): array
    {
        if ($this->apiKey) {
            try {
                $questions = $this->callGeminiForQuestions($field, $profile, $portfolios, $count, $excludeQuestionTexts);
                if (! empty($questions) && count($questions) >= $count) {
                    return array_slice($questions, 0, $count);
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini API call failed, using dynamic fallback bank: '.$e->getMessage());
            }
        }

        return $this->getDynamicFallbackQuestions($field, $count, $excludeQuestionTexts);
    }

    /**
     * Call Google Gemini API endpoint with structured prompt and JSON response format.
     */
    protected function callGeminiForQuestions(string $field, ?StudentProfile $profile, ?Collection $portfolios, int $count, array $excludeQuestionTexts = []): array
    {
        if ($field === 'all') {
            $fieldLabel = 'Bidang Gabungan IT & Teknik Elektro (Software Development & Embedded Systems / IoT)';
        } elseif ($field === 'elektro') {
            $fieldLabel = 'Bidang Teknik Elektro, Embedded Systems & IoT';
        } else {
            $fieldLabel = 'Bidang Teknologi Informasi (Software Development, Web, Database & DevOps)';
        }

        $targetOccupationsContext = '';
        if ($profile) {
            $targetOccupations = $profile->target_occupations;
            if ($targetOccupations->isNotEmpty()) {
                $targetOccupationTitles = $targetOccupations->pluck('title')->implode(', ');
                $skills = $targetOccupations->flatMap(function ($occ) {
                    return $occ->skills->pluck('skill_name');
                })->unique()->take(10)->implode(', ');

                $targetOccupationsContext = "PROFIL TARGET KARIER KANDIDAT:\n"
                    ."- Target Profesi Industri Pilihan: {$targetOccupationTitles}\n"
                    .($skills ? "- Standar Keahlian Kunci yang Diharapkan: {$skills}\n" : '')
                    .'- PENTING: Wajib selaraskan skenario studi kasus teknis dan pemecahan masalah dengan target profesi ini.';
            }
        }

        $portfolioContext = '';
        if ($portfolios && $portfolios->isNotEmpty()) {
            $portfolioContext = 'Mahasiswa memiliki riwayat proyek nyata: '.$portfolios->pluck('title')->implode(', ').'.';
        }

        $excludeContext = '';
        if (! empty($excludeQuestionTexts)) {
            $excludeSample = array_slice($excludeQuestionTexts, 0, 15);
            $excludeContext = "PENTING: Jangan membuat soal yang mirip atau berulang dari soal-soal sebelumnya berikut ini:\n- ".implode("\n- ", $excludeSample)."\n";
        }

        // Variasikan fokus topik secara acak agar setiap sesi memiliki tema unik
        $computerEngineeringSubtopics = [
            'Jaringan Komputer & Subnetting (TCP/IP, CIDR, VLAN, Routing OSPF/BGP, DNS, DHCP)',
            'Keamanan Jaringan & Sistem (Firewall iptables/ufw, Port Scanning, Analisis Wireshark)',
            'Administrasi Server Linux & Cloud (Bash Scripting, SSH Key Management, Docker Container)',
            'Sistem Tertanam & Mikrokontroler (ESP32/STM32 Architecture, FreeRTOS, GPIO)',
            'Protokol Komunikasi Hardware (I2C, SPI, UART, CAN Bus, RS-485)',
            'Internet of Things (IoT Gateway, MQTT Telemetry Broker, Sensor Interfacing & ADC)',
            'Troubleshooting Infrastruktur Jaringan & Kegagalan Hardware Komputer',
        ];

        $softwareSubtopics = [
            'Modern Backend Architecture (Laravel/Node/Go, RESTful API, Idempotency)',
            'Database Engineering (Indexing, ACID, Query Optimization, Skema Relasional)',
            'Web Security & OWASP Top 10 (SQL Injection, XSS, CSRF, Token JWT/Sanctum)',
            'Frontend Architecture & State Management (Modern Component Lifecycle, SPA)',
            'DevOps & CI/CD Pipelines (Automated Testing, Deployment Workflow, Docker)',
            'Struktur Data & Algoritma Praktis (Efisiensi Waktu & Penggunaan Memori)',
            'Asynchronous Message Queues (Redis, Background Job Processing)',
            'Debugging & Log Tracing Kasus Produksi',
        ];

        $hardwareAutomationSubtopics = [
            'PLC & Otomasi Industri (Ladder Logic, Sensor Industri, Relay Protection)',
            'Power Management Sistem Tertanam (Regulator Tegangan, Buck Converter, Battery BMS)',
            'Edge AI & TinyML (Inferensi Model Sensor pada Mikrokontroler)',
            'Sinyal Analog & Pengkondisi Sinyal Sensor (Op-Amp, ADC Filtering)',
        ];

        if ($field === 'all') {
            shuffle($softwareSubtopics);
            shuffle($computerEngineeringSubtopics);
            shuffle($hardwareAutomationSubtopics);
            $subtopicsList = array_merge(
                array_slice($computerEngineeringSubtopics, 0, 2),
                array_slice($softwareSubtopics, 0, 2),
                array_slice($hardwareAutomationSubtopics, 0, 1)
            );
        } elseif ($field === 'elektro') {
            shuffle($hardwareAutomationSubtopics);
            shuffle($computerEngineeringSubtopics);
            $subtopicsList = array_merge(
                array_slice($hardwareAutomationSubtopics, 0, 3),
                array_slice($computerEngineeringSubtopics, 0, 2)
            );
        } else {
            shuffle($softwareSubtopics);
            shuffle($computerEngineeringSubtopics);
            $subtopicsList = array_merge(
                array_slice($softwareSubtopics, 0, 3),
                array_slice($computerEngineeringSubtopics, 0, 2)
            );
        }

        shuffle($subtopicsList);
        $selectedSubtopics = implode(', ', array_slice($subtopicsList, 0, 5));

        $sessionSeed = uniqid('eval_').'-'.now()->timestamp;

        $prompt = <<<PROMPT
Anda adalah sistem penguji ahli sertifikasi kompetensi industri untuk bidang Teknik Komputer, Teknologi Informasi, dan Sistem Tertanam.
Tugas Anda: Buat {$count} butir soal asesmen pilihan ganda berbahasa Indonesia BARU dan UNIK untuk kandidat (mahasiswa / alumni SMK / fresh graduate) pada {$fieldLabel}.
{$targetOccupationsContext}
{$portfolioContext}
Kombinasi fokus subtopik untuk sesi ini: {$selectedSubtopics}.
{$excludeContext}
ID Sesi Unik: {$sessionSeed}

Ketentuan Mutlak:
1. Tepat separuh (50%) soal adalah "hard_skill": Studi kasus teknis nyata, pemecahan masalah (troubleshooting), logika analisis kode/sistem/jaringan/sirkuit, penanganan bug/eror spesifik yang relevan dengan target profesi kandidat.
2. Tepat separuh (50%) soal adalah "soft_skill": Etika rekayasa (engineering ethics), penanganan insiden downtime/kegagalan sistem di bawah tekanan, komunikasi teknis dengan tim/klien, serta prioritisasi solusi di dunia kerja.
3. Setiap soal memiliki 4 opsi jawaban (A, B, C, D) dengan tepat SATU opsi yang benar.
4. HINDARI pertanyaan hafalan definisi singkat atau kepanjangan singkatan. Utamakan skenario nyata industri ("Jika terjadi kondisi X, tindakan rekayasa mana yang paling tepat?").
5. Buat soal berkualitas tinggi, berbobot, dan tidak boleh ada soal duplikat.
5. Format output WAJIB HANYA berupa JSON array objek tanpa teks markdown pembuka/penutup.

Struktur JSON setiap item:
[
  {
    "question_text": "Pertanyaan kasus yang jelas dan mendalam...",
    "type": "hard_skill",
    "topic": "Nama Topik",
    "options": {
      "A": "Teks pilihan A",
      "B": "Teks pilihan B",
      "C": "Teks pilihan C",
      "D": "Teks pilihan D"
    },
    "correct_option": "A",
    "explanation": "Penjelasan singkat mengapa opsi ini paling tepat..."
  }
]
PROMPT;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withoutVerifying()->timeout(25)->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.75,
            ],
        ]);

        if (! $response->successful()) {
            throw new \Exception('Gemini API Error: '.$response->body());
        }

        $data = $response->json();
        $rawJson = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $parsed = json_decode($rawJson, true);
        if (is_array($parsed) && count($parsed) >= 8) {
            return array_slice($parsed, 0, $count);
        }

        throw new \Exception('Failed to parse valid JSON or insufficient questions from Gemini output.');
    }

    /**
     * Call Gemini API to generate a new Trending Occupation Recommendation
     */
    public function generateOccupationRecommendation(string $field): array
    {
        if (! $this->apiKey) {
            throw new \Exception('Gemini API key is not configured.');
        }

        $fieldLabel = $field === 'elektro' ? 'Teknik Elektro, Otomasi & Embedded Systems' : 'Teknologi Informasi & Software Engineering';

        $prompt = <<<PROMPT
Anda adalah ahli karir dan kurikulum industri.
Buat 1 rekomendasi profesi pekerjaan (occupation) BARU dan sedang tren (high demand) di bidang {$fieldLabel}.
Berikan juga 5-6 keahlian inti (skills) spesifik yang sangat dicari industri untuk profesi tersebut.

Format keluaran WAJIB JSON murni tanpa tag markdown:
{
  "title": "Nama Profesi (contoh: Cloud Architect)",
  "category": "Kategori Bidang",
  "description": "Deskripsi singkat mengenai profesi ini (2-3 kalimat)",
  "average_salary": "Rentang Gaji (contoh: Rp 8.000.000 - Rp 15.000.000)",
  "demand_level": "Tinggi",
  "skills": [
    {
      "name": "Nama Keahlian Spesifik (contoh: Kubernetes Cluster Management)",
      "score": 85
    }
  ]
}
PROMPT;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withoutVerifying()->timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.8,
                ],
            ]);

            if (! $response->successful()) {
                throw new \Exception('Gemini API request failed: '.$response->body());
            }

            $body = $response->json();
            $text = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $text = trim(str_replace(['```json', '```'], '', $text));

            $data = json_decode($text, true);

            if (! $data) {
                throw new \Exception('Failed to parse Gemini JSON response.');
            }

            return $data;
        } catch (\Throwable $e) {
            Log::warning('Gemini Occupation generation fallback: '.$e->getMessage());

            // Dummy fallback recommendation
            if ($field === 'elektro') {
                return [
                    'title' => 'IoT & Edge AI Engineer',
                    'category' => 'Hardware & AI',
                    'description' => 'Merancang dan mengimplementasikan model AI pada perangkat edge (ESP32, Jetson Nano) untuk sistem otomatisasi.',
                    'average_salary' => 'Rp 9.000.000 - Rp 18.000.000',
                    'demand_level' => 'Tinggi',
                    'skills' => [
                        ['name' => 'Embedded C/C++', 'score' => 85],
                        ['name' => 'TinyML / Edge Impulse', 'score' => 80],
                        ['name' => 'MQTT & IoT Protocols', 'score' => 85],
                        ['name' => 'Hardware Troubleshooting', 'score' => 80],
                        ['name' => 'Python Scripting', 'score' => 75],
                    ],
                ];
            }

            return [
                'title' => 'Cloud Solutions Architect',
                'category' => 'Cloud & Infrastructure',
                'description' => 'Merancang arsitektur cloud terukur (scalable) dan aman menggunakan layanan AWS, GCP, atau Azure untuk enterprise.',
                'average_salary' => 'Rp 12.000.000 - Rp 25.000.000',
                'demand_level' => 'Sangat Tinggi',
                'skills' => [
                    ['name' => 'AWS/GCP Architecture', 'score' => 90],
                    ['name' => 'Kubernetes & Docker', 'score' => 85],
                    ['name' => 'Infrastructure as Code (Terraform)', 'score' => 85],
                    ['name' => 'CI/CD Pipeline', 'score' => 80],
                    ['name' => 'System Security & Compliance', 'score' => 80],
                ],
            ];
        }
    }

    /**
     * Generate evaluation summary & feedback using Gemini AI.
     */
    public function generateEvaluation(Assessment $assessment): array
    {
        if ($assessment->field === 'all') {
            $field = 'Teknologi Informasi & Teknik Elektro (IT + Embedded Systems)';
        } elseif ($assessment->field === 'elektro') {
            $field = 'Teknik Elektro / Embedded Systems';
        } else {
            $field = 'Teknologi Informasi & Software';
        }
        $score = $assessment->total_score;
        $targetOcc = $assessment->user?->studentProfile?->targetOccupation?->title;
        $targetContext = $targetOcc ? "dengan target profesi sasaran '{$targetOcc}'" : '';

        if ($this->apiKey) {
            try {
                $prompt = "Mahasiswa {$targetContext} telah menyelesaikan asesmen kompetensi {$field} dengan perolehan skor {$score} dari 100. Berikan evaluasi ringkas dan berbobot dalam format JSON berisi 3 kunci: 'feedback' (paragraf ringkasan apresiasi dan evaluasi persiapan kariernya), 'strengths' (daftar kelebihan teknis teridentifikasi), dan 'weaknesses' (daftar aspek teknis spesifik yang perlu ditingkatkan untuk memenuhi standar industri target profesi tersebut).";

                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
                $response = Http::withoutVerifying()->timeout(15)->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $json = json_decode($response->json('candidates.0.content.parts.0.text') ?? '', true);
                    if (is_array($json) && isset($json['feedback'])) {
                        return [
                            'feedback' => $json['feedback'],
                            'strengths' => is_array($json['strengths']) ? implode('; ', $json['strengths']) : (string) $json['strengths'],
                            'weaknesses' => is_array($json['weaknesses']) ? implode('; ', $json['weaknesses']) : (string) $json['weaknesses'],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini feedback generation fallback: '.$e->getMessage());
            }
        }

        // Fallback evaluation
        if ($score >= 80) {
            return [
                'feedback' => "Performa luar biasa! Anda menunjukkan pemahaman konseptual dan analitis yang solid di bidang {$field}, sangat siap untuk level pekerjaan akselerasi dan posisi junior developer.",
                'strengths' => 'Kemampuan pemecahan masalah algoritma/sistem yang akurat; Pemahaman mendalam tentang standar industri.',
                'weaknesses' => 'Perbanyak studi kasus implementasi skala besar dan pengujian otomatis.',
            ];
        } elseif ($score >= 60) {
            return [
                'feedback' => "Hasil yang cukup baik. Anda telah menguasai fundamental {$field}, namun masih terdapat beberapa kesenjangan (skill gap) pada topik lanjutan dan troubleshooting.",
                'strengths' => 'Logika pemrograman dan pemahaman alur kerja dasar sudah baik.',
                'weaknesses' => 'Perlu memperdalam arsitektur sistem modular dan pemantauan error industri.',
            ];
        }

        return [
            'feedback' => 'Anda telah menyelesaikan uji kompetensi awal. Masih terdapat kesenjangan signifikan yang perlu dikejar melalui rekomendasi modul dan roadmap belajar terstruktur.',
            'strengths' => 'Minat eksplorasi pada topik teknologi modern.',
            'weaknesses' => 'Pemahaman dasar sintaksis, logika alur eksekusi, dan debugging hardware/software.',
        ];
    }

    /**
     * Fallback Question Bank (Large, diverse pool of 60+ questions for IT & Elektro).
     * Guarantees different questions on repeated tests by filtering recently answered questions.
     */
    protected function getDynamicFallbackQuestions(string $field, int $count = 10, array $excludeQuestionTexts = []): array
    {
        if ($field === 'all') {
            $allQuestions = array_merge($this->getItQuestionPool(), $this->getElektroQuestionPool());
        } elseif ($field === 'elektro') {
            $allQuestions = $this->getElektroQuestionPool();
        } else {
            $allQuestions = $this->getItQuestionPool();
        }

        // Pisahkan menjadi hard skill dan soft skill
        $hardPool = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'hard_skill'));
        $softPool = array_values(array_filter($allQuestions, fn ($q) => ($q['type'] ?? '') === 'soft_skill'));

        // Filter soal yang pernah dijawab sebelumnya agar setiap tes mendapatkan soal berbeda
        $filterUnasked = function (array $pool) use ($excludeQuestionTexts): array {
            if (empty($excludeQuestionTexts)) {
                return $pool;
            }
            $unasked = array_values(array_filter($pool, function ($q) use ($excludeQuestionTexts) {
                return ! in_array($q['question_text'], $excludeQuestionTexts, true);
            }));

            // Jika bank soal yang belum dikerjakan masih cukup, prioritaskan yang belum dikerjakan
            return count($unasked) >= 3 ? $unasked : $pool;
        };

        $availableHard = $filterUnasked($hardPool);
        $availableSoft = $filterUnasked($softPool);

        shuffle($availableHard);
        shuffle($availableSoft);

        // Alokasikan rasio 50% hard skill dan 50% soft skill
        $targetHard = (int) round($count * 0.5);
        $targetSoft = $count - $targetHard;

        $selectedHard = array_slice($availableHard, 0, min($targetHard, count($availableHard)));
        $selectedSoft = array_slice($availableSoft, 0, min($targetSoft, count($availableSoft)));

        $selected = array_merge($selectedHard, $selectedSoft);

        // Jika jumlah masih kurang dari $count, ambil sisanya dari gabungan pool
        if (count($selected) < $count) {
            $remainingNeeded = $count - count($selected);
            $selectedTexts = array_column($selected, 'question_text');
            $leftover = array_values(array_filter($allQuestions, fn ($q) => ! in_array($q['question_text'], $selectedTexts, true)));
            shuffle($leftover);
            $selected = array_merge($selected, array_slice($leftover, 0, $remainingNeeded));
        }

        // Acak urutan tampilan soal agar nomor soal tidak selalu sama posisinya
        shuffle($selected);

        return array_slice($selected, 0, $count);
    }

    /**
     * Bank Soal Komprehensif Bidang IT (30 Soal Industri Berstandar Kompetensi).
     */
    protected function getItQuestionPool(): array
    {
        return [
            // --- HARD SKILLS (21 Soal) ---
            [
                'question_text' => 'Dalam arsitektur RESTful API modern, metode HTTP manakah yang bersifat idempoten (idempotent) dan digunakan untuk memperbarui seluruh representasi sumber daya (resource)?',
                'type' => 'hard_skill',
                'topic' => 'RESTful API Architecture',
                'options' => [
                    'A' => 'PUT',
                    'B' => 'POST',
                    'C' => 'PATCH',
                    'D' => 'DELETE',
                ],
                'correct_option' => 'A',
                'explanation' => 'PUT bersifat idempoten karena pemanggilan berulang kali dengan payload yang sama akan menghasilkan status akhir sumber daya yang identik.',
            ],
            [
                'question_text' => 'Pada framework Laravel, apa solusi terbaik untuk mengatasi masalah query performa "N+1 Problem" ketika mengambil relasi data mahasiswa beserta daftar portofolionya?',
                'type' => 'hard_skill',
                'topic' => 'ORM & Database Performance',
                'options' => [
                    'A' => 'Menggunakan Eager Loading dengan method User::with(\'portfolios\')->get()',
                    'B' => 'Melakukan query di dalam perulangan foreach blade template',
                    'C' => 'Menonaktifkan relasi foreign key pada migrasi database',
                    'D' => 'Mengubah tipe basis data menjadi NoSQL MongoDB',
                ],
                'correct_option' => 'A',
                'explanation' => 'Eager Loading mengambil semua relasi terkait hanya dalam 1-2 query SQL gabungan, mencegah query berulang sebanyak N baris data.',
            ],
            [
                'question_text' => 'Dalam aplikasi web modern berbasis React / Vue, apa tujuan utama penggunaan Virtual DOM dibandingkan langsung memanipulasi Real DOM browser?',
                'type' => 'hard_skill',
                'topic' => 'Modern Frontend Architecture',
                'options' => [
                    'A' => 'Menghitung perbedaan (diffing) di memori dan meminimalkan operasi render reflow/repaint pada Real DOM yang mahal',
                    'B' => 'Menyimpan seluruh data aplikasi ke dalam LocalStorage secara otomatis',
                    'C' => 'Mengamankan web dari serangan SQL Injection dan XSS secara penuh',
                    'D' => 'Mempercepat transmisi data TCP antara klien dan server',
                ],
                'correct_option' => 'A',
                'explanation' => 'Virtual DOM membandingkan snapshot pohon DOM sebelum dan sesudah perubahan state, lalu hanya menerapkan perubahan spesifik pada browser.',
            ],
            [
                'question_text' => 'Ketika query laporan tracer study pada basis data MySQL yang memiliki 500.000 baris data terasa lambat, optimasi indeks apa yang paling tepat dipasang?',
                'type' => 'hard_skill',
                'topic' => 'Database Indexing & Optimization',
                'options' => [
                    'A' => 'Menambahkan B-Tree Index pada kolom yang sering digunakan di klausa WHERE dan JOIN',
                    'B' => 'Menghapus primary key pada tabel',
                    'C' => 'Mengubah semua tipe data VARCHAR menjadi TEXT',
                    'D' => 'Melakukan query SELECT * tanpa klausa limit',
                ],
                'correct_option' => 'A',
                'explanation' => 'Index B-Tree mempercepat pencarian data dari kompleksitas O(N) full table scan menjadi O(log N).',
            ],
            [
                'question_text' => 'Dalam alur kerja Git kolaboratif tim industri, perintah apa yang digunakan untuk mengambil perubahan terbaru dari remote server sekaligus menggabungkannya ke branch kerja saat ini?',
                'type' => 'hard_skill',
                'topic' => 'Git Version Control',
                'options' => [
                    'A' => 'git pull',
                    'B' => 'git push --force',
                    'C' => 'git stash drop',
                    'D' => 'git reset --hard',
                ],
                'correct_option' => 'A',
                'explanation' => 'git pull menjalankan kombinasi git fetch (mengambil data dari remote) dan git merge (menggabungkan ke branch lokal).',
            ],
            [
                'question_text' => 'Dalam arsitektur container Docker, apa fungsi utama dari file Dockerfile dalam siklus deployment aplikasi web?',
                'type' => 'hard_skill',
                'topic' => 'DevOps & Containerization',
                'options' => [
                    'A' => 'Berisi instruksi deklaratif untuk membangun Image aplikasi yang terisolasi dan konsisten di berbagai lingkungan',
                    'B' => 'Menyimpan password database production secara plaintext',
                    'C' => 'Menggantikan fungsi web server Nginx dan Apache',
                    'D' => 'Sebagai pengganti compiler bahasa pemrograman',
                ],
                'correct_option' => 'A',
                'explanation' => 'Dockerfile mendefinisikan layer sistem operasi dasar, dependensi library, dan konfigurasi runtime untuk menghasilkan Docker Image.',
            ],
            [
                'question_text' => 'Manakah langkah proteksi paling mendasar dan efektif untuk mencegah kerentanan SQL Injection pada aplikasi web?',
                'type' => 'hard_skill',
                'topic' => 'Keamanan Perangkat Lunak',
                'options' => [
                    'A' => 'Menggunakan Parameterized Queries atau Prepared Statements yang memisahkan instruksi SQL dari data masukan pengguna',
                    'B' => 'Menyimpan data password menggunakan enkripsi Base64',
                    'C' => 'Mengubah port default web server dari port 80 menjadi port 8080',
                    'D' => 'Menonaktifkan firewall pada server database',
                ],
                'correct_option' => 'A',
                'explanation' => 'Prepared statements memastikan parameter input diperlakukan murni sebagai data literal, bukan sebagai kode SQL executable.',
            ],
            [
                'question_text' => 'Untuk mencegah serangan Cross-Site Scripting (Stored XSS) pada kolom komentar pengguna, mekanisme apa yang wajib diimplementasikan sebelum merender konten ke browser?',
                'type' => 'hard_skill',
                'topic' => 'Keamanan Web (OWASP)',
                'options' => [
                    'A' => 'HTML Entity Encoding / Sanitasi output sehingga tag seperti <script> tidak dieksekusi browser',
                    'B' => 'Mengharuskan pengguna mengunggah file zip',
                    'C' => 'Menggunakan protokol HTTP/2',
                    'D' => 'Menghapus session cookie setelah 5 detik',
                ],
                'correct_option' => 'A',
                'explanation' => 'Encoding mengonversi karakter berbahaya seperti < dan > menjadi &lt; dan &gt; sehingga browser merendernya sebagai teks biasa.',
            ],
            [
                'question_text' => 'Pada arsitektur sistem berbasis Microservices, pola arsitektur apa yang digunakan untuk mencegah kegagalan satu service merambat (cascading failure) ke seluruh ekosistem aplikasi?',
                'type' => 'hard_skill',
                'topic' => 'Arsitektur Microservices',
                'options' => [
                    'A' => 'Circuit Breaker Pattern',
                    'B' => 'Singleton Pattern',
                    'C' => 'Monolithic Model',
                    'D' => 'Direct Synchronous Loop',
                ],
                'correct_option' => 'A',
                'explanation' => 'Circuit Breaker memutus panggilan sementara ke service yang sedang down/timeout dan memberikan fallback response instan.',
            ],
            [
                'question_text' => 'Ketika aplikasi web mengalami lonjakan pengiriman email konfirmasi (10.000 email sekaligus), arsitektur apa yang paling tepat digunakan agar antarmuka pengguna tidak macet (non-blocking)?',
                'type' => 'hard_skill',
                'topic' => 'Message Broker & Asynchronous Queue',
                'options' => [
                    'A' => 'Asynchronous Message Queue (misal: Redis Queue / RabbitMQ) yang diproses oleh background worker',
                    'B' => 'Menjalankan loop pengiriman email di controller utama request HTTP',
                    'C' => 'Meminta pengguna untuk menunggu loading browser selama 15 menit',
                    'D' => 'Mengubah ukuran RAM komputer klien',
                ],
                'correct_option' => 'A',
                'explanation' => 'Queue menyimpan tugas di antrean pesan dan langsung mengembalikan respon HTTP ke pengguna dalam hitungan milidetik.',
            ],
            [
                'question_text' => 'Dalam prinsip transaksi ACID pada basis data relasional, apa yang dimaksud dengan sifat "Atomicity"?',
                'type' => 'hard_skill',
                'topic' => 'Database Transaction (ACID)',
                'options' => [
                    'A' => 'Semua operasi dalam satu transaksi harus berhasil sepenuhnya, jika satu gagal maka seluruh transaksi dibatalkan (Rollback)',
                    'B' => 'Data disimpan dalam partisi atomik memori cache RAM',
                    'C' => 'Setiap tabel harus memiliki minimal 10 indeks kolom',
                    'D' => 'Database tidak boleh diakses oleh lebih dari satu pengguna dalam satu detik',
                ],
                'correct_option' => 'A',
                'explanation' => 'Atomicity menjamin prinsip "all or nothing", mencegah inkonsistensi data parsial saat terjadi gangguan server.',
            ],
            [
                'question_text' => 'Pada protokol HTTPS, bagaimana proses pertukaran kunci enkripsi terjadi saat proses TLS Handshake awal?',
                'type' => 'hard_skill',
                'topic' => 'Jaringan & Kriptografi',
                'options' => [
                    'A' => 'Menggunakan kriptografi asimetris (Public/Private Key) untuk menyepakati Symmetric Session Key yang efisien',
                    'B' => 'Mengirimkan password database secara plaintext',
                    'C' => 'Hanya menggunakan algoritma hashing MD5 satu arah',
                    'D' => 'Mengabaikan sertifikat SSL jika domain tidak cocok',
                ],
                'correct_option' => 'A',
                'explanation' => 'TLS menggunakan asimetris untuk autentikasi dan negosiasi kunci, kemudian menggunakan simetris untuk enkripsi data berkecepatan tinggi.',
            ],
            [
                'question_text' => 'Pada antarmuka input pencarian real-time (auto-complete), teknik apa yang digunakan untuk menunda eksekusi pemanggilan API hingga pengguna berhenti mengetik selama durasi tertentu?',
                'type' => 'hard_skill',
                'topic' => 'Frontend Optimization',
                'options' => [
                    'A' => 'Debouncing',
                    'B' => 'Throttling',
                    'C' => 'Server Polling',
                    'D' => 'CSS Keyframe Animation',
                ],
                'correct_option' => 'A',
                'explanation' => 'Debounce me-reset timer setiap kali karakter baru diketik, sehingga request API hanya dikirim saat pengguna jeda sejenak.',
            ],
            [
                'question_text' => 'Dalam strategi caching data performa tinggi, pola "Cache-Aside" (Lazy Loading) bekerja dengan cara:',
                'type' => 'hard_skill',
                'topic' => 'Sistem Caching (Redis/Memcached)',
                'options' => [
                    'A' => 'Aplikasi memeriksa cache terlebih dahulu; jika miss, ambil dari database lalu simpan salinannya ke cache',
                    'B' => 'Menghapus database utama dan hanya mengandalkan cache RAM',
                    'C' => 'Menulis data hanya ke cache tanpa pernah menyimpannya ke database',
                    'D' => 'Memuat seluruh tabel database ke cache saat komputer pertama kali dinyalakan',
                ],
                'correct_option' => 'A',
                'explanation' => 'Cache-aside hanya menyimpan data yang benar-benar di-request pengguna, menjaga efisiensi kapasitas memori cache.',
            ],
            [
                'question_text' => 'Berapa kompleksitas waktu rata-rata algoritma Binary Search untuk mencari suatu data pada larik (array) yang sudah terurut sepanjang N elemen?',
                'type' => 'hard_skill',
                'topic' => 'Struktur Data & Algoritma',
                'options' => [
                    'A' => 'O(log N)',
                    'B' => 'O(N)',
                    'C' => 'O(N^2)',
                    'D' => 'O(1)',
                ],
                'correct_option' => 'A',
                'explanation' => 'Binary Search membagi ruang pencarian menjadi separuh pada setiap iterasi, menghasilkan kompleksitas logaritmik O(log N).',
            ],
            [
                'question_text' => 'Dalam prinsip SOLID, prinsip "Dependency Inversion Principle" (DIP) menyatakan bahwa:',
                'type' => 'hard_skill',
                'topic' => 'Clean Code & Desain Perangkat Lunak',
                'options' => [
                    'A' => 'Modul tingkat tinggi tidak boleh bergantung pada modul tingkat rendah; keduanya harus bergantung pada abstraksi (interface)',
                    'B' => 'Semua fungsi harus ditulis dalam satu file class besar',
                    'C' => 'Variabel harus dideklarasikan terbalik dari urutan eksekusi',
                    'D' => 'Database tidak boleh memiliki relasi tabel antar entitas',
                ],
                'correct_option' => 'A',
                'explanation' => 'DIP memisahkan implementasi konkret melalui interface sehingga kode mudah diuji (mocking) dan fleksibel diubah.',
            ],
            [
                'question_text' => 'Pada sistem operasi Linux server, perintah baris apa yang paling cepat digunakan untuk memantau proses mana yang paling banyak memakan CPU dan memori secara interaktif?',
                'type' => 'hard_skill',
                'topic' => 'Linux & Server Administration',
                'options' => [
                    'A' => 'top atau htop',
                    'B' => 'ls -la',
                    'C' => 'chmod 777',
                    'D' => 'ping localhost',
                ],
                'correct_option' => 'A',
                'explanation' => 'Perintah top/htop menampilkan task manager interaktif di terminal dengan urutan penggunaan CPU dan RAM terkini.',
            ],
            [
                'question_text' => 'Struktur standar JSON Web Token (JWT) terdiri dari tiga bagian yang dipisahkan tanda titik (.):',
                'type' => 'hard_skill',
                'topic' => 'Autentikasi & Otorisasi Modern',
                'options' => [
                    'A' => 'Header.Payload.Signature',
                    'B' => 'User.Password.Token',
                    'C' => 'Protocol.Domain.Port',
                    'D' => 'Encryption.Salt.Hash',
                ],
                'correct_option' => 'A',
                'explanation' => 'Header berisi tipe token dan algoritma enkripsi, Payload berisi klaim data, dan Signature memverifikasi keaslian token.',
            ],
            [
                'question_text' => 'Kapan arsitektur basis data NoSQL tipe Dokumen (seperti MongoDB) lebih diutamakan dibandingkan SQL Relasional tradisional?',
                'type' => 'hard_skill',
                'topic' => 'Database Architecture',
                'options' => [
                    'A' => 'Saat skema data bersifat dinamis, tidak terstruktur ketat, dan membutuhkan skalabilitas horizontal cepat',
                    'B' => 'Saat membutuhkan integritas transaksi multi-tabel perbankan yang ketat',
                    'C' => 'Saat data hanya berjumlah kurang dari 100 baris',
                    'D' => 'Saat tidak diperbolehkan menggunakan memori RAM di server',
                ],
                'correct_option' => 'A',
                'explanation' => 'NoSQL dokumen menyimpan data JSON-like polimorfik tanpa perlu migrasi DDL kaku setiap perubahan struktur kolom.',
            ],
            [
                'question_text' => 'Teknik apa yang digunakan oleh web browser untuk mempercepat metrik LCP (Largest Contentful Paint) pada halaman web yang memiliki banyak gambar besar?',
                'type' => 'hard_skill',
                'topic' => 'Web Performance & Core Web Vitals',
                'options' => [
                    'A' => 'Implementasi Lazy Loading (loading="lazy"), format WebP/AVIF modern, dan CDN aset statis',
                    'B' => 'Mengubah gambar menjadi file audio',
                    'C' => 'Menghapus file CSS styling utama',
                    'D' => 'Menjalankan loop JavaScript untuk mengunduh semua gambar bersamaan di thread utama',
                ],
                'correct_option' => 'A',
                'explanation' => 'Lazy loading menunda download gambar off-screen hingga pengguna menggulir mendekati viewport, membebaskan bandwidth.',
            ],
            [
                'question_text' => 'Teknologi protokol apa yang paling efisien untuk mengimplementasikan fitur Dashboard Monitoring Real-Time dengan komunikasi dua arah (full-duplex) tanpa overhead polling berulang?',
                'type' => 'hard_skill',
                'topic' => 'Komunikasi Real-time',
                'options' => [
                    'A' => 'WebSocket',
                    'B' => 'HTTP Short Polling',
                    'C' => 'FTP Transfer',
                    'D' => 'SMTP Mail Service',
                ],
                'correct_option' => 'A',
                'explanation' => 'WebSocket mempertahankan koneksi TCP tunggal terbuka secara persisten, memungkinkan push data instan dari server ke klien.',
            ],

            // --- SOFT SKILLS (9 Soal) ---
            [
                'question_text' => 'Anda menemukan bug kritis di server production tepat 30 menit sebelum jam peluncuran sistem. Sikap profesional rekayasa apa yang paling tepat dilakukan?',
                'type' => 'soft_skill',
                'topic' => 'Problem Solving & Insiden Kerja',
                'options' => [
                    'A' => 'Mengomunikasikan temuan ke tim/stakeholder secara transparan, menganalisis risiko, dan menerapkan hotfix teruji atau menunda peluncuran secara aman',
                    'B' => 'Mengabaikan bug tersebut dengan harapan tidak ditemukan oleh pengguna',
                    'C' => 'Menyalahkan rekan tim yang menulis modul tersebut di grup publik',
                    'D' => 'Melakukan perubahan langsung di server live tanpa membuat cadangan kode dan database',
                ],
                'correct_option' => 'A',
                'explanation' => 'Integritas teknis dan komunikasi risiko secara objektif merupakan soft-skill paling berharga bagi seorang profesional engineering.',
            ],
            [
                'question_text' => 'Ketika melakukan Code Review pada pull request rekan satu tim, Anda menemukan pendekatan kode yang berbeda dengan gaya Anda namun tetap benar dan efisien. Tindakan terbaik Anda adalah:',
                'type' => 'soft_skill',
                'topic' => 'Kolaborasi & Code Review',
                'options' => [
                    'A' => 'Menyetujui PR jika memenuhi standar arsitektur tim dan uji unit lulus, sambil memberikan saran konstruktif opsional',
                    'B' => 'Menolak PR dan memaksakan gaya koding pribadi Anda',
                    'C' => 'Mengabaikan PR tersebut tanpa memberikan review selama berhari-hari',
                    'D' => 'Mengubah kode rekan kerja secara sepihak tanpa izin',
                ],
                'correct_option' => 'A',
                'explanation' => 'Code review berfokus pada kualitas, kebenaran, dan keterbacaan kode tim, bukan preferensi gaya personal penguji.',
            ],
            [
                'question_text' => 'Klien meminta penambahan 3 fitur kompleks baru di tengah sprint yang sudah berjalan dengan batas waktu deadline yang tidak bisa diundur. Bagaimana respons terbaik Anda?',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Ekspektasi & Agile',
                'options' => [
                    'A' => 'Mendiskusikan trade-off secara profesional, menetapkan prioritas fitur MVP, dan memindahkan fitur tambahan ke sprint berikutnya',
                    'B' => 'Langsung mengiyakan semua permintaan meskipun tahu tim tidak akan sanggup menyelesaikannya',
                    'C' => 'Memutus kontak dengan klien dan tidak masuk kerja',
                    'D' => 'Menghapus pengujian unit dan standar keamanan demi mengejar waktu',
                ],
                'correct_option' => 'A',
                'explanation' => 'Pengembang profesional mengelola ruang lingkup secara transparan dengan fokus pada nilai bisnis dan viabilitas teknis.',
            ],
            [
                'question_text' => 'Setelah insiden downtime sistem berhasil dipulihkan, tim mengadakan rapat "Blameless Post-Mortem". Apa tujuan utama dari rapat ini?',
                'type' => 'soft_skill',
                'topic' => 'Budaya Teknik & Retrospektif',
                'options' => [
                    'A' => 'Mengidentifikasi akar penyebab sistemik (Root Cause) dan merancang mitigasi pencegahan agar insiden serupa tidak terulang',
                    'B' => 'Mencari satu orang untuk dijatuhi sanksi dan disalahkan atas insiden',
                    'C' => 'Menghapus log error agar reputasi tim tidak terlihat buruk',
                    'D' => 'Memperdebatkan siapa yang paling berjasa memulihkan sistem',
                ],
                'correct_option' => 'A',
                'explanation' => 'Blameless post-mortem mengasumsikan kesalahan terjadi akibat kelemahan sistem dan prosedur, bukan kelalaian jahat individu.',
            ],
            [
                'question_text' => 'Seorang stakeholder non-teknis memberikan kebutuhan aplikasi yang sangat samar: "Buat sistem yang pintar dan otomatis". Langkah konkret apa yang harus Anda ambil?',
                'type' => 'soft_skill',
                'topic' => 'Komunikasi Kebutuhan Bisnis',
                'options' => [
                    'A' => 'Mengajak stakeholder berdiskusi dengan membuat alur use case sederhana, kriteria keberhasilan yang terukur, dan wireframe interaktif',
                    'B' => 'Langsung menulis kode rumit berdasarkan asumsi sendiri tanpa konfirmasi',
                    'C' => 'Menolak proyek dan mengatakan stakeholder tidak kompeten',
                    'D' => 'Menunggu sampai ada dokumen spesifikasi lengkap setebal 100 halaman',
                ],
                'correct_option' => 'A',
                'explanation' => 'Menerjemahkan ide abstrak menjadi kriteria terukur dan prototipe visual adalah peran kunci perekayasa perangkat lunak.',
            ],
            [
                'question_text' => 'Bagaimana strategi tim developer dalam mengelola "Technical Debt" (utang teknis) agar aplikasi tetap scalable dalam jangka panjang?',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Utang Teknis',
                'options' => [
                    'A' => 'Mendokumentasikan technical debt di backlog dan mengalokasikan persentase kapasitas tertentu di tiap sprint untuk refactoring',
                    'B' => 'Tidak pernah melakukan refactoring sampai aplikasi benar-benar crash',
                    'C' => 'Menulis ulang seluruh aplikasi dari nol setiap kali ada teknologi framework baru',
                    'D' => 'Menyembunyikan kode yang berantakan dengan mengabaikan peringatan linter',
                ],
                'correct_option' => 'A',
                'explanation' => 'Mengalokasikan kapasitas berkala (misal 15-20% per sprint) menjaga keseimbangan antara rilis fitur dan kesehatan basis kode.',
            ],
            [
                'question_text' => 'Tim backend perlu merilis pembaruan endpoint API yang mengubah format payload response secara signifikan (breaking change). Apa langkah profesional terbaik sebelum perubahan di-deploy?',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Rilis & Kolaborasi',
                'options' => [
                    'A' => 'Membuat API versioning baru (misal /api/v2), mengomunikasikan timeline deprecation ke tim mobile/frontend, dan menjaga v1 tetap berjalan sementara',
                    'B' => 'Langsung mengubah endpoint v1 di production tanpa pemberitahuan apapun',
                    'C' => 'Mematikan server mobile secara tiba-tiba agar mereka sadar ada pembaruan',
                    'D' => 'Menolak melakukan pembaruan selamanya demi kompatibilitas lama',
                ],
                'correct_option' => 'A',
                'explanation' => 'Versioning API dan komunikasi transparan melindungi integrasi klien dari kegagalan fungsional yang fatal.',
            ],
            [
                'question_text' => 'Seorang anggota tim junior tampak kesulitan dan frustrasi menyelesaikan tugas modul autentikasi selama 2 hari berturut-turut. Sikap empati teknis apa yang harus ditunjukkan?',
                'type' => 'soft_skill',
                'topic' => 'Mentoring & Kerjasama Tim',
                'options' => [
                    'A' => 'Mengajak berdiskusi santai, melakukan sesi pair-programming untuk memecah masalah menjadi sub-tugas yang lebih kecil, dan memberi bimbingan',
                    'B' => 'Mengambil alih tugasnya secara diam-diam tanpa menjelaskan apapun',
                    'C' => 'Memarahi junior tersebut di depan seluruh anggota tim saat stand-up meeting',
                    'D' => 'Membiarkannya sampai deadline lewat agar dia belajar sendiri',
                ],
                'correct_option' => 'A',
                'explanation' => 'Pair-programming dan bimbingan memecah hambatan teknis sekaligus mempercepat transfer pengetahuan dalam tim.',
            ],
            [
                'question_text' => 'Laporan audit keamanan menemukan celah kerentanan kritis kategori High pada dependensi pihak ketiga yang digunakan sistem Anda. Tindakan prioritas tim adalah:',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Risiko Keamanan',
                'options' => [
                    'A' => 'Mengevaluasi dampak, memperbarui package ke versi patch terbaru di staging, menjalankan automated test suite, dan segera rilis patch ke production',
                    'B' => 'Menunda penanganan celah keamanan selama 6 bulan sampai roadmap fitur selesai',
                    'C' => 'Menghapus modul keamanan dari sistem agar audit tidak mendeteksi celah lagi',
                    'D' => 'Menganggap celah keamanan hanya teori dan tidak mungkin diserang hacker nyata',
                ],
                'correct_option' => 'A',
                'explanation' => 'Kerentanan kritis pada dependensi harus dimitigasi segera dengan proses verifikasi regresi yang terukur.',
            ],
        ];
    }

    /**
     * Bank Soal Komprehensif Bidang Elektro & Embedded Systems (30 Soal Industri).
     */
    protected function getElektroQuestionPool(): array
    {
        return [
            // --- HARD SKILLS (21 Soal) ---
            [
                'question_text' => 'Pada mikrokontroler ESP32, protokol komunikasi apa yang paling tepat digunakan untuk menghubungkan beberapa sensor suhu dalam satu bus dengan hanya menggunakan dua jalur kabel (SDA dan SCL)?',
                'type' => 'hard_skill',
                'topic' => 'Protokol Bus Serial',
                'options' => [
                    'A' => 'I2C (Inter-Integrated Circuit)',
                    'B' => 'SPI (Serial Peripheral Interface)',
                    'C' => 'UART (Universal Asynchronous Receiver-Transmitter)',
                    'D' => 'Modbus RTU',
                ],
                'correct_option' => 'A',
                'explanation' => 'I2C hanya memerlukan 2 jalur kabel (SDA untuk data dan SCL untuk clock) dan mendukung multi-device dengan sistem addressing.',
            ],
            [
                'question_text' => 'Dalam perancangan sistem IoT berdaya rendah (Battery-Powered Edge Device), strategi firmware apa yang paling efektif untuk menghemat konsumsi energi baterai secara signifikan?',
                'type' => 'hard_skill',
                'topic' => 'Manajemen Daya Firmware',
                'options' => [
                    'A' => 'Mengaktifkan mode Deep Sleep saat mikrokontroler tidak membaca data sensor',
                    'B' => 'Meningkatkan frekuensi clock CPU ke nilai maksimum',
                    'C' => 'Mengirim data telemetri ke server cloud setiap milidetik secara kontinu',
                    'D' => 'Menyalakan modul Wi-Fi secara permanen tanpa reconnect',
                ],
                'correct_option' => 'A',
                'explanation' => 'Mode Deep Sleep mematikan modul-modul berdaya tinggi (CPU, Wi-Fi) dan hanya menyisakan timer/RTC, memangkas arus konsumsi hingga level mikroampere.',
            ],
            [
                'question_text' => 'Ketika mengukur tegangan output dari rangkaian PWM motor DC menggunakan osiloskop, sinyal yang tampak mengalami noise frekuensi tinggi akibat lonjakan induktif motor. Komponen pasif apa yang wajib dipasang sebagai proteksi flyback?',
                'type' => 'hard_skill',
                'topic' => 'Rangkaian Proteksi Elektronika',
                'options' => [
                    'A' => 'Dioda Penyearah (Freewheeling/Flyback Diode) paralel berlawanan arah dengan beban induktif',
                    'B' => 'Kapasitor elektrolit seri dengan tegangan supply',
                    'C' => 'Resistor pull-up 10k Ohm ke VCC',
                    'D' => 'Induktor tambahan pada pin enable',
                ],
                'correct_option' => 'A',
                'explanation' => 'Flyback diode menyalurkan energi medan magnet yang tersimpan pada kumparan motor saat saklar transistor mati, mencegah lonjakan tegangan balik (Back-EMF).',
            ],
            [
                'question_text' => 'Pada sistem otomasi industri berbasis PLC (Programmable Logic Controller), instruksi Ladder Diagram apa yang digunakan untuk mengunci output agar tetap aktif meskipun tombol push-button start telah dilepas?',
                'type' => 'hard_skill',
                'topic' => 'Logika PLC & Otomasi',
                'options' => [
                    'A' => 'Rangkaian Latching (Self-Holding Contact)',
                    'B' => 'Timer On-Delay (TON)',
                    'C' => 'Counter Up (CTU)',
                    'D' => 'Comparator EQU',
                ],
                'correct_option' => 'A',
                'explanation' => 'Latching memasang kontak normally-open milik coil output secara paralel dengan tombol start untuk menjaga loop sinyal tetap tertutup.',
            ],
            [
                'question_text' => 'Dalam protokol komunikasi MQTT yang sering digunakan pada IoT, peran apa yang berfungsi sebagai perantara penerima dan penyalur pesan antara publisher dan subscriber?',
                'type' => 'hard_skill',
                'topic' => 'Protokol Komunikasi IoT',
                'options' => [
                    'A' => 'MQTT Broker',
                    'B' => 'Edge Gateway Node',
                    'C' => 'DNS Server',
                    'D' => 'Payload Serializer',
                ],
                'correct_option' => 'A',
                'explanation' => 'MQTT Broker bertanggung jawab menerima semua pesan dari publisher, menyaring berdasarkan topik, dan mengirimkan ke semua subscriber yang berlangganan.',
            ],
            [
                'question_text' => 'Saat memprogram mikrokontroler dengan FreeRTOS, kondisi apa yang terjadi ketika dua buah Task saling menunggu resource yang sedang dikunci oleh satu sama lain sehingga sistem berhenti merespons?',
                'type' => 'hard_skill',
                'topic' => 'RTOS & Embedded Systems',
                'options' => [
                    'A' => 'Deadlock',
                    'B' => 'Priority Inversion',
                    'C' => 'Stack Overflow',
                    'D' => 'Memory Leakage',
                ],
                'correct_option' => 'A',
                'explanation' => 'Deadlock terjadi saat Task A memegang Resource 1 dan menunggu Resource 2, sedangkan Task B memegang Resource 2 dan menunggu Resource 1.',
            ],
            [
                'question_text' => 'Mengapa pin input digital pada mikrokontroler tidak boleh dibiarkan mengambang (floating pin) tanpa resistor pull-up atau pull-down?',
                'type' => 'hard_skill',
                'topic' => 'Perancangan Sirkuit GPIO',
                'options' => [
                    'A' => 'Pin akan menangkap noise elektromagnetik lingkungan sehingga status logikanya berfluktuasi secara acak antara 0 dan 1',
                    'B' => 'Akan menyebabkan mikrokontroler langsung terbakar seketika',
                    'C' => 'Mencegah tegangan suplai naik menjadi dua kali lipat',
                    'D' => 'Memaksa mikrokontroler masuk ke mode upload firmware',
                ],
                'correct_option' => 'A',
                'explanation' => 'Floating pin memiliki impedansi input yang sangat tinggi, sangat rentan terhadap medan statis dan interferensi udara.',
            ],
            [
                'question_text' => 'Berdasarkan Teorema Sampling Nyquist-Shannon, jika suatu sinyal analog memiliki frekuensi maksimum sebesar 5 kHz, berapa laju pencuplikan (sampling rate) minimum ADC agar tidak terjadi aliasing?',
                'type' => 'hard_skill',
                'topic' => 'Pemrosesan Sinyal & ADC',
                'options' => [
                    'A' => '10 kHz (dua kali frekuensi tertinggi sinyal)',
                    'B' => '5 kHz (sama persis dengan frekuensi tertinggi)',
                    'C' => '2.5 kHz (setengah dari frekuensi tertinggi)',
                    'D' => '100 kHz secara mutlak',
                ],
                'correct_option' => 'A',
                'explanation' => 'Teorema Nyquist mensyaratkan fs >= 2 * fmax agar bentuk gelombang analog asli dapat direkonstruksi secara sempurna.',
            ],
            [
                'question_text' => 'Antara protokol SPI dan I2C, mengapa SPI lebih disukai untuk komunikasi modul layar TFT grafis berkecepatan tinggi atau kartu memori SD Card?',
                'type' => 'hard_skill',
                'topic' => 'Bus Komunikasi Data Cepat',
                'options' => [
                    'A' => 'SPI menggunakan jalur data terpisah (Full-Duplex) dan mendukung clock speed puluhan MHz tanpa overhead addressing pada tiap byte',
                    'B' => 'SPI hanya menggunakan 1 kabel sedangkan I2C butuh 10 kabel',
                    'C' => 'SPI tidak membutuhkan sumber tegangan listrik',
                    'D' => 'SPI adalah protokol nirkabel jarak jauh',
                ],
                'correct_option' => 'A',
                'explanation' => 'Jalur terpisah MOSI/MISO dan ketiadaan overhead acknowledgment pada tiap byte data membuat throughput SPI jauh melampaui I2C.',
            ],
            [
                'question_text' => 'Dalam perancangan catu daya sistem elektronika, apa keunggulan utama regulator switching tipe Buck Converter dibandingkan regulator linier seri LM7805 saat menurunkan tegangan dari 24V DC ke 5V DC?',
                'type' => 'hard_skill',
                'topic' => 'Elektronika Daya & Regulator',
                'options' => [
                    'A' => 'Efisiensi konversi daya sangat tinggi (85-95%) sehingga disipasi panas minimal tanpa perlu pendingin besar',
                    'B' => 'Buck converter menghasilkan biaya komponen yang selalu lebih murah',
                    'C' => 'Buck converter sama sekali tidak menghasilkan noise switching',
                    'D' => 'Buck converter hanya bisa digunakan untuk arus di bawah 1 miliampere',
                ],
                'correct_option' => 'A',
                'explanation' => 'Regulator linier mendisipasikan kelebihan tegangan (24V - 5V = 19V) sebagai panas murni, sementara buck converter bekerja dengan efisiensi tinggi.',
            ],
            [
                'question_text' => 'Dalam bahasa C Embedded untuk pemrograman register hardware mikrokontroler, apa fungsi utama dari kata kunci "volatile"?',
                'type' => 'hard_skill',
                'topic' => 'Embedded C Programming',
                'options' => [
                    'A' => 'Memberitahu compiler agar tidak mengoptimasi variabel tersebut karena nilainya dapat berubah sewaktu-waktu oleh hardware/interrupt',
                    'B' => 'Menyimpan variabel secara permanen di memori Flash ROM',
                    'C' => 'Membuat variabel dapat diakses dari komputer jarak jauh',
                    'D' => 'Mengubah tipe data integer menjadi string',
                ],
                'correct_option' => 'A',
                'explanation' => 'Compiler yang agresif dapat menghilangkan pembacaan ulang register jika dianggap nilainya statis; volatile memaksa compiler membaca ulang dari memori fisik.',
            ],
            [
                'question_text' => 'Pada desain tata letak PCB (Printed Circuit Board), di manakah posisi terbaik untuk meletakkan kapasitor decoupling (bypass capacitor 0.1 uF)?',
                'type' => 'hard_skill',
                'topic' => 'Desain PCB & Integritas Sinyal',
                'options' => [
                    'A' => 'Sedekat mungkin dengan pin VCC dan GND dari IC/mikrokontroler yang bersangkutan',
                    'B' => 'Di sudut paling luar dari papan PCB jauh dari IC',
                    'C' => 'Dipasang seri dengan jalur antena RF',
                    'D' => 'Di bawah konektor baterai',
                ],
                'correct_option' => 'A',
                'explanation' => 'Menempatkan kapasitor sedekat mungkin meminimalkan induktansi parasitik jalur tembaga, meredam ripple tegangan transien berkecepatan tinggi.',
            ],
            [
                'question_text' => 'Pada jaringan industri pabrik, protokol komunikasi fisik RS-485 dengan standar Modbus RTU sangat populer digunakan karena keunggulannya dalam hal:',
                'type' => 'hard_skill',
                'topic' => 'Instrumentasi & Jaringan Industri',
                'options' => [
                    'A' => 'Menggunakan sinyal diferensial seimbang (A & B) yang sangat kebal terhadap noise elektromagnetik hingga jarak 1.200 meter',
                    'B' => 'Dapat mengirimkan video 4K tanpa kompresi',
                    'C' => 'Tidak memerlukan kabel tembaga',
                    'D' => 'Beroperasi pada tegangan 10.000 Volt',
                ],
                'correct_option' => 'A',
                'explanation' => 'Penerima sinyal diferensial mengukur selisih tegangan antara jalur A dan B, membatalkan noise mode bersama (common-mode noise).',
            ],
            [
                'question_text' => 'Rangkaian semikonduktor apa yang paling umum digunakan untuk mengendalikan arah putaran (maju dan mundur) serta kecepatan motor DC melalui sinyal logika kontrol?',
                'type' => 'hard_skill',
                'topic' => 'Driver Motor & Kendali Aktuator',
                'options' => [
                    'A' => 'Rangkaian Jembatan H (H-Bridge) berbasis 4 transistor (MOSFET/BJT)',
                    'B' => 'Dioda Zener tunggal',
                    'C' => 'Rangkaian Filter Bandpass RC',
                    'D' => 'Transformator Step-Down',
                ],
                'correct_option' => 'A',
                'explanation' => 'H-Bridge memungkinkan pembalikan polaritas tegangan pada terminal motor dengan mengatur pasangan saklar diagonal yang aktif.',
            ],
            [
                'question_text' => 'Dalam sistem elektronika otomotif dan robotika canggih, bus komunikasi CAN (Controller Area Network) menggunakan mekanisme arbitrase bus berbasis:',
                'type' => 'hard_skill',
                'topic' => 'CAN Bus Protocol',
                'options' => [
                    'A' => 'Non-Destructive Bitwise Arbitration berbasis Identifier pesan (prioritas ID terkecil selalu menang)',
                    'B' => 'Token ring bergantian berurutan',
                    'C' => 'Pengundian acak saat tabrakan paket data (CSMA/CD)',
                    'D' => 'Master tunggal yang mengendalikan seluruh node secara pasif',
                ],
                'correct_option' => 'A',
                'explanation' => 'Bit dominan (logika 0) menimpa bit resesif (logika 1); node dengan ID bernilai lebih kecil memiliki prioritas tertinggi tanpa kehilangan frame data.',
            ],
            [
                'question_text' => 'Rumus frekuensi cutoff (fc) untuk rangkaian filter pasif pelewat rendah (Low-Pass Filter) orde-1 yang terdiri dari satu Resistor R dan satu Kapasitor C adalah:',
                'type' => 'hard_skill',
                'topic' => 'Analisis Rangkaian Elektronika',
                'options' => [
                    'A' => 'fc = 1 / (2 * π * R * C)',
                    'B' => 'fc = 2 * π * R * C',
                    'C' => 'fc = R / (2 * π * C)',
                    'D' => 'fc = C / (R * 2 * π)',
                ],
                'correct_option' => 'A',
                'explanation' => 'Frekuensi cutoff -3dB terjadi ketika reaktansi kapasitif Xc sama dengan nilai resistansi R.',
            ],
            [
                'question_text' => 'Pada ladder diagram PLC, apa perbedaan mendasar antara instruksi Timer On-Delay (TON) dan Timer Off-Delay (TOF)?',
                'type' => 'hard_skill',
                'topic' => 'Pemrograman PLC Industri',
                'options' => [
                    'A' => 'TON menunda aktifnya output setelah input ON, sedangkan TOF menunda matinya output setelah input OFF',
                    'B' => 'TON hanya bekerja pada tegangan AC sedangkan TOF hanya pada DC',
                    'C' => 'TON menghitung waktu mundur sedangkan TOF menghitung waktu maju',
                    'D' => 'TON tidak memerlukan variabel preset time',
                ],
                'correct_option' => 'A',
                'explanation' => 'TON menghitung durasi tunda sebelum menyalakan beban; TOF menjaga beban tetap menyala selama durasi tertentu setelah sinyal pemicu hilang.',
            ],
            [
                'question_text' => 'Fitur perangkat keras mikrokontroler apa yang bertindak sebagai "penjaga" untuk melakukan reset otomatis jika firmware mengalami loop tak berujung (hang/freeze)?',
                'type' => 'hard_skill',
                'topic' => 'Keandalan Sistem Tertanam',
                'options' => [
                    'A' => 'Watchdog Timer (WDT)',
                    'B' => 'Real-Time Clock (RTC)',
                    'C' => 'Baud Rate Generator',
                    'D' => 'Direct Memory Access (DMA)',
                ],
                'correct_option' => 'A',
                'explanation' => 'Firmware harus secara periodik "memberi makan" (refresh) WDT; jika firmware macet dan timer overflow, WDT mereset mikrokontroler ke kondisi awal.',
            ],
            [
                'question_text' => 'Untuk melindungi port input mikrokontroler dari sengatan Electrostatic Discharge (ESD) saat port dihubungkan oleh pengguna di lapangan, komponen apa yang dipasang?',
                'type' => 'hard_skill',
                'topic' => 'Proteksi Sirkuit & EMC',
                'options' => [
                    'A' => 'Dioda TVS (Transient Voltage Suppressor) klem ke GND',
                    'B' => 'Kapasitor elektrolit 1000 uF',
                    'C' => 'Kawat jumper tembaga tanpa hambatan',
                    'D' => 'Resistor variabel potensiometer',
                ],
                'correct_option' => 'A',
                'explanation' => 'Dioda TVS merespons dalam hitungan pikodetik untuk menyalurkan lonjakan tegangan transien ribuan volt ke ground secara aman.',
            ],
            [
                'question_text' => 'Apa keuntungan mengimplementasikan model Machine Learning berbasis TinyML terkuantisasi (INT8) langsung di tingkat edge mikrokontroler?',
                'type' => 'hard_skill',
                'topic' => 'Edge AI & TinyML',
                'options' => [
                    'A' => 'Pengambilan keputusan real-time tanpa latensi internet, hemat bandwidth, dan data sensor sensitif tetap berada di perangkat lokal',
                    'B' => 'Dapat melatih model GPT-4 dari awal di dalam chip mikrokontroler',
                    'C' => 'Menghilangkan kebutuhan baterai pada alat',
                    'D' => 'Mengubah mikrokontroler menjadi superkomputer cloud',
                ],
                'correct_option' => 'A',
                'explanation' => 'Inference di edge menghilangkan ketergantungan konektivitas cloud dan memungkinkan proteksi privasi serta respon milidetik.',
            ],
            [
                'question_text' => 'Dalam perancangan osilator mikrokontroler, mengapa kristal osilator eksternal lebih disukai dibandingkan osilator RC internal untuk komunikasi UART berkecepatan tinggi?',
                'type' => 'hard_skill',
                'topic' => 'Pewaktuan & Osilator Mikrokontroler',
                'options' => [
                    'A' => 'Kristal memiliki toleransi frekuensi sangat presisi (ppm) dan stabil terhadap fluktuasi suhu sehingga baud rate tidak melenceng',
                    'B' => 'Kristal menghasilkan tegangan listrik sendiri tanpa konsumsi arus',
                    'C' => 'Osilator internal tidak bisa menghasilkan frekuensi lebih dari 10 Hz',
                    'D' => 'Kristal dapat menyimpan data firmware saat listrik mati',
                ],
                'correct_option' => 'A',
                'explanation' => 'Pergeseran frekuensi pada RC internal akibat suhu dapat memicu framing error pada komunikasi serial asinkron (UART).',
            ],

            // --- SOFT SKILLS (9 Soal) ---
            [
                'question_text' => 'Ketika proyek IoT kelompok Anda mengalami kegagalan transmisi data ke MQTT broker di industri saat pengujian lapangan, langkah investigasi awal apa yang paling bijak dan terstruktur?',
                'type' => 'soft_skill',
                'topic' => 'Troubleshooting Lapangan',
                'options' => [
                    'A' => 'Memeriksa konektivitas jaringan dasar (ping/gateway) dan serial monitor log perangkat secara terisolasi',
                    'B' => 'Langsung mengganti seluruh mikrokontroler dengan modul yang baru',
                    'C' => 'Menghapus seluruh baris kode firmware dan mulai menulis dari awal',
                    'D' => 'Menyalahkan tim penyedia server cloud sebelum memeriksa log lokal',
                ],
                'correct_option' => 'A',
                'explanation' => 'Troubleshooting rekayasa yang profesional selalu mengisolasi variabel masalah dari lapisan paling bawah (koneksi fisik/jaringan) menggunakan log.',
            ],
            [
                'question_text' => 'Saat merakit prototipe papan sirkuit baru di laboratorium, tim mendapati salah satu IC regulator mengeluarkan asap akibat polaritas catu daya terbalik. Sikap kerja standar apa yang harus diambil?',
                'type' => 'soft_skill',
                'topic' => 'Keselamatan Kerja & Prosedur Lab',
                'options' => [
                    'A' => 'Segera putuskan sumber daya listrik, evaluasi kerusakan, periksa skema proteksi polaritas terbalik, dan catat temuan untuk revisi PCB',
                    'B' => 'Menyiram papan sirkuit dengan air saat masih terhubung ke listrik PLN',
                    'C' => 'Menyembunyikan komponen yang terbakar dan pura-pura tidak tahu',
                    'D' => 'Terus menaikkan voltase daya untuk melihat apa yang akan terjadi selanjutnya',
                ],
                'correct_option' => 'A',
                'explanation' => 'Keselamatan laboratorium menuntut pemutusan daya segera, identifikasi akar masalah (menambahkan dioda reverse polarity), dan dokumentasi insiden.',
            ],
            [
                'question_text' => 'Di tengah proses produksi massal perangkat otomasi, pabrik komponen mengumumkan bahwa chip mikrokontroler utama discontinue (EOL). Bagaimana keputusan rekayasa terbaik tim?',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Rantai Pasok Hardware',
                'options' => [
                    'A' => 'Mencari chip alternatif pin-to-pin compatible atau merancang ulang layout PCB minimal dengan chip yang memiliki ketersediaan pasokan jangka panjang',
                    'B' => 'Menghentikan seluruh bisnis perusahaan dan membatalkan pesanan klien secara permanen',
                    'C' => 'Membeli komponen bekas rusak dari sumber tidak jelas tanpa verifikasi datasheet',
                    'D' => 'Mengabaikan kabar tersebut dan berharap pabrik membuat pengecualian',
                ],
                'correct_option' => 'A',
                'explanation' => 'Ketahanan desain perangkat keras dinilai dari kemampuan rekayasawan beradaptasi terhadap ketersediaan komponen rantai pasok global.',
            ],
            [
                'question_text' => 'Teknisi lapangan melaporkan bahwa sensor getaran mesin motor industri sering mengirim data yang tidak menentu. Operator menuduh firmware yang bermasalah, sementara programmer menduga sensor kendor. Solusi kolaboratif terbaik adalah:',
                'type' => 'soft_skill',
                'topic' => 'Komunikasi & Diagnosa Lapangan',
                'options' => [
                    'A' => 'Bersama-sama ke lapangan, mengukur sinyal sensor dengan instrumen osiloskop portabel, memverifikasi mounting fisik, dan membaca raw data serial',
                    'B' => 'Saling berdebat di grup chat tanpa ada yang memeriksa kondisi fisik mesin di pabrik',
                    'C' => 'Mematikan fitur alarm sensor agar tidak ada laporan gangguan lagi',
                    'D' => 'Menyerahkan masalah kepada divisi keuangan perusahaan',
                ],
                'correct_option' => 'A',
                'explanation' => 'Verifikasi berbasis data riil dan pengukuran instrumen langsung di lapangan memotong spekulasi dan mempercepat penyelesaian akar masalah.',
            ],
            [
                'question_text' => 'Anda melihat rekan teknisi hendak mengukur terminal listrik 380V pada panel distribusi tanpa menggunakan sarung tangan isolasi dan kacamata pelindung. Tindakan Anda adalah:',
                'type' => 'soft_skill',
                'topic' => 'K3 & Keselamatan Kelistrikan',
                'options' => [
                    'A' => 'Segera menegur dan menghentikannya secara tegas, mengingatkan bahaya busur api (Arc Flash), dan memastikan APD lengkap terpasang',
                    'B' => 'Membiarkannya karena merasa itu bukan urusan pribadi Anda',
                    'C' => 'Merekam videonya untuk dijadikan lelucon di media sosial',
                    'D' => 'Ikut-ikutan mengukur tanpa APD agar dinilai pemberani',
                ],
                'correct_option' => 'A',
                'explanation' => 'Intervensi keselamatan kerja (K3) adalah kewajiban mutlak setiap individu demi melindungi keselamatan nyawa tim di area bertegangan tinggi.',
            ],
            [
                'question_text' => 'Dalam proyek kolaborasi pembuatan alat IoT, terjadi ketidakcocokan antara hardware engineer dan firmware engineer terkait pin GPIO yang digunakan. Bagaimana cara pencegahan terbaik di masa depan?',
                'type' => 'soft_skill',
                'topic' => 'Standardisasi Rekayasa Perangkat Keras',
                'options' => [
                    'A' => 'Menyepakati dan membekukan dokumen "Hardware Pinout Map & Register Specification" sebelum pembuatan layout PCB dan penulisan kode dimulai',
                    'B' => 'Melarang programmer dan hardware engineer saling berbicara satu sama lain',
                    'C' => 'Hanya mempercayai ingatan lisan tanpa membuat dokumentasi tertulis',
                    'D' => 'Menyerahkan pemilihan pin secara acak setiap hari',
                ],
                'correct_option' => 'A',
                'explanation' => 'Dokumen spesifikasi pinout antarmuka adalah kontrak resmi antara tim perancang skematik dan pengembang firmware.',
            ],
            [
                'question_text' => 'Klien menginginkan perangkat pelacak hewan ternak berbasis baterai koin kecil dapat menyala selama 5 tahun terus-menerus sambil mengirim data GPS setiap 1 detik. Bagaimana cara menjelaskan realitas teknis kepadanya?',
                'type' => 'soft_skill',
                'topic' => 'Manajemen Ekspektasi Klien',
                'options' => [
                    'A' => 'Menyajikan lembar kalkulasi power budget (kapasitas baterai mAh vs konsumsi arus modul GPS/transmisi) dan menawarkan kompromi duty cycle interval transmisi',
                    'B' => 'Menyetujui permintaan klien dan berjanji palsu alat akan tahan 5 tahun',
                    'C' => 'Mengatakan klien bodoh karena tidak mengerti hukum fisika',
                    'D' => 'Membeli baterai biasa dan menempelkan stiker "Baterai 5 Tahun"',
                ],
                'correct_option' => 'A',
                'explanation' => 'Edukasi klien dengan kalkulasi ilmiah dan penawaran opsi kompromi (misal: deep sleep dengan transmisi tiap 30 menit) adalah tanda profesionalisme.',
            ],
            [
                'question_text' => 'Setelah terjadi insiden konveyor otomatis pabrik berhenti mendadak akibat sensor photo-electric tertutup debu tebal, rekomendasi preventif menyeluruh apa yang perlu diberikan ke manajemen pabrik?',
                'type' => 'soft_skill',
                'topic' => 'Pemeliharaan Preventif & SOP',
                'options' => [
                    'A' => 'Menetapkan jadwal rutin pembersihan sensor pada checklist harian operator dan menambahkan sensor redundan dengan logika peringatan dini degradasi sinyal',
                    'B' => 'Melepas semua sensor keselamatan dan mengoperasikan mesin secara manual dengan risiko tinggi',
                    'C' => 'Menyalahkan debu pabrik dan menuntut agar seluruh pabrik beroperasi tanpa debu sama sekali',
                    'D' => 'Tidak melakukan apapun karena mesin sudah berjalan normal kembali',
                ],
                'correct_option' => 'A',
                'explanation' => 'Pencegahan jangka panjang melibatkan pemeliharaan preventif berjadwal serta perancangan sistem peringatan dini (predictive maintenance).',
            ],
            [
                'question_text' => 'Sebuah tim teknisi pemula kesulitan melakukan flashing firmware baru ke perangkat PLC dan modul IoT di pabrik cabang. Cara terbaik membimbing mereka dari kantor pusat adalah:',
                'type' => 'soft_skill',
                'topic' => 'Knowledge Transfer & Dokumentasi',
                'options' => [
                    'A' => 'Menyediakan panduan visual SOP langkah demi langkah (Standard Operating Procedure), video demonstrasi, dan mengadakan sesi remote support',
                    'B' => 'Menyuruh mereka membaca manual teknis 500 halaman dalam bahasa asing tanpa bimbingan',
                    'C' => 'Mengatakan bahwa mereka tidak kompeten sebagai teknisi',
                    'D' => 'Menghapus hak akses teknisi tersebut dari sistem',
                ],
                'correct_option' => 'A',
                'explanation' => 'Transfer keahlian yang terstruktur dengan media panduan visual dan pendampingan memperkuat kemandirian operasional tim lapangan.',
            ],
        ];
    }

    /**
     * Generate ATS-friendly CV Professional Summary & Highlights using Gemini AI.
     */
    public function generateCvContent(User $user, ?Occupation $targetOccupation = null): array
    {
        $profile = $user->studentProfile;
        $latestAssessment = $user->assessments()->where('status', 'completed')->latest()->first();
        $portfolios = $user->portfolios()->get();
        $targetTitle = $targetOccupation?->title ?? $profile?->targetOccupation?->title ?? 'Software Engineer & Technology Specialist';
        $field = $targetOccupation?->field ?? $profile?->target_field ?? 'it';

        if ($this->apiKey) {
            try {
                $prodi = $profile?->prodi ?? 'Teknik Komputer';
                $jurusan = $profile?->jurusan ?? 'Teknologi Informasi';
                $ipk = $profile?->ipk ?? '3.50';
                $score = $latestAssessment ? $latestAssessment->total_score : 75;
                $strengths = $latestAssessment?->ai_strengths ?? 'Problem Solving & Algoritma';
                $portfolioList = $portfolios->pluck('title')->implode(', ');

                $prompt = <<<PROMPT
Anda adalah spesialis rekrutmen talenta dan konsultan CV ATS (Applicant Tracking System) untuk industri teknologi dan engineering.
Berdasarkan data profil mahasiswa Politeknik Negeri Padang berikut:
- Nama: {$user->name}
- Program Studi: {$prodi}, Jurusan {$jurusan} (IPK: {$ipk})
- Target Profesi: {$targetTitle} (Bidang: {$field})
- Nilai Uji Asesmen Kompetensi Kampus: {$score}/100
- Keahlian Unggulan Terverifikasi: {$strengths}
- Proyek Portofolio Riil: {$portfolioList}

Tugas: Buat konten CV ATS bahasa Indonesia formal dengan format JSON persis berikut:
{
  "headline": "Judul profesional singkat (contoh: Junior Full Stack Web Developer | Laravel & React Specialist)",
  "summary": "Ringkasan profesional 3-4 kalimat padat yang menonjolkan target posisi, nilai asesmen kompetensi yang telah teruji, kemampuan praktis, serta kesiapan berkontribusi di industri.",
  "skills_keywords": ["Keahlian 1", "Keahlian 2", "Keahlian 3", "Keahlian 4", "Keahlian 5", "Keahlian 6"]
}
PROMPT;

                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
                $response = Http::timeout(15)->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $json = json_decode($response->json('candidates.0.content.parts.0.text') ?? '', true);
                    if (is_array($json) && ! empty($json['summary'])) {
                        return [
                            'headline' => $json['headline'] ?? $targetTitle,
                            'summary' => $json['summary'],
                            'skills_keywords' => $json['skills_keywords'] ?? [],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Gemini CV content generation fallback: '.$e->getMessage());
            }
        }

        return $this->getFallbackCvContent($user, $targetTitle, $field, $latestAssessment, $portfolios);
    }

    /**
     * Fallback structured CV generator if API offline.
     */
    protected function getFallbackCvContent(User $user, string $targetTitle, string $field, ?Assessment $latestAssessment, Collection $portfolios): array
    {
        $profile = $user->studentProfile;
        $prodi = $profile?->prodi ?? 'Teknik Komputer';
        $ipk = $profile?->ipk ?? '3.50';
        $score = $latestAssessment ? $latestAssessment->total_score : 80;
        $scoreText = $latestAssessment ? "telah tersertifikasi uji kompetensi internal kampus dengan skor {$score}%" : 'memiliki indeks kesiapan kerja yang solid';

        if ($field === 'elektro') {
            $headline = "{$targetTitle} | Embedded Systems & Hardware Engineering";
            $summary = "Mahasiswa tingkat akhir {$prodi} Politeknik Negeri Padang (IPK {$ipk}) dengan fokus keahlian pada perancangan sistem tertanam, instrumentasi, dan Internet of Things. Telah menyelesaikan rangkaian proyek perangkat keras serta {$scoreText}. Siap berkontribusi optimal pada program pekerjaan industri dan posisi engineering tingkat awal.";
            $skills = ['Embedded C/C++', 'Microcontroller (ESP32/STM32)', 'IoT Protocols (MQTT/HTTP)', 'Circuit Design & PCB', 'Hardware Debugging', 'Sensor Integration'];
        } else {
            $headline = "{$targetTitle} | Software Engineering & Web Solutions";
            $summary = "Mahasiswa tingkat akhir {$prodi} Politeknik Negeri Padang (IPK {$ipk}) dengan spesialisasi pengembangan aplikasi modern dan arsitektur perangkat lunak. Memiliki pengalaman portofolio nyata dan {$scoreText}. Terbiasa menerapkan clean code, version control kolaboratif, serta siap memberikan dampak nyata bagi tim pengembangan industri.";
            $skills = ['Full Stack Development', 'RESTful API & Backend', 'Modern JavaScript / Frontend', 'Database Design (SQL)', 'Git & Collaborative Workflow', 'Software Testing'];
        }

        return [
            'headline' => $headline,
            'summary' => $summary,
            'skills_keywords' => $skills,
        ];
    }

    /**
     * Generate Skill Gap Analysis using CBF (Content-Based Filtering) and RAG approach via Gemini.
     */
    public function generateSkillGapAnalysis(User $user, Occupation $occupation, ?Assessment $latestAssessment, Collection $portfolios, Collection $occupationSkills): ?array
    {
        if (! $this->apiKey) {
            return null;
        }

        $profile = $user->studentProfile;
        $assessmentData = $latestAssessment ? "Skor: {$latestAssessment->total_score}/100. Kekuatan: {$latestAssessment->ai_strengths}. Kelemahan: {$latestAssessment->ai_weaknesses}." : 'Belum mengikuti asesmen kompetensi.';

        $portfolioContext = '';
        if ($portfolios->isNotEmpty()) {
            foreach ($portfolios as $idx => $p) {
                $portfolioContext .= ($idx + 1).". Judul: {$p->title}\nDeskripsi: {$p->description}\nTeknologi: {$p->technologies}\n\n";
            }
        } else {
            $portfolioContext = 'Belum memiliki portofolio proyek.';
        }

        $targetOccupationsContext = '';
        if ($profile && $profile->targetOccupation) {
            $targetOccupationsContext = '- Target Profesi Utama: '.$profile->targetOccupation->title;
        }

        $skillsList = '';
        foreach ($occupationSkills as $skill) {
            $skillsList .= "- {$skill->skill_name} (Standar minimal/Benchmark: {$skill->benchmark_score}/100)\n";
        }

        $prompt = <<<PROMPT
Anda adalah AI penguji kompetensi industri (CBF dan RAG Evaluator). Tugas Anda adalah mengukur seberapa cocok mahasiswa ini dengan lowongan pekerjaan berikut.

Konteks Mahasiswa:
- Nama: {$user->name}
- Bio/Profil: {$profile?->bio}
- Hasil Asesmen Ujian Kompetensi (Teori): {$assessmentData}
- Portofolio/Proyek Praktis yang dikerjakan:
{$targetOccupationsContext}
{$portfolioContext}

Konteks Lowongan (Occupation Target):
- Posisi Pekerjaan: {$occupation->title}
- Bidang: {$occupation->field}
- Deskripsi Posisi: {$occupation->description}
- Keahlian (Skills) yang Dibutuhkan dan Standar (Benchmark):
{$skillsList}

Instruksi Evaluasi (RAG & CBF):
1. Evaluasi secara semantik seberapa cocok portofolio dan asesmen mahasiswa dengan keahlian yang diminta pekerjaan.
2. Tentukan `student_level` (0-100) untuk masing-masing keahlian. Jika ada di portofolio/asesmen, skor tinggi (mendekati/di atas benchmark). Jika tidak ada bukti, skor rendah (gap negatif).
3. Hitung `match_percentage` (0-100) secara keseluruhan. `gap_score` adalah 100 - match_percentage.
4. Berikan 'reason' berupa kalimat penjelasan singkat (1-2 kalimat) mengapa mahasiswa mendapat skor tersebut pada tiap skill, dan sebutkan bukti dari portofolionya (jika ada).
5. Buat 'ai_recommendation' berupa 1 paragraf kesimpulan.

Keluarkan WAJIB HANYA format JSON berikut tanpa teks markdown pembuka/penutup:
{
    "match_percentage": 75,
    "gap_score": 25,
    "ai_recommendation": "Mahasiswa ini sangat kuat di backend karena portofolionya menunjukan hal tersebut, namun lemah di DevOps...",
    "gap_details": [
        {
            "skill_name": "Nama Skill persis sesuai list di atas",
            "benchmark": 80,
            "student_level": 70,
            "gap": -10,
            "reason": "Penjelasan singkat AI..."
        }
    ]
}
PROMPT;

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
            $response = Http::timeout(25)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => 0.4,
                ],
            ]);

            if ($response->successful()) {
                $json = json_decode($response->json('candidates.0.content.parts.0.text') ?? '', true);
                if (is_array($json) && isset($json['gap_details'])) {
                    return $json;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini Skill Gap generation fallback: '.$e->getMessage());
        }

        return null;
    }
}
