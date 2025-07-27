<?php
namespace Ok\Crmtab;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

/**
 * Class PatientTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PATIENT string(255) mandatory
 * </ul>
 *
 * @package Bitrix\Custom
 **/

class CustomPatientTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'k_custom_patient';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle(Loc::getMessage('PATIENT_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
            ,
            'PATIENT' => (new StringField('PATIENT',
                [
                    'validation' => function()
                    {
                        return[
                            new LengthValidator(null, 255),
                        ];
                    },
                ]
            ))->configureTitle(Loc::getMessage('PATIENT_ENTITY_PATIENT_FIELD'))
                ->configureRequired(true)
            ,
        ];
    }
}
