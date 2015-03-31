<?php

class Meanbee_Gmailactions_Block_Creditmemo extends Meanbee_Gmailactions_Block_Gmailactions
{
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(
            'meanbee_gmailactions_options/creditmemo_email/enabled',
            $this->getOrder()->getStore()
        );
    }

    public function getJsonArray()
    {
        return $this->generateCreditMemoArray($this->getOrder());
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
}
