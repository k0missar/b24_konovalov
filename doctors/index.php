<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Otus\List\DoctorsTable;

$arDoctors = DoctorsTable::query()
    ->setSelect([
        'IBLOCK_ELEMENT_ID',
        'ELEMENT.NAME',
        'ELEMENT.CODE',
    ])
    ->fetchAll();

$list = [];
foreach ($arDoctors as $doctor) {
    $item['data']['ID'] = $doctor['IBLOCK_ELEMENT_ID'];
    $item['data']['NAME'] = '<a href="/doctors/' . $doctor['OTUS_LIST_DOCTORS_ELEMENT_CODE'] . '/">' . $doctor['OTUS_LIST_DOCTORS_ELEMENT_NAME'] . '</a>';
    $item['data']['CODE'] = $doctor['OTUS_LIST_DOCTORS_ELEMENT_CODE'];
    $list[] = $item;
}
$APPLICATION->SetPageProperty('title', 'Список врачей');
$APPLICATION->SetTitle('Список врачей');
?>

<?php
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '', [
    'GRID_ID' => 'doctor_list',
    'COLUMNS' => [
        ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'NAME', 'name' => 'ФИО врача', 'sort' => 'NAME', 'default' => true],
        ['id' => 'CODE', 'name' => 'Символьный код', 'sort' => 'CODE', 'default' => true],
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


<?php
$list2 = [
    [
        'data' => ['ID' => '14',
        'TITLE' => 'Ремонт Hyundai Tucson',
        'OPPORTUNITY' => '750.00',
        'DATE_CREATE' => '03-08-2025',
        'MODEL' => 'Hyundai',
        'MARKA' => 'Tucson',
        'VIN' => 'KM8J3CA46MU654321',]
    ],
    [
        'data' => ['ID' => '15',
        'TITLE' => 'Ремонт Kio Rio',
        'OPPORTUNITY' => '990.00',
        'DATE_CREATE' => '03-08-2025',
        'MODEL' => 'Kia Rio',
        'MARKA' => 'X-Line',
        'VIN' => 'Z94C251BBLR345678',]
    ]
];
?>

<?php
$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => 'garage_deals_grid_213123213123123',
        'COLUMNS' => [
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'TITLE', 'name' => 'Название сделки', 'sort' => 'TITLE', 'default' => true],
            ['id' => 'MODEL', 'name' => 'Модель', 'sort' => 'MODEL', 'default' => true],
            ['id' => 'MARKA', 'name' => 'Марка', 'sort' => 'MARKA', 'default' => true],
            ['id' => 'VIN', 'name' => 'VIN', 'sort' => 'VIN', 'default' => true],
            ['id' => 'DATE_CREATE', 'name' => 'Дата сделки', 'sort' => 'DATE_CREATE', 'default' => true],
            ['id' => 'OPPORTUNITY', 'name' => 'Стоимость сделки', 'sort' => 'OPPORTUNITY', 'default' => true],
        ],
        'ROWS' => $list2, //Самое интересное, опишем ниже
        'AJAX_MODE'           => 'Y',
        'AJAX_OPTION_JUMP'    => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
    ]);
?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
