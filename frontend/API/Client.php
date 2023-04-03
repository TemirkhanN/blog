<?php

declare(strict_types=1);

namespace Frontend\API;

use Frontend\API\Model\Post;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Client
{
    private string $userToken = '';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {

    }

    public function setUserToken(string $userToken): void
    {
        $this->userToken = $userToken;
    }

    public function createPost(string $title, string $preview, string $content, array $tags): Post
    {

    }

    public function editPost(string $slug, string $title, string $preview, string $content, array $tags): Post
    {

    }

    public function getPost(string $slug): ?Post
    {
        $response = $this->sendRequest('GET', 'http://blog_server/api/posts/' . $slug);

        return Post::unmarshall($response->toArray());
    }

    public function publishPost(string $slug): void
    {
    }

    public function getPosts(int $page, int $limit, ?string $tag = null): PostsCollection
    {
        $query = [
            'limit' => $limit,
            'offset' => ($page-1) * $limit,
        ];

        if ($tag !== null) {
            $query['tag'] = $tag;
        }

        $response = $this->sendRequest('GET', 'http://blog_server/api/posts?' . http_build_query($query));

        $postsRaw = $response->toArray();

        $posts = [];
        foreach ($postsRaw['data'] as $postRaw) {
            $posts[] = Post::unmarshall($postRaw);
        }

        $metadataRaw = $postsRaw['pagination'];
        $metadata = new Metadata($metadataRaw['limit'], $metadataRaw['offset'], $metadataRaw['total']);

        return new PostsCollection($posts, $metadata);
    }

    private function sendRequest(string $method, string $uri, array $payload = []): ResponseInterface
    {
        $options = [];
        if ($payload !== []) {
            $options['json'] = json_encode($payload);
        }

        if ($this->userToken !== '') {
            $options['headers'] = [
                'Authorization' => $this->userToken,
            ];
        }

        return $this->httpClient->request($method, $uri, $options);
    }
}
