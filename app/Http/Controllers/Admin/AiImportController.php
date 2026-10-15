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
     * Cari foto/gambar berkualitas tinggi dan akurat dari Wikipedia / Wikimedia Commons (Global & Lokal).
     */
    private function searchWikipediaImage(string $query): ?string
    {
        $cleanQuery = trim(preg_replace('/[^\w\s\-,]/u', ' ', $query));
        if (empty($cleanQuery)) {
            return null;
        }

        // Prioritas Wikipedia bahasa Inggris (paling lengkap untuk entitas global), Indonesia, dan Wikimedia Commons
        $endpoints = [
            'https://en.wikipedia.org/w/api.php',
            'https://id.wikipedia.org/w/api.php',
            'https://commons.wikimedia.org/w/api.php',
        ];

        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout(4)
                    ->withHeaders(['User-Agent' => 'GoxsgrowEdu/1.0 (edu@goxsgrow.test)'])
                    ->get($endpoint, [
                        'action' => 'query',
                        'generator' => 'search',
                        'gsrsearch' => $cleanQuery,
                        'gsrlimit' => 1,
                        'prop' => 'pageimages',
                        'pithumbsize' => 800,
                        'format' => 'json',
                    ]);

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
                Log::warning("Wikipedia/Commons image search error ({$endpoint}): ".$e->getMessage());
            }
        }

        return null;
    }

    /**
     * Cari potongan audio musik/lagu secara dinamis dari iTunes Search API & Deezer API (Global & Lokal).
     */
    private function searchAudioPreview(string $songTitle, ?string $artist = null, ?string $topicContext = null): ?string
    {
        $cleanTitle = trim(preg_replace('/[^\w\s\-\'\.,]/u', ' ', $songTitle));
        $cleanArtist = $artist ? trim(preg_replace('/[^\w\s\-\'\.,]/u', ' ', $artist)) : null;

        $queries = [];
        if ($cleanArtist && $cleanTitle) {
            $queries[] = "{$cleanArtist} {$cleanTitle}";
        }
        if ($cleanTitle) {
            $queries[] = $cleanTitle;
        }
        if ($topicContext && $cleanTitle && stripos($cleanTitle, $topicContext) === false) {
            $queries[] = "{$topicContext} {$cleanTitle}";
        }

        // 1. Coba iTunes Search API (Mendukung jutaan lagu global & Indonesia dengan 30s audio preview M4A/AAC)
        foreach ($queries as $q) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'GoxsgrowEdu/1.0 (edu@goxsgrow.test)'])
                    ->get('https://itunes.apple.com/search', [
                        'term' => $q,
                        'entity' => 'song',
                        'limit' => 1,
                    ]);

                if ($response->successful()) {
                    $results = $response->json('results');
                    if (! empty($results[0]['previewUrl'])) {
                        return $results[0]['previewUrl'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("iTunes audio search error for query '{$q}': ".$e->getMessage());
            }
        }

        // 2. Coba Deezer Search API (Alternatif global MP3 preview)
        foreach ($queries as $q) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'GoxsgrowEdu/1.0 (edu@goxsgrow.test)'])
                    ->get('https://api.deezer.com/search', [
                        'q' => $q,
                        'limit' => 1,
                    ]);

                if ($response->successful()) {
                    $tracks = $response->json('data');
                    if (! empty($tracks[0]['preview'])) {
                        return $tracks[0]['preview'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Deezer audio search error for query '{$q}': ".$e->getMessage());
            }
        }

        // 3. Cek katalog lagu daerah Indonesia jika judul cocok
        $catalog = $this->getIndonesianSongCatalog();
        foreach ($catalog as $title => $audioLink) {
            if (stripos($cleanTitle, $title) !== false || ($topicContext && stripos($topicContext, $title) !== false)) {
                return $audioLink;
            }
        }

        return null;
    }

    private function generateDummyQuestionsForTopic($topic, $count, $questionType = 'multiple_choice')
    {
        $dummy = [];
        $songs = $this->getIndonesianSongCatalog();
        $songNames = array_keys($songs);

        $isTaylorSwift = (stripos($topic, 'taylor') !== false || stripos($topic, 'swift') !== false);
        $isPopGlobal = (stripos($topic, 'pop') !== false || stripos($topic, 'barat') !== false || stripos($topic, 'kpop') !== false || stripos($topic, 'bts') !== false || stripos($topic, 'anime') !== false || stripos($topic, 'musik') !== false || stripos($topic, 'lagu') !== false);

        // Pool gambar global jika topik Taylor Swift
        $tsImagePool = [
            [
                'subject' => 'Taylor Swift',
                'question' => 'Perhatikan foto musisi wanita peraih 4 kali Grammy Album of the Year pada gambar di atas. Siapakah penyanyi ini?',
                'correct' => 'Taylor Swift',
                'distractors' => ['Katy Perry', 'Ariana Grande', 'Selena Gomez'],
            ],
            [
                'subject' => 'The Eras Tour',
                'question' => 'Gambar di atas menampilkan panggung konser spektakuler dari tur musik berpendapatan tertinggi dalam sejarah. Apakah nama tur ini?',
                'correct' => 'The Eras Tour',
                'distractors' => ['Renaissance World Tour', 'The Formation World Tour', 'Chromatica Ball'],
            ],
            [
                'subject' => '1989 (Taylor Swift album)',
                'question' => 'Foto di atas berkaitan dengan album era pop fenomenal Taylor Swift yang memuat lagu hits "Shake It Off" dan "Blank Space". Album apakah ini?',
                'correct' => '1989 (Taylor\'s Version)',
                'distractors' => ['Reputation', 'Lover', 'Red'],
            ],
            [
                'subject' => 'Grammy Award',
                'question' => 'Piala bergengsi industri musik dunia pada gambar di atas telah dimenangkan Taylor Swift sebanyak 14 kali sepanjang kariernya. Trofi apakah ini?',
                'correct' => 'Grammy Award',
                'distractors' => ['Billboard Music Award', 'MTV Video Music Award', 'American Music Award'],
            ],
        ];

        // Pool gambar global umum (landmark dunia, sains, karya seni)
        $globalImagePool = [
            [
                'subject' => 'Eiffel Tower',
                'question' => 'Perhatikan landmark megah dunia yang terletak di Paris, Prancis pada gambar di atas. Monumen apakah ini?',
                'correct' => 'Menara Eiffel',
                'distractors' => ['Menara Pisa', 'Big Ben', 'Empire State Building'],
            ],
            [
                'subject' => 'Saturn',
                'question' => 'Perhatikan planet dalam tata surya dengan sistem cincin es spektakuler pada gambar di atas. Planet apakah ini?',
                'correct' => 'Planet Saturnus',
                'distractors' => ['Planet Jupiter', 'Planet Uranus', 'Planet Neptunus'],
            ],
            [
                'subject' => 'Mona Lisa',
                'question' => 'Karya lukisan abad ke-16 mahakarya Leonardo da Vinci yang dipajang di Museum Louvre pada gambar di atas adalah...',
                'correct' => 'Lukisan Mona Lisa',
                'distractors' => ['The Starry Night', 'The Last Supper', 'The Scream'],
            ],
            [
                'subject' => 'Colosseum',
                'question' => 'Amfiteater batu kuno peninggalan Kekaisaran Romawi di Italia pada gambar di atas dikenal sebagai...',
                'correct' => 'Colosseum Roma',
                'distractors' => ['Parthenon Athena', 'Piramida Giza', 'Machu Picchu'],
            ],
            [
                'subject' => 'Albert Einstein',
                'question' => 'Fisikawan jenius penemu rumus E=mc² dan peraih Penghargaan Nobel Fisika pada foto di atas adalah...',
                'correct' => 'Albert Einstein',
                'distractors' => ['Isaac Newton', 'Nikola Tesla', 'Galileo Galilei'],
            ],
        ];

        // Pool pertanyaan pilihan ganda Taylor Swift
        $tsMcPool = [
            [
                'question' => 'Album studio Taylor Swift manakah yang berhasil memenangkan penghargaan Grammy untuk kategori Album of the Year pertama kalinya?',
                'correct' => 'Fearless',
                'distractors' => ['1989', 'Red', 'Folklore'],
            ],
            [
                'question' => 'Lagu hits nomor 1 Billboard dari Taylor Swift yang memiliki lirik ikonik "Cause the players gonna play, play, play..." adalah...',
                'correct' => 'Shake It Off',
                'distractors' => ['Blank Space', 'Bad Blood', 'Style'],
            ],
            [
                'question' => 'Berapa kali Taylor Swift berhasil memenangkan piala tertinggi Album of the Year di ajang Grammy Awards hingga tahun 2024?',
                'correct' => '4 Kali',
                'distractors' => ['2 Kali', '3 Kali', '5 Kali'],
            ],
            [
                'question' => 'Lagu re-recording versi legendaris 10 menit berjudul "All Too Well" terdapat dalam album Taylor Swift mana?',
                'correct' => 'Red (Taylor\'s Version)',
                'distractors' => ['Fearless (Taylor\'s Version)', '1989 (Taylor\'s Version)', 'Speak Now (Taylor\'s Version)'],
            ],
            [
                'question' => 'Apakah judul tur konser dunia Taylor Swift yang memecahkan rekor sebagai tur konser berpendapatan tertinggi dalam sejarah musik?',
                'correct' => 'The Eras Tour',
                'distractors' => ['The 1989 World Tour', 'Reputation Stadium Tour', 'The Red Tour'],
            ],
        ];

        // Pool fakta benar/salah Taylor Swift
        $tsTfPool = [
            [
                'question' => 'Taylor Swift adalah musisi pertama dalam sejarah Grammy yang memenangkan Album of the Year sebanyak 4 kali.',
                'is_correct' => true,
            ],
            [
                'question' => 'Film dokumenter konser "Taylor Swift: The Eras Tour" dinobatkan sebagai film konser terlaris sepanjang masa.',
                'is_correct' => true,
            ],
            [
                'question' => 'Album debut pertama Taylor Swift beraliran musik Heavy Metal dan dirilis pada tahun 2022.',
                'is_correct' => false,
            ],
            [
                'question' => 'Lagu hits "Cruel Summer" dan "Lover" berasal dari album studio ketujuh Taylor Swift yang berjudul "Lover".',
                'is_correct' => true,
            ],
            [
                'question' => 'Taylor Swift lahir di London, Inggris dan merilis album perdananya di benua Eropa.',
                'is_correct' => false,
            ],
        ];

        for ($i = 1; $i <= $count; $i++) {
            $effectiveType = $questionType === 'mixed'
                ? ['multiple_choice', 'true_false', 'image', 'audio'][($i - 1) % 4]
                : $questionType;

            if ($effectiveType === 'image') {
                if ($isTaylorSwift) {
                    $item = $tsImagePool[($i - 1) % count($tsImagePool)];
                } else {
                    $item = $globalImagePool[($i - 1) % count($globalImagePool)];
                }

                // Cari gambar nyata dari Wikipedia untuk subjek tersebut
                $imageUrl = $this->searchWikipediaImage($item['subject']);
                if (! $imageUrl) {
                    $imageUrl = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';
                }

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
                    'media_url' => $imageUrl,
                    'options' => $options,
                ];
            } elseif ($effectiveType === 'audio') {
                if ($isTaylorSwift) {
                    $artistName = 'Taylor Swift';
                    $trackPool = [
                        'Cruel Summer' => ['Blank Space', 'Anti-Hero', 'Cardigan'],
                        'Blank Space' => ['Shake It Off', 'Style', 'Bad Blood'],
                        'Anti-Hero' => ['Lavender Haze', 'Karma', 'Midnight Rain'],
                        'Shake It Off' => ['22', 'We Are Never Ever Getting Back Together', 'Love Story'],
                        'Love Story' => ['You Belong With Me', 'Fearless', 'Teardrops on My Guitar'],
                        'Cardigan' => ['August', 'Betty', 'Exile'],
                        'Style' => ['Wildest Dreams', 'Out of the Woods', 'Clean'],
                    ];
                    $titles = array_keys($trackPool);
                    $correctSong = $titles[($i - 1) % count($titles)];
                    $distractors = $trackPool[$correctSong];
                    $questionText = 'Dengarkan cuplikan audio di atas! Lagu Taylor Swift apakah yang sedang diperdengarkan ini?';
                } elseif ($isPopGlobal) {
                    $artistName = null;
                    $trackPool = [
                        'Dynamite' => ['Butter', 'Permission to Dance', 'Boy With Luv'],
                        'Viva La Vida' => ['Yellow', 'Fix You', 'Paradise'],
                        'Shape of You' => ['Perfect', 'Bad Habits', 'Thinking Out Loud'],
                        'As It Was' => ['Watermelon Sugar', 'Golden', 'Sign of the Times'],
                        'Blinding Lights' => ['Save Your Tears', 'Starboy', 'The Hills'],
                    ];
                    $titles = array_keys($trackPool);
                    $correctSong = $titles[($i - 1) % count($titles)];
                    $distractors = $trackPool[$correctSong];
                    $questionText = 'Dengarkan potongan musik di atas! Lagu populer apakah yang sedang diputar ini?';
                } else {
                    $artistName = null;
                    $songIndex = ($i - 1) % count($songNames);
                    $correctSong = $songNames[$songIndex];
                    $distractorPool = array_diff($songNames, [$correctSong]);
                    shuffle($distractorPool);
                    $distractors = array_values(array_slice($distractorPool, 0, 3));
                    $questionText = 'Dengarkan audio musik di atas! Lagu apakah yang sedang diperdengarkan ini?';
                }

                // Cari audio preview nyata dari iTunes / Deezer / Catalog
                $audioUrl = $this->searchAudioPreview($correctSong, $artistName, $topic);
                if (! $audioUrl) {
                    $catalogValues = array_values($songs);
                    $audioUrl = $catalogValues[($i - 1) % count($catalogValues)];
                }

                $options = [
                    ['text' => $correctSong, 'is_correct' => true],
                    ['text' => $distractors[0] ?? 'Lagu Pengecoh A', 'is_correct' => false],
                    ['text' => $distractors[1] ?? 'Lagu Pengecoh B', 'is_correct' => false],
                    ['text' => $distractors[2] ?? 'Lagu Pengecoh C', 'is_correct' => false],
                ];
                shuffle($options);

                $dummy[] = [
                    'question' => $questionText,
                    'type' => 'audio',
                    'media_url' => $audioUrl,
                    'options' => $options,
                ];
            } elseif ($effectiveType === 'true_false') {
                if ($isTaylorSwift) {
                    $tfItem = $tsTfPool[($i - 1) % count($tsTfPool)];
                    $questionText = $tfItem['question'];
                    $isTrue = $tfItem['is_correct'];
                } else {
                    $isTrue = $i % 2 !== 0;
                    $questionText = "Pernyataan nomor {$i} tentang materi '{$topic}' adalah fakta yang sah dan terverifikasi secara ilmiah.";
                }

                $dummy[] = [
                    'question' => $questionText,
                    'type' => 'true_false',
                    'media_url' => null,
                    'options' => [
                        ['text' => 'BENAR', 'is_correct' => $isTrue],
                        ['text' => 'SALAH', 'is_correct' => ! $isTrue],
                    ],
                ];
            } else {
                if ($isTaylorSwift) {
                    $mcItem = $tsMcPool[($i - 1) % count($tsMcPool)];
                    $options = [
                        ['text' => $mcItem['correct'], 'is_correct' => true],
                        ['text' => $mcItem['distractors'][0], 'is_correct' => false],
                        ['text' => $mcItem['distractors'][1], 'is_correct' => false],
                        ['text' => $mcItem['distractors'][2], 'is_correct' => false],
                    ];
                    $questionText = $mcItem['question'];
                } else {
                    $options = [
                        ['text' => "Jawaban Benar materi {$topic}", 'is_correct' => true],
                        ['text' => "Pernyataan pengecoh materi {$topic} A", 'is_correct' => false],
                        ['text' => "Pernyataan pengecoh materi {$topic} B", 'is_correct' => false],
                        ['text' => "Pernyataan pengecoh materi {$topic} C", 'is_correct' => false],
                    ];
                    $questionText = "Pertanyaan nomor {$i} mengenai pemahaman materi: {$topic}?";
                }
                shuffle($options);

                $dummy[] = [
                    'question' => $questionText,
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
            'true_false' => "Semua soal WAJIB bertipe 'true_false' (Pernyataan Benar atau Salah). Sajikan pernyataan fakta menarik seputar topik: '{$keyword}' secara global/universal. Setiap soal hanya memiliki 2 pilihan: 'BENAR' dan 'SALAH'. Kolom 'type' diisi 'true_false' dan 'media_url' bernilai null.",
            'image' => "Semua soal WAJIB bertipe 'image' (Tebak Gambar). Seluruh pertanyaan dan subjek gambar WAJIB disesuaikan sepenuhnya dengan materi/topik: '{$keyword}' secara GLOBAL/UNIVERSAL (bisa artis/penyanyi dunia seperti Taylor Swift, landmark dunia seperti Menara Eiffel/Colosseum, film, astronomi/sains, teknologi, tokoh sejarah dunia, flora/fauna dunia, atau apapun topik yang diminta). JANGAN membatasi ke budaya/daerah lokal Indonesia kecuali jika topik memang memintanya! Kolom 'type' WAJIB diisi 'image'. Kolom 'correct_subject' WAJIB diisi nama tokoh/objek/tempat/karya spesifik yang akan dicari fotonya secara global (contoh jika Taylor Swift: 'Taylor Swift' atau 'The Eras Tour'; jika Landmark: 'Menara Eiffel' atau 'Colosseum'; jika Sains: 'Planet Saturnus' atau 'Albert Einstein'). Pertanyaan format: 'Perhatikan gambar di atas! Siapakah/Apakah nama subjek pada gambar tersebut?'. Pilihan jawaban 4 opsi dengan 1 pilihan benar yang sama persis dengan 'correct_subject' dan is_correct: true.",
            'audio' => "Semua soal WAJIB bertipe 'audio' (Tebak Musik / Lagu). Soal dan judul lagu WAJIB disesuaikan sepenuhnya dengan materi/topik: '{$keyword}' secara GLOBAL (bisa artis internasional seperti Taylor Swift, band pop, rock, K-Pop, soundtrack anime/film, musisi dunia, maupun lagu daerah Indonesia jika topik memintanya). Kolom 'type' WAJIB diisi 'audio'. Kolom 'song_title' WAJIB diisi dengan judul lagu spesifik yang dicari. Kolom 'artist' diisi dengan nama penyanyi/artis (contoh: 'Taylor Swift'). Format pertanyaan: 'Dengarkan potongan musik di atas! Lagu apakah yang sedang diputar ini?'. Salah satu pilihan jawaban (options) WAJIB sama dengan 'song_title' dan bernilai is_correct: true.",
            'mixed' => "Buatkan kuis variasi campuran yang seimbang dari 4 jenis format: 'multiple_choice' (pilihan ganda), 'true_false' (benar/salah), 'image' (tebak gambar dengan 'correct_subject' spesifik), dan 'audio' (tebak potongan musik dengan 'song_title' dan 'artist'). SEMUA soal dari segala format tersebut WAJIB mengangkat topik yang sama: '{$keyword}' secara GLOBAL/UNIVERSAL (bukan lagu/budaya daerah kecuali jika topik memang memintanya). Tentukan kolom 'type' sesuai format masing-masing soal.",
            default => "Semua soal bertipe 'multiple_choice' (Pilihan Ganda 4 opsi). Seluruh pertanyaan dan pilihan jawaban WAJIB mengangkat materi/topik: '{$keyword}' secara mendalam dan berwawasan luas/global. Kolom 'type' diisi 'multiple_choice' dan 'media_url' bernilai null.",
        };

        $prompt = "Kamu adalah pembuat kuis edukasi cerdas. Buatkan persis {$count} soal tentang materi/topik: '{$keyword}'.
Aturan jenis kuis:
{$typeInstruction}

Hasil WAJIB HANYA berupa JSON murni dengan format array persis seperti ini:
[
    {
        \"question\": \"Pertanyaan kuis?\",
        \"type\": \"multiple_choice / true_false / image / audio\",
        \"correct_subject\": \"Nama subjek objek gambar yang spesifik untuk dicari fotonya secara global (hanya untuk tipe image, contoh: Taylor Swift / Menara Eiffel / Planet Saturnus)\",
        \"song_title\": \"Judul lagu yang dicari (hanya untuk tipe audio, contoh: Cruel Summer / Tokecang)\",
        \"artist\": \"Nama penyanyi atau musisi (hanya untuk tipe audio, contoh: Taylor Swift)\",
        \"options\": [
            {\"text\": \"Jawaban benar\", \"is_correct\": true},
            {\"text\": \"Jawaban salah 1\", \"is_correct\": false},
            {\"text\": \"Jawaban salah 2\", \"is_correct\": false},
            {\"text\": \"Jawaban salah 3\", \"is_correct\": false}
        ]
    }
]";

        $candidateModels = [
            'gemini-flash-latest',
            'gemini-3.5-flash-lite',
            'gemini-3.6-flash',
            'gemini-3.5-flash',
            'gemini-3.7-flash',
            'gemini-3.8-flash',
            'gemini-2.5-flash',
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

            // Logika Media untuk Tipe Lagu (Tebak Lagu / Musik)
            if ($savedType === 'audio') {
                if (empty($mediaUrl) || ! str_starts_with($mediaUrl, 'http')) {
                    $songTitle = $item['song_title'] ?? '';
                    $artist = $item['artist'] ?? null;
                    $correctOptText = null;
                    foreach ($item['options'] as $opt) {
                        if (! empty($opt['is_correct'])) {
                            $correctOptText = trim($opt['text'] ?? '');
                            break;
                        }
                    }

                    // Prioritas 1: Cari preview audio online dari iTunes / Deezer
                    $selectedAudio = $this->searchAudioPreview(
                        $songTitle ?: ($correctOptText ?: $keyword),
                        $artist,
                        $keyword
                    );

                    // Prioritas 2: Jika belum ketemu, coba cari dengan nama judul dari opsi benar
                    if (! $selectedAudio && $correctOptText && $correctOptText !== $songTitle) {
                        $selectedAudio = $this->searchAudioPreview($correctOptText, $artist, $keyword);
                    }

                    // Prioritas 3: Cek kecocokan katalog lagu daerah lokal
                    if (! $selectedAudio) {
                        foreach ($songCatalog as $title => $audioLink) {
                            if (! empty($songTitle) && stripos($songTitle, $title) !== false) {
                                $selectedAudio = $audioLink;
                                break;
                            }
                            if ($correctOptText && stripos($correctOptText, $title) !== false) {
                                $selectedAudio = $audioLink;
                                break;
                            }
                        }
                    }

                    // Fallback jika tidak ada audio online maupun lokal yang cocok
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
