<?php

declare(strict_types=1);

namespace App\Security;

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
     * @param string[]       $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if ($attributes !== [self::ACTION_CREATE_POST]) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        if ($user === null) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($user->getUserIdentifier() !== 'admin') {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
