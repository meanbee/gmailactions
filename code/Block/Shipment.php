<?php

class Meanbee_Gmailactions_Block_Shipment extends Meanbee_Gmailactions_Block_Gmailactions
{
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(
            'meanbee_gmailactions_options/shipment_email/enabled',
            $this->getShipment()->getStore()
        );
    }

    public function getJsonArray()
    {
        return $this->generateShipmentArray($this->getShipment());
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
}
