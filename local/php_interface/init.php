<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;

Loader::includeModule('ok.crmtab');

$eventManager = \Bitrix\Main\EventManager::getInstance();

if (file_exists(__DIR__ . "/src/autoloader.php")) {
    require_once __DIR__ . "/src/autoloader.php";
}

$eventManager->addEventHandlerCompatible('rest', 'OnRestServiceBuildDescription', ['\Otus\Rest\CustomRest',
    'OnRestServiceBuildDescriptionHandler']);

$eventManager->addEventHandler(
    'iblock', 'OnIBlockPropertyBuildList', ['Otus\Field\CustomField', 'GetUserTypeDescription']
);

AddEventHandler("main", "OnProlog", function() {
    Asset::getInstance()->addJs("/local/js/otus/field/script.js");
    //Asset::getInstance()->addJs("/local/js/otus/procedure.js");
});