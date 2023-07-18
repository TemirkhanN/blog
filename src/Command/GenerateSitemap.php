<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\SitemapGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSitemap extends Command
{
    private const SITEMAP_DIR = __DIR__ . './../../public';

    public function __construct(
        private readonly SitemapGenerator $sitemapGenerator,
    ) {
        parent::__construct('sitemap:generate');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $content = $this->sitemapGenerator->generate();

        if ($content === '') {
            $output->writeln('Could not generate sitemap');
            return -1;
        }

        if (file_put_contents(self::SITEMAP_DIR . '/sitemap.xml', $content) === false) {
            $output->writeln('Could not save sitemap file');

            return -1;
        }

        return 0;
    }
}
