<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "extra_layouts".
 *
 * @property int $id
 * @property int $userid
 * @property int $eventid
 * @property int $parentid
 */
class ExtraLayouts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extra_layouts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'eventid', 'parentid'], 'required'],
            [['userid', 'eventid', 'parentid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'eventid' => 'Eventid',
            'parentid' => 'Parentid',
        ];
    }
}
