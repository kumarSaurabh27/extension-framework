<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Applications;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\Application;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ApplicationMetadata;

class ECommerceOrders extends Application implements EventSubscriberInterface
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
