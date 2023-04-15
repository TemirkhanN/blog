<?php

declare(strict_types=1);

namespace Frontend\Controller\Admin;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Frontend\Service\Access;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostEditorController extends AbstractBlogController
{
    public function __invoke(Request $request, string $slug, Access $access): Response
    {
        if (!$access->isAdmin()) {
            return $this->renderer->render(Page::ERROR_FORBIDDEN);
        }

        if ($slug !== '') {
            return $this->handleUpdate($slug, $request);
        }

        return $this->handleCreation($request);
    }

    private function handleCreation(Request $request): Response
    {
        $title   = '';
        $preview = '';
        $content = '';
        $tags    = '';

        $error = '';
        if ($request->isMethod('POST')) {
            $title   = (string) $request->request->get('title', '');
            $preview = (string) $request->request->get('preview', '');
            $content = (string) $request->request->get('content', '');
            $tags    = (string) $request->request->get('tags', '');

            $postCreation = $this->blogApi->createPost($title, $preview, $content, $this->unserializeTags($tags));
            if ($postCreation->isSuccessful()) {
                $post = $postCreation->getData();

                return new RedirectResponse('/blog/' . $post->slug);
            }

            $error = $postCreation->getError()->getMessage();
        }

        return $this->renderer->render(
            Page::ADMIN_POST_EDIT,
            compact('title', 'preview', 'content', 'tags', 'error')
        );
    }

    private function handleUpdate(string $postSlug, Request $request): Response
    {
        $post = $this->blogApi->getPost($postSlug);
        if ($post === null) {
            return $this->renderer->render(Page::ERROR_NOT_FOUND);
        }

        $title   = $post->title;
        $preview = $post->preview;
        $content = $post->content;
        $tags    = $this->serializeTags($post->tags);

        $error = '';
        if ($request->isMethod('POST')) {
            $title   = (string) $request->request->get('title', '');
            $preview = (string) $request->request->get('preview', '');
            $content = (string) $request->request->get('content', '');
            $tags    = (string) $request->request->get('tags', '');

            $postUpdate = $this->blogApi
                ->editPost($postSlug, $title, $preview, $content, $this->unserializeTags($tags));

            if ($postUpdate->isSuccessful()) {
                return new RedirectResponse('/blog/' . $postUpdate->getData()->slug);
            }

            $error = $postUpdate->getError()->getMessage();
        }

        return $this->renderer->render(
            Page::ADMIN_POST_EDIT,
            compact('title', 'preview', 'content', 'tags', 'error')
        );
    }

    /**
     * @param string $raw
     *
     * @return string[]
     */
    private function unserializeTags(string $raw): array
    {
        return array_map('trim', explode(',', $raw));
    }

    /**
     * @param string[] $tags
     *
     * @return string
     */
    private function serializeTags(array $tags): string
    {
        return implode(',', $tags);
    }
}
