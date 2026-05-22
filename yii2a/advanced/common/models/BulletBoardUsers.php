<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\BulletBoards;
/**
 * This is the model class for table "bullet_board_users".
 *
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
            [['bullet_board_id', 'user_id', 'has_seen','userType'], 'integer'],

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
            'userType'=>'userType',
            'has_seen' => 'Has Seen',
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


    public function search($params)
    {
        /*$query = BulletBoardUsers::find()
                ->select('bullet_board_users.*,bullet_boards.*') 
                ->leftJoin('bullet_boards', 'bullet_boards.id = bullet_board_users.bullet_board_id')
                ->where(['bullet_board_users.userType' => 1])
                ->with('bullet_boards')
                ->all();*/
        //$query = BulletBoardUsers::find()->where(['userType' => 1]);
        $query = BulletBoards::find()->all();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'heading' => $this->heading,
        ]);

        $query->andFilterWhere(['like', 'heading', $this->heading]);

        return $dataProvider;
    }
}
