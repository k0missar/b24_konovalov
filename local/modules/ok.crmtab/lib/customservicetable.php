<?php
namespace Ok\Crmtab;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;

use Ok\Crmtab\CustomDoctorTable;
use Ok\Crmtab\CustomPatientTable;

/**
 * Class ServiceTable
 *
 * Fields:
 * <ul>
 * <li> DOCTOR_ID int mandatory
 * <li> PATIENT_ID int mandatory
 * <li> DESCRIPTION text optional
 * </ul>
 *
 * @package Bitrix\Custom
 **/

class CustomServiceTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'k_custom_service';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'autocomplete' => true,
                    'primary' => true,
                ]
            ),
            new Fields\IntegerField(
                'DOCTOR_ID',
                [
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'PATIENT_ID',
                [
                    'required' => true,
                ]
            ),
            new Fields\TextField(
                'DESCRIPTION',
                [
                    'required' => true,
                ]
            ),
            new Fields\Relations\Reference(
                'DOCTOR_TABLE',
                '\Ok\Crmtab\CustomDoctorTable',
                ['=this.DOCTOR_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            ),
            new Fields\Relations\Reference(
                'PATIENT_TABLE',
                '\Ok\Crmtab\CustomPatientTable',
                ['=this.PATIENT_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            )
        ];
    }
}
