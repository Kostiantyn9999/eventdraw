<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subtemplates".
 *
 * @property int $id
 * @property int $templateid
 * @property string $subtemplatename
 * @property int $active
 * @property int $created_at
 * @property int $updated_at
 * @property string $image
 * @property string $xmlCode
 */
class Subtemplates extends \yii\db\ActiveRecord
{
    public $imageFile;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subtemplates';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    public function rules()
    {
        return [
            [['templateid', 'subtemplatename' ,'xmlCode'], 'required'],
            [['templateid', 'active', 'created_at', 'updated_at'], 'integer'],
            [['subtemplatename'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
            [['image','xmlCode'], 'string'],
            ['image', 'default', 'value' => ''],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'templateid' => 'Templateid',
            'subtemplatename' => 'Sub Template Name',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'image' => 'Image file name',
            'imageFile' => 'Template image',
            'xmlCode' => 'XML Code',

        ];
    }
}
