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
            'timeout'  => 120,
        ]);

        $this->apiKey = env('AI_API_KEY');
        $this->apiKeyGROQ = env('AI_API_KEYGROQ');
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

        $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}", [
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

        // Call Groq API (OpenAI-compatible endpoint)
        try {
            $groqBaseUrl = env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1');
            $groqModel = env('GROQ_MODEL', 'mixtral-8x7b-32768');

            $response = $this->client->post($groqBaseUrl . '/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKeyGROQ,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $groqModel,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ],
                'timeout' => 120,
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('AIService Groq response: ', (array)$result);

            // Extract text from OpenAI-compatible response format
            $text = null;

            if (isset($result['choices'][0]['message']['content'])) {
                $text = $result['choices'][0]['message']['content'];
            } elseif (isset($result['choices'][0]['text'])) {
                $text = $result['choices'][0]['text'];
            }

            if (!$text) {
                // Fallback: log full response for debugging
                Log::warning('AIService Groq: No text extracted from response', $result);
                $text = json_encode($result);
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('AIService Groq request failed: ' . $e->getMessage());
            return "Tidak ada rekomendasi ditemukan.";
        }
    }
}
