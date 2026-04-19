<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "client_event_folders".
 *
 * @property int $id
 * @property int $clientid
 * @property int $eventid
 * @property string $folderName
 * @property int $parentid
 */
class ClientEventFolders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_event_folders';
    }

    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clientid', 'eventid', 'folderName', 'parentid'], 'required'],
            [['clientid', 'eventid', 'parentid'], 'integer'],
            [['folderName'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clientid' => 'Client',
            'eventid' => 'Event',
            'folderName' => 'Folder Name',
            'parentid' => 'Parent',
        ];
    }
}
