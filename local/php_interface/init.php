<?php
use Bitrix\Main\Loader;

Loader::includeModule('ok.crmtab');

$eventManager = \Bitrix\Main\EventManager::getInstance();

if (file_exists(__DIR__ . "/src/autoloader.php")) {
    require_once __DIR__ . "/src/autoloader.php";
}

$eventManager->addEventHandlerCompatible('rest', 'OnRestServiceBuildDescription', ['\Otus\Rest\CustomRest',
    'OnRestServiceBuildDescriptionHandler']);

//$eventManager->addEventHandler(
//    'crm', 'onEntityDetailsTabsInitialized', ['Ok\Crmtab\Handlers', 'updateTabs']
//);
