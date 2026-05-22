<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Client;
use Yii;

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
            [['id', 'clientActive', 'created_at','updated_at','clientPayment','ShowMaxCapPlans','AllowSaveCloud','status','AllowFavouriteStencils','Allow3D','AllowImportPdf','AllowShare','clientType','last_client_login','last_client_email','siDate','ClientVenueType','client_max_sessions','AllowSaveFolder'], 'integer'],
            [['clientName', 'clientNotes','clientEmail','clientCountry'], 'safe'],
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
        //   $query = Client::find();

        $query = Client::find()
        ->select([
            '{{client}}.*', 
            'MAX({{user}}.totSession) AS client_max_sessions' 
        ])
        ->leftJoin('user', '`user`.`clientid` = `client`.`id`')
        ->groupBy('{{client}}.id') ;



        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        // ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'clientName',
                    'clientEmail',
                    'clientCountry',
                    'last_client_login',
                    'status',
                    'clientType',
                    'ClientVenueType',
                    'clientNotes',
                    'created_at',
                    'siDate',
                    'updated_at',
                    'clientPayment',
                    'ShowMaxCapPlans',
                    'AllowSaveCloud',
                    'AllowFavouriteStencils',
                    'Allow3D',
                    'AllowImportPdf',
                    'AllowShare',
                    'expiry_date',
                    'last_client_email',
                    'AllowSaveFolder',


                    'client_max_sessions' => [
                        'asc' => ['MAX(user.totSession)' => SORT_ASC],
                        'desc' => ['MAX(user.totSession)' => SORT_DESC],
                        'label' => 'Max Sessions',
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'client.id' => $this->id,
            'clientActive' => $this->clientActive,
            'clientPayment' => $this->clientPayment,
            'client.ShowMaxCapPlans' => $this->ShowMaxCapPlans,
            'client.AllowSaveCloud' => $this->AllowSaveCloud,
            'client.AllowFavouriteStencils' => $this->AllowFavouriteStencils,
            'client.AllowShare' => $this->AllowShare,
            'client.Allow3D' => $this->Allow3D,
            'client.status' => $this->status,
            'client.AllowImportPdf' => $this->AllowImportPdf,
            'clientType' => $this->clientType,
            'clientCountry' =>$this->clientCountry,
            'client.siDate' =>$this->siDate,
            'ClientVenueType'=>$this->ClientVenueType,
            'client.AllowSaveFolder' => $this->AllowSaveFolder

        ]);

      

         if ($this->last_client_login == '') { //any time
         }
         else if ($this->last_client_login == '0') { //last week
            $query->andFilterWhere(['>', 'last_client_login', time() - 60*60*24*7 ]);
         }
         else if ($this->last_client_login == '1') { //last 30 days
            $query->andFilterWhere(['>', 'last_client_login', time() - 60*60*24*30 ]);
         }
        else if ($this->last_client_login == '2') { //over 30 days
            $query->andFilterWhere(['<', 'last_client_login', time() - 60*60*24*30 ]);
         }
         else if ($this->last_client_login == '3') { //not set
            $query->andWhere(['last_client_login' => null]);
         }

        ///////////////////////////////
         if ($this->created_at == '') { //any time
         }
         else if ($this->created_at == '0') { //last week
            $query->andFilterWhere(['>', 'client.created_at', time() - 60*60*24*7 ]);
         }
         else if ($this->created_at == '1') { //last 30 days
            $query->andFilterWhere(['>', 'client.created_at', time() - 60*60*24*30 ]);
         }
        else if ($this->created_at == '2') { //over 30 days
            $query->andFilterWhere(['<', 'client.created_at', time() - 60*60*24*30 ]);
         }
         else if ($this->created_at == '3') { //not set
            $query->andWhere(['client.created_at' => null]);
         }



         /////////////////////


        if ($this->last_client_email == '') { //any time
            
         }
         else if ($this->last_client_email == '0') { //last week
            $query->andFilterWhere(['>', 'last_client_email', time() - 60*60*24*7 ]);
         }
         else if ($this->last_client_email == '1') { //last 30 days
            $query->andFilterWhere(['>', 'last_client_email', time() - 60*60*24*30 ]);
         }
        else if ($this->last_client_email == '2') { //over 30 days
            $query->andFilterWhere(['<', 'last_client_email', time() - 60*60*24*30 ]);
         }
           else if ($this->last_client_login == '3') { //not set
            $query->andWhere(['last_client_email' => null]);
         }

        $query->andFilterWhere(['like', 'clientNotes', $this->clientNotes]);

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

        // 'filter'=>array("1"=>"0-100","2"=>"101-500","3"=>"501-1000","4"=>"More 1000"),
         if ($this->client_max_sessions == '') { //any time
         }
         else if ($this->client_max_sessions == '1') { //0-100
            $query->having('client_max_sessions <= 100');
         } 
         else if ($this->client_max_sessions == '2') { //101-500
            $query->having('client_max_sessions > 100 and client_max_sessions <= 500');
         } 
         else if ($this->client_max_sessions == '3') { //501-1000
            $query->having('client_max_sessions > 500 and client_max_sessions <= 1000');
         } 
         else if ($this->client_max_sessions == '4') { //More 1000
            $query->having('client_max_sessions > 1000');
         } 

        return $dataProvider;
    }
}
