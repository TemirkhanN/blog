<?php

declare(strict_types=1);

namespace Frontend\API;

use Frontend\API\Model\Comment;
use Frontend\API\Model\Post;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class Client
{
    private const ENDPOINT_LOGIN = 'api/auth/tokens';
    private const ENDPOINT_POSTS = 'api/posts';
    private const ENDPOINT_POST = 'api/posts/%s';
    private const ENDPOINT_COMMENTS = 'api/posts/%s/comments';

    private string $apiHost;

    private string $userToken = '';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
        $apiHost = 'http://192.168.2.38:8081'; // TODO
       // $apiHost = 'https://temirkhan.nasukhov.me'; // TODO

        $this->apiHost = rtrim($apiHost, '/') . '/';
    }

    /**
     * @return ResultInterface<string>
     */
    public function createUserToken(string $login, string $password): ResultInterface
    {
        $result = $this->sendRequest('POST', self::ENDPOINT_LOGIN, compact('login', 'password'));

        $data = $result->toArray(false);

        if (!isset($data['token'])) {
            return Result::error(Error::create($data['message'], $data['code'], $data));
        }

        return Result::success($data['token']);
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
        $response = $this->sendRequest('GET', sprintf(self::ENDPOINT_POST, $slug));
        $commentsRaw = $this->sendRequest('GET', sprintf(self::ENDPOINT_COMMENTS, $slug));

        $comments = array_map([Comment::class, 'unmarshall'], $commentsRaw->toArray());

        return Post::unmarshall($response->toArray() + ['comments' => $comments]);
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

        $response = $this->sendRequest('GET', self::ENDPOINT_POSTS . '?' . http_build_query($query));

        $postsRaw = $response->toArray();
        $posts = array_map([Post::class, 'unmarshall'], $postsRaw['data']);

        $metadataRaw = $postsRaw['pagination'];
        $metadata = new Metadata($metadataRaw['limit'], $metadataRaw['offset'], $metadataRaw['total']);

        return new PostsCollection($posts, $metadata);
    }

    private function sendRequest(string $method, string $uri, array $payload = []): ResponseInterface
    {
        $options = [];
        if ($payload !== []) {
            $options['json'] = $payload;
        }

        if ($this->userToken !== '') {
            $options['headers'] = [
                'Authorization' => $this->userToken,
            ];
        }

        return $this->httpClient->request($method, $this->apiHost . $uri, $options);
    }
}
