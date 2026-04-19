<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "userstencils".
 *
 * @property int $ID
 * @property int $userid
 * @property int $stencilid
 */
class UserStencils extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userstencils';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'stencilid'], 'required'],
            [['userid', 'stencilid'], 'integer'],
        ];
    }

    public static function setUserStns($userid, $stns)
    {
        //clear all user stencils
        self::deleteAll(['userid' => $userid]);
        //add new records for all selected stencils

        if ($stns) {
            foreach ($stns as $v) {
                $userstsl= new UserStencils();
                $userstsl->userid = $userid;
                $userstsl->stencilid = $v;
                $userstsl->save();
            }
        }
    }

    public function getStencilName()
    {
        $Stencil=Stencil::findOne(['id' => $this->stencilid]);
        if ($Stencil){
            return $Stencil->stencilName;
        }
        else{
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'userid' => 'Userid',
            'stencilid' => 'Stencilid',
        ];
    }
}
