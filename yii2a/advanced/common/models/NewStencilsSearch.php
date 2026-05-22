<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NewStencils;

/**
 * StencilSearch represents the model behind the search form of `common\models\Stencil`.
 */
class NewStencilsSearch extends NewStencils
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID', 'created_at', 'updated_at', 'stencilActive'], 'integer'],
            [['stencilName', 'stencilXML','userid'], 'safe'],
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
        $query = NewStencils::find()
        ->leftJoin('user', 'new_stencils.userid=user.id');


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
            'ID' => $this->ID,
            'stencilActive' => $this->stencilActive,
        ]);

        if ($this->created_at == '') { //any time
         }
         else if ($this->created_at == '0') { //last week
            $query->andFilterWhere(['>', 'new_stencils.created_at', time() - 60*60*24*7 ]);
         }
         else if ($this->created_at == '1') { //last 30 days
            $query->andFilterWhere(['>', 'new_stencils.created_at', time() - 60*60*24*30 ]);
         }
        else if ($this->created_at == '2') { //over 30 days
            $query->andFilterWhere(['<', 'new_stencils.created_at', time() - 60*60*24*30 ]);
         }
         else if ($this->created_at == '3') { //not set
            $query->andWhere(['new_stencils.created_at' => null]);
         }

        if ($this->updated_at == '') { //any time
         }
         else if ($this->updated_at == '0') { //last week
            $query->andFilterWhere(['>', 'new_stencils.updated_at', time() - 60*60*24*7 ]);
         }
         else if ($this->updated_at == '1') { //last 30 days
            $query->andFilterWhere(['>', 'new_stencils.updated_at', time() - 60*60*24*30 ]);
         }
        else if ($this->updated_at == '2') { //over 30 days
            $query->andFilterWhere(['<', 'new_stencils.updated_at', time() - 60*60*24*30 ]);
         }
         else if ($this->updated_at == '3') { //not set
            $query->andWhere(['new_stencils.updated_at' => null]);
         }


        $query->andFilterWhere(['like', 'stencilName', $this->stencilName])
            ->andFilterWhere(['like', 'stencilXML', $this->stencilXML])
            ->andFilterWhere(['like', 'user.userfullname', $this->userid]);

        return $dataProvider;
    }
}
