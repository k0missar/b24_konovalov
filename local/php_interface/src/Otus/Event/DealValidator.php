<?php
namespace Otus\Event;

use Bitrix\Main\Loader;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loader::includeModule('crm');

class DealValidator
{
    /**
     * Метод проверяет существует ли сделка с указанным автомобилем в активной стадии.
     * Если да, то запрещает создание новой сделки с оповещением, если активных сделок с автомобилем 0, то создает
     *
     * @param array $arFields - массив будущего элемента переданный событием
     *
     * @return bool - true - разрешает создание элемента, false - запрещает
     */
    public static function checkSmartProcess(array &$arFields): bool
    {
        $factory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

        $count = $factory->getItemsCount([
            'CATEGORY_ID' => $arFields['CATEGORY_ID'] ?: 1,
            'UF_CRM_1754162306' => $arFields['UF_CRM_1754162306'],
            '!CLOSED' => 'Y',
        ]);

        if ($count > 0) {
            $deal = self::getActivDeal($arFields);
            $arFields['RESULT_MESSAGE'] = Loc::getMessage("OTUS_EVENT_ACTIVE_ORDER_EXISTS") . " - {$deal[0]['TITLE']}";
            return false; // отмена создания сделки
        }

        return true; // разрешаем создание
    }

    /**
     * Метод находит сделку в системе
     *
     * @param array $data - массив элемента сделки со всеми полями (стандартные поля системы)
     *
     * @return array возвращает ID и название сделки
     */
    public static function getActivDeal(array $data): array
    {
        $arResult = DealTable::getList([
            'select' => ['ID', 'TITLE'],
            'filter' => [
                '=CATEGORY_ID' => $data['CATEGORY_ID'],
                '=UF_CRM_1754162306' => $data['UF_CRM_1754162306'],
                '!=CLOSED' => 'Y',
            ],
            'limit' => 1,
        ])->fetchAll();

        return $arResult;
    }
}
