<?php

namespace common\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "usersessions".
 *
 * @property int $id
 * @property int $userid
 * @property string $loginIP
 * @property int $loginAt
 */
class UserSessions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usersessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid'], 'required'],
            [['userid', 'loginAt'], 'integer'],
            [['loginIP'], 'string', 'max' => 50],
        ];
    }


    public static function addUserSession($userid)
    {
        //add new records for all selected templates



        if ($userid) {
            $usersess = new UserSessions();
            $usersess->userid = $userid;
            $usersess->loginAt = time();
            $usersess->loginIP=Yii::$app->request->userIP;
            $usersess->save();

            //calculate count of user sessions
            $result = \Yii::$app->db->createCommand("CALL updateUserSessions(:paramName1)")
                ->bindValue(':paramName1' , $userid )
                ->execute();

        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'User',
            'loginIP' => 'IP address',
            'loginAt' => 'Login datetime',
        ];
    }
}
