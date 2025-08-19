<?php

namespace Otus\CustomTabs;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use CCrmOwnerType;

class TabsGarage
{
    /**
     * Метод добавляет кастомный таб и выводит результат работы компонента
     *
     * @param Event $event событие
     *
     * @return EventResult - обновленные табы
     */
    public static function updateTabs(Event $event)
    {
        $entityTypeId = $event->getParameter('entityTypeID');
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');

        if ($entityTypeId !== \CCrmOwnerType::Contact) {
            return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs]);
        }

        $tabs[] = [
            'id' => 'custom_garage_tab_' . $entityTypeId . '_' . $entityId,
            'name' => 'Гараж',
            'enabled' => true,
            'loader' => [
                'serviceUrl' => sprintf(
                    '/local/components/otus/crm.tab.garage/lazyload.ajax.php?site=%s&%s',
                    \SITE_ID,
                    \bitrix_sessid_get(),
                ),
                'componentData' => [
                    'template' => '',
                    'params' => [
                    ],
                ],
            ],
        ];

        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs,]);
    }
}