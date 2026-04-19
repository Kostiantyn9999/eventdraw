<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "xmlshapesbackup".
 *
 * @property int $id
 * @property int $shapesid
 * @property string $shapesName
 * @property string $xmlCode
 * @property int $shapesActive
 * @property int $created_at
 * @property int $updated_at
 * @property string $serverName
 * @property int $update_user
 */
class XmlShapesBackup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function tableName()
    {
        return 'xmlshapesbackup';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shapesid', 'shapesName', 'xmlCode', 'created_at', 'updated_at'], 'required'],
            [['shapesid', 'shapesActive', 'created_at', 'updated_at','update_user'], 'integer'],
            [['xmlCode'], 'string'],
            [['shapesName','serverName'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shapesid' => 'Shapesid',
            'shapesName' => 'Shapes Name',
            'xmlCode' => 'Xml Code',
            'shapesActive' => 'Shapes Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'serverName' => 'Server Name',
            'update_user' => 'Updated by User'
        ];
    }
}
