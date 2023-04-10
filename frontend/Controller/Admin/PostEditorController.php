<?php
declare(strict_types=1);

namespace Frontend\Controller\Admin;

use Frontend\Controller\AbstractBlogController;
use Frontend\Resource\View\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostEditorController extends AbstractBlogController
{
    public function __invoke(Request $request): Response
    {
        $title = '';
        $preview = '';
        $content = '';
        $tags = '';
        $error = '';
        if ($request->isMethod('POST')) {
            $title = (string)$request->request->get('title', '');
            $preview = (string)$request->request->get('preview', '');
            $content = (string)$request->request->get('content', '');
            $tags = (string)$request->request->get('tags', '');

            $postCreation = $this->blogApi->createPost($title, $preview, $content, $this->parseTags($tags));
            if ($postCreation->isSuccessful()) {
                $post = $postCreation->getData();

                return new RedirectResponse('/blog/' . $post->slug);
            }

            $error = $postCreation->getError()->getMessage();
        }

        return $this->renderer->render(
            Page::POST_EDIT,
            compact('title', 'preview', 'content', 'tags', 'error')
        );
    }

    /**
     * @param string $raw
     *
     * @return string[]
     */
    private function parseTags(string $raw): array
    {
        return explode(',', $raw);
    }
}
