<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Temirkhan\View\ViewFactory;

class ViewCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $viewTaggedServiceIds = $container->findTaggedServiceIds('view');
        if ($viewTaggedServiceIds === []) {
            return;
        }

        $viewFactoryDefinition = $container->findDefinition(ViewFactory::class);
        foreach ($viewTaggedServiceIds as $viewTaggedServiceId => $tagInfo) {
            \assert(isset($tagInfo[0]['view']), sprintf('Invalid view definition for %s', $viewTaggedServiceId));

            $viewFactoryDefinition->addMethodCall(
                'registerView',
                [
                $tagInfo[0]['view'],
                 new Reference($viewTaggedServiceId),
                ]
            );
        }
    }
}
