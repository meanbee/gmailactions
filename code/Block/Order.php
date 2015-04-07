<?php

class Meanbee_Gmailactions_Block_Order extends Meanbee_Gmailactions_Block_Gmailactions
{
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(
            'meanbee_gmailactions_options/order_email/enabled',
            $this->getOrder()->getStore()
        );
    }

    public function getJsonArray()
    {
        return $this->generateOrderArray($this->getOrder());
    }
}
