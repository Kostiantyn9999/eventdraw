<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bullet_board_users".
 *
 * @property int $id
 * @property int $bullet_board_id
 * @property int $user_id
 * @property int $has_seen
 *
 * @property BulletBoards $bulletBoard
 * @property User $user
 */
class BulletBoardUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bullet_board_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bullet_board_id', 'user_id'], 'required'],
            [['bullet_board_id', 'user_id', 'has_seen'], 'integer'],
            [['bullet_board_id'], 'exist', 'skipOnError' => true, 'targetClass' => BulletBoards::className(), 'targetAttribute' => ['bullet_board_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bullet_board_id' => 'Bullet Board ID',
            'user_id' => 'User ID',
            'has_seen' => 'Has Seen',
            'id' => 'ID',
        ];
    }

    public static function findByID($id) {
        return static::findOne(['id' => $id]);
    }

    /**
     * Gets query for [[BulletBoard]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBulletBoard()
    {
        return $this->hasOne(BulletBoards::className(), ['id' => 'bullet_board_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function primaryKey()
    {
        return [
            'bullet_board_id',
            'user_id',
            'has_seen'
        ];
    }
}
