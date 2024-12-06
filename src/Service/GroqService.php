<?php

namespace App\Service;

use GuzzleHttp\Client;

class GroqService
{
    private $apiKey;
    private $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => 'https://api.groq.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function generateResponse(array $messages): array
{
    try {
        // Log des messages envoyés
        error_log('Messages envoyés à Groq: ' . json_encode($messages));

        $response = $this->client->post('https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'model' => 'llama3-8b-8192',
                'messages' => $messages
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        // Log de la réponse brute
        error_log('Réponse de Groq: ' . json_encode($data));

        return $data['choices'][0]['message'] ?? ['content' => 'Aucun message reçu'];
    } catch (\Exception $e) {
        return ['content' => 'Erreur : ' . $e->getMessage()];
    }
}
}