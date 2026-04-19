<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property int $eventid
 * @property int $userid
 * @property string|null $message
 * @property int $status
 * @property int|null $parentid
 * @property int $created_at
 * @property int $updated_at
 */
class Comments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }
    
    public function behaviors()
        {
            return [
                    TimestampBehavior::className(),
                ];
        }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eventid', 'userid', 'created_at', 'updated_at'], 'required'],
            [['eventid', 'userid', 'status', 'parentid', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventid' => 'Eventid',
            'userid' => 'Userid',
            'message' => 'Message',
            'status' => 'Status',
            'parentid' => 'Parentid',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
