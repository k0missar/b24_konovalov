<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
use Bitrix\Main\Diag\Debug;
use Otus\Diag\FileExceptionHandlesrLogCustom;

$arLog = [
    'Тест' => 'Тестовое сообщение'
];

//Debug::writeToFile($arLog);

//Debug::dumpToFile($arLog);

//Debug::startTimeLabel('testTime');
//for ($i = 0; $i < 1000; $i++) {
//
//}
//Debug::endTimeLabel('testTime');
//echo print_r(Debug::getTimeLabels('testTime'), 1);


FileExceptionHandlesrLogCustom::hw();

echo $var;