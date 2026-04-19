<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;

/**
 * ClientSearch represents the model behind the search form of `common\models\Client`.
 */
class ClientSearch extends Client
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'clientActive', 'created_at','updated_at','clientPayment','ShowMaxCapPlans','AllowSaveCloud','status','AllowFavouriteStencils'], 'integer'],
            [['clientName', 'clientEmail'], 'safe'],
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
        $query = Client::find();

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
            'clientActive' => $this->clientActive,
            'clientPayment' => $this->clientPayment,
            'ShowMaxCapPlans' => $this->ShowMaxCapPlans,
            'AllowSaveCloud' => $this->AllowSaveCloud,
            'AllowFavouriteStencils' => $this->AllowFavouriteStencils,
        ]);

        $query->andFilterWhere(['like', 'clientName', $this->clientName])
            ->andFilterWhere(['like', 'clientEmail', $this->clientEmail]);

        $query->orFilterWhere([
            'or',
            ['like', 'clientName', $this->clientName],
            ['like', 'clientEmail', $this->clientName],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'clientName', $this->clientEmail],
            ['like', 'clientEmail', $this->clientEmail],
        ]);

        return $dataProvider;
    }
}
