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

    public function generateMeetingRecommendation(array $availabilities, string $duration = "1 hour", string $tanggal = null)
    {
        $prompt = "Berperanlah sebagai asisten rapat yang membantu mencari waktu terbaik untuk rapat bersama berdasarkan data jadwal pribadi anggota berikut. "
            . "Durasi rapat yang diminta adalah {$duration}. ";
        if ($tanggal) {
            $prompt .= "Rapat hanya boleh direkomendasikan pada tanggal $tanggal dan tidak boleh ke tanggal yang sudah lewat. ";
        }
        $prompt .= "Jika tidak ada waktu yang memungkinkan untuk semua anggota, berikan jawaban yang jujur dan masuk akal, misalnya menyarankan untuk memperpendek durasi atau mencari hari lain. "
            . "Jawaban harus berupa rekomendasi jadwal yang singkat, jelas, dan alami, bukan dalam bentuk JSON atau tabel. "
            . "Gunakan gaya bahasa seperti asisten pribadi yang memberikan saran.\n\n"
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
}
