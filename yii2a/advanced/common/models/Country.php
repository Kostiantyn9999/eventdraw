<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "country".
 *
 * @property string $COUNTRY_ISO
 * @property string|null $COUNTRY_ISO3
 * @property int $CALLING_CODE
 * @property string $NAME
 * @property int $COUNTRY_ORDER
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COUNTRY_ISO', 'CALLING_CODE', 'NAME'], 'required'],
            [['CALLING_CODE', 'COUNTRY_ORDER'], 'integer'],
            [['COUNTRY_ISO'], 'string', 'max' => 2],
            [['COUNTRY_ISO3'], 'string', 'max' => 3],
            [['NAME'], 'string', 'max' => 80],
            [['COUNTRY_ISO'], 'unique'],
        ];
    }

    public static function getCountryList()
        {
            $countries = \common\models\Country::find()
                ->select(['COUNTRY_ISO3', 'NAME'])
                ->orderBy(['COUNTRY_ORDER' => SORT_ASC, 'NAME' => SORT_ASC])
                ->all();

            $items = ArrayHelper::map($countries, 'COUNTRY_ISO3', 'NAME');
            return $items;
        }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COUNTRY_ISO' => 'Country Iso',
            'COUNTRY_ISO3' => 'Country Iso3',
            'CALLING_CODE' => 'Calling Code',
            'NAME' => 'Name',
            'COUNTRY_ORDER' => 'Country Order',
        ];
    }
}
