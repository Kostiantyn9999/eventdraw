<?php

namespace common\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;
//fake comment to append it in commit
/**
 * This is the model class for table "bullet_boards".
 *
 * @property int $id
 * @property int $user_id
 * @property int $bulletin_id
 *
 */
class userBulletinModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bulletboarduserdata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'],'integer'],
            [['bullet_board_id','user_id','has_seen'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bullet_board_id' => 'Bulletin ID',
            'user_id'=>'USER ID',
            'has_seen'=>'Has  Seen'
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    public static function customFind($bullet_board_id)
    {
        return static::findOne(['bullet_board_id' => $bullet_board_id]);
    }
}
