<?php

namespace Webkul\UVDesk\ExtensionBundle\Framework;

abstract class HelpdeskComponent implements HelpdeskComponentInterface
{
    public static function env() : array
    {
        return ['all'];
    }

    public static function services() : array
    {
        return [];
    }

    public abstract static function getTitle() : string;
    public abstract static function getDescription() : string;
}
