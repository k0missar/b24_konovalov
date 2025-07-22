<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Header('Content-Type: text/html; charset=' . LANG_CHARSET);

Loader::includeModule('crm');

$request = Application::getInstance()->getContext()->getRequest();

$componentData = $request->get('PARAMS');

if(is_array($componentData)){
    $componentParams = isset($componentData['params']) && is_array($componentData['params']) ? $componentData['params'] : [];
}

$server = $request->getServer();

$ajaxLoaderParams = [
    'url' => $server->get('REQUEST_URI'),
    'method' => 'POST',
    'dataType' => 'ajax',
    'data' => [
        'PARAMS' => $componentData,
    ]
];

$componentParams['AJAX_LOADER'] = $ajaxLoaderParams;

$APPLICATION->IncludeComponent(
    'bitrix:ui.sidepanel.wrapper',
    '',
    [
        'PLAIN_VIEW' => false,
        'USE_PADDING' => true,
        'POPUP_COMPONENT_NAME' => 'ok.crmtab:crm.tab',
        'POPUP_COMPONENT_TEMPLATE_NAME' => $componentData['template'] ?? '',
        'POPUP_COMPONENT_PARAMS' => $componentParams,
    ],
);

\CMain::FinalActions();