<?php
defined('B_PROLOG_INCLUDED') || die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Bizproc\Activity\PropertiesDialog;

use Otus\Dadata\Dadata;

class CBPMyCustomActivity extends BaseActivity
{
    const DADATA_API_TOKEN = 'b3c2d210b0a09765e151404a2db43dd5f2b58e11';
    const DADATA_API_SECRET = '79187dc02a065dddd4856cade54cc77dc1d4a58a';

    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'CompanyInn' => '',

            'Text' => null,
        ];

        $this->SetPropertiesTypes([
            'Text' => ['Type' => FieldType::STRING],
        ]);
    }

    protected static function getFileName(): string
    {
        return __FILE__;
    }
    protected function internalExecute(): ErrorCollection
    {
        $errors = parent::internalExecute();

        $rootAcrivity = $this->GetRootActivity();

        $dadata = new Dadata(self::DADATA_API_TOKEN, self::DADATA_API_SECRET);
        $dadata->init();

        $fields = [
            "query" => $this->CompanyInn,
            "type" => "LEGAL"
        ];

        $response = $dadata->suggest('party', $fields);
        $companyName = $response['suggestions'][0]['value'];

        $rootAcrivity->SetVariable('ZAKAZCHIK_INN', $this->CompanyInn);
        $rootAcrivity->SetVariable('ZAKAZCHIK', $companyName);

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/_LOG/ACTIVITY.log', print_r($testID, true), FILE_APPEND);

        return $errors;
    }
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'CompanyInn' => [
                'Name' => 'ИНН для поиска',
                'FieldName' => 'subject',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Default' => 'Дефолтный Subject',
                'Options' => [],
            ]
        ];
        return $map;
    }
}