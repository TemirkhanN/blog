<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Publication
 */
class Post
{
    /**
     * Title
     *
     * @var string
     */
    private $title;

    /**
     * User-friendly URL post name
     *
     * @var string
     */
    private $slug;

    /**
     * @var DateTimeImmutable
     */
    private $publishedAt;

    /**
     * Content
     *
     * @var string
     */
    private $content;

    /**
     * Author
     *
     * @var Author
     */
    private $author;

    /**
     * Constructor
     *
     * @param Author $author
     * @param string $title
     * @param string $content
     */
    public function __construct(Author $author, string $title, string $content)
    {
        $this->title       = $title;
        $this->content     = htmlspecialchars($content, ENT_QUOTES);
        $this->slug        = preg_replace('#\W#', '', $title);
        $this->author      = $author;
        $this->publishedAt = new DateTimeImmutable();
    }

    /**
     * Returns identifier
     *
     * @return string
     */
    public function getId(): string
    {
        return sprintf('%s_%s', $this->author->getName(), $this->getSlug());
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Returns author
     *
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * Returns slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Returns post publish datetime
     *
     * @return DateTimeInterface
     */
    public function getPublishedAt(): DateTimeInterface
    {
        return $this->publishedAt;
    }
}
