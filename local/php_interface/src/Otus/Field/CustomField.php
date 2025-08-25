<?php

namespace Otus\Field;

use Bitrix\Iblock;
use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Main\Context;

class CustomField
{
    public static function GetUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => 'otus_custom_field',
            'USER_TYPE' => 'otus_custom_field',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => '__otus: Список процедур для записи',
            'PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_STRING,
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],

            'GetPublicViewHTML'        => [__CLASS__, 'GetPublicViewHTML'],
        ];
    }

    public static function getProcedure($elementID)
    {
        $arResult = [];

        $resResult = ElementDoctorsTable::GetList([
            'select' => [
                'PROC_' => 'PROTSEDURA.ELEMENT'
            ],
            'filter' => ['=ID' => $elementID, 'IBLOCK_ID' => 17],
        ])->FetchAll();

        foreach ($resResult as $procedure) {
            $item = [];
            $item['PROCEDURE_ID'] = $procedure['PROC_ID'];
            $item['PROCEDURE_NAME'] = $procedure['PROC_NAME'];
            $arResult[] = $item;
        }

        return $arResult;
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {
        $request = Context::getCurrent()->getRequest();
        $arGet = $request->getQueryList()->toArray();

        $arProcedure = self::getProcedure($arGet['ID']);

        $html = '<div>Список процедур для записи клиентом:</div>';
        foreach ($arProcedure as $item) {
            $html .= '<div>- ' . htmlspecialcharsbx($item['PROCEDURE_NAME']) . '</div>';
        }

        return $html;
    }

    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        $arProcedure = self::getProcedure($value['ELEMENT_ID']);

        $html = '';
        foreach ($arProcedure as $name) {
            $html .= '<a href="javascript:void(0);" 
            class="otus-procedure" 
            data-id="'.$name['PROCEDURE_ID'].'"
            data-doctor="'.$value['ELEMENT_ID'].'">'
                . htmlspecialcharsbx($name['PROCEDURE_NAME'])
                . '</a><br>';
        }
        return $html;
    }

    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return self::GetAdminListViewHTML($arProperty, $value, $strHTMLControlName);
    }
}