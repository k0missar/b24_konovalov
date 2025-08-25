<?php

namespace Otus\Common;

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
use Bitrix\Catalog\Model\Product;
use Bitrix\Catalog\ProductTable;
use Bitrix\Bizproc\Automation\Factory;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Localization\Loc;

use Otus\Common\SendMessage;
use Otus\Common\UserData;
use Otus\Common\ProductAutoUpdate;

Loc::loadMessages(__FILE__);

Loader::includeModule('crm');
Loader::includeModule('catalog');
Loader::includeModule('iblock');
class СreatePurchaseRequests
{
    const SMART_PROCESS_ID = 1042;
    const HEAD_PROCUREMENT = 18; // Начальник отдела закупок
    const DEPARTAMENT_PROCUREMENT = 19; // Сотрудники отдела закупок
    const API_URL = 'https://cc40595.tw1.ru/rest/1';
    const API_KEY = '0yieximnkwract9f';
    const API_METHOD = 'im.message.add.json';

    /**
     * Получаем торговые предложения с нулевыми остатками
     *
     * @return array массив торговых предложений с нуливыми остатками, содержит ID и Название торгового предложения
     */
    public static function getOffers(): array
    {
        $arResult = [];

        $arResult = ProductTable::getList([
            'select' => [
                'ID',
                'ELEMENT_NAME' => 'ELEMENT.NAME'],
            'filter' => [
                '=QUANTITY' => 0,
                '=TYPE' => 4
            ],
            'runtime' => [
                new Reference(
                    'ELEMENT',
                    ElementTable::class,
                    Join::on('this.ID', 'ref.ID'),
                    ['join_type' => 'INNER']
                )
            ]
        ])->fetchAll();

        return $arResult;
    }

    /**
     * Метод обрабатывает торговые предложения с нулевым остатком.
     * Внутрия себя получает торговые предложения с нулевыми остатками, создает заявки на их закупку, уведомляет ответственных
     *
     * @return void
     */
    public static function runPurchaseRequests(): void
    {
        $productID = self::getOffers();

        if (empty($productID)) {
            return;
        }

        $factory = Container::getInstance()->getFactory(self::SMART_PROCESS_ID);

        $userID = UserData::getUserID(self::DEPARTAMENT_PROCUREMENT);
        if(empty($userID)) {
            $userID = UserData::getUserID(self::HEAD_PROCUREMENT);
        }

        if (empty($userID)) {
            return;
        }

        foreach ($productID as $product) {
            shuffle($userID);

            $managerID = $userID[0]['ID'];

            $data = [
                'TITLE'              => Loc::getMessage("OTUS_EVENT_PURCHASE_REQUEST") . ' ' . $product['ELEMENT_NAME'],
                'ASSIGNED_BY_ID'     => $managerID,
                'UF_CRM_4_PRODUCT'    => $product['ID'],
                'UF_CRM_4_QUANTITY'  => 10,
            ];

            $item = $factory->createItem($data);
            $res = $item->save();

            if ($res->isSuccess()) {
                $smartProcessId = $res->getId();
                $smartProcessName = $item->getTitle();

                $message = Loc::getMessage("OTUS_EVENT_NEW_PURCHASE_REQUEST") . " [URL=/crm/type/1042/details/{$smartProcessId}/]{$smartProcessName}[/URL]";
                SendMessage::init(
                    (int)$managerID,
                    $message,
                    [
                        'URL' => self::API_URL,
                        'METHOD' => self::API_METHOD,
                        'APIKEY' => self::API_KEY
                    ]
                );
            }
        }
    }

    /**
     * Специальный метод для агента, выполняет обновление остатков и создает заявки для заказов закупщикам при нулевом остатке
     *
     * @return string
     */
    public static function agentRunPurchaseRequests(): string
    {
        ProductAutoUpdate::setProductBalance();
        self::runPurchaseRequests();
        return __CLASS__ . '::agentRunPurchaseRequests();';
    }
}