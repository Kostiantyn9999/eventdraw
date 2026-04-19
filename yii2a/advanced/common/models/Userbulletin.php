<?php

namespace common\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "bullet_boards".
 *
 * @property int $id
 * @property int $user_id
 * @property int $bulletin_id
 *
 */
class Userbulletin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_bulletin_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id','bulletin_id'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bulletin_id' => 'bulletin_id',
            'user_id'=>'user_id',
        ];
    }
}
