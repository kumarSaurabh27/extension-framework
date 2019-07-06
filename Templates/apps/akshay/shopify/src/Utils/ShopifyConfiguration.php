<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils;

use UVDesk\CommunityPackages\Akshay\Shopify\Utils\Channel\Channel;

final class ShopifyConfiguration
{
    const DEFAULT_TEMPLATE = __DIR__ . "/../../templates/defaults.yaml";
    const CONFIGURATION_TEMPLATE = __DIR__ . "/../../templates/configs.php";

    private $collection = [];

    public function addChannel(Channel $mailbox)
    {
        $this->collection[] = $mailbox;

        return $this;
    }

    public function removeChannel(Channel $mailbox)
    {
        if ($mailbox->getId() != null) {
            foreach ($this->collection as $index => $configuration) {
                if ($configuration->getId() == null) {
                    continue;
                }
                
                if ($configuration->getId() == $mailbox->getId()) {
                    unset($this->collection[$index]);
                    break;
                }
            }
        }

        $this->collection = array_values($this->collection);

        return $this;
    }

    public function getChannels() : array
    {
        return $this->collection;
    }

    public function __toString()
    {
        if (!empty($this->collection)) {
            $stream = array_reduce($this->collection, function($stream, $mailbox) {
                return $stream . (string) $mailbox;
            }, '');
    
            return strtr(require self::CONFIGURATION_TEMPLATE, [
                '[[ MAILBOXES ]]' => $stream,
            ]);
        }

        return file_get_contents(self::DEFAULT_TEMPLATE);
    }
}
