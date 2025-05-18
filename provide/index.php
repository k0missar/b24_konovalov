<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Otus\List\ProvidedTable;

$resProvide = ProvidedTable::getList([
    'select' => ['ID', 'DESCRIPTION', 'CREATED_POST', 'DOCTOR_NAME.NAME', 'PROCEDURE_NAME.NAME']
])->fetchAll();

$list = [];
foreach ($resProvide as $item) {
    $provide['data']['ID'] = $item['ID'];
    $provide['data']['CREATED_POST'] = $item['CREATED_POST']->format('d.m.Y H:i');
    $provide['data']['DOCTOR_NAME'] = $item['OTUS_LIST_PROVIDED_DOCTOR_NAME_NAME'];
    $provide['data']['PROCEDURE_NAME'] = $item['OTUS_LIST_PROVIDED_PROCEDURE_NAME_NAME'];
    $provide['data']['DESCRIPTION'] = $item['DESCRIPTION'];
    $list[] = $provide;
}

$APPLICATION->SetPageProperty('title', 'Список оказанных услуг');
$APPLICATION->SetTitle('Список оказанных услуг');
?>

<?php
$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'provide_list',
    'COLUMNS' => [
        ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'CREATED_POST', 'name' => 'Дата проведения', 'sort' => 'NAME', 'default' => true],
        ['id' => 'DOCTOR_NAME', 'name' => 'ФИО доктора', 'sort' => 'PHONE', 'default' => true],
        ['id' => 'PROCEDURE_NAME', 'name' => 'Название процедуры', 'sort' => 'ADR', 'default' => true],
        ['id' => 'DESCRIPTION', 'name' => 'Описание оказанной услуги', 'sort' => 'ADR', 'default' => true],
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
