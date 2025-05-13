<?php
namespace Otus\List;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

/**
 * Class ElementPropM17Table
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> IBLOCK_PROPERTY_ID int mandatory
 * <li> VALUE text mandatory
 * <li> VALUE_ENUM int optional
 * <li> VALUE_NUM double optional
 * <li> DESCRIPTION string(255) optional
 * </ul>
 *
 * @package Bitrix\Iblock
 **/

class DoctorsProperyTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'b_iblock_element_prop_m17';
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
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
                ->configureSize(8)
            ,
            'IBLOCK_ELEMENT_ID' => (new IntegerField('IBLOCK_ELEMENT_ID',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_IBLOCK_ELEMENT_ID_FIELD'))
                ->configureRequired(true)
            ,
            'IBLOCK_PROPERTY_ID' => (new IntegerField('IBLOCK_PROPERTY_ID',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_IBLOCK_PROPERTY_ID_FIELD'))
                ->configureRequired(true)
            ,
            'VALUE' => (new TextField('VALUE',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_VALUE_FIELD'))
                ->configureRequired(true)
            ,
            'VALUE_ENUM' => (new IntegerField('VALUE_ENUM',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_VALUE_ENUM_FIELD'))
            ,
            'VALUE_NUM' => (new FloatField('VALUE_NUM',
                []
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_VALUE_NUM_FIELD'))
            ,
            'DESCRIPTION' => (new StringField('DESCRIPTION',
                [
                    'validation' => function()
                    {
                        return[
                            new LengthValidator(null, 255),
                        ];
                    },
                ]
            ))->configureTitle(Loc::getMessage('ELEMENT_PROP_M17_ENTITY_DESCRIPTION_FIELD'))
            ,
        ];
    }
}