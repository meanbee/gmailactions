<?php

abstract class Meanbee_Gmailactions_Block_Gmailactions extends Mage_Core_Block_Template
{
    abstract function getJsonArray();

    public function isEnabled()
    {
        return false;
    }

    public function _toHtml()
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $json = json_encode($this->getJsonArray());

        return '<script type="application/ld+json">' . $json . '</script>';
    }

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

    public function generateAcceptedOfferArray(Mage_Sales_Model_Order $order)
    {
        $array = array();
        foreach ($order->getAllVisibleItems() as $item) {
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
}
