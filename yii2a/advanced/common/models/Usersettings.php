<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "usersettings".
 *
 * @property int $id
 * @property int $userid
 * @property string $meas_unit
 * @property string $copy_dist
 * @property int $localDir
 */
class Usersettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usersettings';
    }

    public static function getMeasurementName($MeasurementCode)
    {
        if ($MeasurementCode =='M') {
            return 'Meters';
        }
        else if ($MeasurementCode =='FT') {
            return 'Feet';
        }
        else {
            return 'Both (Meters and Feet)';
        }
    }

  public static function getLocalDirName($localDir)
    {
        if ($localDir ==1) {
            return 'Yes';
        }
       
        else {
            return 'No';
        }
    }

    public static function setUserSettings($userid, $meas_unit, $localDir)
    {

        $UserSettings=  Usersettings::findOne(['userid' => $userid]);
        if ($UserSettings){
            $UserSettings->meas_unit  =$meas_unit;
            $UserSettings->localDir  =$localDir;
            $UserSettings->save();
        }
        else{
            $UserSettings= new Usersettings();
            $UserSettings->userid = $userid;
            $UserSettings->meas_unit = $meas_unit;
            $UserSettings->localDir  =$localDir;
            $UserSettings->copy_dist = 1;

            $UserSettings->save(false);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid'], 'required'],
            [['userid','localDir'], 'integer'],
            [['meas_unit'], 'string', 'max' => 255],
            [['copy_dist'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'meas_unit' => 'Measurement Units',
            'localDir' => 'Local Directory'
        ];
    }
}
