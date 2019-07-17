<?php

namespace UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\Platforms\Shopify;

use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\Api\Admin\StoreProperties\Shop;
use UVDesk\CommunityPackages\UVDesk\ECommerce\Utils\ECommerceChannelConfigurationInterface;

class ChannelConfiguration implements ECommerceChannelConfigurationInterface
{
    private $id;
    private $name;
    private $domain;
    private $client;
    private $password;
    private $timezone;
    private $ianaTimezone;
    private $currencyFormat;
    private $isEnabled = false;
    private $isVerified = false;
    private $verificationErrorMessage;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setIanaTimezone($ianaTimezone)
    {
        $this->ianaTimezone = $ianaTimezone;

        return $this;
    }

    public function getIanaTimezone()
    {
        return $this->ianaTimezone;
    }

    public function setCurrencyFormat($currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;

        return $this;
    }

    public function getCurrencyFormat()
    {
        return $this->currencyFormat;
    }

    public function setIsEnabled(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getIsEnabled() : bool
    {
        return $this->isEnabled;
    }

    public function load() : bool
    {
        try {
            $response = Shop::get($this->getDomain(), $this->getClient(), $this->getPassword());

            $this->id = $response['id'];
            $this->name = $response['name'];
            $this->timezone = $response['timezone'];
            $this->ianaTimezone = $response['iana_timezone'];
            $this->currencyFormat = $response['money_with_currency_in_emails_format'];

            return true;
        } catch (\Exception $e) {
        }
        
        return false;
    }

    public function getVerificationErrorMessage() : ?string
    {
        return $this->verificationErrorMessage ?? null;
    }

    public function __toString()
    {
        $template = require __DIR__ . "/../../../templates/configs/store-configuration.php";

        return strtr($template, [
            '[[ id ]]' => $this->getId(),
            '[[ domain ]]' => $this->getDomain(),
            '[[ name ]]' => $this->getName(),
            '[[ client ]]' => $this->getClient(),
            '[[ password ]]' => $this->getPassword(),
            '[[ enabled ]]' => $this->getIsEnabled() ? 'true' : 'false',
            '[[ timezone ]]' => $this->getTimezone(),
            '[[ iana_timezone ]]' => $this->getIanaTimezone(),
            '[[ currency_format ]]' => $this->getCurrencyFormat(),
        ]);
    }
}
