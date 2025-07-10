<?php
class TLDR_API
{

    /**
     * Generate a concise excerpt from post content using the Turboline AI API.
     *
     * @param string $content The full post content.
     * @return string The generated excerpt or an empty string on failure.
     */
    public static function generate_excerpt($content)
    {
        $api_key = get_option('tldr_key');
        $word_limit = get_option('tldr_limit') ?: 150;

        if (empty($api_key) || empty($content)) {
            return '';
        }

        // Build prompt
        $prompt = self::build_prompt($content, $word_limit);

        // Send request
        $response = self::send_request($prompt, $api_key);

        if (is_wp_error($response)) {
            error_log('[TLDR Plugin] API request failed: ' . $response->get_error_message());
            return '';
        }

        $response_body = wp_remote_retrieve_body($response);
        $result = json_decode($response_body, true);

        return $result['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Build the prompt string to send to the API.
     *
     * @param string $content
     * @param int $word_limit
     * @return string
     */
    private static function build_prompt($content, $word_limit)
    {
        $trimmed = wp_strip_all_tags($content);
        $trimmed = mb_substr($trimmed, 0, 2000);

        return "You are a Lead Editor at a very popular online magazine. Based on this followingcontent you can generate a concise tldr excerpt in no more than {$word_limit} words. Also make sure to provide full sentences and do not cut off any tldr mid-sentence. If the content has mention of data, analysis or figures, try using it in the tldr summary.\n\n\ {$trimmed}";
    }

    /**
     * Send the request to the Turboline AI API.
     *
     * @param string $prompt
     * @param string $api_key
     * @return array|WP_Error
     */
    private static function send_request($prompt, $api_key)
    {
        $url = 'https://api.turboline.ai/tldr/openai/deployments/gpt-4o/chat/completions?api-version=2025-03-01-preview';

        $body = json_encode([
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ]
        ]);

        return wp_remote_post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
                'api-key' => $api_key,
            ],
            'body' => $body,
            'timeout' => 20,
        ]);
    }
}