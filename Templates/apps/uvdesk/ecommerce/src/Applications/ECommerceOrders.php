<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Applications;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationMetadata;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application\ApplicationInterface;

class ECommerceOrders extends Application implements ApplicationInterface, EventSubscriberInterface
{
    public static function getMetadata() : ApplicationMetadata
    {
        return new ECommerceOrdersMetadata();
    }

    public static function getSubscribedEvents()
    {
        return array();
    }
}
