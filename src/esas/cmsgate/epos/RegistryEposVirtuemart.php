<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 15.07.2019
 * Time: 11:22
 */

namespace esas\cmsgate\epos;

use esas\cmsgate\CmsConnectorVirtuemart;
use esas\cmsgate\descriptors\ModuleDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposVirtuemart;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormVirtuemart;

class RegistryEposVirtuemart extends RegistryEpos
{
    public function __construct()
    {
        $this->cmsConnector = new CmsConnectorVirtuemart();
        $this->paysystemConnector = new PaysystemConnectorEpos();
    }

    /**
     * Переопделение для упрощения типизации
     * @return RegistryEposVirtuemart
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * Переопделение для упрощения типизации
     * @return ConfigFormVirtuemart
     */
    public function getConfigForm()
    {
        return parent::getConfigForm();
    }

    /**
     * @return CmsConnectorVirtuemart
     */
    public function getCmsConnector()
    {
        return parent::getCmsConnector();
    }


    public function createConfigForm()
    {
        $managedFields = $this->getManagedFieldsFactory()->getManagedFieldsExcept(AdminViewFields::CONFIG_FORM_COMMON,
            [
                ConfigFieldsEpos::shopName(),
                ConfigFieldsEpos::paymentMethodName(),
                ConfigFieldsEpos::paymentMethodDetails(),
                ConfigFieldsEpos::paymentMethodNameWebpay(),
                ConfigFieldsEpos::paymentMethodDetailsWebpay(),
                ConfigFieldsEpos::useOrderNumber(),
            ]);
        $configForm = new ConfigFormVirtuemart(
            AdminViewFields::CONFIG_FORM_COMMON,
            $managedFields);
        return $configForm;
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelEposVirtuemart($orderWrapper);
    }

    function getUrlWebpay($orderWrapper)
    {
        return CmsConnectorVirtuemart::generatePaySystemControllerUrl("complete") .
            "&" . RequestParamsEpos::ORDER_NUMBER . "=" . $orderWrapper->getOrderNumber() .
            "&" . RequestParamsEpos::INVOICE_ID . "=" . $orderWrapper->getExtId();
    }

    public function createModuleDescriptor()
    {
        return new ModuleDescriptor(
            "epos",
            new VersionDescriptor("1.13.0", "2020-12-29"),
            "Прием платежей через ЕРИП (сервис E-POS)",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-virtuemart-epos/browse",
            VendorDescriptor::esas(),
            "Выставление пользовательских счетов в ЕРИП"
        );
    }
}