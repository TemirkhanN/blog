<?php
declare(strict_types=1);

namespace App\Service\Response\ValueObject;

/**
 * System message
 */
class SystemMessage
{
    /**
     * Message
     *
     * @var string
     */
    private $message;

    /**
     * Code
     *
     * @var int
     */
    private $code;

    /**
     * Constructor
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct(string $message, int $code)
    {
        $this->message = $message;
        $this->code    = $code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
