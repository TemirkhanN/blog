<?php

declare(strict_types=1);

namespace Frontend\Service\RendererExtension;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\MarkdownConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

class Markdown extends AbstractExtension
{
    private ?ConverterInterface $converter = null;
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'markdown_to_html',
                $this->converterFn(),
                ['is_safe' => ['all']]
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('contains_code', $this->codeDetectorFn()),
        ];
    }

    private function converterFn(): callable
    {
        return function (string $markdown): string {
            return $this->getMarkdownConverter()->convert($markdown)->getContent();
        };
    }

    private function codeDetectorFn(): callable
    {
        return static function (string $input): bool {
            return preg_match('#```[a-zA-Z]+#', $input) === 1;
        };
    }

    private function getMarkdownConverter(): ConverterInterface
    {
        if ($this->converter === null) {
            $env = new Environment([
                'default_attributes' => [
                    Link::class => ['target' => '_blank'],
                ],
            ]);
            $env->addExtension(new CommonMarkCoreExtension());
            $env->addExtension(new DefaultAttributesExtension());

            $this->converter = new MarkdownConverter($env);
        }

        return $this->converter;
    }
}
