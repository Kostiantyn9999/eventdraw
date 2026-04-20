<?php

namespace common\models;

use FontLib\Table\Type\name;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use \DateTime;

/**
 * This is the model class for table "xmltemplate".
 *
 * @property int $id
 * @property string $templateName
 * @property string $xmlCode
 * @property int $templateActive
 * @property integer $created_at
 * @property integer $updated_at
 * @property int $templateDefault
 * @property int $clientid
 * @property int $shadow
 * @property string $image
 * @property string $momentusSpaceDescr
 * @property string $momentusSpaceCode
 * @property int|null $momentusEventSpaceDiagramId
 */
class Template extends \yii\db\ActiveRecord
{
    public $imageFile;
    public $subtemplate;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xmltemplate';
    }

    public static function getUserTemplates($userid)
    {

        $tmpls = \common\models\Template::find()
            ->select(['id', 'clientid','templateName'])
            ->where(['templateActive' => 1])
            ->orderBy(['templateName' => SORT_ASC])->all();

        $userTemplates = \common\models\UserTemplates::findAll([
            'userid' => $userid,
        ]);
        $userInfo = \common\models\User::findIdentity($userid);

        $arrlength = count($tmpls);
        $tmpllength = count($userTemplates);

        $tmplNames = "";

        $userClientID = \common\models\Client::findIdentity($userInfo->clientid);

        if ($userClientID)
        {
            $userClientName = $userClientID->clientName;


            //set template clientid as current clientid if this template assigned to client
            for($x = 0; $x < $arrlength; $x++) {
                $clientTemplates = \common\models\ClientTemplates::findAll([
                    'clientid' => $userClientID->id,
                    'templateid' =>$tmpls[$x]->id,
                ]);
                if ($clientTemplates)
                {
                    $tmpls[$x]->clientid =-1;
                }
            }
        }
        else
        {
            $userClientName = "";
        }

        for($x = 0; $x < $arrlength; $x++) {


            //check is this template clientid is the same as user clientid
            if ( ($tmpls[$x]->clientid == -1) || ($tmpls[$x]->clientid != null) && ($tmpls[$x]->clientid == $userInfo->clientid)) {
                $tmplNames = $tmplNames . $tmpls[$x]->id . "|" . '!' . $tmpls[$x]->templateName . "|";
            } else {
                //add all default templates for trial users only
                if (($tmpls[$x]->templateDefault == 1) && ($userInfo->userPayment == false)) {
                    $tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                } else {
                    //check is this user is Admin or Support (except google templates
                    if (($userInfo->userIsAdmin || $userInfo->userIsSupport) && strpos(strtoupper($tmpls[$x]->templateName),'OOGLE') == false)
                    {
                        $tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                    }
                    else {
                        //check is this template assigned to this user
                        for ($y = 0; $y < $tmpllength; $y++) {
                            if ($tmpls[$x]->id == $userTemplates[$y]->templateid) {
                                $tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                            }
                        }
                    }
                }
            }
        }

        $retr_arr["tmplNames"] = $tmplNames;
        $retr_arr["clientName"] = $userClientName;

        return $retr_arr;
    }
    /**
     * {@inheritdoc}
     */

    public static function findByID($id) {
        return static::findOne(['id' => $id,]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function getUserTemplatesJson($userid)
    {
        $tmpls = \common\models\Template::find()
            ->select(['id', 'clientid','templateName','created_at', 'updated_at','templateDefault'])
            ->where(['templateActive' => 1])
            ->orderBy(['templateName' => SORT_ASC])->all();

        $userTemplates = \common\models\UserTemplates::findAll([
            'userid' => $userid,
        ]);
        $userInfo = \common\models\User::findIdentity($userid);

      
        $ClientFolders = \common\models\ClientTemplateFolders::findAll([
            'clientid' => $userInfo->clientid,
        ]);

        $folderlength = count($ClientFolders);


        //check incorrectly parentID
        for($z = 0; $z < $folderlength; $z++) {
            $parent_id = 0 ;
             for($y = 0; $y < $folderlength; $y++) {
                 if ($ClientFolders[$y]->id == $ClientFolders[$z]->parentid)
                 {
                     $parent_id  = $ClientFolders[$z]->parentid;
                     break;
                 }
             }
             $ClientFolders[$z]->parentid =  $parent_id;
        }

        $arrlength = count($tmpls);
        $tmpllength = count($userTemplates);

        $userClientID = \common\models\Client::findIdentity($userInfo->clientid);
        $userClientStatus = $userInfo->status;


        $myArray = array();

        if ($userClientID)
        {
            $userClientName = $userClientID->clientName;
            $userClientStatus =$userClientID->status ;
            

         

            //set template clientid as current clientid if this template assigned to client
            for($x = 0; $x < $arrlength; $x++) {
                $clientTemplates = \common\models\ClientTemplates::findAll([
                    'clientid' => $userClientID->id,
                    'templateid' =>$tmpls[$x]->id,
                ]);
                if ($clientTemplates)
                {
                    $tmpls[$x]->clientid =-1;
                }
            }
        }
        else
        {
            $userClientName = "";
        }

        //create folders assigned for this client
        for($z = 0; $z < $folderlength; $z++) {
            if( $ClientFolders[$z]->templateid == -1)
            {
                $newItem = array(
                             "eventActive" => 0,
                             "eventDate" => '',
                             "eventImage" => '',
                             "eventName" => $ClientFolders[$z]->folderName,
                             "id" =>  $ClientFolders[$z]->id,
                             "lastmodified" =>  '',
                             "userName" => '',
                             "createddate" => '',
                             "myplans" => 'not',
                             "isFolder" => 1,
                             "parentID" => $ClientFolders[$z]->parentid,
                             "clientID" => $userClientID->id
                             );

                $myArray[] = $newItem;             
            }
        }

        

         for($x = 0; $x < $arrlength; $x++) {

             $modif_date = DateTime::createFromFormat( 'U', $tmpls[$x]->updated_at );
             $created_date = DateTime::createFromFormat( 'U', $tmpls[$x]->created_at );

            //looking for parent id in clientFolders table
            $parent_id = 0;

            for($z = 0; $z < $folderlength; $z++) {
                if( $ClientFolders[$z]->templateid == $tmpls[$x]->id)
                {
                    $parent_id = $ClientFolders[$z]->parentid;
                    break; 

                }
            }

             $newItem = array(
                             "eventActive" => 0,
                             "eventDate" => '',
                             "eventImage" => 'media_srv/tmpl_images/' . $tmpls[$x]->id . '.png?' . date('YmdHis'),
                             "eventName" => $tmpls[$x]->templateName,
                             "id" => 'tpl' . $tmpls[$x]->id,
                             "lastmodified" =>  $modif_date->format('d M Y' ),
                             "userName" => '',
                             "createddate" => $created_date->format('d M Y' ),
                             "myplans" => 'my plan',
                             "isFolder" => 0,
                             "parentID" => $parent_id,
                             "clientID" => $tmpls[$x]->clientid
                             );
             


            //check is this template clientid is the same as user clientid
            if ( ($tmpls[$x]->clientid == -1) || ($tmpls[$x]->clientid != null) && ($tmpls[$x]->clientid == $userInfo->clientid)) {
                //$tmplNames = $tmplNames . $tmpls[$x]->id . "|" . '!' . $tmpls[$x]->templateName . "|";
                $newItem["myplans"] = 'not';
                $myArray[] = $newItem;
            } else {
                //add all default templates for trial users only
                if (($tmpls[$x]->templateDefault == 1) && ($userClientStatus != 10)) {
                      //$tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                    $myArray[] = $newItem;
                } else {
                    //check is this user is Admin or Support (except google templates
                    if (($userInfo->userIsAdmin || $userInfo->userIsSupport) && strpos(strtoupper($tmpls[$x]->templateName),'OOGLE') == false)
                    {
                        //$tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                        $myArray[] = $newItem;
                    }
                    else {
                        //check is this template assigned to this user
                        for ($y = 0; $y < $tmpllength; $y++) {
                            if ($tmpls[$x]->id == $userTemplates[$y]->templateid) {
                                //$tmplNames = $tmplNames . $tmpls[$x]->id . "|" . $tmpls[$x]->templateName . "|";
                               $myArray[] = $newItem;
                            }
                        }
                    }
                }
            }
        }

        $retr_arr["tmplNames"] = $myArray;
        $retr_arr["clientName"] = $userClientName;

        return $retr_arr;


    } 
    

    public function getClientName()
    {
        $Client=Client::findOne(['id' => $this->clientid]);
        if ($Client){
            return $Client->clientName;
        }
        else{
            return null;
        }
    }

    public static function getClientList()
    {
        $clients = \common\models\Client::find()->all();

        $items = ArrayHelper::map($clients,'id','clientName');
        return $items;
    }

    public function rules()
    {
        return [
            [['templateName', 'xmlCode'], 'required'],
            [['xmlCode','momentusSpaceDescr','momentusSpaceCode'], 'string'],
            [['templateActive','templateDefault','clientid','shadow','momentusEventSpaceDiagramId'], 'integer'],
            ['templateActive', 'default', 'value' => 1],
            [['templateName'], 'string', 'max' => 100],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
            [['image'], 'string'],
            ['image', 'default', 'value' => ''],
            ['subtemplate', 'validateSubtemplate', 'skipOnEmpty' => true],
         ];
    }

    public function validateSubtemplate($attribute)
    {
        $requiredValidator = new RequiredValidator();

//        foreach($this->$attribute as $index => $row) {
//            $error = null;
//            foreach (['subtemplatename', 'xmlCode'] as $name) {
//                $error = null;
//                $value = isset($row[$name]) ? $row[$name] : null;
//                $requiredValidator->validate($value, $error);
//                if (!empty($error)) {
//                    $key = $attribute . '[' . $index . '][' . $name . ']';
//                    $this->addError($key, $error);
//                }
//            }
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ED ID',
            'templateName' => 'ED Template Name',
            'xmlCode' => 'Xml Code',
            'templateActive' => 'Active',
            'created_at'=> 'Created',
            'updated_at'=> 'Updated',
            'templateDefault' => 'Default',
            'clientid' => 'Client',
            'shadow' => 'Shadow',
            'imageFile' => 'Template image',
            'image' => 'Image file name',
            'momentusSpaceDescr'=> 'Momentus Space',
            'momentusSpaceCode'=> 'Momentus Space Code',
            'momentusEventSpaceDiagramId' => 'Default EventSpaceDiagram ID',
        ];
    }

    public function init() {
        parent::init ();
        $this->templateActive  = 1;
    }

}
