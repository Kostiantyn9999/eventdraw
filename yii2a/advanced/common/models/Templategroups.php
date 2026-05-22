<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "templategroups".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $groupname
 */
class Templategroups extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'templategroups';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['groupname'], 'required'],
            [['groupname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'groupname' => 'Group Name',
        ];
    }
}
