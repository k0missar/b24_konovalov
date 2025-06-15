<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Currency\CurrencyTable;

Loc::loadMessages(__FILE__);
Loader::includeModule('currency');

$listCurrency = [];

$resCurrency = CurrencyTable::getList([
    'select' => ['CURRENCY']
]);

while ($currency = $resCurrency->fetch()) {
    if ($currency['CURRENCY'] === 'RUB') continue;
    $listCurrency[$currency['CURRENCY']] = $currency['CURRENCY'];
}

$arComponentParameters = [
    'PARAMETERS' => [
        'CURRENCY' => [
            'PARENT'  => 'BASE',
            'NAME'    => Loc::getMessage('LIST_CURRENCY_SHOW'),
            'TYPE'    => 'LIST',
            'VALUES'  => $listCurrency,
            'DEFAULT' => 'USD',
        ],
    ],
];

