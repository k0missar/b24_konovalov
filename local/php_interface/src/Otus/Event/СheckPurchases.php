<?php

namespace Otus\Event;

use Bitrix\Main\Localization\Loc;

use Otus\Common\SendMessage;

Loc::loadMessages(__FILE__);

class СheckPurchases
{
    const API_URL = 'https://cc40595.tw1.ru/rest/1';
    const API_KEY = '0yieximnkwract9f';
    const API_METHOD = 'im.message.add.json';

    /**
     * Метод отправляет сообщение ответсвенному о том что на него создана заявка на закупку
     *
     * @param object $data - объект события
     * @return void
     */
    public static function check(object $data): void
    {
        $smartID = $data->getParameters()["item"]->getEntityTypeId();

        if ((int)$smartID === 1042) {
            $dataItem = $data->getParameters();
            $item = $dataItem['item'];
            $arResult = $item->getData();

            $message = Loc::getMessage("OTUS_EVENT_NEW_PURCHASE_REQUEST") . " [URL=/crm/type/1042/details/{$arResult['ID']}/]{$arResult['TITLE']}[/URL]";
            SendMessage::init(
                (int)$arResult['ASSIGNED_BY_ID'],
                $message,
                [
                    'URL' => self::API_URL,
                    'METHOD' => self::API_METHOD,
                    'APIKEY' => self::API_KEY
                ]
            );

            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/_LOG/check-purchases.log', print_r($arResult, true) . "\n ======================================================================== \n", FILE_APPEND);
        }

    }
}