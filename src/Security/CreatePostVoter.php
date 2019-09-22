<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CreatePostVoter implements VoterInterface
{
    /**
     * @const string
     */
    private const ACTION_CREATE_POST = 'create_post';

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        if ($attributes !== [self::ACTION_CREATE_POST]) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$token->isAuthenticated()) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
