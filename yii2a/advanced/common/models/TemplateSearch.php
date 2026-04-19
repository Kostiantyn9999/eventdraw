<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Template;

/**
 * TemplateSearch represents the model behind the search form of `common\models\Template`.
 */
class TemplateSearch extends Template
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at','updated_at','clientid','templateActive','templateDefault','shadow'], 'integer'],
            [['templateName', 'xmlCode','momentusSpaceDescr','momentusSpaceCode'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Template::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'templateActive' => $this->templateActive,
            'templateDefault' => $this->templateDefault,
            'clientid' => $this->clientid,
            'shadow' => $this->shadow,
        ]);

        $query->andFilterWhere(['like', 'templateName', $this->templateName])
            ->andFilterWhere(['like', 'xmlCode', $this->xmlCode])
            ->andFilterWhere(['like', 'momentusSpaceDescr', $this->momentusSpaceDescr])
            ->orFilterWhere(['like', 'momentusSpaceCode', $this->momentusSpaceDescr]);

        return $dataProvider;
    }
}
