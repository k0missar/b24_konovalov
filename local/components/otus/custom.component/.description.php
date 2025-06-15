<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = [
    'NAME'        => Loc::getMessage('MYCOMPONENT_NAME'),
    'DESCRIPTION' => Loc::getMessage('MYCOMPONENT_DESCRIPTION'),
    'PATH'        => [
        'ID'   => 'mycompany',
        'NAME' => Loc::getMessage('MYCOMPONENT_NAMESPACE'),
    ],
];