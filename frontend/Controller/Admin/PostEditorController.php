<?php

declare(strict_types=1);

namespace Frontend\Controller\Admin;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Frontend\Service\Access;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class PostEditorController extends AbstractBlogController
{
    public function __invoke(Request $request, int $id, Access $access): Response
    {
        if (!$access->isAdmin()) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        if ($id !== 0) {
            return $this->handleUpdate($id, $request);
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
            $title   = $request->request->getString('title');
            $preview = $request->request->getString('preview');
            $content = $request->request->getString('content');
            $tags    = $request->request->getString('tags');

            $postCreation = $this->blogApi->createPost($title, $preview, $content, $this->unserializeTags($tags));
            if ($postCreation->isSuccessful()) {
                $post = $postCreation->getData();

                return new RedirectResponse($this->getPostUri($post));
            }

            $error = $postCreation->getError()->getMessage();
        }

        return new Response(
            $this->renderer->render(
                Page::ADMIN_POST_EDIT,
                compact('title', 'preview', 'content', 'tags', 'error')
            )
        );
    }

    private function handleUpdate(int $id, Request $request): Response
    {
        $post = $this->blogApi->getPost($id);
        if ($post === null) {
            return new Response($this->renderer->render(Page::ERROR, ['error' => 404]), 404);
        }

        $title   = $post->title;
        $preview = $post->preview;
        $content = $post->content;
        $tags    = $this->serializeTags($post->tags);

        $error = '';
        if ($request->isMethod('POST')) {
            $title   = $request->request->getString('title');
            $preview = $request->request->get('preview');
            $content = $request->request->get('content');
            $tags    = $request->request->get('tags');

            $postUpdate = $this->blogApi->editPost($id, $title, $preview, $content, $this->unserializeTags($tags));
            if ($postUpdate->isSuccessful()) {
                return new RedirectResponse($this->getPostUri($postUpdate->getData()));
            }

            $error = $postUpdate->getError()->getMessage();
        }

        return new Response(
            $this->renderer->render(
                Page::ADMIN_POST_EDIT,
                compact('title', 'preview', 'content', 'tags', 'error')
            )
        );
    }

    /**
     * @param string $raw
     *
     * @return string[]
     */
    private function unserializeTags(string $raw): array
    {
        return array_filter(array_map('trim', explode(',', $raw)));
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
