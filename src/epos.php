<?php

use esas\cmsgate\CmsConnectorVirtuemart;
use esas\cmsgate\epos\controllers\ControllerEposInvoiceAdd;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshAddBill;
use esas\cmsgate\hutkigrosh\controllers\ControllerHutkigroshCompletionPage;
use esas\cmsgate\hutkigrosh\utils\RequestParamsHutkigrosh;
use esas\cmsgate\Registry;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

if (!class_exists('vmPSPlugin')) {
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}
require_once(dirname(__FILE__) . '/init.php');

class plgVMPaymentEpos extends vmPSPlugin
{
    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->_tablepkey = 'id';
        $this->_tableId = 'id';
        $varsToPush = $this->getVarsToPush();
        $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
    }

    //+
    function getTableSQLFields()
    {
        $SQLfields = array(
            'id' => 'int(1) UNSIGNED NOT NULL AUTO_INCREMENT', //к сожалению, обязательное поле virtuemart/administrator/components/com_virtuemart/plugins/vmplugin.php:488
            'virtuemart_order_id' => 'int(1) UNSIGNED',
            'ext_trx_id' => 'char(64)'
        );
        return $SQLfields;
    }


    function plgVmConfirmedOrder($cart, $order)
    {
        $orderWrapper = Registry::getRegistry()->getOrderWrapper($order['details']['BT']->virtuemart_order_id);
        $controller = new ControllerEposInvoiceAdd();
        $resp = $controller->process($orderWrapper);
        if ($resp->hasError())
            return false;

        if (!class_exists('VirtueMartCart'))
            require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
        $cart = VirtueMartCart::getCart();
        $cart->emptyCart();
        /**
         * На этом этапе мы только выполняем запрос к HG для добавления счета. Мы не показываем итоговый экран
         * (с кнопками webpay), а выполняем автоматический редирект на step7
         **/
        $redirectParams = array(
            RequestParamsEpos::INVOICE_ID => $resp->getInvoiceId(),
            RequestParamsEpos::ORDER_NUMBER => $orderWrapper->getOrderNumber());
        Factory::getApplication()->redirect(CmsConnectorVirtuemart::generatePaySystemControllerPath("complete") . '&' . http_build_query($redirectParams));
    }

    function checkConditions($cart, $method, $cart_prices)
    {
        //todo check configuration
        return true;
    }

    //+
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id)
    {
        return $this->onStoreInstallPluginTable($jplugin_id);
    }

    //+
    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart, &$msg)
    {
        return $this->OnSelectCheck($cart);
    }

    //+
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn)
    {
        //todo добавить надпись про sandbox
        return $this->displayListFE($cart, $selected, $htmlIn);
    }

    //+
    public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name)
    {
        return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId)
    {
        if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
            return NULL;
        }
        if (!$this->selectedThisElement($method->payment_element)) {
            return FALSE;
        }
        $this->getPaymentCurrency($method);

        $paymentCurrencyId = $method->payment_currency;
        return;
    }

    //+
    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array(), &$paymentCounter)
    {
        return $this->onCheckAutomaticSelected($cart, $cart_prices, $paymentCounter);
    }

    //+
    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name)
    {
        $this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    //+
    public function plgVmOnCheckoutCheckDataPayment(VirtueMartCart $cart)
    {
        return null;
    }

    function plgVmonShowOrderPrintPayment($order_number, $method_id)
    {
        return $this->onShowOrderPrint($order_number, $method_id);
    }

    //+
    function plgVmDeclarePluginParamsPaymentVM3(&$data)
    {
        return $this->declarePluginParams('payment', $data);
    }

    //*
    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table)
    {
        return $this->setOnTablePluginParams($name, $id, $table);
    }
}
