<?php

declare(strict_types=1);

namespace App\Service\Post\Dto;

/**
 * Post creation data transfer object.
 */
class PostData
{
    /**
     * @var string[]
     */
    public array $tags;

    /**
     * @param string   $title
     * @param string   $preview
     * @param string   $content
     * @param string[] $tags
     *
     * @throws InvalidInput
     */
    public function __construct(
        public readonly string $title,
        public readonly string $preview,
        public readonly string $content,
        array $tags = []
    ) {
        $errors = [];
        foreach ($tags as $pos => $tag) {
            if (!preg_match('#^[a-zA-Z0-9]+$#', $tag)) {
                $errors['tags'][$pos] = 'Tag contains invalid symbols';
            }
        }

        if ($errors !== []) {
            throw new InvalidInput($errors);
        }

        $this->tags = $tags;
    }

    /**
     * @param array<mixed> $raw
     *
     * @return self
     *
     * @throws InvalidInput
     */
    public static function unmarshall(array $raw): self
    {
        $errors = [];
        foreach (['title', 'preview', 'content'] as $property) {
            $value = $raw[$property] ?? '';
            if ($value === '') {
                $errors[$property] = 'This value should not be blank.';
            } elseif (!is_string($value)) {
                $errors[$property] = 'Property expected to be a string';
            }
        }

        if (isset($raw['tags'])) {
            $tags = $raw['tags'];
            if (!is_array($tags)) {
                $errors['tags'] = 'Property expected to be an array';
            } else {
                foreach ($tags as $pos => $tag) {
                    if ($tag === '') {
                        $errors['tags'][$pos] = 'This value should not be blank.';
                    } elseif (!is_string($tag)) {
                        $errors[$property] = 'Property expected to be a string';
                    }
                }
            }
        }

        if ($errors !== []) {
            throw new InvalidInput($errors);
        }

        return new self($raw['title'], $raw['preview'], $raw['content'], $raw['tags'] ?? []);
    }
}
