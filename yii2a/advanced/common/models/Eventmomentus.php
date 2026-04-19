<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "eventmomentus".
 *
 * @property int $id
 * @property int $eventid
 * @property string $momentus_event
 * @property string $momentus_function
 */
class Eventmomentus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eventmomentus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eventid', 'momentus_event', 'momentus_function'], 'required'],
            [['eventid'], 'integer'],
            [['momentus_event', 'momentus_function'], 'string', 'max' => 255],
            [['eventid'], 'unique'],
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
            'momentus_event' => 'Momentus Event',
            'momentus_function' => 'Momentus Function',
        ];
    }
}
