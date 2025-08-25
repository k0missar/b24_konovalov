<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Iblock\Elements\ElementDoctorsTable;
use Bitrix\Main\Context;

class DoctorProcedureComponent extends CBitrixComponent
{
    public function getProcedures($doctorId)
    {
        $arResult = [];

        $resResult = ElementDoctorsTable::GetList([
            'select' => ['PROC_' => 'PROTSEDURA.ELEMENT'],
            'filter' => ['=ID' => $doctorId, 'IBLOCK_ID' => 17],
        ])->FetchAll();

        foreach ($resResult as $procedure) {
            $arResult[] = [
                'ID' => $procedure['PROC_ID'],
                'NAME' => $procedure['PROC_NAME']
            ];
        }

        return $arResult;
    }

    public function executeComponent()
    {
        $doctorId = intval($this->arParams['DOCTOR_ID']);
        $this->arResult['PROCEDURES'] = $this->getProcedures($doctorId);
        $this->includeComponentTemplate();
    }
}
