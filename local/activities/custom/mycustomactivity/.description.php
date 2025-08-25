<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

$arActivityDescription = [
    "NAME" => Loc::getMessage("CA_DESCR_NAME"),
    "DESCRIPTION" => Loc::getMessage("CA_DESCR_DESCR"),
    "TYPE" => "activity",
    "CLASS" => "MyCustomActivity",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => [
        "ID" => "other",
    ],
    "RETURN" => [
        "Text" => [
            "NAME" => Loc::getMessage("CA_DESCR_FIELD_TEXT"),
            "TYPE" => "string",
        ],
    ],
];
