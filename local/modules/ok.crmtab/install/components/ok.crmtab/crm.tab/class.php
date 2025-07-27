<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\Loader;
use Ok\Crmtab\CustomServiceTable;

Loader::includeModule('ok.crmtab');

class CustomComponent extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    public function executeComponent(): void
    {
        $this->prepareGridData();
        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();
        $this->arResult['FILTER_ID'] = 'CUSTOM_TAB_SERVICE_GRID';

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter($filterData);

        $sort = $gridOptions->getSorting([
            'sort' => ['ID' => 'DESC'],
            'vars' => ['by' => 'by', 'order' => 'order'],
        ]);

        // Подсчет количества
        $countQuery = CustomServiceTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter);
        $nav->setRecordCount($countQuery->queryCountTotal());

        // Основной запрос
        $query = CustomServiceTable::query()
            ->setSelect([
                'ID',
                'DESCRIPTION',
                'DOCTOR_NAME' => 'DOCTOR_TABLE.DOCTOR',
                'PATIENT_NAME' => 'PATIENT_TABLE.PATIENT',
            ])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort']);

        $result = $query->exec();

        $this->arResult['GRID_LIST'] = $this->prepareGridList($result);
        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter[] = [
                'LOGIC' => 'OR',
                ['?DESCRIPTION' => $filterData['FIND']],
                ['?DOCTOR_TABLE.DOCTOR' => $filterData['FIND']],
                ['?PATIENT_TABLE.PATIENT' => $filterData['FIND']],
            ];
        }

        if (!empty($filterData['DOCTOR_NAME'])) {
            $filter['?DOCTOR_TABLE.DOCTOR'] = $filterData['DOCTOR_NAME'];
        }

        if (!empty($filterData['PATIENT_NAME'])) {
            $filter['?PATIENT_TABLE.PATIENT'] = $filterData['PATIENT_NAME'];
        }

        if (!empty($filterData['DESCRIPTION'])) {
            $filter['?DESCRIPTION'] = $filterData['DESCRIPTION'];
        }

        return $filter;
    }

    private function prepareGridList(Result $data): array
    {
        $rows = [];

        while ($item = $data->fetch()) {
            $rows[] = [
                'id' => $item['ID'],
                'data' => [
                    'ID' => $item['ID'],
                    'DOCTOR_NAME' => $item['DOCTOR_NAME'],
                    'PATIENT_NAME' => $item['PATIENT_NAME'],
                    'DESCRIPTION' => $item['DESCRIPTION'],
                ],
                'actions' => $this->getElementActions(),
            ];
        }

        return $rows;
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
            ],
            [
                'id' => 'DOCTOR_NAME',
                'name' => 'Доктор',
                'sort' => false,
                'default' => true,
            ],
            [
                'id' => 'PATIENT_NAME',
                'name' => 'Пациент',
                'sort' => false,
                'default' => true,
            ],
            [
                'id' => 'DESCRIPTION',
                'name' => 'Описание процедуры',
                'sort' => false,
                'default' => true,
            ]
        ];
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'DOCTOR_NAME',
                'name' => 'Доктор',
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'PATIENT_NAME',
                'name' => 'Пациент',
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'DESCRIPTION',
                'name' => 'Описание процедуры',
                'type' => 'string',
                'default' => true,
            ],
        ];
    }

    private function getElementActions(): array
    {
        return [];
    }
}
