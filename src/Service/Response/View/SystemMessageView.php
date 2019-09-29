<?php
declare(strict_types=1);

namespace App\Service\Response\View;

use App\Service\Response\ValueObject\SystemMessage;
use Temirkhan\View\ViewInterface;

/**
 * Representation for unauthorized access
 */
class SystemMessageView implements ViewInterface
{
    /**
     * Returns view
     *
     * @param mixed $context
     *
     * @return array|null
     */
    public function getView($context)
    {
        if (!$context instanceof SystemMessage) {
            return null;
        }

        return [
            'code'    => $context->getCode(),
            'message' => $context->getMessage(),
        ];
    }
}
