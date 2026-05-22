<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\web\View;
use yii\db\Expression;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'maxSession', 'totSession', 'clientid', 'status', 'created_at', 'updated_at', 'last_login', 'userIsAdmin', 'userIsSupport', 'userPayment', 'company_admin', 'ShowMaxCapPlans', 'is_subscribed', 'email_count','userType','AllowSaveCloud','userSavedFloorplansCount','UserDoNotEmail','siDate'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'verification_token', 'userfullname', 'userStencil', 'firstname', 'surname', 'last_email_date','UserCompanyName','UserCountry'], 'safe'],
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
        $query = User::find()
        ->leftJoin('client', 'user.clientid=client.id');

        // ->where(['clientid' => \Yii::$app->user->identity->clientid]);

   
    $dataProvider = new ActiveDataProvider([
    'query' =>  $query,
    'sort' => [
            'attributes' => [
                'id',
                'clientid',
                 'firstname',
                 'surname',
                 'email',
                 'username',
                 'last_login' => [
                    'asc' => ['last_login' => SORT_DESC,],
                    'desc' => ['last_login' => SORT_ASC],
                ],
                 'totSession',
                 'status',
                 'userType',
                'expiry_date' => [
                    'asc' => ['expiry_date' => SORT_DESC,],
                    'desc' => ['expiry_date' => SORT_ASC],
                ],
                 'userPayment',
                'created_at' => [
                    'asc' => ['created_at' => SORT_DESC,],
                    'desc' => ['created_at' => SORT_ASC],
                ],
                'is_subscribed',
                'email_count',
                'last_email_date',
                'AllowSaveCloud',
                'userSavedFloorplansCount',
                'UserCompanyName',
                'UserCountry',
                'UserDoNotEmail',
                'company_admin',
                'siDate'
                
            ]
        ]
    ]);

        //$dataProvider->sort->defaultOrder = ['last_login' => SORT_DESC, 'expiry_date' => SORT_DESC, 'created_at' => SORT_DESC];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
            'updated_at' => $this->updated_at,
            'userIsAdmin' => $this->userIsAdmin,
            'userIsSupport' => $this->userIsSupport,
            'userPayment' => $this->userPayment,
            'AllowSaveCloud' => $this->AllowSaveCloud,
            'maxSession' => $this->maxSession,
            'totSession' => $this->totSession,
            'clientid' => $this->clientid,
            'company_admin' => $this->company_admin,
            'ShowMaxCapPlans' => $this->ShowMaxCapPlans,
            'is_subscribed' => $this->is_subscribed,
            'userType' => $this->userType,
            'email_count' => $this->email_count,
            'UserCompanyName' => $this->UserCompanyName,
            'UserCountry' => $this->UserCountry,
            'UserDoNotEmail' => $this->UserDoNotEmail,
            'user.siDate' =>$this->siDate
        ]);

        if ($this->last_login == '') { //any time
         }
         else if ($this->last_login == '0') { //last week
            $query->andFilterWhere(['>', 'last_login', time() - 60*60*24*7 ]);
         }
         else if ($this->last_login == '1') { //last 30 days
            $query->andFilterWhere(['>', 'last_login', time() - 60*60*24*30 ]);
         }
        else if ($this->last_login == '2') { //over 30 days
            $query->andFilterWhere(['<', 'last_login', time() - 60*60*24*30 ]);
         }
         else if ($this->last_login == '3') { //not set
            $query->andWhere(['last_login' => null]);
         }

         /////////////////////

          if ($this->created_at == '') { //any time
         }
         else if ($this->created_at == '0') { //last week
            $query->andFilterWhere(['>', 'user.created_at', time() - 60*60*24*7 ]);
         }
         else if ($this->created_at == '1') { //last 30 days
            $query->andFilterWhere(['>', 'user.created_at', time() - 60*60*24*30 ]);
         }
        else if ($this->created_at == '2') { //over 30 days
            $query->andFilterWhere(['<', 'user.created_at', time() - 60*60*24*30 ]);
         }
         else if ($this->created_at == '3') { //not set
            $query->andWhere(['user.created_at' => null]);
         }



         /////////////////////
        if ($this->last_email_date == '') { //any time
            
         }
         else if ($this->last_email_date == '0') { //last week
            //  $query->andFilterWhere(['>', 'last_email_date', time() - 60*60*24*7 ]);
             $query->andFilterWhere(['>', 'last_email_date', new Expression('DATE_SUB(CURDATE(), INTERVAL 7 DAY)') ]);

          }
         else if ($this->last_email_date == '1') { //last 30 days
            // $query->andFilterWhere(['>', 'last_email_date', time() - 60*60*24*30 ]);
            $query->andFilterWhere(['>', 'last_email_date', new Expression('DATE_SUB(CURDATE(), INTERVAL 30 DAY)') ]);

         }
        else if ($this->last_email_date == '2') { //over 30 days
            //$query->andFilterWhere(['<', 'last_email_date', time() - 60*60*24*30 ]);
            $query->andFilterWhere(['<', 'last_email_date', new Expression('DATE_SUB(CURDATE(), INTERVAL 30 DAY)') ]);

         }
           else if ($this->last_email_date == '3') { //not set
            $query->andWhere(['last_email_date' => null]);
         }





        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'verification_token', $this->verification_token])
            ->andFilterWhere(['like', 'userfullname', $this->userfullname])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'surname', $this->surname]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->username],
            ['like', 'firstname', $this->username],
            ['like', 'surname', $this->username],
            ['like', 'email', $this->username],
            ['like', 'clientName', $this->username],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->email],
            ['like', 'firstname', $this->email],
            ['like', 'surname', $this->email],
            ['like', 'email', $this->email],
            ['like', 'clientName', $this->email],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->firstname],
            ['like', 'firstname', $this->firstname],
            ['like', 'surname', $this->firstname],
            ['like', 'email', $this->firstname],
            ['like', 'clientName', $this->firstname],
        ]);


        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->surname],
            ['like', 'firstname', $this->surname],
            ['like', 'surname', $this->surname],
            ['like', 'email', $this->surname],
            ['like', 'clientName', $this->surname],
        ]);
        return $dataProvider;
    }

    //---------------------------------------------
    public function searchClient($params)
    {
        $query = User::find()
            ->where(['clientid' => \Yii::$app->user->identity->clientid]);
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
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'userIsAdmin' => $this->userIsAdmin,
            'userIsSupport' => $this->userIsSupport,
            'userPayment' => $this->userPayment,
            'maxSession' => $this->maxSession,
            'totSession' => $this->totSession,
            'clientid' => $this->clientid,
            'userType' => $this->userType,
            'company_admin' => $this->company_admin,

        ]);



        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'userfullname', $this->userfullname])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'verification_token', $this->verification_token])
            ->andFilterWhere(['like', 'userfullname', $this->userfullname]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->username],
            ['like', 'userfullname', $this->username],
            ['like', 'email', $this->username],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->userfullname],
            ['like', 'userfullname', $this->userfullname],
            ['like', 'email', $this->userfullname],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->email],
            ['like', 'userfullname', $this->email],
            ['like', 'email', $this->email],
        ]);

        return $dataProvider;
    }
}
