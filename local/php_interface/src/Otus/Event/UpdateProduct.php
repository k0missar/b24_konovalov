<?php
namespace Otus\Event;

use Bitrix\Iblock\Elements\ElementCatalogOffersTable;
use Bitrix\Catalog\Model\Product;
use Bitrix\Main\Loader;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

class UpdateProduct
{
    /**
     * Метод проверят объект на пренадлженость к смарт-процессу закупок, если элемент в стадии успешного заверешения
     * то обновляет остаток закупленного торгого предложения
     *
     * @param array $data - объект события, смарт-процесса
     * @return void
     */
    public static function runUpdateProduct(object $data): void
    {
        $smartID = $data->getParameters()["item"]->getEntityTypeId();
        if ((int)$smartID === 1042) {
            $dataItem = $data->getParameters();
            $item = $dataItem['item'];
            $arResult = $item->getData();

            if ($arResult['STAGE_ID'] === 'DT1042_7:SUCCESS') {
                $arProduct = ElementCatalogOffersTable::getList([
                    'select' => [
                        'ID',
                        'QUANTITY' => 'PRODUCT.QUANTITY',
                    ],
                    'filter' => [
                        '=ID' => $arResult['UF_CRM_4_PRODUCT'],
                        'ACTIVE' => 'Y',
                    ],
                    'runtime' => [
                        new Reference(
                            'PRODUCT',
                            ProductTable::class,
                            Join::on('this.ID', 'ref.ID'),
                            ['join_type' => 'INNER']
                        )
                    ]
                ])->fetch();

                Product::update($arProduct['ID'], [
                    'QUANTITY' => (int)$arProduct['QUANTITY'] + (int)$arResult['UF_CRM_4_QUANTITY'],
                ]);
            }
        }
    }
}
