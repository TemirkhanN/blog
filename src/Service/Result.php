<?php

declare(strict_types=1);

namespace App\Service;

/**
 * @template T
 */
class Result
{
    private string $error = '';

    /**
     * @var T
     */
    private $data;

    private function __construct()
    {
    }

    /**
     * phpcs:disable Squiz.Commenting.FunctionComment.TypeHintMissing
     *
     * @param T $data
     *
     * @return static<T>
     */
    public static function success($data): self
    {
        // phpcs:enable
        $result       = new self();
        $result->data = $data;

        return $result;
    }

    /**
     * @param string $error
     *
     * @return static<T>
     */
    public static function error(string $error): self
    {
        assert($error !== '');

        $result        = new self();
        $result->error = $error;

        return $result;
    }

    public function isSuccessful(): bool
    {
        return $this->error === '';
    }

    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return T
     */
    public function getData()
    {
        assert($this->isSuccessful(), 'The result was an error. There is no data.');

        return $this->data;
    }
}
