<?php

namespace Otus\Common;

use Bitrix\Main\UserTable;

class UserData
{
    /**
     * Метод выбирает всех пользователей из переданного раздела
     *
     * @param int $userID - ID подразделения пользователей
     *
     * @return array - массив с пользователями состоящий из ID - ключ/значение
     */
    public static function getUserID(int $userID): array
    {
        $users = UserTable::getList([
            'select' => ['ID'],
            'filter' => [
                'UF_DEPARTMENT' => $userID,
                'ACTIVE' => 'Y'
            ]
        ])->fetchAll();

        return $users;
    }
}