<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Service\Result;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramNotifier
{
    private const METHOD_NAME = 'sendMessage';

    public function __construct(private readonly string $botToken, private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param int    $chatId
     * @param string $message
     *
     * @return Result<null>
     */
    public function sendNotification(int $chatId, string $message): Result
    {
        $uri = sprintf('https://api.telegram.org/bot%s/%s', $this->botToken, self::METHOD_NAME);

        $payload = [
            'json' => [
                'chat_id' => $chatId,
                'text'    => $message,
            ],
        ];

        $response = $this->httpClient->request('POST', $uri, $payload);

        if ($response->getStatusCode() > 400) {
            return Result::error($response->getContent());
        }

        return Result::success([]);
    }
}
