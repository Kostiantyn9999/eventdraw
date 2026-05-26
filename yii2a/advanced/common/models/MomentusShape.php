<?php

namespace common\models;

use Yii;

/**
 * Model for the "shapes" table.
 *
 * @property int $id
 * @property int $source_id
 * @property string $shapeType
 * @property int $category
 * @property int $elevate
 * @property int $height
 * @property string|null $description
 * @property string $model
 * @property string|null $shapetypes
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $clientid
 */
class MomentusShape extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%shapes}}';
    }

    /**
     * Whether the shapes table has a clientid column (added by migration).
     */
    public static function hasClientIdColumn()
    {
        static $has = null;
        if ($has === null) {
            $schema = static::getTableSchema();
            $has = $schema !== null && isset($schema->columns['clientid']);
        }

        return $has;
    }

    public function rules()
    {
        $integerFields = ['source_id', 'category', 'elevate', 'height'];
        if (static::hasClientIdColumn()) {
            $integerFields[] = 'clientid';
        }

        return [
            [['source_id', 'shapeType', 'model'], 'required'],
            [$integerFields, 'integer'],
            [['shapeType', 'description', 'model', 'shapetypes'], 'string', 'max' => 255],
            [['source_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_id' => 'Source ID',
            'shapeType' => 'Shape Type',
            'category' => 'Category',
            'elevate' => 'Elevate',
            'height' => 'Height',
            'description' => 'Description',
            'model' => 'Model File',
            'shapetypes' => 'Shape Types',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'clientid' => 'Client',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'clientid']);
    }

    /**
     * All org-specific mappings for this shape.
     * @return \yii\db\ActiveQuery
     */
    public function getMomentusMappings()
    {
        return $this->hasMany(ShapeMomentusMapping::class, ['shape_id' => 'id']);
    }

    /**
     * Mappings filtered by a specific org_code.
     * @return \yii\db\ActiveQuery
     */
    public function getMomentusMappingsByOrg($orgCode)
    {
        return $this->hasMany(ShapeMomentusMapping::class, ['shape_id' => 'id'])
            ->andWhere(['org_code' => $orgCode])
            ->orderBy(['sequence' => SORT_ASC]);
    }
}
