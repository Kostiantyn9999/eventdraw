<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use common\models\Event;

/**
 * EventSearch represents the model behind the search form of `common\models\Event`.
 */
class EventSearch extends Event
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'userid', 'eventActive','eventid','totalSessions','event_shared'], 'integer'],
            [['eventName', 'xmlCode', 'eventdate', 'eventInfo','clientName','userName','matterportTemplateID'], 'safe'],
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
        $query = (new \yii\db\Query())
        ->select(['e.id as eventid', 'e.eventName as eventName' , 'u.userfullname as userName', 
                  'u.clientid as clientid', 'c.clientname as clientName',
                  'e.imageCode as imageCode', 'e.created_at as created_at', 'e.updated_at as updated_at',
                  'e.eventActive as eventActive', 'u.totSession as totalSessions', 'e.event_shared as event_shared'])
                //   'm.matterport_id as matterportTemplateID'])
        ->from('events e')
        ->leftJoin('user u', 'u.id = e.userid')
        ->leftJoin('client c', 'c.id = u.clientid');
        // ->leftJoin('matterport_assign m', 'm.layout_id = e.id');
        // ->leftJoin('xmltemplate t', 't.id = m.matterport_id ');


        // add conditions that should always apply here
              
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'eventid',
                    'eventName',
                    'userName',
                    'clientName',
                    'created_at',
                    'updated_at',
                    'eventActive',
                    'totalSessions',
                    'event_shared'
                ]
            ]
        ]);

           
            
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // if ($this->matterportTemplateID == 1)
        // {
        //      $query->andFilterWhere(['>', 'm.matterport_id',0]);

        // }
        // else if ($this->matterportTemplateID == 0)
        // {
        //      $query->andFilterWhere(['<=', 'm.matterport_id',0]);

        // }
        // grid filtering conditions
        $query->andFilterWhere([
            'e.id' => $this->eventid,
            'u.clientid' => $this->clientName,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'eventActive' => $this->eventActive,
            'u.totSession' => $this->totalSessions,
            'event_shared' => $this->event_shared,
        ]);

        $query->andFilterWhere(['like', 'eventName', $this->eventName])
              ->andFilterWhere(['like', 'u.userfullname', $this->userName]);
            // ->andFilterWhere(['like', 'eventInfo', $this->eventInfo]);

        return $dataProvider;
    }
}
