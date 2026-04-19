<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NewUserBroadcastEmailTemplates;

/**
 * NewUserBroadcastEmailTemplatesSearch represents the model behind the search form of `common\models\NewUserBroadcastEmailTemplates`.
 */
class NewUserBroadcastEmailTemplatesSearch extends NewUserBroadcastEmailTemplates
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','hours'], 'integer'],
            [['heading', 'template_image', 'link_heading', 'link', 'button_link'], 'safe'],
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
        $query = NewUserBroadcastEmailTemplates::find()->orderBy('order');

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
            'hours' => $this->hours,

        ]);

        $query->andFilterWhere(['like', 'heading', $this->heading])
            ->andFilterWhere(['like', 'template_image', $this->template_image])
            ->andFilterWhere(['like', 'link_heading', $this->link_heading])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'button_link', $this->button_link]);

        return $dataProvider;
    }
}
