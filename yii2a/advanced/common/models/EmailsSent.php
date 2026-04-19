<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "emails_sent".
 *
 * @property int $id
 * @property string $email
 * @property int $is_sent
 * @property string $date_time
 * @property string $subject
 * @property text $email_body
 */
class EmailsSent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emails_sent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['is_sent'], 'integer'],
            [['date_time'], 'safe'],
            [['email_body'], 'safe'],
            [['email', 'date_time','subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'is_sent' => 'Is Sent',
            'date_time' => 'Date Time',
            'subject' => 'Subject',
            'email_body' => 'Mail Content',
        ];
    }
}
