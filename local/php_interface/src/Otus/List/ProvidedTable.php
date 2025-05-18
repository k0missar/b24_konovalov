<?php
namespace Otus\List;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\Type\DateTime;

/**
 * Class ProvidedTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> DOCTOR_ID int optional
 * <li> PROCEDURE_ID int optional
 * <li> CREATED_POST datetime optional default current datetime
 * <li> DESCRIPTION string(255) optional
 * <li> PATIENT_ID int optional
 * </ul>
 *
 * @package Otus\List
 **/

class ProvidedTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'k_services_provided';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => 'ID'
                ]
            ),

            new IntegerField(
                'DOCTOR_ID',
                [
                    'title' => 'DOCTOR_ID',
                ]
            ),

            new IntegerField(
                'PROCEDURE_ID',
                [
                    'title' => 'PROCEDURE_ID',
                ]
            ),

            new DatetimeField(
                'CREATED_POST',
                [
                    'title' => 'CREATED_POST',
                ]
            ),

            new StringField(
                'DESCRIPTION',
                [
                    'title' => 'DESCRIPTION',
                ]
            ),

            new IntegerField(
                'PATIENT_ID',
                [
                    'title' => 'PATIENT_ID',
                ]
            ),

            new Reference(
                'DOCTOR_NAME',
                '\Bitrix\Iblock\Elements\ElementDoctorsTable',
                ['this.DOCTOR_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),

            new Reference(
                'PROCEDURE_NAME',
                '\Bitrix\Iblock\Elements\ElementProceduresTable',
                ['this.PROCEDURE_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            )
        ];
    }
}
