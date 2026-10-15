<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiImportController extends Controller
{
    public function create(Game $game)
    {
        return view('manage.games.questions.ai_import', compact('game'));
    }

    public function store(Request $request, Game $game)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'], // 5MB max
            'count' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        if (empty($apiKey)) {
            return back()->withErrors(['Api Key belum di-setting! Silakan tambahkan GEMINI_API_KEY=kunci_rahasiamu di file .env aplikasi ini agar fitur AI benar-benar bisa bekerja membaca file.']);
        }

        $file = $request->file('file');
        $count = (int) $request->count;
        $mimeType = $file->getMimeType();

        // Convert file to Base64
        $base64Data = base64_encode(file_get_contents($file->getRealPath()));

        // Tentukan prompt
        $prompt = "Kamu adalah sistem pembuat kuis otomatis. Saya melampirkan sebuah dokumen/gambar materi pelajaran. 
Tugasmu:
1. Baca dan pahami isi dokumen ini.
2. Buatkan persis {$count} soal pilihan ganda berdasarkan informasi nyata dari dokumen ini (jangan mengarang jika tidak ada di dokumen, cari hal penting).
3. Setiap soal harus memiliki 4 pilihan jawaban (1 benar, 3 salah).
4. Hasil HANYA boleh berupa JSON murni (tanpa tag markdown ```json), dengan struktur array persis seperti ini:
[
    {
        \"question\": \"Pertanyaan pertama?\",
        \"options\": [
            {\"text\": \"Jawaban benar\", \"is_correct\": true},
            {\"text\": \"Jawaban salah\", \"is_correct\": false},
            {\"text\": \"Jawaban salah\", \"is_correct\": false},
            {\"text\": \"Jawaban salah\", \"is_correct\": false}
        ]
    }
]";

        // Daftar model dengan auto-failover jika terjadi 503 High Demand
        $candidateModels = [
            'gemini-3.5-flash-lite',
            'gemini-3.6-flash',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            'gemini-3.7-flash',
        ];

        $parsedData = null;
        $isFallback = false;

        foreach ($candidateModels as $modelName) {
            try {
                $response = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=".$apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    [
                                        'inlineData' => [
                                            'mimeType' => $mimeType,
                                            'data' => $base64Data,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'maxOutputTokens' => 4096,
                            'temperature' => 0.7,
                        ],
                    ]
                );

                if ($response->successful()) {
                    $aiResponseText = $response->json('candidates.0.content.parts.0.text');
                    $aiResponseText = preg_replace('/```json\s*/i', '', $aiResponseText);
                    $aiResponseText = preg_replace('/```/i', '', $aiResponseText);

                    $decoded = json_decode(trim($aiResponseText), true);
                    if (is_array($decoded) && count($decoded) > 0) {
                        $parsedData = $decoded;
                        Log::info("Gemini File Import succeeded with model: {$modelName}");
                        break;
                    }
                } else {
                    Log::warning("Gemini model {$modelName} error: ".$response->status().' '.$response->body());
                }
            } catch (\Exception $e) {
                Log::warning("Gemini model {$modelName} exception: ".$e->getMessage());
            }
        }

        if (! is_array($parsedData) || count($parsedData) === 0) {
            $parsedData = $this->generateDummyQuestions($file->getClientOriginalName(), $count);
            $isFallback = true;
        }

        // Simpan ke database
        foreach ($parsedData as $item) {
            if (empty($item['question']) || empty($item['options'])) {
                continue;
            }

            $q = $game->questions()->create([
                'question_text' => $item['question'],
                'time_limit_seconds' => 30,
                'points' => 100,
                'order' => $game->questions()->max('order') + 1,
            ]);

            foreach ($item['options'] as $opt) {
                $q->options()->create([
                    'option_text' => $opt['text'] ?? 'Opsi',
                    'is_correct' => isset($opt['is_correct']) && $opt['is_correct'] == true,
                ]);
            }
        }

        if ($isFallback) {
            return redirect()->route('manage.games.questions.index', $game)
                ->with('success', "⚠️ Server AI sedang sibuk (High Demand). Kami telah membuat {$count} soal SIMULASI agar kamu tetap bisa melanjutkan pengujian.");
        }

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', '✨ Berhasil! AI (Gemini) telah membaca file Anda dan meracik '.count($parsedData).' soal asli.');
    }

    private function generateDummyQuestions($filename, $count)
    {
        $dummy = [];
        for ($i = 1; $i <= $count; $i++) {
            $options = [
                ['text' => "Pernyataan Benar dari file $filename", 'is_correct' => true],
                ['text' => 'Fakta salah yang mengecoh', 'is_correct' => false],
                ['text' => 'Data yang tidak relevan', 'is_correct' => false],
                ['text' => 'Kesimpulan yang kurang tepat', 'is_correct' => false],
            ];
            shuffle($options);
            $dummy[] = [
                'question' => "Berdasarkan file $filename, manakah pernyataan ke-{$i} yang paling tepat?",
                'options' => $options,
            ];
        }

        return $dummy;
    }

    private function getIndonesianSongCatalog(): array
    {
        return [
            'Kicir-Kicir' => 'https://upload.wikimedia.org/wikipedia/commons/3/3f/DKI_Jakarta_-_Kicir-Kicir.wav',
            'Tokecang' => 'https://upload.wikimedia.org/wikipedia/commons/6/69/Jawa_Barat_-_Tokecang.wav',
            'Bungong Jeumpa' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Aceh_-_Bungong_Jeumpa.wav',
            'Cublak-Cublak Suweng' => 'https://upload.wikimedia.org/wikipedia/commons/5/58/Jawa_Tengah_-_Cublak-Cublak_Suweng.wav',
            'Tanjung Perak' => 'https://upload.wikimedia.org/wikipedia/commons/b/bc/Jawa_Timur_-_Tanjung_Perak.wav',
            'Cik Cik Periuk' => 'https://upload.wikimedia.org/wikipedia/commons/c/c8/Kalimantan_Barat_-_Cik_Cik_Periuk.wav',
            'Ampar-Ampar Pisang' => 'https://upload.wikimedia.org/wikipedia/commons/c/cb/Kalimantan_Selatan_-_Ampar-Ampar Pisang.wav',
            'Hela Rotane' => 'https://upload.wikimedia.org/wikipedia/commons/8/8c/Maluku_-_Hela_Rotane.wav',
            'Waktu Hujan Sore-Sore' => 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Maluku_-_Waktu_Hujan_Sore-Sore.wav',
            'Rasa Sayange' => 'https://upload.wikimedia.org/wikipedia/commons/1/1b/Maluku_Utara_-_Rasa_Sayange.wav',
            'Sajojo' => 'https://upload.wikimedia.org/wikipedia/commons/2/24/Papua_-_Sajojo.wav',
            'Yamko Rambe Yamko' => 'https://upload.wikimedia.org/wikipedia/commons/0/0c/Papua_-_Yamko_Rambe_Yamko.wav',
            'Soleram' => 'https://upload.wikimedia.org/wikipedia/commons/6/6d/Riau_-_Soleram.wav',
            'Si Patokaan' => 'https://upload.wikimedia.org/wikipedia/commons/7/7e/Sulawesi_Utara_-_Si_Patokaan.wav',
            'Sigulempong' => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Sumatera_Utara_-_Sigulempong.wav',
            'Janger' => 'https://upload.wikimedia.org/wikipedia/commons/1/1f/Bali_-_Janger.wav',
            'Pang Lipangdang' => 'https://upload.wikimedia.org/wikipedia/commons/d/d6/Lampung_-_Pang_Lipangdang.wav',
        ];
    }

    /**
     * Cari foto/gambar berkualitas tinggi dan akurat dari Wikipedia / Wikimedia Commons.
     */
    private function searchWikipediaImage(string $query): ?string
    {
        $cleanQuery = trim(preg_replace('/[^\w\s\-,]/u', ' ', $query));
        if (empty($cleanQuery)) {
            return null;
        }

        $langs = ['id', 'en'];
        foreach ($langs as $lang) {
            try {
                $url = "https://{$lang}.wikipedia.org/w/api.php?action=query&generator=search&gsrsearch=".urlencode($cleanQuery).'&gsrlimit=1&prop=pageimages&pithumbsize=800&format=json';
                $response = Http::timeout(4)
                    ->withHeaders(['User-Agent' => 'GoxsgrowEdu/1.0 (edu@goxsgrow.test)'])
                    ->get($url);

                if ($response->successful()) {
                    $pages = $response->json('query.pages');
                    if (is_array($pages)) {
                        foreach ($pages as $page) {
                            if (! empty($page['thumbnail']['source'])) {
                                return $page['thumbnail']['source'];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Wikipedia image search error ({$lang}): ".$e->getMessage());
            }
        }

        return null;
    }

    private function generateDummyQuestionsForTopic($topic, $count, $questionType = 'multiple_choice')
    {
        $dummy = [];
        $songs = $this->getIndonesianSongCatalog();
        $songNames = array_keys($songs);

        $imageExamples = [
            [
                'question' => 'Gambar di atas menunjukkan kemegahan candi Buddha terbesar di dunia yang terletak di Magelang, Jawa Tengah. Apakah nama candi ini?',
                'media_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/2/25/Pradaksina.jpg/960px-Pradaksina.jpg',
                'correct' => 'Candi Borobudur',
                'distractors' => ['Candi Prambanan', 'Candi Mendut', 'Candi Sewu'],
            ],
            [
                'question' => 'Perhatikan hewan endemik Indonesia pada gambar di atas. Apakah nama spesies kadal raksasa yang dilindungi ini?',
                'media_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/4/44/Komodo_dragon_%28Varanus_komodoensis%29_4.jpg/960px-Komodo_dragon_%28Varanus_komodoensis%29_4.jpg',
                'correct' => 'Komodo',
                'distractors' => ['Biawak Pohon', 'Kadal Duri', 'Iguana Purba'],
            ],
            [
                'question' => 'Gambar di atas menampilkan rumah adat tradisional Minangkabau dengan atap gonjong menyerupai tanduk kerbau. Rumah adat apakah ini?',
                'media_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/f/fd/Rumah_Gadang.jpg/960px-Rumah_Gadang.jpg',
                'correct' => 'Rumah Gadang',
                'distractors' => ['Rumah Joglo', 'Rumah Tongkonan', 'Rumah Honai'],
            ],
            [
                'question' => 'Perhatikan alat musik tradisional dari bambu pada gambar di atas. Alat musik asal Jawa Barat ini dimainkan dengan cara digoyangkan, yaitu...',
                'media_url' => 'https://upload.wikimedia.org/wikipedia/commons/8/87/Indonesianbamboomusicangklung.jpg',
                'correct' => 'Angklung',
                'distractors' => ['Kolintang', 'Sasando', 'Calung'],
            ],
            [
                'question' => 'Gambar di atas menunjukkan bunga raksasa langka tanpa akar, tangkai, atau daun yang merupakan bunga endemik Sumatera. Bunga apakah ini?',
                'media_url' => 'https://thumb.wikimedia.org/wikipedia/commons/thumb/d/dd/Rafflesia_arnoldii%2C_Sumatra.jpg/960px-Rafflesia_arnoldii%2C_Sumatra.jpg',
                'correct' => 'Rafflesia Arnoldii',
                'distractors' => ['Bunga Bangkai', 'Kantong Semar', 'Anggrek Hitam'],
            ],
        ];

        for ($i = 1; $i <= $count; $i++) {
            $effectiveType = $questionType === 'mixed'
                ? ['multiple_choice', 'true_false', 'image', 'audio'][($i - 1) % 4]
                : $questionType;

            if ($effectiveType === 'image') {
                $item = $imageExamples[($i - 1) % count($imageExamples)];

                $options = [
                    ['text' => $item['correct'], 'is_correct' => true],
                    ['text' => $item['distractors'][0], 'is_correct' => false],
                    ['text' => $item['distractors'][1], 'is_correct' => false],
                    ['text' => $item['distractors'][2], 'is_correct' => false],
                ];
                shuffle($options);

                $dummy[] = [
                    'question' => $item['question'],
                    'type' => 'image',
                    'media_url' => $item['media_url'],
                    'options' => $options,
                ];
            } elseif ($effectiveType === 'audio') {
                $songIndex = ($i - 1) % count($songNames);
                $correctSong = $songNames[$songIndex];
                $audioUrl = $songs[$correctSong];

                $distractorPool = array_diff($songNames, [$correctSong]);
                shuffle($distractorPool);
                $distractors = array_slice($distractorPool, 0, 3);

                $options = [
                    ['text' => $correctSong, 'is_correct' => true],
                    ['text' => $distractors[0] ?? 'Gundul Pacul', 'is_correct' => false],
                    ['text' => $distractors[1] ?? 'Halo-Halo Bandung', 'is_correct' => false],
                    ['text' => $distractors[2] ?? 'Manuk Dadali', 'is_correct' => false],
                ];
                shuffle($options);

                $dummy[] = [
                    'question' => 'Dengarkan audio musik tradisional di atas! Lagu daerah apakah yang sedang diperdengarkan ini?',
                    'type' => 'audio',
                    'media_url' => $audioUrl,
                    'options' => $options,
                ];
            } elseif ($effectiveType === 'true_false') {
                $isTrue = $i % 2 !== 0;
                $dummy[] = [
                    'question' => "Pernyataan nomor {$i} tentang materi '{$topic}' adalah fakta yang sah dan terverifikasi secara ilmiah.",
                    'type' => 'true_false',
                    'media_url' => null,
                    'options' => [
                        ['text' => 'BENAR', 'is_correct' => $isTrue],
                        ['text' => 'SALAH', 'is_correct' => ! $isTrue],
                    ],
                ];
            } else {
                $options = [
                    ['text' => "Jawaban Benar materi {$topic}", 'is_correct' => true],
                    ['text' => "Pernyataan pengecoh materi {$topic} A", 'is_correct' => false],
                    ['text' => "Pernyataan pengecoh materi {$topic} B", 'is_correct' => false],
                    ['text' => "Pernyataan pengecoh materi {$topic} C", 'is_correct' => false],
                ];
                shuffle($options);

                $dummy[] = [
                    'question' => "Pertanyaan nomor {$i} mengenai pemahaman materi: {$topic}?",
                    'type' => 'multiple_choice',
                    'media_url' => null,
                    'options' => $options,
                ];
            }
        }

        return $dummy;
    }

    public function createText(Game $game)
    {
        return view('manage.games.questions.ai_generate', compact('game'));
    }

    public function storeText(Request $request, Game $game)
    {
        $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:1', 'max:20'],
            'question_type' => ['nullable', 'string', 'in:multiple_choice,true_false,image,audio,mixed'],
        ]);

        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        if (empty($apiKey)) {
            return back()->withErrors(['Api Key belum di-setting di file .env']);
        }

        $keyword = $request->keyword;
        $count = (int) $request->count;
        $questionType = $request->input('question_type', 'multiple_choice');

        $songCatalog = $this->getIndonesianSongCatalog();
        $songListString = implode(', ', array_keys($songCatalog));

        // Instruksi tipe kuis spesifik untuk AI
        $typeInstruction = match ($questionType) {
            'true_false' => "Semua soal WAJIB bertipe 'true_false' (Pernyataan Benar atau Salah). Setiap soal hanya memiliki 2 pilihan: 'BENAR' dan 'SALAH'. Kolom 'type' diisi 'true_false' dan 'media_url' bernilai null.",
            'image' => "Semua soal WAJIB bertipe 'image' (Tebak Gambar). Pertanyaan menanyakan objek/tokoh/tempat/hewan/tumbuhan/benda yang tampak pada gambar. Kolom 'type' WAJIB diisi 'image'. Berikan kolom 'correct_subject' yang berisi nama subjek gambar yang benar dan spesifik (contoh: 'Candi Borobudur', 'Komodo', 'Rumah Gadang', 'Ir. Soekarno', 'Angklung', 'Rafflesia arnoldii', 'Gunung Bromo', dll). Pilihan jawaban 4 opsi dengan 1 pilihan benar yang cocok dengan 'correct_subject'.",
            'audio' => "Semua soal WAJIB bertipe 'audio' (Tebak Lagu Daerah / Suara Musik). Buat soal menebak lagu daerah dari daftar ini: [{$songListString}]. Kolom 'type' WAJIB diisi 'audio'. Kolom 'song_title' WAJIB diisi persis salah satu judul lagu dari daftar tersebut (contoh: 'Kicir-Kicir', 'Tokecang', 'Cublak-Cublak Suweng', 'Yamko Rambe Yamko', dll). Pertanyaan format: 'Dengarkan lagu di atas! Lagu daerah apakah ini?'. Salah satu pilihan jawaban WAJIB sama dengan judul di 'song_title' dan bernilai is_correct: true.",
            'mixed' => "Buatkan kuis variasi campuran dari tipe 'multiple_choice', 'true_false' (2 opsi), 'image' (dengan 'correct_subject'), dan 'audio' (dengan 'song_title' dari [{$songListString}]). Tentukan kolom 'type' sesuai jenisnya masing-masing.",
            default => "Semua soal bertipe 'multiple_choice' (Pilihan Ganda 4 opsi). Kolom 'type' diisi 'multiple_choice' dan 'media_url' bernilai null.",
        };

        $prompt = "Kamu adalah pembuat kuis edukasi cerdas. Buatkan persis {$count} soal tentang materi/topik: '{$keyword}'.
Aturan jenis kuis:
{$typeInstruction}

Hasil WAJIB HANYA berupa JSON murni dengan format array persis seperti ini:
[
    {
        \"question\": \"Pertanyaan soal?\",
        \"type\": \"{$questionType}\",
        \"correct_subject\": \"Nama subjek objek gambar yang benar (hanya untuk tipe image, contoh: Candi Borobudur / Komodo / Ir. Soekarno)\",
        \"song_title\": \"Judul lagu dari daftar lagu (hanya untuk tipe audio, contoh: Tokecang)\",
        \"options\": [
            {\"text\": \"Jawaban benar\", \"is_correct\": true},
            {\"text\": \"Jawaban salah 1\", \"is_correct\": false},
            {\"text\": \"Jawaban salah 2\", \"is_correct\": false},
            {\"text\": \"Jawaban salah 3\", \"is_correct\": false}
        ]
    }
]";

        $candidateModels = [
            'gemini-3.5-flash-lite',
            'gemini-3.6-flash',
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            'gemini-3.7-flash',
        ];

        $parsedData = null;
        $isFallback = false;

        foreach ($candidateModels as $modelName) {
            try {
                $response = Http::timeout(60)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=".$apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'maxOutputTokens' => 4096,
                            'temperature' => 0.7,
                        ],
                    ]
                );

                if ($response->successful()) {
                    $aiResponseText = $response->json('candidates.0.content.parts.0.text');
                    Log::info("Gemini Raw AI Response ({$modelName}): ".substr($aiResponseText, 0, 200));

                    $cleanText = preg_replace('/```json\s*/i', '', $aiResponseText);
                    $cleanText = preg_replace('/```/i', '', $cleanText);

                    $decoded = json_decode(trim($cleanText), true);
                    if (is_array($decoded) && count($decoded) > 0) {
                        $parsedData = $decoded;
                        Log::info("Gemini Topic Generation succeeded with model: {$modelName}");
                        break;
                    } else {
                        Log::warning("JSON decode failed on model {$modelName}: ".json_last_error_msg());
                    }
                } else {
                    Log::warning("Gemini model {$modelName} returned status ".$response->status().': '.$response->body());
                }
            } catch (\Exception $e) {
                Log::warning("Gemini model {$modelName} exception: ".$e->getMessage());
            }
        }

        if (! is_array($parsedData) || count($parsedData) === 0) {
            $parsedData = $this->generateDummyQuestionsForTopic($keyword, $count, $questionType);
            $isFallback = true;
        }

        // Simpan ke database
        foreach ($parsedData as $itemIndex => $item) {
            if (empty($item['question']) || empty($item['options'])) {
                continue;
            }

            // Tentukan tipe kuis final
            if ($questionType === 'image') {
                $savedType = 'image';
            } elseif ($questionType === 'audio') {
                $savedType = 'audio';
            } elseif ($questionType === 'true_false') {
                $savedType = 'true_false';
            } elseif ($questionType === 'multiple_choice') {
                $savedType = 'multiple_choice';
            } else {
                $savedType = in_array($item['type'] ?? '', ['multiple_choice', 'true_false', 'image', 'audio'])
                    ? $item['type']
                    : 'multiple_choice';
            }

            $mediaUrl = ! empty($item['media_url']) ? trim($item['media_url']) : null;

            // Logika Media untuk Tipe Gambar (Tebak Gambar)
            if ($savedType === 'image') {
                $correctOptText = null;
                foreach ($item['options'] as $opt) {
                    if (! empty($opt['is_correct'])) {
                        $correctOptText = trim($opt['text'] ?? '');
                        break;
                    }
                }

                $searchQueries = array_values(array_filter([
                    $item['correct_subject'] ?? null,
                    $correctOptText,
                    $item['image_keyword'] ?? null,
                    $keyword,
                ]));

                $wikiImage = null;
                foreach ($searchQueries as $sq) {
                    $wikiImage = $this->searchWikipediaImage($sq);
                    if ($wikiImage) {
                        break;
                    }
                }

                if ($wikiImage) {
                    $mediaUrl = $wikiImage;
                } elseif (empty($mediaUrl) || ! str_starts_with($mediaUrl, 'http')) {
                    // Fallback foto edukasi Unsplash terpercaya
                    $mediaUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';
                }
            }

            // Logika Media untuk Tipe Lagu (Tebak Lagu)
            if ($savedType === 'audio') {
                if (empty($mediaUrl) || ! str_starts_with($mediaUrl, 'http')) {
                    // Cari lagu yang cocok dari katalog
                    $selectedAudio = null;
                    $songTitle = $item['song_title'] ?? '';

                    foreach ($songCatalog as $title => $audioLink) {
                        if (! empty($songTitle) && stripos($songTitle, $title) !== false) {
                            $selectedAudio = $audioLink;
                            break;
                        }
                    }

                    if (! $selectedAudio) {
                        // Cek dari pertanyaan atau opsi yang benar
                        foreach ($songCatalog as $title => $audioLink) {
                            if (stripos($item['question'], $title) !== false) {
                                $selectedAudio = $audioLink;
                                break;
                            }
                            foreach ($item['options'] as $opt) {
                                if (! empty($opt['is_correct']) && stripos($opt['text'] ?? '', $title) !== false) {
                                    $selectedAudio = $audioLink;
                                    break 2;
                                }
                            }
                        }
                    }

                    // Fallback jika tidak ada judul yang cocok persis: pilih berurutan dari katalog
                    if (! $selectedAudio) {
                        $catalogValues = array_values($songCatalog);
                        $selectedAudio = $catalogValues[$itemIndex % count($catalogValues)];
                    }

                    $mediaUrl = $selectedAudio;
                }
            }

            $q = $game->questions()->create([
                'question_text' => $item['question'],
                'type' => $savedType,
                'media_url' => $mediaUrl,
                'time_limit_seconds' => 30,
                'points' => 100,
                'order' => $game->questions()->max('order') + 1,
            ]);

            foreach ($item['options'] as $opt) {
                $q->options()->create([
                    'option_text' => $opt['text'] ?? 'Opsi',
                    'is_correct' => isset($opt['is_correct']) && $opt['is_correct'] == true,
                ]);
            }
        }

        if ($isFallback) {
            return redirect()->route('manage.games.questions.index', $game)
                ->with('success', "⚠️ Server AI sedang sibuk (High Demand). Kami telah membuat {$count} soal SIMULASI untuk Topik '{$keyword}' agar kamu tetap bisa melanjutkan.");
        }

        $typeLabel = match ($questionType) {
            'true_false' => 'Benar/Salah',
            'image' => 'Tebak Gambar',
            'audio' => 'Tebak Lagu',
            'mixed' => 'Campuran Multi-Tipe',
            default => 'Pilihan Ganda',
        };

        return redirect()->route('manage.games.questions.index', $game)
            ->with('success', '✨ Berhasil! AI telah meracik '.count($parsedData)." soal [{$typeLabel}] berdasarkan topik '{$keyword}'.");
    }
}
