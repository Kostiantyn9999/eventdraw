<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii\data\SqlDataProvider;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property string $eventName
 * @property string $xmlCode
 * @property int $created_at
 * @property int $updated_at
 * @property string $eventdate
 * @property int $userid
 * @property int $eventActive
 * @property string $imageCode
 * @property string $eventInfo
 * @property int $event_shared
 
 */
class Event extends \yii\db\ActiveRecord
{

    public $future_event =0;
    public $eventid ='';
    public $last_event =0;
    public $clientName = '';
    public $totalSessions = '';
    public $userName = '';
    public $event_version;
    public $event_matterport;
    public $matterportTemplateID;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eventName', 'xmlCode', 'created_at', 'updated_at', 'eventdate','userid'], 'required'],
            [['xmlCode','imageCode','eventInfo'], 'string'],
            [['created_at', 'updated_at','userid','eventActive','event_shared'], 'integer'],
            [['momentus_space_diagram_id', 'momentus_event_id'], 'integer'],
            [['momentus_org_code', 'momentus_space_code'], 'string', 'max' => 50],
            [['eventdate','matterportTemplateID'], 'safe'],
            [['eventName'], 'string', 'max' => 100],
            ['event_version', 'default', 'value' => ''],
            ['event_matterport', 'default', 'value' => ''],
        ];
    }

public function getClientName()
    {
        $User=\common\models\User::findOne(['id' => $this->userid]);
        if ($User){
           $Client=\common\models\User::findOne(['id' => $this->userid]);
                if ($Client){
                    return $Client->clientName;
                }
                else{
                    return null;
                 }
        }
        else{
            return null;
        }

        
    }

    public static function getClientList()
    {
        // $clients = \common\models\Client::find()
        //     ->select(['id', 'clientName'])
        //     ->orderBy(['clientName' => SORT_ASC])->all();

        $dataProvider = new SqlDataProvider([
            'sql' => 'select id, clientName from client where id in (select DISTINCT clientid from user where id in (SELECT DISTINCT userid from events)) order by 2',
              'pagination' => false,
        ]);

        $clients = $dataProvider->getModels();

        $items = ArrayHelper::map($clients,'id','clientName');

        //replace & char to space to avoid incorrect show in list
        //  $arrlength = count($items);
        //  for($x = 0; $x < $arrlength; $x++) {
        //   $items[x]->clientName = str_replace($items[x]->clientName, '&' , 'new' );
        //  }
        //var_dump($clients);die;
        return $items;
    }


    public function getUserTotalSessions()
    {
        $User=\common\models\User::findOne(['id' => $this->userid]);
        if ($User){
            return $User->totSession;
        }
        else{
            return null;
        }
    }

    public function getUserName()
    {
        $User=\common\models\User::findOne(['id' => $this->userid]);
        if ($User){
            return $User->userfullname;
        }
        else{
            return null;
        }
    }

    public function getSize()
    {
        if ($this->xmlCode)
        {
            return round(strlen($this->xmlCode) / 1024, 2);
        }
        else {
            return null;
        }
        
    }

    public static function getUserList()
    {

        $users = \common\models\User::find()
            ->select(['id', 'userfullname'])
            ->orderBy(['userfullname' => SORT_ASC])->all();

        $items = ArrayHelper::map($users,'id','userfullname');
        return $items;
    }

    public static function findByID($id) {
        return static::findOne(['id' => $id, 'eventActive' => 1]);
    }

    public static function findByIDAll($id) {
        return static::findOne(['id' => $id]);
    }

    public static function findByName($eventname,$userid) {
        return static::findOne(['eventName' => $eventname, 'userid' => $userid, 'eventActive' => 1]);
    }

     public static function getUserEvents($userid, $includeImages)
    {
        //return all events for current user and all users the same company as user
        $evnts = \common\models\Event::find()
            ->select(['id', 'eventName','eventdate','userid','updated_at','eventActive','imageCode'])
            ->where(['eventActive' => 1])
            ->orderBy(['updated_at' => SORT_DESC])->all();

        $arrlength = count($evnts);
        $userInfo = \common\models\User::findIdentity($userid);

        $userClientID = \common\models\Client::findIdentity($userInfo->clientid);

        //if there is company for this user
        if ($userClientID)
        {
            $users = \common\models\User::find()
                ->select(['id','userfullname'])
                ->where(['clientid' => $userInfo->clientid])
                ->orderBy(['id' => SORT_ASC])->all();
        }
        else
        {
            $users = \common\models\User::find()
                ->select(['id','userfullname'])
                ->where(['id' => $userid])
                ->orderBy(['id' => SORT_ASC])->all();
        }

        $userlength = count($users);


        //now copy all need events to new array
        $evntsFiltered = [];
        $formatter = \Yii::$app->formatter;
        $formatter->dateFormat = 'yyyy-MM-dd';

        for($x = 0; $x < $arrlength; $x++) {
            for($y = 0; $y < $userlength; $y++) {
                if ($evnts[$x]->userid ==$users[$y]->id){
                    //replace userid with full name
                    $evnts[$x]->userid = $users[$y]->userfullname;
                    //replace updated_at with correct date format
                    $evnts[$x]->updated_at = $formatter->asDate($evnts[$x]->updated_at);

                    //if no need image, clear it value
                    if (!$includeImages) {
                        $evnts[$x]->imageCode = '';
                    }


                    //eventActive will show is it no event date(0), future(1) or past(-1)
                    if ( $evnts[$x]->eventdate == null)
                    {
                        $evnts[$x]->eventActive = 0;
                    }
                    else if ( $evnts[$x]->eventdate > date('Y-m-d'))
                    {
                        $evnts[$x]->eventActive = 1;
                    }
                    else
                    {
                        $evnts[$x]->eventActive = -1;
                    }
                    array_push($evntsFiltered,$evnts[$x])  ;
                    break;
                }
            }
        }

        return  $evntsFiltered;



    }


        public function saveNewVersion()
    {
        //get file with selected version and save it to database
        
        $s3 = Yii::$app->get('s3');
        $filename_looking = 'eventdraw_data/events/' .  $this->id . '.xml';
        $fileNameLocal = uniqid(rand(), true) . '.xml';
       
        $result = $s3->commands()->get($filename_looking)->withVersionId($this->event_version)->saveAs($fileNameLocal)->execute();
        $strXML = file_get_contents($fileNameLocal);

             //delete temp files
        if (!unlink($fileNameLocal)) {
         }

        //save to database
        $evnt = Event::findOne(['id' => $this->id]);
        if ($evnt){
            $evnt->xmlCode =  $strXML;
            //$evnt->eventSize = round(strlen($evnt->xmlCode) / 1024, 2);
            $evnt->save(false);
        }
        
        return true;
    }


    public function saveMatterport()
    {
        //remove all existing records from table matterport_assign for this event id
        
        $delete_matterport_assign=\common\models\MatterportAssign::find()
            ->where(['layout_id' =>$this->id])
            ->all();
        
        foreach($delete_matterport_assign as $delete)
        {
            $delete->delete();
        }

        if ($this->event_matterport)
        {
            //assign selected template for this event
            $Matterport_assign=new \common\models\MatterportAssign;
            $Matterport_assign->layout_id = $this->id;
            $Matterport_assign->matterport_id = $this->event_matterport;
                    
            $Matterport_assign->save(false);
        }
        
        
        //$this->event_version
        //save to database
        // $evnt = Event::findOne(['id' => $this->id]);
        // if ($evnt){
        //     $evnt->xmlCode =  $strXML;
        //     //$evnt->eventSize = round(strlen($evnt->xmlCode) / 1024, 2);
        //     $evnt->save(false);
        // }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventid' => 'ID',
            'eventName' => 'Cloud Floor plan',
            'xmlCode' => 'Xml Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'eventdate' => 'Event Date',
            'userid' => 'User',
            'eventActive' => 'Active',
            'imageCode' => 'Image',
            'eventInfo' => 'Event Info',
            'eventSize' => 'Size (KB)',
            'clientName'=> 'Client Name',
            'totalSessions' => 'Total Sessions',
            'userName' => 'User Name',
            'event_version'=> 'Event Version',
            'event_shared' => 'Shared',
            'event_matterport'=> 'Event Matterport',
            'matterportTemplateID' => 'Assigned Matterport',
            'momentus_space_diagram_id' => 'Momentus Space Diagram ID',
            'momentus_org_code' => 'Momentus Org Code',
            'momentus_event_id' => 'Momentus Event ID',
            'momentus_space_code' => 'Momentus Space Code',
        ];
    }
}
