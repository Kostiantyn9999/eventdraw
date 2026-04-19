<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_open_events".
 *
 * @property int $id
 * @property int $userid
 * @property int $eventid
 * @property int $opentime
 * @property int $edittime
 * @property int $status
 */
class UserOpenEvents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_open_events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'eventid'], 'required'],
            [['userid', 'eventid', 'opentime','edittime','status'], 'integer'],
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
            'opentime' => 'Open Time',
            'edittime' => 'Edit Time',
            'status' => 'Status',
        ];
    }
}
