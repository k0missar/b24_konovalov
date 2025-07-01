<?php
$eventManager = \Bitrix\Main\EventManager::getInstance();

if (file_exists(__DIR__ . "/src/autoloader.php")) {
    require_once __DIR__ . "/src/autoloader.php";
}

$eventManager->addEventHandlerCompatible('rest', 'OnRestServiceBuildDescription', ['\Otus\Rest\CustomRest',
    'OnRestServiceBuildDescriptionHandler']);
