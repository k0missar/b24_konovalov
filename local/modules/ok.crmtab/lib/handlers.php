<?php
namespace Ok\Crmtab;

//use Aholin\Crmcustomtab\Orm\BookTable;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class Handlers
{
    public static function updateTabs(Event $event)
    {
        $entityTypeId = $event->getParameter('entityTypeID');
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');
        $tabs[] = [
            'id' => 'custom_service_tab_' . $entityTypeId . '_' . $entityId,
            'name' => 'Последние услуги',
            'enabled' => true,
            'loader' => [
                'serviceUrl' => sprintf(
                    '/bitrix/components/ok.crmtab/crm.tab/lazyload.ajax.php?site=%s&%s',
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
