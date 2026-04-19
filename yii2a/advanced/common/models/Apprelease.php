<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db;

/**
 * This is the model class for table "apprelease".
 *
 * @property int $id
 * @property int $nrelease
 * @property string $description
 * @property string $htmlCode
 * @property int $created_at
 * @property int $updated_at
 */
    class Apprelease extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apprelease';
    }

    public function behaviors()
    {
        return [
                TimestampBehavior::className(),
            ];
    }

    public static function getLastReleaseHTML($userid)
        {

            //get how many times this release video was showed to user
            if (Yii::$app->user->identity->nrelease < 1000) {
                //if this release was shown
                $modelrec = Apprelease::find()
                    ->orderBy(['nrelease' => SORT_DESC])
                    ->one();

                return $modelrec->htmlCode;
            }
            else
            {
                return "";
            }
        }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nrelease', 'description'], 'required'],
            [['nrelease', 'created_at', 'updated_at'], 'integer'],
            [['htmlCode'], 'string'],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nrelease' => 'Release number',
            'description' => 'Description',
            'htmlCode' => 'Html Code',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
        ];
    }
}
