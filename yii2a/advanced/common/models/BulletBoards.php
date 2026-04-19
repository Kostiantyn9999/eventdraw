<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bullet_boards".
 *
 * @property int $id
 * @property string $note
 *
 * @property BulletBoardUsers[] $bulletBoardUsers
 */
class BulletBoards extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bullet_boards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note'], 'required'],
            [['note'], 'string'],
         ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note' => 'Note',
        ];
    }

    /**
     * Gets query for [[BulletBoardUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBulletBoardUsers()
    {
        return $this->hasMany(BulletBoardUsers::className(), ['bullet_board_id' => 'id']);
    }

    public static function getUserBulletMessage($userid)
    {
        $retValue = '';
        //now search first message for this user with has_seen = 0
     $tmpls = \common\models\BulletBoardUsers::find()
     ->select(['id','bullet_board_id'])
     ->where(['user_id' => $userid])
     ->andWhere(['between','has_seen', '0', '1'])
     ->orderBy(['bullet_board_id' => SORT_ASC])->one();

    if ( $tmpls) {
    
       $board_id = $tmpls->bullet_board_id;

       $item =  \common\models\BulletBoards::findOne(['id' => $board_id]);
       if ($item) {
           $retValue =  $item->note; 
        }
        
        $rec = \common\models\BulletBoardUsers::findByID($tmpls->id);
        if ($rec)
        {
            $rec->has_seen = $rec->has_seen + 1;
            $rec->save(false);
        }
      
    }

    return $retValue;
    }

}
    
