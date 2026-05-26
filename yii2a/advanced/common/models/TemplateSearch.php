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
            [['id', 'created_at','updated_at','clientid','templateActive','templateDefault','shadow','matterportid','created_by'], 'integer'],
            //[['templateName', 'xmlCode','templateSize'], 'safe'],
            [['templateName', 'templateSize', 'momentusSpaceDescr', 'momentusSpaceCode', 'momentusEventSpaceDiagramId'], 'safe'],
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

        $query->select(['id',
                        'templateActive',
                        'templateDefault',
                        'clientid',
                        'shadow',
                        'templateSize',
                        'templateName',
                        'created_at',
                        'updated_at',
                        'matterportid',
                        'created_by']);
                        
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
            'matterportid' => $this->matterportid,
            'created_by' => $this->created_by,
        ]);



        if ($this->templateSize == 1) {
            //less 20 k
            $query->andFilterWhere(['<', 'templateSize', 20 ]);
        }
        else if ($this->templateSize == 2) {
            //more 20 k
            $query->andFilterWhere(['>', 'templateSize', 20 ]);
        }
        else if ($this->templateSize == 3) {
            //more 50 k
            $query->andFilterWhere(['>', 'templateSize', 50 ]);
        }
        else if ($this->templateSize == 4) {
            //more 100 k
            $query->andFilterWhere(['>', 'templateSize', 100 ]);
        }
        else if ($this->templateSize == 5) {
            //more 1 MB
            $query->andFilterWhere(['>', 'templateSize', 1 * 1024 ]);
        }
        else if ($this->templateSize == 6) {
            //more 10 MB
            $query->andFilterWhere(['>', 'templateSize', 10 * 1024]);
        }

        $query->andFilterWhere(['like', 'templateName', $this->templateName]);
        // $query->andFilterWhere(['like', 'templateName', $this->templateName])
        //     ->andFilterWhere(['like', 'xmlCode', $this->xmlCode]);

        return $dataProvider;
    }

    /**
     * Client-scoped search for the client admin app.
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchClient($params)
    {
        $dataProvider = $this->search($params);

        if (!Client::usesClientResourceScoping()) {
            return $dataProvider;
        }

        $dataProvider->query->andWhere(['clientid' => \Yii::$app->user->identity->clientid]);

        return $dataProvider;
    }
}
