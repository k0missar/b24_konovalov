<?php

namespace Otus\Common;

use Bitrix\Iblock\Elements\ElementCatalogOffersTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Catalog\Model\Product;

Loader::includeModule('iblock');

class ProductAutoUpdate
{
    /**
     * Обновляет остатки торговых предложений
     *
     * @return void
     */
    public static function setProductBalance(): void
    {
        $balance = self::getCountProduct();
        self::productUpdate($balance);
    }

    /**
     * Проверяет количество торговых предложений и возвращает массив чисел, каждое число - это случайный остаток торгового предложения
     *
     * @return array - массив случайных чисел, количество чисел равно количеству торговых предложений
     */
    public static function getCountProduct(): array
    {
        $arCount = [];
        $httpClient = new HttpClient();
        $count = ElementCatalogOffersTable::getCount();

        for ($i = 0; $i < $count; $i++) {
            $num = $httpClient->get('https://www.random.org/integers/?num=1&min=0&max=10&col=1&base=10&format=plain&rnd=new');

            if (!is_numeric($num)) {
                $num = 0;
            }
            $resArray = [0, $num, $num];
            shuffle($resArray);
            $arCount[] = $resArray[0];

            sleep(1);
        }

        return $arCount;
    }

    /**
     * Получает массив случайных чисел - остатков, количество чисел = количеству торговых предложений
     * Обновляет остатки торговых предложений
     * Если массивы не равных выбрасывает ошибку
     *
     * @param array $arBalance - массив случайных чисел - остатков
     *
     * @return void
     */
    public static function productUpdate(array $arBalance): void
    {
        $arProduct = ElementCatalogOffersTable::getList([
            'select' => ['ID'],
            'filter' => ['ACTIVE' => 'Y'],
        ])->fetchAll();

        $countProducts = count($arProduct);

        if ($countProducts !== count($arBalance)) {
            throw new \RuntimeException('Количество остатков не совпадает с количеством офферов');
        }

        foreach ($arProduct as $index => $value) {
            Product::update($value['ID'], [
                'QUANTITY' => $arBalance[$index],
            ]);
        }
    }
}
