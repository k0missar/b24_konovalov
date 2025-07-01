<?php
namespace Otus\Rest;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Iblock\Elements\ElementProceduresTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Rest\RestException;

Loader::includeModule('iblock');
Loader::includeModule('rest');

class CustomRest
{
    const IBLOCK_ID_PROCEDURE = 16;

    /**
     * Описание REST сервиса и его методов
     *
     * @return array Описание доступных REST методов
     */
    public static function OnRestServiceBuildDescriptionHandler(): array
    {
        Loc::getMessage('REST_SCOPE_OTUS.PROCEDURE_LIST');

        return [
            'otus.procedures' => [
                'otus.procedures.add' => [__CLASS__, 'add'],
                'otus.procedures.getList' => [__CLASS__, 'getList'],
                'otus.procedures.getID' => [__CLASS__, 'getID'],
                'otus.procedures.delete' => [__CLASS__, 'delete'],
                'otus.procedures.update' => [__CLASS__, 'update']
            ]
        ];
    }

    /**
     * Добавляет новую процедуру
     *
     * @param array $arParams Массив параметров, обязателен ключ 'NAME'
     * @param int $navStart Смещение для постраничной навигации (не используется)
     * @param \CRestServer $server Объект сервера REST API
     * @return string JSON с сообщением об успешном добавлении или исключение при ошибке
     * @throws RestException
     */
    public static function add(array $arParams, int $navStart, \CRestServer $server): string
    {
        if (empty($arParams['NAME'])) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_EMPTY_NAME'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        $element = new \CIBlockElement;

        $arProperty = [
            'NAME' => $arParams['NAME'],
            'IBLOCK_ID' => self::IBLOCK_ID_PROCEDURE,
        ];

        if ($newElement = $element->Add($arProperty)) {
            return json_encode(Loc::getMessage('OTUS_ADD_NEW_PROCEDURE', ['#ID#' => $newElement]), JSON_UNESCAPED_UNICODE);
        } else {
            throw new RestException(
                json_encode($element->LAST_ERROR, JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }
    }

    /**
     * Удаляет процедуру по ID
     *
     * @param array $arParams Массив параметров, обязателен ключ 'ID'
     * @param int $navStart Смещение для постраничной навигации (не используется)
     * @param \CRestServer $server Объект сервера REST API
     * @return string JSON с сообщением об успешном удалении или ошибке
     * @throws RestException
     */
    public static function delete(array $arParams, int $navStart, \CRestServer $server): string
    {
        global $DB;
        if (empty($arParams['ID'])) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_ID_MISSING'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        $DB->StartTransaction();
        if (!\CIBlockElement::Delete((int)$arParams['ID'])) {
            $DB->Rollback();
            return json_encode(Loc::getMessage('OTUS_ERROR_DELETE_PROCEDURE'), JSON_UNESCAPED_UNICODE);
        } else {
            $DB->Commit();
            return json_encode(Loc::getMessage('OTUS_DELETE_PROCEDURE'), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Обновляет название процедуры по ID
     *
     * @param array $arParams Массив параметров, обязателен ключ 'ID' и 'NAME'
     * @param int $navStart Смещение для постраничной навигации (не используется)
     * @param \CRestServer $server Объект сервера REST API
     * @return string JSON с сообщением об успешном обновлении или исключение при ошибке
     * @throws RestException
     */
    public static function update(array $arParams, int $navStart, \CRestServer $server): string
    {
        if (empty($arParams['ID'])) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_ID_MISSING'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        if (empty($arParams['NAME'])) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_EMPTY_NAME'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        $element = new \CIBlockElement;

        $arFields = [
            'NAME' => $arParams['NAME'],
        ];

        $id = (int)$arParams['ID'];

        if ($element->Update($id, $arFields)) {
            return json_encode(Loc::getMessage('OTUS_UPDATE_NEW_PROCEDURE', ['#ID#' => $id]), JSON_UNESCAPED_UNICODE);
        } else {
            throw new RestException(
                json_encode($element->LAST_ERROR, JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }
    }

    /**
     * Возвращает список всех процедур
     *
     * @param array $arParams Массив параметров (не используется)
     * @param int $navStart Смещение для постраничной навигации (не используется)
     * @param \CRestServer $server Объект сервера REST API
     * @return string JSON с массивом процедур или исключение при пустом списке
     * @throws RestException
     */
    public static function getList(array $arParams, int $navStart, \CRestServer $server): string
    {
        $arResult = ElementProceduresTable::getList([
            'select' => ['ID', 'NAME']
        ])->fetchAll();

        if (empty($arResult)) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_EMPTY_LIST'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return json_encode($arResult, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Возвращает процедуру по ID
     *
     * @param array $arParams Массив параметров, обязателен ключ 'ID'
     * @param int $navStart Смещение для постраничной навигации (не используется)
     * @param \CRestServer $server Объект сервера REST API
     * @return string JSON с данными процедуры или исключение при ошибке
     * @throws RestException
     */
    public static function getID(array $arParams, int $navStart, \CRestServer $server): string
    {
        if (empty($arParams['ID'])) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_ID_MISSING'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        $arResult = ElementProceduresTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => ['=ID' => (int)$arParams['ID']]
        ])->fetchAll();

        if (empty($arResult)) {
            throw new RestException(
                json_encode(Loc::getMessage('OTUS_ERROR_ID_NOT_FOUND'), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT,
                \CRestServer::STATUS_OK
            );
        }

        return json_encode($arResult, JSON_UNESCAPED_UNICODE);
    }
}

