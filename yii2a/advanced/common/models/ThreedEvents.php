<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "threed_events".
 *
 * @property int $id
 * @property int|null $eventid
 * @property string|null $token
 */
class ThreedEvents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'threed_events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eventid'], 'integer'],
            [['token'], 'string', 'max' => 50],
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
            'token' => 'Token',
        ];
    }
}
