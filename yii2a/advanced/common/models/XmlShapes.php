<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "xmlshapes".
 *
 * @property int $id
 * @property string $shapesName
 * @property string $xmlCode
 * @property int $shapesActive
 * @property int $created_at
 * @property int $updated_at
 */
class XmlShapes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xmlshapes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shapesName', 'xmlCode', 'created_at', 'updated_at'], 'required'],
            [['xmlCode'], 'string'],
            [['shapesActive', 'created_at', 'updated_at'], 'integer'],
            [['shapesName'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function findByShapeName($shapeName)
    {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        return static::findOne(['shapesName' => $shapeName]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shapesName' => 'Shapes Name',
            'xmlCode' => 'Xml Code',
            'shapesActive' => 'Shapes Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
