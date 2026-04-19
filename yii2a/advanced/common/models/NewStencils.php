<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "new_stencils".
 *
 * @property int $ID
 * @property int $userid
 * @property string $stencilName
 * @property string $stencilXML
 * @property int $stencilActive
 * @property integer $created_at
 * @property integer $updated_at
 */
class NewStencils extends \yii\db\ActiveRecord
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
        return 'new_stencils';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'stencilName', 'stencilXML'], 'required'],
            [['userid','stencilActive'], 'integer'],
            [['stencilXML'], 'string'],
            [['stencilName'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'userid' => 'Userid',
            'stencilName' => 'Stencil Name',
            'stencilXML' => 'Stencil Xml',
            'stencilActive' => 'Active',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
        ];
    }
}
