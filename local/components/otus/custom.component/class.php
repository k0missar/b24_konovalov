<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Currency\CurrencyTable;

Loc::loadMessages(__FILE__);

Loader::includeModule('currency');

class CustomComponent extends \CBitrixComponent
{
    public function getCurrencyRate($currency)
    {
        $arResult = null;

        $resCurrency = CurrencyTable::getList([
            'select' => ['CURRENCY', 'AMOUNT'],
            'filter' => ['=CURRENCY' => $currency]
        ]);

        while ($elCurrency = $resCurrency->fetch()) {
            $arResult = $elCurrency;
        }

        return $arResult;
    }
    public function onPrepareComponentParams($arParams)
    {
        // Установим значение по умолчанию, если параметр не передан
        if (empty($arParams['CURRENCY']))
        {
            $arParams['CURRENCY'] = 'USD';
        }

        return $arParams;
    }
    public function executeComponent()
    {
        $this->arResult = $this->getCurrencyRate($this->arParams['CURRENCY']);

        $this->includeComponentTemplate();
    }
}
