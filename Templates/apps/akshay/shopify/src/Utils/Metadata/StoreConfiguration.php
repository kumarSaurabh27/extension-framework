<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils\Metadata;

class StoreConfiguration
{
    const TEMPLATE = __DIR__ . "/../../../templates/channel.php";

    private $domain;
    private $api_key;
    private $api_password;

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }

    public function getApiKey()
    {
        return $this->api_key;
    }

    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;

        return $this;
    }

    public function getApiPassword()
    {
        return $this->api_password;
    }

    public function __toString()
    {
        return strtr(require self::TEMPLATE, [
            '[[ id ]]' => $this->getId(),
            '[[ name ]]' => $this->getName(),
            '[[ status ]]' => $this->getIsEnabled() ? 'true' : 'false',
            // '[[ swiftmailer_id ]]' => $swiftmailerConfiguration ? $swiftmailerConfiguration->getId() : '~',
            // '[[ imap_host ]]' => $imapConfiguration->getHost(),
            // '[[ imap_username ]]' => $imapConfiguration->getUsername(),
            // '[[ imap_password ]]' => $imapConfiguration->getPassword(),
        ]);
    }
}
