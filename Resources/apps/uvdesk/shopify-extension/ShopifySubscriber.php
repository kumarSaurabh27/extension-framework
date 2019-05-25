<?php

namespace HelpdeskExtension\UVDesk\ShopifyEcommerce;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ShopifySubscriber implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadApplicationChannelCollection(ApplicationEvent $event)
    {
        $event->addEventResponse(['content' =>  []]);
    }

    public function saveApplication(ApplicationEvent $event)
    {
    }

    public function removeApplicationChannel(ApplicationEvent $event)
    {
    }

    public function uninstallApplication(ApplicationEvent $event)
    {
    }

    public static function getSubscribedEvents()
    {
        return ApplicationEvent::getEventCycle('Shopify');
    }

    public function loadFormArray(ApplicationEvent $event)
    {
        $formFields = [
            'formFields' => [
                [
                    'type' => 'text',
                    'name' => 'storename',
                    'label' => $this->translate('Store Name'),
                    'info' => $this->translate('Your Shopify Store Name'),
                ],
                [
                    'type' => 'password',
                    'name' => 'api_key',
                    'label' => $this->translate('API Key'),
                    'info' => $this->translate('Your Shopify API Key'),
                ],
                [
                    'type' => 'password',
                    'name' => 'api_password',
                    'label' => $this->translate('API Password'),
                    'info' => $this->translate('Your Shopify API Password'),
                ],
            ],
        ];

        $event->addEventResponse($formFields);
        return $formFields;
    }

    private function getOrderResponse($orderId, array $platformConfiguration)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_URL, 'https://' . $platformConfiguration['storename'] . '.myshopify.com/admin/orders.json?name=' . $orderId . '&status=any');
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, [
            'Accept: application/xml',
            'Content-Type: application/xml',
            'Authorization: Basic ' . base64_encode($platformConfiguration['api_key'] . ':' . $platformConfiguration['api_password'])
        ]);
        $curlResponse = curl_exec($curlHandler);
        curl_close($curlHandler);

        return json_decode($curlResponse, true);
    }

    private function getProductResponse($productId, array $platformConfiguration)
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_URL, 'https://' . $platformConfiguration['storename'] . '.myshopify.com/admin/products.json?ids=' . $productId);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, [
            'Accept: application/xml',
            'Content-Type: application/xml',
            'Authorization: Basic ' . base64_encode($platformConfiguration['api_key'] . ':' . $platformConfiguration['api_password'])
        ]);
        $curlResponse = curl_exec($curlHandler);
        curl_close($curlHandler);

        return json_decode($curlResponse, true);
    }

    public function processApplicationConfiguration(ApplicationEvent $event)
    {
        $eventData = $event->getEventData();
        $submittedFormData = !empty($eventData['request']) ? $eventData['request']->request->all() : [];

        // Validate Form Fields
        $validateFormRequest = true;
        foreach ($this->loadFormArray($event)['formFields'] as $formField) {
            if (empty($submittedFormData[$formField['name']])) {
                $validateFormRequest = false;
                break;
            }
        }

        // Process Configuration Data
        if (true == $validateFormRequest) {
            $channelDetails = [
                'title' => $submittedFormData['title'],
                'storename' => $submittedFormData['storename'],
                'api_key' => $submittedFormData['api_key'],
                'api_password' => $submittedFormData['api_password'],
            ];

            $orderResponse = $this->getOrderResponse('1', $channelDetails);
            if (isset($orderResponse['orders']) || (!empty($orderResponse['errors']) && strpos($orderResponse['errors'], '[API]') !== false)) {
                $event->addEventData(['channelDetails' => $channelDetails]);
            } else {
                // Configuration Error
                $event->stopPropagation();
                $event->raiseSessionMessage('warning', $this->translate('An unexpected error occurred while connecting with your webstore. Please check the provided details again.'));
            }
        } else {
            // Invalid Form
            $event->stopPropagation();
            $event->raiseSessionMessage('warning', $this->translate('An unexpected error occurred while processing your form details. Please check the provided details again.'));
        }
    }

    public function getOrderDetails(ApplicationEvent $event)
    {
        $orderCollection = [];
        $eventData = $event->getEventData();
        $channelDetails = $eventData['channelDetails'];
        $collectedOrders = ['validOrders' => [], 'invalidOrders' => []];
        $requestOrderCollection = array_map('trim', explode(',', $eventData['orderId']));

        foreach ($requestOrderCollection as $requestOrderIncrementId) {
            // Get Order Details
            $orderInstance = [];
            $orderResponse = $this->getOrderResponse($requestOrderIncrementId, $eventData['channelDetails']);

            if (!empty($orderResponse['orders'])) {
                // Add to Collection
                $orderCollection[] = ['order' => $orderResponse['orders']];
                $collectedOrders['validOrders'][] = $requestOrderIncrementId;
            } else {
                $collectedOrders['invalidOrders'][] = $requestOrderIncrementId;
            }
        }

        if (!empty($orderCollection)) {
            $event->addEventData(['orderCollection' => $orderCollection]);
            $event->addEventResponse(['collectedOrders' => $collectedOrders]);
        } else {
            // Failed to retrieve meaningful data. Stop Propagation
            $event->stopPropagation();
            $event->addEventResponse(['propagationMessage' => $this->translate('Warning! Unable to retrieve orders.')]);
        }
    }

    public function formatOrderDetails(ApplicationEvent $event)
    {
        // Format Data
        $eventData = $event->getEventData();
        $formattedOrderDetails = ['orders' => []];
        $channelDetails = $eventData['channelDetails'];
        $orderCollection = $eventData['orderCollection'];

        foreach ($orderCollection as $orderInstance) {
            $orderDetails = $orderInstance['order'];

            foreach ($orderDetails as $orderItem) {
                // Order Information
                $formattedOrderInstance = [
                    'id' => $orderItem['order_number'],
                    'total_price' => implode(' ', [$orderItem['currency'], $orderItem['total_price']]),
                ];

                if (!empty($orderItem['refunds'])) {
                    $formattedOrderInstance['total_refund'] = implode(' ', [$orderItem['currency'], number_format((float) $orderItem['refunds'][0]['transactions'][0]['amount'], 2, '.', '')]);
                }

                $orderPlacedTime = new \DateTime($orderItem['created_at']);
                $orderPlacedTime->setTimeZone(new \DateTimeZone('UTC'));
                $formattedOrderInstance['order_details']['Order Placed'] = $orderPlacedTime->format('Y-m-d H:i:s');
                // $formattedOrderInstance['order_details']['Order Closing Date'] = !empty($orderItem['closed_at']) ? $orderItem['closed_at'] : 'Not closed';

                // Order Cancellation Status
                if (!empty($orderItem['cancelled_at'])) {
                    $formattedOrderInstance['order_details']['Order Cancellation Status'] = 'Cancelled';
                    $formattedOrderInstance['order_details']['Order Cancellation Date'] = $orderItem['cancelled_at'];
                    $formattedOrderInstance['order_details']['Order Cancellation Reason'] = $orderItem['cancel_reason'];
                } else {
                    $formattedOrderInstance['Order Cancellation Status'] = 'Not Cancelled';
                }

                // Payment Information
                $formattedOrderInstance['payment_details'] = [
                    'Payment Status' => ucwords($orderItem['financial_status']),
                    'Order Processing Method' => ucwords($orderItem['processing_method']),
                    'Order Payment Gateways' => (!empty($orderItem['payment_gateway_names']) ? ucwords(implode(', ', $orderItem['payment_gateway_names'])) : 'NA'),
                    'Order Fulfillment Status' => ($orderItem['fulfillment_status'] == 'fulfilled') ? 'Fulfilled' : (($orderItem['fulfillment_status'] == 'partial') ? 'Partial' : 'Pending'),
                ];

                // Customer Details
                // if (!empty($orderItem['customer'])) {
                //     $customerDetails = $orderItem['customer'];
                //     $formattedOrderInstance['Customer ID'] = $customerDetails['id'];
                //     $formattedOrderInstance['Customer Name'] = $customerDetails['first_name'] . ' ' . $customerDetails['last_name'];
                //     $formattedOrderInstance['Customer Email'] = $customerDetails['email'];
                //     $formattedOrderInstance['Customer Phone'] = $customerDetails['phone'];
                // }

                // Shipping Address
                if (!empty($orderItem['shipping_address'])) {
                    $shippingDetails = $orderItem['shipping_address'];
                    $shippingAddressItems = [
                        $shippingDetails['name'],
                        implode(', ', [$shippingDetails['address1'], (!empty($shippingDetails['address2']) ? $shippingDetails['address2'] : '')]) . ', ' . $shippingDetails['city'] . (!empty($shippingDetails['province']) ? ', ' . $shippingDetails['province'] : ''),
                        $shippingDetails['country_code'],
                    ];

                    $formattedOrderInstance['shipping_details']['Shipping Address'] = implode('</br>', $shippingAddressItems);
                } else {
                    $formattedOrderInstance['shipping_details']['Shipping Address'] = 'NA';
                }

                // Order Items
                foreach ($orderItem['line_items'] as $orderItemInstance) {
                    $productResponse = $this->getProductResponse($orderItemInstance['product_id'], $channelDetails);
                    if (!empty($productResponse['products'][0]['handle'])) {
                        $productLink = 'https://' . $channelDetails['storename'] . '.myshopify.com' .  '/products/' . $productResponse['products'][0]['handle'];
                    } else {
                        $productLink = '';
                    }

                    $formattedOrderInstance['product_details'][] = [
                        'title' => ucwords($orderItemInstance['title']),
                        'link' => !empty($productLink) ? $productLink : '',
                        'price' => implode(' ', [$orderItem['currency'], $orderItemInstance['price']]),
                        'quantity' => (int) floor($orderItemInstance['quantity']),
                    ];
                }
            }

            $formattedOrderDetails['orders'][] = $formattedOrderInstance;
        }

        $event->addEventResponse(['orderDetails' => $formattedOrderDetails]);
    }
}

?>
