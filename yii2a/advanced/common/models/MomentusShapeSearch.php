<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class MomentusShapeSearch extends MomentusShape
{
    public $mapping_resource_code;
    public $mapping_resource_description;
    public $mapping_resource_type;
    public $mapping_sequence;
    public $mapping_id;

    public function rules()
    {
        return [
            [['id', 'source_id', 'category', 'elevate', 'height'], 'integer'],
            [['shapeType', 'description', 'model', 'shapetypes'], 'safe'],
            [['mapping_resource_code', 'mapping_resource_description', 'mapping_resource_type', 'mapping_sequence', 'mapping_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param array $params query params
     * @param string $orgCode org_code to LEFT JOIN mapping table (empty = no join)
     */
    public function search($params, $orgCode = '')
    {
        $query = static::find()->alias('s');

        if ($orgCode !== '') {
            $query->leftJoin(
                '{{%shape_momentus_mapping}} m',
                's.id = m.shape_id AND m.org_code = :org_code',
                [':org_code' => $orgCode]
            );

            $query->addSelect([
                's.*',
                'm.momentus_resource_code AS mapping_resource_code',
                'm.momentus_resource_description AS mapping_resource_description',
                'm.momentus_resource_type AS mapping_resource_type',
                'm.sequence AS mapping_sequence',
                'm.id AS mapping_id',
            ]);
        }

        $sortConfig = [
            'defaultOrder' => ['shapeType' => SORT_ASC],
            'attributes' => [
                'id' => ['asc' => ['s.id' => SORT_ASC], 'desc' => ['s.id' => SORT_DESC]],
                'shapeType' => ['asc' => ['s.shapeType' => SORT_ASC], 'desc' => ['s.shapeType' => SORT_DESC]],
                'description' => ['asc' => ['s.description' => SORT_ASC], 'desc' => ['s.description' => SORT_DESC]],
            ],
        ];

        if ($orgCode !== '') {
            $sortConfig['attributes']['mapping_resource_code'] = [
                'asc' => ['m.momentus_resource_code' => SORT_ASC],
                'desc' => ['m.momentus_resource_code' => SORT_DESC],
            ];
            $sortConfig['attributes']['mapping_resource_description'] = [
                'asc' => ['m.momentus_resource_description' => SORT_ASC],
                'desc' => ['m.momentus_resource_description' => SORT_DESC],
            ];
            $sortConfig['attributes']['mapping_resource_type'] = [
                'asc' => ['m.momentus_resource_type' => SORT_ASC],
                'desc' => ['m.momentus_resource_type' => SORT_DESC],
            ];
            $sortConfig['attributes']['mapping_sequence'] = [
                'asc' => ['m.sequence' => SORT_ASC],
                'desc' => ['m.sequence' => SORT_DESC],
            ];
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sortConfig,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            's.id' => $this->id,
            's.source_id' => $this->source_id,
            's.category' => $this->category,
        ]);

        $query->andFilterWhere(['like', 's.shapeType', $this->shapeType])
            ->andFilterWhere(['like', 's.description', $this->description])
            ->andFilterWhere(['like', 's.model', $this->model]);

        if ($orgCode !== '') {
            $query->andFilterWhere(['like', 'm.momentus_resource_code', $this->mapping_resource_code])
                ->andFilterWhere(['like', 'm.momentus_resource_description', $this->mapping_resource_description])
                ->andFilterWhere(['like', 'm.momentus_resource_type', $this->mapping_resource_type]);
        }

        return $dataProvider;
    }
}
