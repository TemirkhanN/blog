<?php

declare(strict_types=1);

namespace App\Security;

use App\Domain\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ViewPostVoter implements VoterInterface
{
    /**
     * @const string
     */
    private const ACTION_VIEW_POST = 'view_post';

    /**
     * @param TokenInterface $token
     * @param mixed          $subject
     * @param string[]       $attributes
     *
     * @return int
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if ($attributes !== [self::ACTION_VIEW_POST]) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$subject instanceof Post) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$subject->publishedAt() && !$this->isAdmin($token)) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }

    private function isAdmin(TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($user === null) {
            return false;
        }

        if ($user->getUserIdentifier() !== 'admin') {
            return false;
        }

        return true;
    }
}
