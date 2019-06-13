<?php

namespace Webkul\UVDesk\ExtensionBundle\DependencyInjection\Compilers;

use Webkul\UVDesk\CoreBundle\Extensibles\Builder;
use Symfony\Component\DependencyInjection\Reference;
use Webkul\UVDesk\CoreBundle\Extensibles\Tickets\Snippet;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webkul\UVDesk\ExtensionBundle\Extensions\ECommerce\TicketOrders;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TicketSnippetExtensibles implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(Snippet::class)) {
            return;
        }

        $snippet = $container->findDefinition(Snippet::class);
        // $snippet->addMethodCall('addSegment', array(new Reference(TicketOrders::class)));

        foreach ($container->findTaggedServiceIds('uvdesk_core_extensions.ticket_snippet') as $serviceId => $serviceTags) {
            $snippet->addMethodCall('addSegment', array(new Reference($serviceId)));
            // $build->addMethodCall('registerExtension', array(new Reference($serviceId), $serviceTags));
        }
    }
}
