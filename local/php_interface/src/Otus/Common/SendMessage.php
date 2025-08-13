<?php

namespace Otus\Common;

use Bitrix\Main\Web\HttpClient;

class SendMessage
{
    /**
     * Метод подготавливает/формирует данные и делает отправку
     *
     * @param int $userID - идентификатор пользователя в системе для которого предназначено сообщение
     * @param string $message - текст сообщения
     * @param array $arParams - массив параметров
     * [
     *      URL - адрес вебхука без / на конце
     *      APIKEY - ключ вебхука
     *      METHOD - метод вебхука
     * ]
     * @return void
     */
    public static function init(int $userID, string $message, array $arParams): void
    {
        $service = $arParams['URL'];
        $apiKey = $arParams['APIKEY'];
        $method = $arParams['METHOD'];

        $url = $service . '/' . $apiKey . '/' . $method;

        self::send($userID, $message, $url);
    }

    /**
     * Метод отправляет сообщение для пользователя через вебхук REST API Bitrix24
     *
     * @param int $id - идентификатор пользователя в системе для которого предназначено сообщение
     * @param string $message - текст сообщения
     * @param string $url - адрес вебхука
     *
     * @return string
     */
    public static function send(int $id, string $message, string $url): string
    {
        $client = new HttpClient();

        $client->setHeader('Content-Type', 'application/json', true);
        $client->setHeader('Accept', 'application/json', true);

        $postData = json_encode([
            'DIALOG_ID' => $id,
            'MESSAGE' => $message,
        ]);

        $result = $client->post($url, $postData);

        return $result;
    }
}