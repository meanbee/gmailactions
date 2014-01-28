<?php

class Meanbee_Gmailactions_Block_Gmailactions extends Mage_Core_Block_Template {

    public function getViewOrderUrl($order) {
        return Mage::getUrl("sales/order/view",array('order_id' => $order->getId(), '_secure' => true));
    }
    public function getViewShipmentUrl($order) {
        return Mage::getUrl("sales/order/shipment",array('order_id' => $order->getId(), '_secure' => true));
    }
    public function getViewCreditmemoUrl($order) {
        return Mage::getUrl("sales/order/creditmemo",array('order_id' => $order->getId(), '_secure' => true));
    }
} 