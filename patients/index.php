<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

//use Otus\List\DoctorsTable;
//use Otus\List\ProcedureTable;
//use Otus\List\DoctorsProperyTable;
use Bitrix\Iblock\Elements\ElementPatientsTable;
use Bitrix\Main\Context;

$resPatients = ElementPatientsTable::getList([
    'select' => [
        'ID',
        'NAME',
        'TELEFON.VALUE',
        'ADRES.VALUE'],
    'filter' => ['IBLOCK_ID' => 18]
])->fetchAll();

$list = [];
foreach ($resPatients as $item) {
    $patient['data']['ID'] = $item['ID'];
    $patient['data']['NAME'] = $item['NAME'];
    $patient['data']['PHONE'] = $item['IBLOCK_ELEMENTS_ELEMENT_PATIENTS_TELEFON_VALUE'];
    $patient['data']['ADR'] = $item['IBLOCK_ELEMENTS_ELEMENT_PATIENTS_ADRES_VALUE'];
    $list[] = $patient;
}

$APPLICATION->SetPageProperty('title', 'Список пациентов');
$APPLICATION->SetTitle('Список пациентов');
?>

<?php
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'patient_list',
    'COLUMNS' => [
        ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'NAME', 'name' => 'ФМО пациента', 'sort' => 'NAME', 'default' => true],
        ['id' => 'PHONE', 'name' => 'Телефон', 'sort' => 'PHONE', 'default' => true],
        ['id' => 'ADR', 'name' => 'Адрес', 'sort' => 'ADR', 'default' => true],
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
