<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "venuetype".
 *
 * @property int $id
 * @property string $venueTypeName
 */
class VenueType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venuetype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['venueTypeName'], 'required'],
            [['venueTypeName'], 'string', 'max' => 255],
        ];
    }


 public static function getVenueTypeList()
        {
            $venueTypes = \common\models\VenueType::find()
                ->select(['id', 'venueTypeName'])
                ->orderBy(['venueTypeName' => SORT_ASC])
                ->all();

            $items = ArrayHelper::map($venueTypes, 'id', 'venueTypeName');
            return $items;
        }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'venueTypeName' => 'Venue Type Name',
        ];
    }
}
