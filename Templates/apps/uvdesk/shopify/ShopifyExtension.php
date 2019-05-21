<?php

namespace HelpdeskExtension\Shopify\ExtensionBundle;

class ShopifyExtension
{
    public function getContainerExtension()
    {
        return new HelpdeskExtension();
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
