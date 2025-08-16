<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation; // пагинация
use Bitrix\Main\Grid\Options as GridOptions; // сортировка
use Bitrix\Main\UI\Filter\Options as FilterOptions; // фильтр
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
Loader::includeModule('crm');

class TabGarage extends \CBitrixComponent
{
    const SM_LIST_AUTO = 1038;

    public function configureActions(): array
    {
        return [];
    }

    /**
     * Основной метод формиующий данные для шаблона компонента
     *
     * @return void
     */
    public function executeComponent(): void
    {
        try {
            $this->arResult['FILTER_ID'] = 'GARAGE_TAB_GRID'; // ID грида и фильтра
            $this->arResult['HEADERS'] = $this->getGridHeaders(); // Заголовки грида

            $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
            $navParams = $gridOptions->getNavParams();

            $nav = new PageNavigation($this->arResult['FILTER_ID']);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            $filterOptions = new FilterOptions($this->arResult['FILTER_ID']);
            $filterData = $filterOptions->getFilter([]);
            $filter = $this->prepareFilter($filterData); // Подготовка фильтра

            $sort = $gridOptions->getSorting([
                'sort' => ['ID' => 'DESC'],
                'vars' => ['by' => 'by', 'order' => 'order']
            ]);

            // Получаем сделки и автомобили
            $deals = $this->getContactDeals($filter, $sort['sort'], $nav->getOffset(), $nav->getLimit());
            $totalCount = $this->getDealsCount($filter);

            $nav->setRecordCount($totalCount); // установка общего количества

            $this->arResult['LIST_AUTO'] = $this->prepareGridRows($deals); // подготовка строк
            $this->arResult['NAV'] = $nav;
            $this->arResult['UI_FILTER'] = $this->getFilterFields(); //  поля фильтра
            $this->includeComponentTemplate();
        } catch (\Throwable $exception) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/_LOG/ERROR_CLASS.log', $exception->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * Метод обрабатывает массив сделок и подгатавливает данные для вывода в bx.ui.grid
     *
     * @param array $data - массив со сделками
     *
     * @return array - подготовленный массив для bx.ui.grid
     */
    private function prepareGridRows(array $data): array
    {
        $rows = [];

        foreach ($data as $deal) {
            $rows[] = [
                'id' => $deal['ID'],
                'data' => [
                    'ID' => $deal['ID'],
                    'TITLE' => '<a href="/crm/deal/details/' . $deal['ID'] . '/">' . $deal['TITLE'] . '</a>',
                    'OPPORTUNITY' => $deal['OPPORTUNITY'],
                    'DATE_CREATE' => $deal['DATE_CREATE']->format('d-m-Y'),
                    'MODEL' => $deal['MODEL'],
                    'MARKA' => $deal['MARKA'],
                    'VIN' => $deal['VIN'],
                ],
                'actions' => [
                    [
                        'text' => Loc::getMessage("OTUS_EVENT_OPEN_DEAL"),
                        'onclick' => 'document.location.href="/crm/deal/details/' . $deal['ID'] . '/"'
                    ],
                ],
            ];
        }

        return $rows;
    }

    /**
     * Получаем список сделок по фильтру в котором есть автомобили клиента
     *
     * @param array $filter - фильтр
     * @param array $sort - сортировка
     * @param int $offset - смещение
     * @param int $limit - ограничение кол-ва элементов
     *
     * @return array - массив элементов
     */
    private function getContactDeals(array $filter = [], array $sort = [], int $offset = 0, int $limit = 20): array
    {
        $listAuto = $this->getListAuto(self::SM_LIST_AUTO);
        $autoIds = array_keys($listAuto);

        if (!$autoIds) {
            return [];
        }

        $baseFilter = ['UF_CRM_1754162306' => $autoIds];

        if ($filter) {
            $baseFilter[] = $filter;
        }

        $arResult = DealTable::getList([
            'select' => ['ID', 'TITLE', 'OPPORTUNITY', 'UF_CRM_1754162306', 'DATE_CREATE'],
            'filter' => $baseFilter,
            'order' => $sort ?: ['ID' => 'DESC'],
            'limit' => $limit,
            'offset' => $offset,
        ])->fetchAll();

        foreach ($arResult as &$deal) {
            if (isset($listAuto[$deal['UF_CRM_1754162306']])) {
                $deal['MODEL'] = $listAuto[$deal['UF_CRM_1754162306']]['UF_CRM_3_1753624389'];
                $deal['MARKA'] = '<a href="/crm/type/1038/details/' . $deal['UF_CRM_1754162306'] . '/">' . $listAuto[$deal['UF_CRM_1754162306']]['UF_CRM_3_1753624417'] . '</a>';
                $deal['VIN'] = $listAuto[$deal['UF_CRM_1754162306']]['UF_CRM_3_1753624426'];
            }
        }

        return $arResult;
    }

    /**
     * Метод возвращает количество элементов сделок с указанным автомобилем
     *
     * @param array $filter - фильтрация
     *
     * @return int - количество сделок с указанным автомобилем
     */
    private function getDealsCount(array $filter = []): int
    {
        $listAuto = $this->getListAuto(self::SM_LIST_AUTO);
        $autoIds = array_keys($listAuto);

        if (!$autoIds) {
            return 0;
        }

        $baseFilter = ['UF_CRM_1754162306' => $autoIds];

        if ($filter) {
            $baseFilter[] = $filter;
        }

        return DealTable::getCount($baseFilter);
    }

    /**
     * Метод для фильтрации элементов в bx.ui.filter
     *
     * @param array $filterData
     *
     * @return array - массив элементов для фильтра
     */
    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter[] = [
                'LOGIC' => 'OR',
                ['?TITLE' => $filterData['FIND']],
                ['?OPPORTUNITY' => $filterData['FIND']],
            ];
        }

        // Если фильтр по модели, марке или VIN есть, то:
        // Получаем список автомобилей, удовлетворяющих фильтру
        $autoFilter = [];
        if (!empty($filterData['MODEL'])) {
            $autoFilter['%UF_CRM_3_1753624389'] = $filterData['MODEL'];
        }
        if (!empty($filterData['MARKA'])) {
            $autoFilter['%UF_CRM_3_1753624417'] = $filterData['MARKA'];
        }
        if (!empty($filterData['VIN'])) {
            $autoFilter['%UF_CRM_3_1753624426'] = $filterData['VIN'];
        }

        if ($autoFilter) {
            $listAuto = $this->getListAutoFiltered($autoFilter);
            $autoIds = array_keys($listAuto);
            if ($autoIds) {
                $filter['UF_CRM_1754162306'] = $autoIds;
            } else {
                // Если нет автомобилей под фильтр, то ничего не возвращаем
                $filter['UF_CRM_1754162306'] = -1; // фиктивный ID, чтобы ничего не нашли
            }
        }

        return $filter;
    }

    /**
     * Метод возвращает массив колонок для bx.ui.grid
     *
     * @return array[]
     */
    private function getGridHeaders(): array
    {
        return [
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'TITLE', 'name' => Loc::getMessage("OTUS_EVENT_TITLE"), 'sort' => false, 'default' => true],
            ['id' => 'OPPORTUNITY', 'name' => Loc::getMessage("OTUS_EVENT_AMOUNT"), 'sort' => false, 'default' => true],
            ['id' => 'DATE_CREATE', 'name' => Loc::getMessage("OTUS_EVENT_DATE_CREATE"), 'sort' => false, 'default' => true],
            ['id' => 'MODEL', 'name' => Loc::getMessage("OTUS_EVENT_MODEL"), 'sort' => false, 'default' => true],
            ['id' => 'MARKA', 'name' => Loc::getMessage("OTUS_EVENT_BRAND"), 'sort' => false, 'default' => true],
            ['id' => 'VIN', 'name' => Loc::getMessage("OTUS_EVENT_VIN"), 'sort' => false, 'default' => true],
        ];
    }

    /**
     * Метод возвращает массив колонок для bx.ui.filter
     *
     * @return array[]
     */
    private function getFilterFields(): array
    {
        return [
            ['id' => 'MODEL', 'name' => Loc::getMessage("OTUS_EVENT_MODEL"), 'type' => 'string', 'default' => true],
            ['id' => 'MARKA', 'name' => Loc::getMessage("OTUS_EVENT_BRAND"), 'type' => 'string', 'default' => true],
            ['id' => 'VIN', 'name' => Loc::getMessage("OTUS_EVENT_VIN"), 'type' => 'string', 'default' => true],
        ];
    }

    private function getElementActions(): array
    {
        return []; //пока не используется
    }

    /**
     * Метод возвращает массив списка автомобилей клиента
     *
     * @param $id - смарт-процесс где харнятся автомобиле
     * @return array - массив списка автомобилей клиента
     */
    private function getListAuto($id): array
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $referer = $request->getServer()->get('HTTP_REFERER');

        $contactId = 0;
        if (preg_match('#/contact/details/(\d+)/#', $referer, $matches)) {
            $contactId = (int)$matches[1];
        }

        $arResult = [];

        $automobileFactory = Container::getInstance()->getFactory($id);

        if ($automobileFactory) {
            $resAutomobiles = $automobileFactory->getItems([
                'select' => ['ID', 'UF_CRM_3_1753624389', 'UF_CRM_3_1753624417', 'UF_CRM_3_1753624426'],
                'filter' => ['=UF_CRM_3_1753624215' => 'C_' . $contactId],
                'limit' => 1000
            ]);
            foreach ($resAutomobiles as $automobile) {
                $id = $automobile->getId();
                $arResult[$id]  = $automobile->getData();
            }
        }

        return $arResult;
    }

    /**
     * Метод, который возвращает авто с применением фильтра по автомобилям
     *
     * @param array $autoFilter
     * @return array
     */
    private function getListAutoFiltered(array $autoFilter): array
    {
        $request = \Bitrix\Main\Context::getCurrent()->getRequest();
        $referer = $request->getServer()->get('HTTP_REFERER');

        $contactId = 0;
        if (preg_match('#/contact/details/(\d+)/#', $referer, $matches)) {
            $contactId = (int)$matches[1];
        }

        $arResult = [];

        $automobileFactory = Container::getInstance()->getFactory(self::SM_LIST_AUTO);

        if ($automobileFactory) {
            $resAutomobiles = $automobileFactory->getItems([
                'select' => ['ID', 'UF_CRM_3_1753624389', 'UF_CRM_3_1753624417', 'UF_CRM_3_1753624426'],
                'filter' => array_merge(['=UF_CRM_3_1753624215' => 'C_' . $contactId], $autoFilter),
                'limit' => 1000
            ]);
            foreach ($resAutomobiles as $automobile) {
                $id = $automobile->getId();
                $arResult[$id] = $automobile->getData();
            }
        }

        return $arResult;
    }
}
