<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify;

use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\PackageMetadata;
use UVDesk\CommunityPackages\Akshay\Shopify\Utils\ShopifyConfiguration;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackage;
use Webkul\UVDesk\ExtensionFrameworkBundle\Definition\ConfigurablePackageInterface;

class ShopifyPackage extends ConfigurablePackage implements ConfigurablePackageInterface
{
    public static function install(PackageMetadata $metadata) : void
    {
        self::updatePackageConfiguration($metadata, file_get_contents(__DIR__ . "/../templates/defaults.yaml"));
    }

    public function parseConfigurations()
    {
        $configs = $this->getConfigurations();

        // Read configurations from package config.
        $shopifyConfiguration = new ShopifyConfiguration();
        
        foreach ($this->getConfigurations() as $id => $params) {
            // Swiftmailer Configuration
            $swiftmailerConfiguration = null;
            foreach ($swiftmailerConfigurations as $configuration) {
                if ($configuration->getId() == $params['smtp_server']['mailer_id']) {
                    $swiftmailerConfiguration = $configuration;
                    break;
                }
            }
            
            // IMAP Configuration
            ($imapConfiguration = ImapConfiguration::guessTransportDefinition($params['imap_server']['host']))
                ->setUsername($params['imap_server']['username'])
                ->setPassword($params['imap_server']['password']);
            // Mailbox Configuration
            ($mailbox = new Mailbox($id))
                ->setName($params['name'])
                ->setIsEnabled($params['enabled'])
                ->setImapConfiguration($imapConfiguration);
            
            if (!empty($swiftmailerConfiguration)) {
                $mailbox->setSwiftMailerConfiguration($swiftmailerConfiguration);
            } else if (!empty($params['smtp_server']['mailer_id']) && true === $ignoreInvalidAttributes) {
                $mailbox->setSwiftMailerConfiguration($swiftmailerService->createConfiguration('smtp', $params['smtp_server']['mailer_id']));
            }

            $mailboxConfiguration->addMailbox($mailbox);
        }

        return $mailboxConfiguration;
    }
}
