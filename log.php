<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
use Bitrix\Main\UserTable;

\Bitrix\Main\Loader::includeModule('intranet');

$departmentId = 19; // ID подраздела из инфоблока "departments"

$users = UserTable::getList([
    'select' => ['ID', 'NAME', 'LAST_NAME', 'UF_DEPARTMENT'],
    'filter' => [
        'UF_DEPARTMENT' => $departmentId,
        'ACTIVE' => 'Y'
    ]
])->fetchAll();

echo '<pre>' . print_r($users, 1) . '</pre>';