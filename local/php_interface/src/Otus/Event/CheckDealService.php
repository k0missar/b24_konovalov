<?php

namespace Otus\Event;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

use Otus\Common\SendMessage;

Loc::loadMessages(__FILE__);
Loader::includeModule('crm');
class CheckDealService
{
    const API_URL = 'https://cc40595.tw1.ru/rest/1';
    const API_KEY = '0yieximnkwract9f';
    const API_METHOD = 'im.message.add.json';

    private static $dealData = [];

    public static function runCheckDealService($arFields)
    {
        self::$dealData = $arFields;

        $message = Loc::getMessage("OTUS_EVENT_NEW_PURCHASE_REQUEST") . " [URL=/crm/deal/details/" . self::$dealData['ID'] . "/]" . self::$dealData['TITLE'] . "[/URL]";

        SendMessage::init(self::$dealData['ASSIGNED_BY_ID'],
            $message,
            [
            'URL' => self::API_URL,
            'METHOD' => self::API_METHOD,
            'APIKEY' => self::API_KEY
        ]);
    }
}

