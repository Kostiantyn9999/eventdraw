<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "realistic_events".
 *
 * @property int $id
 * @property int $realistic_id
 * @property int $event_id
 */
class RealisticEvents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'realistic_events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['realistic_id', 'event_id'], 'required'],
            [['realistic_id', 'event_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'realistic_id' => 'Realistic ID',
            'event_id' => 'Event ID',
        ];
    }
}
