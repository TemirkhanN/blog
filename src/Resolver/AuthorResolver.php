<?php
declare(strict_types=1);

namespace App\Resolver;


use App\Entity\Author;
use App\Repository\AuthorRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthorResolver implements ArgumentValueResolverInterface
{
    /**
     * @var array
     */
    private $tokens;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * Constructor
     *
     * @param array                     $tokens
     * @param AuthorRepositoryInterface $authorRepository
     */
    public function __construct(array $tokens, AuthorRepositoryInterface $authorRepository)
    {
        $this->tokens           = $tokens;
        $this->authorRepository = $authorRepository;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if ($argument->getType() === Author::class) {
            return true;
        }

        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $token = $request->headers->get('Authorization');

        $matchedAuthor = null;
        foreach ($this->tokens as $authorId => $authorToken) {
            if ($authorToken === $token) {
                $matchedAuthor = $authorId;
                break;
            }
        }

        if ($matchedAuthor === null) {
            throw new UnauthorizedHttpException('Bearer blog');
        }

        $author = $this->authorRepository->findByName($matchedAuthor);
        if ($author === null) {
            throw new UnauthorizedHttpException('Bearer blog');
        }

        yield $author;
    }
}