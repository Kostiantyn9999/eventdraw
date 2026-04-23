<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
 * @property string $event_uuid
 * @property int|null $momentus_space_diagram_id
 * @property string|null $momentus_org_code
 * @property int|null $momentus_event_id
 * @property string|null $momentus_space_code
 */
class Event extends \yii\db\ActiveRecord
{

    public $future_event =0;
    public $last_event =0;

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
            [['xmlCode','imageCode','eventInfo','event_uuid'], 'string'],
            [['created_at', 'updated_at','userid','eventActive'], 'integer'],
            [['momentus_space_diagram_id', 'momentus_event_id'], 'integer'],
            [['momentus_org_code', 'momentus_space_code'], 'string', 'max' => 50],
            [['eventdate'], 'safe'],
            [['eventName'], 'string', 'max' => 100],
        ];
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

         $query = \common\models\Event::find()
        ->select(['id', 'eventName', 'eventdate', 'userid', 'updated_at', 'eventActive', 'event_uuid'])
        ->where(['eventActive' => 1])
        ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
        // ->asArray()
        ;


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

         $EventFolders = \common\models\ClientEventFolders::findAll([
            'clientid' => $userInfo->clientid
        ]);

        $folderlength = count($EventFolders);


        //now copy all need events to new array
        $evntsFiltered = [];
        $formatter = \Yii::$app->formatter;
        $formatter->dateFormat = 'yyyy-MM-dd';

        

        
        foreach ($query->each(100) as $row) {

            for($y = 0; $y < $userlength; $y++) {
                if ($row->userid ==$users[$y]->id){
                    //replace userid with full name

                    $row->eventInfo =$row->userid;
                    $row->userid = $users[$y]->userfullname;
                    //replace updated_at with correct date format
                    $row->updated_at = $formatter->asDate($row->updated_at);
                    //if no need image, clear it value
                    if (!$includeImages) {
                        $row->imageCode = '';
                    }


                    //eventActive will show is it no event date(0), future(1) or past(-1)
                    if ( $row->eventdate == null)
                    {
                        $row->eventActive = 0;
                    }
                    else if ( $row->eventdate > date('Y-m-d'))
                    {
                        $row->eventActive = 1;
                    }
                    else
                    {
                        $row->eventActive = -1;
                    }
                    array_push($evntsFiltered,$row)  ;
                    break;
                }
            }
        }


        $evntsResult = [];

        //create folders assigned for this client
        for($z = 0; $z < $folderlength; $z++) {
            if( $EventFolders[$z]->eventid == -1)
            {
                $newItem = array(
                             "eventActive" => 0,
                             "eventName" => $EventFolders[$z]->folderName,
                             "event_uuid" => '',
                             "eventdate" => '',
                             "id" =>  $EventFolders[$z]->id,
                             "imageCode" =>  '',
                             "updated_at" => '',
                             "userid" => '',
                             "isFolder" => 1,
                             "parentID" => $EventFolders[$z]->parentid,
                             "clientID" => $userInfo->clientid

                             );

                array_push($evntsResult,$newItem)  ;            
            }
        }

        $resultlength = count($evntsFiltered);

        $test = '';
        for($x = 0; $x < $resultlength; $x++) {
        
            

            $parent_id = 0;
            for($z = 0; $z < $folderlength; $z++) {

                if( $EventFolders[$z]->eventid == $evntsFiltered[$x]->id)
                {
                    $parent_id = $EventFolders[$z]->parentid;
                    break; 
                }
            }

            $UserClients=\common\models\User::findOne(['id' => $evntsFiltered[$x]->eventInfo]);
            if ($UserClients){
               $clientID = $UserClients->clientid;
            }
            else
            {
               $clientID = null; 
            }

             $newItem = array(
                             "eventActive" => $evntsFiltered[$x]->eventActive,
                             "eventName" => $evntsFiltered[$x]->eventName,
                             "event_uuid" => $evntsFiltered[$x]->event_uuid,
                             "eventdate" => $evntsFiltered[$x]->eventdate,
                             "id" => $evntsFiltered[$x]->id,
                             "imageCode" =>  $evntsFiltered[$x]->imageCode,
                             "updated_at" => $evntsFiltered[$x]->updated_at,
                             "userid" => $evntsFiltered[$x]->userid,
                             "isFolder" => 0,
                             "parentID" => $parent_id,
                             "clientID" => $clientID
                             );

                array_push($evntsResult,$newItem)  ;   

        }


        return  $evntsResult;



    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'eventName' => 'Event Name',
            'xmlCode' => 'Xml Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'eventdate' => 'Event Date',
            'userid' => 'User',
            'eventActive' => 'Event Active',
            'imageCode' => 'Image',
            'eventInfo' => 'Event Info',
            'event_uuid'=> 'Event UUID',
            'momentus_space_diagram_id' => 'Momentus Space Diagram ID',
            'momentus_org_code' => 'Momentus Org Code',
            'momentus_event_id' => 'Momentus Event ID',
            'momentus_space_code' => 'Momentus Space Code',
        ];
    }
}
