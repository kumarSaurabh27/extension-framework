<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils\Channel;

class Channel
{
    const TEMPLATE = __DIR__ . "/../../../templates/channel.php";

    private $id = null;
    private $name = null;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    private function setId($id)
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

    public function setIsEnabled(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getIsEnabled() : bool
    {
        return $this->isEnabled;
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
