<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Iblock\Elements\ElementBookingTable;
use Bitrix\Main\Loader;

Loader::includeModule('iblock');

if(!check_bitrix_sessid() || $_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['status'=>'error', 'message'=>'Invalid request']));
}

$request = Context::getCurrent()->getRequest();
$doctorId = intval($request->getPost('doctorId'));
$procedureId = intval($request->getPost('procedureId'));
$datetime = $request->getPost('datetime');
$fio = $request->getPost('fio');

try {
    $el = new \CIBlockElement;
    $arFields = [
        "IBLOCK_ID" => 19, // ID инфоблока "booking"
        "NAME"      => "Запись на процедуру " . $procedureId,
        "ACTIVE"    => "Y",
        "PROPERTY_VALUES" => [
            "DOKTOR"       => $doctorId,
            "PROTSEDURA"   => $procedureId,
            "CLIENT"       => $fio,
            "VREMYA_ZAPISI"=> $datetime,
        ],
    ];
    if ($id = $el->Add($arFields)) {
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/_LOG/SUC_procedure.log", "[".date("Y-m-d H:i:s")."] Добавлен ID=$id\n", FILE_APPEND);
    } else {
        file_put_contents($_SERVER['DOCUMENT_ROOT']."/_LOG/ERROR_procedure.log", "[".date("Y-m-d H:i:s")."] Ошибка: ".$el->LAST_ERROR."\n", FILE_APPEND);
    }
} catch (\Throwable $exception) {
    $logFile = $_SERVER['DOCUMENT_ROOT'].'/_LOG/ERROR_procedure.log';
    file_put_contents($logFile, $exception->getMessage(), FILE_APPEND, );
}

echo json_encode(['status'=>'ok']);


