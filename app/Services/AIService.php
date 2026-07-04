<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/',
            'timeout'  => 120,
        ]);

        $this->apiKey = env('AI_API_KEY');
    }

    public function speechToText($filePath, $language = 'id', $continueText = null)
    {
        $audioData = base64_encode(file_get_contents($filePath));
        
        if ($language === 'id') {
            $prompt = "Transkripkan audio ini hanya ke dalam teks Bahasa Indonesia. Jangan terjemahkan ke bahasa lain. Jangan tambahkan kata 'Translation'.";
        } else {
            $prompt = "Transcribe this audio only into English text. Do not translate to another language. Do not add the word 'Translation'.";
        }

        if ($continueText) {
            $prompt .= $language === 'id'
                ? " Lanjutkan transkripsi setelah teks berikut tanpa mengulang dan tetap gunakan Bahasa Indonesia:\n\n{$continueText}"
                : " Continue transcription after this text without repeating and keep using English:\n\n{$continueText}";
        }

        $response = $this->client->post("v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}", [
            'json' => [
                'contents' => [[
                    'parts' => [
                        [
                            "inlineData" => [
                                "data" => $audioData,
                                "mimeType" => "audio/mpeg"
                            ]
                        ],
                        [
                            "text" => $prompt
                        ]
                    ]
                ]]
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        Log::info('AIService speechToText result: ', $result);

        return $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    public function generateMeetingRecommendation(array $availabilities, string $duration = "1 hour", string $tanggalMulai = null, string $tanggalSelesai = null)
    {
        $prompt = "Berperanlah sebagai asisten rapat yang membantu mencari waktu terbaik untuk rapat bersama berdasarkan data jadwal ketersediaan pribadi anggota berikut. "
            . "Durasi rapat yang diminta adalah sekitar {$duration} untuk setiap sesi/harinya. ";
            
        if ($tanggalMulai && $tanggalSelesai && $tanggalMulai !== $tanggalSelesai) {
            $prompt .= "Rapat ini adalah RAPAT MULTI-HARI yang akan berlangsung dalam rentang tanggal $tanggalMulai sampai $tanggalSelesai. "
                . "PENTING: Dalam rapat multi-hari, jam mulai dan jam selesai rapat BISA BERBEDA di tiap harinya (multi-jam) menyesuaikan ketersediaan peserta pada hari tersebut. "
                . "Oleh karena itu, berikan beberapa pilihan opsi rangkaian jadwal (misal: Opsi 1, Opsi 2, dst.). Pada setiap opsi, rincikan tanggal beserta jam rapat (jam mulai - jam selesai) untuk SETIAP HARI dalam rentang tersebut secara spesifik. ";
        } elseif ($tanggalMulai) {
            $prompt .= "Rapat akan diadakan pada tanggal $tanggalMulai. Berikan beberapa pilihan opsi jam rapat di tanggal tersebut. ";
        }
        
        $prompt .= "Berikan beberapa pilihan opsi jadwal rapat yang paling memungkinkan untuk dihadiri oleh semua peserta. "
            . "Jika tidak ada waktu yang 100% memungkinkan untuk semua orang di hari tertentu, berikan jawaban yang jujur dan masuk akal, misalnya menyarankan opsi dengan tingkat kehadiran tertinggi atau mencari alternatif waktu. "
            . "Sertakan juga pertanyaan singkat di akhir kepada pengguna: apakah rapat bisa dilaksanakan pada jam istirahat/makan siang (12:00-13:00)? Jika tidak, sarankan agar menghindari opsi di jam tersebut.\n\n"
            
            // --- ATURAN FORMATTING UNTUK MENGURANGI SIMBOL DAN MEMPERJELAS TEKS ---
            . "ATURAN FORMATTING (SANGAT PENTING):\n"
            . "1. JANGAN gunakan tabel Markdown (|---|), garis pembatas (---), atau simbol dekoratif yang berlebihan.\n"
            . "2. Gunakan kalimat biasa dan daftar angka sederhana (1, 2, 3) atau bullet point (*) sederhana agar mudah dibaca di layar HP maupun Web.\n"
            . "3. Hindari penggunaan heading berderet (###). Cukup gunakan teks tebal (bold) untuk menegaskan bagian penting, nama opsi, atau tanggal & jam.\n"
            . "4. Buat respons yang ringkas, terstruktur rapi, langsung pada inti rekomendasi, ramah, dan alami seperti pesan dari asisten pribadi.\n\n"
            
            . "DATA KETERSEDIAAN:\n"
            . json_encode($availabilities, JSON_PRETTY_PRINT);

        $response = $this->client->post("v1beta/models/gemini-2.0-flash:generateContent?key={$this->apiKey}", [
            'json' => [
                'contents' => [[
                    'parts' => [
                        ["text" => $prompt]
                    ]
                ]]
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Tidak ada rekomendasi ditemukan.";

        return $text;
    }

    // public function generateMeetingRecommendation(array $availabilities, string $duration = "1 hour", string $tanggalMulai = null, string $tanggalSelesai = null)
    // {
    //     $prompt = "Berperanlah sebagai asisten rapat yang membantu mencari waktu terbaik untuk rapat bersama berdasarkan data jadwal ketersediaan pribadi anggota berikut. "
    //         . "Durasi rapat yang diminta adalah sekitar {$duration} untuk setiap sesi/harinya. ";
            
    //     if ($tanggalMulai && $tanggalSelesai && $tanggalMulai !== $tanggalSelesai) {
    //         $prompt .= "Rapat ini adalah RAPAT MULTI-HARI yang akan berlangsung dalam rentang tanggal $tanggalMulai sampai $tanggalSelesai. "
    //             . "PENTING: Dalam rapat multi-hari, jam mulai dan jam selesai rapat BISA BERBEDA di tiap harinya (multi-jam) menyesuaikan ketersediaan peserta pada hari tersebut. "
    //             . "Oleh karena itu, berikan beberapa pilihan opsi rangkaian jadwal (misal: Opsi 1, Opsi 2, dst.). Pada setiap opsi, rincikan tanggal beserta jam rapat (jam mulai - jam selesai) untuk SETIAP HARI dalam rentang tersebut secara spesifik. ";
    //     } elseif ($tanggalMulai) {
    //         $prompt .= "Rapat akan diadakan pada tanggal $tanggalMulai. Berikan beberapa pilihan opsi jam rapat di tanggal tersebut. ";
    //     }
        
    //     $prompt .= "Berikan beberapa pilihan opsi jadwal rapat yang paling memungkinkan untuk dihadiri oleh semua peserta. "
    //         . "Jika tidak ada waktu yang 100% memungkinkan untuk semua orang di hari tertentu, berikan jawaban yang jujur dan masuk akal, misalnya menyarankan opsi dengan tingkat kehadiran tertinggi atau mencari alternatif waktu. "
    //         . "Sertakan juga pertanyaan singkat di akhir kepada pengguna: apakah rapat bisa dilaksanakan pada jam istirahat/makan siang (12:00-13:00)? Jika tidak, sarankan agar menghindari opsi di jam tersebut.\n\n"
            
    //         // --- ATURAN FORMATTING UNTUK MENGURANGI SIMBOL DAN MEMPERJELAS TEKS ---
    //         . "ATURAN FORMATTING (SANGAT PENTING):\n"
    //         . "1. JANGAN gunakan tabel Markdown (|---|), garis pembatas (---), atau simbol dekoratif yang berlebihan.\n"
    //         . "2. Gunakan kalimat biasa dan daftar angka sederhana (1, 2, 3) atau bullet point (*) sederhana agar mudah dibaca di layar HP maupun Web.\n"
    //         . "3. Hindari penggunaan heading berderet (###). Cukup gunakan teks tebal (bold) untuk menegaskan bagian penting, nama opsi, atau tanggal & jam.\n"
    //         . "4. Buat respons yang ringkas, terstruktur rapi, langsung pada inti rekomendasi, ramah, dan alami seperti pesan dari asisten pribadi.\n\n"
            
    //         . "DATA KETERSEDIAAN:\n"
    //         . json_encode($availabilities, JSON_PRETTY_PRINT);

    //     $response = $this->client->post("https://ai.sumopod.com/v1/chat/completions", [
    //         'headers' => [
    //             'Authorization' => "Bearer ", // Gunakan variabel/env, jangan hardcode API key
    //             'Content-Type'  => 'application/json',
    //         ],
    //         'json' => [
    //             'model' => 'MiniMax-M2.7-highspeed', 
    //             'messages' => [
    //                 [
    //                     'role'    => 'user',
    //                     'content' => $prompt
    //                 ]
    //             ],
    //             // Sedikit diturunkan agar AI mematuhi instruksi formatting dengan lebih ketat
    //             'temperature' => 0.6 
    //         ]
    //     ]);

    //     $result = json_decode($response->getBody(), true);

    //     $text = $result['choices'][0]['message']['content'] ?? "Tidak ada rekomendasi ditemukan.";

    //     return $text;
    // }
}
