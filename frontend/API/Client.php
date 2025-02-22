<?php

declare(strict_types=1);

namespace Frontend\API;

use Frontend\API\Model\Comment;
use Frontend\API\Model\Post;
use Symfony\Component\HttpClient\Exception\TimeoutException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TemirkhanN\Generic\Error;
use TemirkhanN\Generic\Result;
use TemirkhanN\Generic\ResultInterface;

class Client
{
    private const ENDPOINT_LOGIN         = '/api/auth/tokens';
    private const ENDPOINT_POSTS         = '/api/posts';
    private const ENDPOINT_POST          = '/api/posts/%d';
    private const ENDPOINT_POST_LEGACY   = '/api/posts/%s';
    private const ENDPOINT_POST_RELEASES = '/api/posts/%d/releases';
    private const ENDPOINT_COMMENTS      = '/api/posts/%d/comments';

    private string $userToken = '';

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @return ResultInterface<string>
     */
    public function createUserToken(string $login, string $password): ResultInterface
    {
        $data = $this->sendRequest('POST', self::ENDPOINT_LOGIN, compact('login', 'password'));

        if (!isset($data['token'])) {
            return Result::error(Error::create($data['message'], $data['code'], $data));
        }

        return Result::success($data['token']);
    }

    public function setUserToken(string $userToken): void
    {
        $this->userToken = $userToken;
    }

    /**
     * @param string   $title
     * @param string   $preview
     * @param string   $content
     * @param string[] $tags
     *
     * @return ResultInterface<Post>
     */
    public function createPost(string $title, string $preview, string $content, array $tags): ResultInterface
    {
        $response = $this->sendRequest('POST', self::ENDPOINT_POSTS, compact('title', 'preview', 'content', 'tags'));

        $error = $this->getErrorMessage($response);

        if ($error !== '') {
            return Result::error(Error::create($error));
        }

        return Result::success(Post::unmarshall($response));
    }

    /**
     * @param int      $id
     * @param string   $title
     * @param string   $preview
     * @param string   $content
     * @param string[] $tags
     *
     * @return ResultInterface<Post>
     */
    public function editPost(
        int $id,
        string $title,
        string $preview,
        string $content,
        array $tags
    ): ResultInterface {
        $payload = compact('title', 'preview', 'content', 'tags');

        try {
            $response = $this->sendRequest('PATCH', sprintf(self::ENDPOINT_POST, $id), $payload);
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        $error = $this->getErrorMessage($response);
        if ($error !== '') {
            return Result::error(Error::create($error));
        }

        return Result::success(Post::unmarshall($response));
    }

    /**
     * @param int $id
     *
     * @return ResultInterface<Post>
     */
    public function getPost(int $id): ResultInterface
    {
        try {
            $response = $this->sendRequest('GET', sprintf(self::ENDPOINT_POST, $id));
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        if ($this->getErrorMessage($response) !== '') {
            return Result::error($this->resourceNotFound());
        }

        try {
            $commentsRaw = $this->sendRequest('GET', sprintf(self::ENDPOINT_COMMENTS, $id));
            $comments    = array_map([Comment::class, 'unmarshall'], $commentsRaw);
        } catch (TimeoutException $e) {
            $comments = [];
        }

        return Result::success(Post::unmarshall($response + ['comments' => $comments]));
    }

    /**
     * @param string $slug
     *
     * @return ResultInterface<Post>
     */
    public function getPostBySlug(string $slug): ResultInterface
    {
        try {
            $response = $this->sendRequest('GET', sprintf(self::ENDPOINT_POST_LEGACY, $slug));
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        if ($this->getErrorMessage($response) !== '') {
            return Result::error($this->resourceNotFound());
        }

        return $this->getPost($response['id']);
    }

    /**
     * @param int $id
     *
     * @return ResultInterface<void>
     */
    public function publishPost(int $id): ResultInterface
    {
        try {
            $response = $this->sendRequest('POST', sprintf(self::ENDPOINT_POST_RELEASES, $id));
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        $error = $this->getErrorMessage($response);
        if ($error !== '') {
            return Result::error(Error::create($error));
        }

        return Result::success();
    }

    /**
     * @param int     $page
     * @param int     $limit
     * @param ?string $tag
     *
     * @return ResultInterface<PostsCollection>
     */
    public function getPosts(int $page, int $limit, ?string $tag = null): ResultInterface
    {
        $query = [
            'limit'  => $limit,
            'offset' => ($page - 1) * $limit,
        ];

        if ($tag !== null) {
            $query['tag'] = $tag;
        }

        try {
            $postsRaw = $this->sendRequest('GET', self::ENDPOINT_POSTS . '?' . http_build_query($query));
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        $posts       = array_map([Post::class, 'unmarshall'], $postsRaw['data']);
        $metadataRaw = $postsRaw['pagination'];
        $metadata    = new Metadata($metadataRaw['limit'], $metadataRaw['offset'], $metadataRaw['total']);

        return Result::success(new PostsCollection($posts, $metadata));
    }

    /**
     * @param int    $id
     * @param string $comment
     *
     * @return ResultInterface<void>
     */
    public function addComment(int $id, string $comment): ResultInterface
    {
        $payload = ['text' => $comment];
        try {
            $response = $this->sendRequest('POST', sprintf(self::ENDPOINT_COMMENTS, $id), $payload);
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        $error = $this->getErrorMessage($response);
        if ($error !== '') {
            return Result::error(Error::create($error));
        }

        return Result::success();
    }

    /**
     * @param string $commentId
     * @param int    $postId
     * @param string $reply
     *
     * @return ResultInterface<void>
     */
    public function replyToComment(string $commentId, int $postId, string $reply): ResultInterface
    {
        $payload  = ['text' => $reply];
        $endpoint = sprintf(self::ENDPOINT_COMMENTS . '/%s', $postId, $commentId);

        try {
            $response = $this->sendRequest('POST', $endpoint, $payload);
        } catch (TimeoutException $e) {
            return Result::error($this->timeoutError());
        }

        $error = $this->getErrorMessage($response);
        if ($error !== '') {
            return Result::error(Error::create($error));
        }

        return Result::success();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $payload
     *
     * @return array response data
     *
     * @throws TimeoutException
     */
    private function sendRequest(string $method, string $uri, array $payload = []): array
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

        return $this->httpClient->request($method, $uri, $options)->toArray(false);
    }

    private function getErrorMessage(array $responseData): string
    {
        // TODO incorrect when it comes to structures containing "message"
        if (!isset($responseData['message'])) {
            return '';
        }

        return $responseData['message'];
    }

    private function timeoutError(): Error
    {
        return Error::create('Service is currently unavailable', ApiError::TEMPORARILY_UNREACHABLE->value);
    }

    private function resourceNotFound(): Error
    {
        return Error::create('Resource not found', ApiError::RESOURCE_NOT_FOUND->value);
    }
}
