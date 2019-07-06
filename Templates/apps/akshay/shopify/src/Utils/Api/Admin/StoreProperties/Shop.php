<?php

namespace UVDesk\CommunityPackages\Akshay\Shopify\Utils\Api\Admin\StoreProperties;

/**
 * Read More: https://help.shopify.com/en/api/getting-started/authentication/oauth#verification
 */
abstract class Shop
{
    public static function get($shop_domain, $auth_code, $client_id, $client_secret)
    {
        $curlHandler = curl_init('https://' . $shopDomain . '/admin/shop.json');
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, ["X-Shopify-Access-Token: " . $accessToken]);

        $curlResponse = curl_exec($curlHandler);
        $jsonResponse = json_decode($curlResponse, true);
        curl_close($curlHandler);

        if (empty($jsonResponse['shop'])) {
            throw new \Exception('Unable to retrieve store details');
        }

        return $jsonResponse['shop'];
    }
}
