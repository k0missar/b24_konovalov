<?php
namespace Otus\Event;

use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementLessionEventTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Binding\DealContactTable;

Loader::includeModule('iblock');
Loader::includeModule('crm');

class UpdateIBlock
{
    const IBLOCK_ID = 21;
    public static function runUpdate($arFields)
    {
        if ((int)$arFields['IBLOCK_ID'] === self::IBLOCK_ID) {

            $arElement = self::getElementByID($arFields['ID']);

            $arParams = [
                'TITLE' => $arElement['NAME'],
                'OPPORTUNITY' => $arElement['SUMMA_VALUE'],
                'ASSIGNED_BY_ID' => $arElement['OTVETSTVENNYY_VALUE'],
                'CONTACT_ID' => $arElement['KLIENT_VALUE'],
                'ID' => $arElement['SDELKA_VALUE'],
            ];

            $result = self::setPropertyDeal($arParams);

            if ($result) {
                DealContactTable::unbindAllContacts($arElement['SDELKA_VALUE']);
                DealContactTable::bindContactIDs($arElement['SDELKA_VALUE'], [$arElement['KLIENT_VALUE']]);
            }

            file_put_contents($_SERVER['DOCUMENT_ROOT'].'/_LOG/UpdateIBlock.log', print_r($arElement, true));
        }
    }

    public static function getElementByID($id)
    {
        $arResult = ElementLessionEventTable::GetList([
            'select' => [
                'ID',
                'NAME',
                'SUMMA_' => 'SUMMA',
                'KLIENT_' => 'KLIENT',
                'OTVETSTVENNYY_' => 'OTVETSTVENNYY',
                'SDELKA_' => 'SDELKA',
            ],
            'filter' => ['ID' => $id]
        ])->Fetch();

        return $arResult;
    }

    public static function setPropertyDeal($arParams)
    {
        $result = DealTable::update($arParams['ID'], [
            'TITLE' => $arParams['TITLE'],
            'OPPORTUNITY' => $arParams['OPPORTUNITY'],
            'ASSIGNED_BY_ID' => $arParams['ASSIGNED_BY_ID'],
            'CONTACT_ID' => $arParams['CONTACT_ID'],
        ]);
        
        if ($result->isSuccess()) {
            return true;
        } else {
            return false;
        }
    }
}
