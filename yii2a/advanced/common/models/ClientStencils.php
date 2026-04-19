<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "clientstencils".
 *
 * @property int $ID
 * @property int $clientid
 * @property int $stencilid
 */
class ClientStencils extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientstencils';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clientid', 'stencilid'], 'required'],
            [['clientid', 'stencilid'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */

     
    public static function setClientStns($clientid, $stns)
    {
        //clear all client stencils
        self::deleteAll(['clientid' => $clientid]);
        //add new records for all selected stencils

        if ($stns) {
            foreach ($stns as $v) {
                $userstsl= new ClientStencils();
                $userstsl->clientid = $clientid;
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

    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'clientid' => 'Clientid',
            'stencilid' => 'Stencilid',
        ];
    }
}
