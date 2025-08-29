<?php

namespace Otus\Event;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementLessionEventTable;

Loader::includeModule('crm');
Loader::includeModule('iblock');

class UpdateDeal
{
    const CATEGORY_ID = 0;
    const IBLOCK_ID = 21;
    public static function runUpdate($arFields)
    {
        if ((int)$arFields['CATEGORY_ID'] === self::CATEGORY_ID) {
            $dataDeal = self::getDealById($arFields['ID']);

            $arParams = [
                'NAME' => $dataDeal['TITLE'],

                'OPPORTUNITY' =>   $dataDeal['OPPORTUNITY'],
                'ASSIGNED_BY_ID' =>    $dataDeal['ASSIGNED_BY_ID'],
                'CONTACT_ID' =>    $dataDeal['CONTACT_ID'],

            ];

            $result = self::updateElement($dataDeal['UF_CRM_1756123039'], $arParams);

            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_LOG/UpdateDeal.log', print_r($result, true));
        }
    }

    public static function getDealById($id)
    {
        $arResult = DealTable::GetList([
            'select' => ['ID', 'ASSIGNED_BY_ID', 'TITLE', 'CATEGORY_ID', 'OPPORTUNITY', 'CONTACT_ID', 'UF_CRM_1756123039'],
            'filter' => ['ID' => $id],
        ])->Fetch();

        return $arResult;
    }

    public static function updateElement($id, $arParams)
    {
        try {
            $result = ElementLessionEventTable::update($id, [
                'NAME' => $arParams['NAME']
            ]);

            if (!$result->isSuccess()) {
                return $result->getErrorMessages();
            }

            \CIBlockElement::SetPropertyValuesEx(
                $id,
                self::IBLOCK_ID,
                [
                    'SUMMA' => $arParams['OPPORTUNITY'],
                    'OTVETSTVENNYY' => $arParams['ASSIGNED_BY_ID'],
                    'KLIENT' => $arParams['CONTACT_ID'],
                ]
            );

            return true;
        } catch (\Throwable $exception) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_LOG/ERROR.log', print_r($exception->getMessage(), true));
        }
    }
}