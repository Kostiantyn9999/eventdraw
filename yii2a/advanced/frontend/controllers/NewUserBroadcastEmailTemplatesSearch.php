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
            [['id','hours','userType','no_of_time_delay','no_of_time_bulletin_show'], 'integer'],
            [['heading', 'template_image', 'link_heading', 'link', 'button_link','display_as'], 'safe'],
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
        //$query = NewUserBroadcastEmailTemplates::find()->orderBy('order');
        $query = NewUserBroadcastEmailTemplates::find()->where(['userType' => 1])->orderBy('order');
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

    public function search_event_organizer($params)
    {
        $query = NewUserBroadcastEmailTemplates::find()->where(['userType' => 2])->orderBy('order');

        // add conditions that should always apply here

        $dataOrganizer = new ActiveDataProvider([
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

        return $dataOrganizer;
    }

    public function searchBulletin($params)
    {
        //$query = NewUserBroadcastEmailTemplates::find()->orderBy('order');
        $query = NewUserBroadcastEmailTemplates::find()->where(['userType' => 1,'display_as'=>'Yes'])->orderBy('order');
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

    public function searchBulletinOrg($params)
    {
        $query = NewUserBroadcastEmailTemplates::find()->where(['userType' => 2,'display_as'=>'Yes'])->orderBy('order');

        // add conditions that should always apply here

        $dataOrganizer = new ActiveDataProvider([
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

        return $dataOrganizer;
    }


    public function getAllBulletin($params)
    {      
        // echo $params['NewUserBroadcastEmailTemplatesSearch']['heading'];
        // print_r($params['NewUserBroadcastEmailTemplatesSearch']['heading']);
        // print_r($params['heading']);
        // $query = NewUserBroadcastEmailTemplates::find()
        //     ->where(['!=', 'display_as', 'Onboarding'])
        //     ->orderBy(['id' => SORT_DESC])
        //     ->->leftJoin('bulletboarduserdata as UB', 'T.id = UB.bullet_board_id');

        // if (!empty($params['NewUserBroadcastEmailTemplatesSearch']['heading'])) {
        //     $query->andWhere(['like', 'heading', $params['NewUserBroadcastEmailTemplatesSearch']['heading']]);
        // }

        $query = (new Query())
            ->select('T.*,UB.user_id,U.userfullname')
            ->from('new_user_broadcast_email_templates as T')
            ->leftJoin('bulletboarduserdata as UB', 'T.id = UB.bullet_board_id')
            ->leftJoin('users as U', 'U.id= UB.user_id')
            ->where(['!=', 'T.display_as', 'Onboarding'])
            ->orderBy(['T.id' => SORT_DESC])
            ->all();

        
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

        //$query->andFilterWhere(['like', 'heading', $params['NewUserBroadcastEmailTemplatesSearch']['heading']])->andFilterWhere(['like', 'button_link', $this->button_link]);

        return $dataProvider;
    }

}
