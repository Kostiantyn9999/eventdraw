<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "new_stencils".
 *
 * @property int $ID
 * @property int $userid
 * @property string $stencilName
 * @property string $stencilXML
 * @property int $stencilActive
 * @property integer $created_at
 * @property integer $updated_at
 */
class NewStencils extends \yii\db\ActiveRecord
{
    public $stencil_version;
    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function tableName()
    {
        return 'new_stencils';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'stencilName', 'stencilXML'], 'required'],
            [['userid','stencilActive'], 'integer'],
            [['stencilXML'], 'string'],
            [['stencilName'], 'string', 'max' => 512],
            ['stencil_version', 'default', 'value' => ''],
        ];
    }

    public function saveNewVersion()
    {
        //get file with selected version and save it to database
        
        $s3 = Yii::$app->get('s3');
        $filename_looking = 'eventdraw_data/new_stencils/' .  $this->ID . '.xml';
        $fileNameLocal = uniqid(rand(), true) . '.xml';
       
        $result = $s3->commands()->get($filename_looking)->withVersionId($this->stencil_version)->saveAs($fileNameLocal)->execute();
        $strXML = file_get_contents($fileNameLocal);

             //delete temp files
        if (!unlink($fileNameLocal)) {
         }

        //save to database
        $st = NewStencils::findOne(['ID' => $this->ID]);
        if ($st){
            $st->stencilXML =  $strXML;
            $st->save(false);
        }
        
        return true;
    }

     public static function getDataList()
    {
        $info = array('Last week','Within  30 days','Over 30 days','Not Set');

        return $info;
    }

  public function getUserName()
    {
        $User = User::findOne(['id' => $this->userid]);
        if ($User) {
            return $User->userfullname;
        } else {
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
            'userid' => 'User',
            'stencilName' => 'Stencil Name',
            'stencilXML' => 'Stencil Xml',
            'stencilActive' => 'Active',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
            'stencil_version'=> 'Stencil Version',
        ];
    }
}
