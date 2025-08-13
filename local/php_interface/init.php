<?php
use Bitrix\Main\Loader;

Loader::includeModule('ok.crmtab');
Loader::includeModule('crm');

$eventManager = \Bitrix\Main\EventManager::getInstance();

if (file_exists(__DIR__ . "/src/autoloader.php")) {
    require_once __DIR__ . "/src/autoloader.php";
}

$eventManager->addEventHandlerCompatible('rest', 'OnRestServiceBuildDescription', ['\Otus\Rest\CustomRest',
    'OnRestServiceBuildDescriptionHandler']);

$eventManager->addEventHandler(
    'crm', 'onEntityDetailsTabsInitialized', ['Otus\CustomTabs\TabsGarage', 'updateTabs']
);

// Проверка пред добавлением сделки на ремонт
$eventManager->addEventHandler(
    'crm', 'OnBeforeCrmDealAdd', ['Otus\Event\DealValidator', 'checkSmartProcess']
);

// Событие после добавления сервисной сделки
$eventManager->addEventHandler(
    'crm', 'OnAfterCrmDealAdd', ['Otus\Event\CheckDealService', 'runCheckDealService']
);

$eventManager->addEventHandler(
    'crm', 'onCrmDynamicItemAdd', ['Otus\Event\СheckPurchases', 'check']
);

// Событие обновления товаров после успешного завершения самат-процесса закупок
$eventManager->addEventHandler(
    'crm', 'onCrmDynamicItemUpdate', ['Otus\Event\UpdateProduct', 'runUpdateProduct']
);