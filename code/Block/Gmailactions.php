<?php

class Meanbee_Gmailactions_Block_Gmailactions extends Mage_Core_Block_Template
{

    public function getViewOrderUrl($order)
    {
        return Mage::getUrl(
            "sales/order/view",
            array(
                'order_id' => $order->getId(),
                '_secure'  => Mage::getStoreConfig('web/secure/use_in_frontend')
            )
        );
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return string
     */
    public function getOrderStatus($order)
    {
        $state = $order->getState();
        $map   = array(
            'new'             => 'Processing',
            'pending_payment' => 'ProblemWithOrder',
            'processing'      => 'Processing',
            'complete'        => 'Delivered',
            'closed'          => 'Cancelled',
            'cancelled'       => 'Cancelled',
            'holded'          => 'ProblemWithOrder',
        );

        return 'http://schema.org/OrderStatus/' . $map[$state];
    }

    public function getViewShipmentUrl($order)
    {
        return Mage::getUrl(
            "sales/order/shipment",
            array(
                'order_id' => $order->getId(),
                '_secure'  => Mage::getStoreConfig('web/secure/use_in_frontend')
            )
        );
    }

    public function getViewCreditmemoUrl($order)
    {
        return Mage::getUrl(
            "sales/order/creditmemo",
            array(
                'order_id' => $order->getId(),
                '_secure'  => Mage::getStoreConfig('web/secure/use_in_frontend')
            )
        );
    }

    public function generateOrderArray(Mage_Sales_Model_Order $order)
    {
        $array = array(
            '@context'      => 'http://schema.org',
            '@type'         => 'Order',
            'merchant'      => array(
                '@type' => 'Organization',
                'name'  => $order->getStore()->getFrontendName(),
            ),
            'orderNumber'   => $order->getIncrementId(),
            'priceCurrency' => $order->getOrderCurrency()->toString(),
            'price'         => (string) number_format($order->getBaseTotalDue(), 2),
            'acceptedOffer' => $this->generateAcceptedOfferArray($order),
        );

        if (!$order->getCustomerIsGuest()) {
            $array['url'] = $this->getViewOrderUrl($order);
        }

        return $array;
    }

    public function generateAcceptedOfferArray(Mage_Sales_Model_Order $order)
    {
        $array = array();
        foreach ($order->getAllItems() as $item) {
            $_offer = array(
                '@type'            => 'Offer',
                'itemOffered'      => array(
                    '@type' => 'Product',
                    'name'  => $item->getName(),
                    'sku'   => $item->getSku(),
                    'image' => $item->getImageUrl(),
                ),
                'price'            => (string) number_format($item->getPrice(), 2),
                'priceCurrency'    => $order->getBaseCurrency()->getCurrencyCode(),
                'eligibleQuantity' => array(
                    '@type' => 'QuantitativeValue',
                    'value' => $item->getQtyOrdered(),
                ),
            );

            $array[] = $_offer;
        }

        return $array;
    }

    public function generateShipmentArray(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $address         = $shipment->getShippingAddress();
        $trackingMethods = $shipment->getAllTracks();
        $primaryTrack    = count($trackingMethods) ? $trackingMethods[0] : false;

        $array = array(
            '@context'             => 'http://schema.org',
            '@type'                => 'ParcelDelivery',
            'deliveryAddress'      => array(
                '@type'           => 'PostalAddress',
                'streetAddress'   => $address->getStreetFull(),
                'addressLocality' => $address->getCity(),
                'addressRegion'   => $address->getRegionCode(),
                'addressCountry'  => $address->getCountry(),
                'postalCode'      => $address->getPostcode(),
            ),
            'partOfOrder'          => $this->generateOrderArray($shipment->getOrder()),
            "expectedArrivalUntil" => date("c", time() + 60 * 60 * 24 * 365.25),
        );

        if (count($trackingMethods)) {
            $array['carrier']        = $primaryTrack->getTitle();
            $array['trackingNumber'] = $primaryTrack->getNumber();
            $array['trackingUrl']    = $this->helper('shipping')->getTrackingPopUpUrlByTrackId($primaryTrack->getId());
        }

        return $array;
    }

    public function generateCreditMemoArray(Mage_Sales_Model_Order $order)
    {
        $store = $order->getStore();

        return array(
            '@context'    => 'http://schema.org',
            '@type'       => 'EmailMessage',
            'description' => Mage::getStoreConfig("meanbee_gmailactions_options/creditmemo_email/description", $store),
            'action'      => array(
                '@type' => 'ViewAction',
                'url'   => $this->getViewCreditmemoUrl($order),
                'name'  => Mage::getStoreConfig('meanbee_gmailactions_options/creditmemo_email/action_name', $store)
            )
        );
    }
}
