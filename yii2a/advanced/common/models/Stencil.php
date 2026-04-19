<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "stencil".
 *
 * @property int $id
 * @property int $created_at
 * @property int $updated_at
 * @property string $stencilName
 * @property string $stencilXML
 * @property int $stencilActive
 * @property int $isMasterStencil
 * @property int $stencilOrder
 */
class Stencil extends \yii\db\ActiveRecord
{

    public $xmlFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stencil';
    }

 public static function getUserStencils($userid)
    {
        $userStencils  = [];
        //return all master stencils and user defined stencils order by StencilOrder then by stencil name
        $AllStencils = \common\models\Stencil::find()
            ->orderBy(['stencilOrder'=>SORT_ASC, 'stencilName'=>SORT_ASC])
            ->all();

        $userStencilsList = \common\models\UserStencils::findAll([
            'userid' => $userid,
        ]);

        // get list of client stencils
        $User=User::findOne(['id' => $userid]);
        $Client=Client::findOne(['id' => $User->clientid]);

        if ($Client)
        {
            $ClientStencilsList = \common\models\ClientStencils::findAll([
            'clientid' => $Client->id,
        ]);
        }
        else
        {
          $ClientStencilsList = \common\models\ClientStencils::findAll([
            'clientid' => -1,
        ]);  
        }
        

        $arrlength = count($AllStencils);
        $arrlength2 = count($userStencilsList);
        $arrlength3 = count($ClientStencilsList);

        for($x = 0; $x < $arrlength; $x++) {
            //if this stencil is master
            $foundStencil = false;
            if ($AllStencils[$x]->isMasterStencil == 1)
            {
                $foundStencil = true;
            }
            //maybe this stencil is assigned for user directly
            else if ($AllStencils[$x]->id == Yii::$app->user->identity->userStencilid)
            {
                $foundStencil = true;
            }

            //else maybe this stencil is assigned to user stencils list
            else {
                for($y = 0; $y < $arrlength2; $y++) {
                    if ($AllStencils[$x]->id == $userStencilsList[$y]->stencilid)
                    {
                        $foundStencil = true;
                    }
                }
            }

            //check maybe in client stencil list
            if (!$foundStencil)
            {
                for($y = 0; $y < $arrlength3; $y++) {
                    if ($AllStencils[$x]->id == $ClientStencilsList[$y]->stencilid)
                    {
                        $foundStencil = true;
                    }
                }
            }
            if ($foundStencil) {
                array_push($userStencils, $AllStencils[$x]);
            }
        }

        return $userStencils;
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function findStencilByID($id)
    {
        return static::findOne(['id' => $id, 'stencilActive' => 1]);
    }

    public function rules()
    {
        return [
            [['stencilName'], 'required'],
            [['stencilActive','isMasterStencil','stencilOrder'], 'integer'],
            ['isMasterStencil', 'default', 'value' => 0],
            ['stencilOrder', 'default', 'value' => 1],
            [['xmlFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xml'],
            [['stencilXML'], 'string', 'max' => 255],
            [['stencilName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
            'stencilName' => 'Stencil Name',
            'stencilXML' => 'XML',
            'stencilActive' => 'Active',
            'xmlFile' => 'XML File',
            'isMasterStencil'=> 'Master Stencil',
            'stencilOrder'=> 'Order #',
        ];
    }
}
