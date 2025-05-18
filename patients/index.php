<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Otus\List\DoctorsTable;
use Otus\List\ProcedureTable;
use Otus\List\DoctorsProperyTable;
use Bitrix\Main\Context;

$requestUri = Context::getCurrent()->getRequest()->getRequestUri();
$pathParts = explode('/', $requestUri);
$doctorCode = $pathParts[2];
$arDoctor = DoctorsTable::query()
    ->registerRuntimeField(
        'PROCEDURE_ID',
        [
            'data_type' => DoctorsProperyTable::class,
            'reference' => ['=this.IBLOCK_ELEMENT_ID' => 'ref.IBLOCK_ELEMENT_ID'],
        ]
    )
    ->registerRuntimeField(
        'PROCEDURE_VALUE',
        [
            'data_type' => ProcedureTable::class,
            'reference' => ['=this.PROCEDURE_ID.VALUE' => 'ref.IBLOCK_ELEMENT_ID'],
        ]
    )
    ->registerRuntimeField(
        'ELEMENT',
        [
            'data_type' => \Bitrix\Iblock\ElementTable::class,
            'reference' => ['=this.IBLOCK_ELEMENT_ID' => 'ref.ID'],
        ]
    )
    ->setFilter([
        '=ELEMENT.CODE' => $doctorCode,
    ])
    ->setSelect([
        'IBLOCK_ELEMENT_ID',
        'ELEMENT.NAME',
        'ELEMENT.CODE',
        'PROCEDURE_VALUE.ELEMENT.NAME',
    ])
    ->fetchAll();

$list = [];
foreach ($arDoctor as $doctor) {
    $item = [];
    $item['data']['ID'] = $doctor['IBLOCK_ELEMENT_ID'];
    $item['data']['PROCEDURE'] = $doctor['OTUS_LIST_DOCTORS_PROCEDURE_VALUE_ELEMENT_NAME'];
    $list[] = $item;
}

$APPLICATION->SetPageProperty('title', $arDoctor[0]['OTUS_LIST_DOCTORS_ELEMENT_NAME']);
$APPLICATION->SetTitle($arDoctor[0]['OTUS_LIST_DOCTORS_ELEMENT_NAME']);
?>

<?php
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'report_list',
    'COLUMNS' => [
        ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'PROCEDURE', 'name' => 'Название процедуры', 'sort' => 'NAME', 'default' => true],
    ],
    'ROWS' => $list, //Самое интересное, опишем ниже
    'SHOW_ROW_CHECKBOXES' => true,
    //'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' => [
        ['NAME' => "5", 'VALUE' => '5'],
        ['NAME' => '10', 'VALUE' => '10'],
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100']
    ],
    'AJAX_OPTION_JUMP'          => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => true,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => true,
    'SHOW_PAGINATION'           => true,
    'SHOW_SELECTED_COUNTER'     => true,
    'SHOW_TOTAL_COUNTER'        => true,
    'SHOW_PAGESIZE'             => true,
    'SHOW_ACTION_PANEL'         => false,
    'ACTION_PANEL'              => [
        'GROUPS' => [
            'TYPE' => [
                'ITEMS' => [
                    [
                        'ID'    => 'set-type',
                        'TYPE'  => 'DROPDOWN',
                    ],
                    [
                        'ID'       => 'edit',
                        'TYPE'     => 'BUTTON',
                        'TEXT'        => 'Редактировать',
                        'CLASS'        => 'icon edit',
                        'ONCHANGE' => ''
                    ],
                    [
                        'ID'       => 'delete',
                        'TYPE'     => 'BUTTON',
                        'TEXT'     => 'Удалить',
                        'CLASS'    => 'icon remove',
                        //'ONCHANGE' => $onchange->toArray()
                    ],
                ],
            ]
        ],
    ],
    'ALLOW_COLUMNS_SORT'        => true,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'N'
]);
?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
