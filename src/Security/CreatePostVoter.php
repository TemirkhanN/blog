<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Author;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter that checks rights to create post
 */
class CreatePostVoter implements VoterInterface
{
    /**
     * @const string
     */
    private const ACTION_CREATE_POST = 'create_post';

    /**
     * @param TokenInterface $token
     * @param mixed          $subject
     * @param array          $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if ($attributes !== [self::ACTION_CREATE_POST] || !$subject instanceof Author) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$token->isAuthenticated()) {
            return VoterInterface::ACCESS_DENIED;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($subject->getName() !== $user->getUsername()) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
