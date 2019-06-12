<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection\Compilers;

use Webkul\UVDesk\CoreBundle\Extensibles\Builder;
use Symfony\Component\DependencyInjection\Reference;
use Webkul\UVDesk\CoreBundle\Extensibles\Tickets\Snippet;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ECommerce implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Snippet::class)) {
            return;
        }

        $snippet = $container->findDefinition(Snippet::class);

        dump('Yo we here');
        dump($snippet);
        die;

        // foreach ($container->findTaggedServiceIds('uvdesk.extendable_component') as $serviceId => $serviceTags) {
        //     $build->addMethodCall('registerExtension', array(new Reference($serviceId), $serviceTags));
        // }
    }
}
