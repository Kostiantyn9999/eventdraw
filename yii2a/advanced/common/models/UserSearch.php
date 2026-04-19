<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\web\View;

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
            [['id', 'maxSession','totSession','clientid','status', 'created_at','updated_at','last_login', 'userIsAdmin', 'userIsSupport', 'userPayment','company_admin','ShowMaxCapPlans'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'verification_token','userfullname','userStencil','firstname','surname'], 'safe'],
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
        $query = User::find();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'userIsAdmin' => $this->userIsAdmin,
            'userIsSupport' => $this->userIsSupport,
            'userPayment' => $this->userPayment,
            'maxSession' => $this->maxSession,
            'totSession' => $this->totSession,
            'clientid' => $this->clientid,
            'company_admin' => $this->company_admin,
            'ShowMaxCapPlans' => $this->ShowMaxCapPlans,

        ]);

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
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->email],
            ['like', 'firstname', $this->email],
            ['like', 'surname', $this->email],
            ['like', 'email', $this->email],
        ]);

        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->firstname],
            ['like', 'firstname', $this->firstname],
            ['like', 'surname', $this->firstname],
            ['like', 'email', $this->firstname],
        ]);


        $query->orFilterWhere([
            'or',
            ['like', 'username', $this->surname],
            ['like', 'firstname', $this->surname],
            ['like', 'surname', $this->surname],
            ['like', 'email', $this->surname],
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'userIsAdmin' => $this->userIsAdmin,
            'userIsSupport' => $this->userIsSupport,
            'userPayment' => $this->userPayment,
            'maxSession' => $this->maxSession,
            'totSession' => $this->totSession,
            'clientid' => $this->clientid,
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
