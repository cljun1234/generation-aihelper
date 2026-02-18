<?php

class AIService {

    private const PROVIDER_OPENAI = 'openai';
    private const PROVIDER_DEEPSEEK = 'deepseek';

    private const URL_OPENAI = 'https://api.openai.com/v1/chat/completions';
    private const URL_DEEPSEEK = 'https://api.deepseek.com/chat/completions';

    /**
     * Generate text using the specified AI provider.
     *
     * @param string $provider 'openai' or 'deepseek'
     * @param string $model e.g., 'gpt-3.5-turbo', 'deepseek-chat'
     * @param string $systemPrompt The system instruction
     * @param string $userPrompt The user input combined with context
     * @param string $apiKey The API key
     * @return array ['success' => bool, 'data' => string|null, 'error' => string|null]
     */
    public static function generateCompletion($provider, $model, $systemPrompt, $userPrompt, $apiKey = null) {

        // Fallback to ENV if apiKey is not provided
        if (empty($apiKey)) {
            if ($provider === self::PROVIDER_OPENAI) {
                $apiKey = getenv('OPENAI_API_KEY');
            } elseif ($provider === self::PROVIDER_DEEPSEEK) {
                $apiKey = getenv('DEEPSEEK_API_KEY');
            }
        }

        if (empty($apiKey)) {
            return ['success' => false, 'error' => "API Key missing for provider: $provider"];
        }

        $url = '';
        if ($provider === self::PROVIDER_OPENAI) {
            $url = self::URL_OPENAI;
        } elseif ($provider === self::PROVIDER_DEEPSEEK) {
            $url = self::URL_DEEPSEEK;
        } else {
            return ['success' => false, 'error' => "Invalid provider: $provider"];
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ];

        $data = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7
        ];

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout for safety
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => "Curl Error: $error"];
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMessage = isset($responseData['error']['message']) ? $responseData['error']['message'] : "HTTP Error $httpCode";
            return ['success' => false, 'error' => "API Error: $errorMessage"];
        }

        if (isset($responseData['choices'][0]['message']['content'])) {
            return ['success' => true, 'data' => $responseData['choices'][0]['message']['content']];
        } else {
            return ['success' => false, 'error' => "Invalid response structure from API"];
        }
    }
}
