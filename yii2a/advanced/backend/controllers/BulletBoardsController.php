<?php

namespace backend\controllers;

use Yii;
use common\models\NewUserBroadcastEmailTemplates;
use common\models\NewUserBroadcastEmailTemplatesSearch;
use common\models\BulletBoards;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\EmailTemplate;
use common\models\Client;
use common\models\userBulletinModel;
class BulletBoardsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NewUserBroadcastEmailTemplates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $NewUserBroadcastEmailTemplatesSearch = new NewUserBroadcastEmailTemplatesSearch();
        $dataProvider = NewUserBroadcastEmailTemplatesSearch::find()->indexBy('id')->orderBy(['id'=>SORT_DESC])->limit(150)->all();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NewUserBroadcastEmailTemplates model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionEligibleUsers()
    {
        $data = Yii::$app->request->post();

        return json_encode(EmailHelper::eligibleEmailUsers($data));
    }
    /**
     * Creates a new NewUserBroadcastEmailTemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($selected = '',$from='')
    {
        $model = new NewUserBroadcastEmailTemplates();

        $emailBroadCastTEmplate = new NewUserBroadcastEmailTemplates();
        $users = Yii::$app->request->post('users');


        foreach ($users as $email) {
            $user = User::find()->where(['email' => $email])->one();
        }
        $listofUserID     =  Yii::$app->request->post('user_id');


        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $record = $this->findModel($model->id);
            
            if (!empty($record)) {
                $listofUserID     =  Yii::$app->request->post('user_id');

                //print_r($listofUserID);die;



                $clientIDArray =array();
                $userIDArray = array();

                // Loop through the array and separate the IDs
                foreach ($listofUserID as $item) {
                    // Check if the item contains "client" or "user"
                    if (strpos($item, "client_") === 0) {
                        $clientID = str_replace('client_','',$item);
                        array_push($clientIDArray,$clientID);
                    } elseif (strpos($item, "user_") === 0) {
                        $userID = str_replace('user_','',$item);
                        array_push($userIDArray,$userID);
                    }else{
                        array_push($userIDArray,$item);
                    }
                }
                if(!empty($clientIDArray)){foreach($clientIDArray as $key=> $row){
                    $userBulletin   =   new userBulletinModel();
                    $client = Client::findOne($row);
                    $userBulletin['user_id']            =   $row;
                    $userBulletin['bullet_board_id']    =   $model->id;
                    $userBulletin['has_seen']           =   $model->no_of_time_bulletin_show;
                    $userBulletin['userType']           =   $client->clientType?$client->clientType:'0';
                    $userBulletin['email']              =   $client->clientEmail?$client->clientEmail:'0';
                    $userBulletin->save();
                } }

                foreach($userIDArray as $key=> $obj){
                    $userBulletin   =   new userBulletinModel();
                    $userInfo                           =   User::find()->where(['email' => $obj])->one();
                    //echo $userInfo->id.'<br/>';
                    $userBulletin['user_id']            =   $userInfo->id;
                    $userBulletin['bullet_board_id']    =   $model->id;
                    $userBulletin['has_seen']           =   $model->no_of_time_bulletin_show;
                    $userBulletin['userType']           =   $userInfo->userType?$userInfo->userType:'0';
                    $userBulletin['email']              =   $userInfo->email?$userInfo->email:'0';
                    $userBulletin->save();
                }
                //die;
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        Yii::$app->session->set('selectedUsers', []);
        Yii::$app->session->set('previouslySelectedUsers', []);
        if(!empty($_GET['from'])){
            $from   =    $_GET['from'];
        }else{
            $from   =   '';
        }
        $selectedUsers = array();
        $totalPages = 0;
        if (!empty($selected)) {
            if ($from == 'client') {
                $selectedClients = json_decode($selected);
                foreach ($selectedClients as $id) {
                    $client = Client::findOne($id);
                    // var_dump($client);
                    $all_client_users = User::find()->where(['clientid' => $id])->all();
                    // echo '<pre>';
                    // var_dump($all_client_users);
                    // die();
                    $new_client = array(
                            'email'=>$client->clientEmail,
                            'id'=>'client_'.$id
                        );
                        array_push($selectedUsers, $new_client);

                    if(!empty($all_client_users)){foreach($all_client_users as $row_user){
                        $new_row = array(
                            'email'=>$row_user->email,
                            'id'=>'user_'.$row_user->id
                        );
                        array_push($selectedUsers, $new_row);
                    }}
                    
                }
            } else {
                $userIds = json_decode($selected);
                foreach ($userIds as $id) {
                    $user = User::findOne($id);
                    //$selectedUsers[] = $user->email;
                    $new_row = array(
                        'email'=>$user->email,
                        'id'=>$id
                    );
                    array_push($selectedUsers, $new_row);
                }
            }
        }
        $email_template             =   new EmailTemplate();
        $tempData               =   EmailTemplate::find()->select(['id', 'template_name'])->where(['template_type' =>'bulletin-template'])->all();
        

        return $this->render('create', [
            'model' => $model,
            'selectedUsers'=>$selectedUsers,
            'dummy'=>"cm",
            'tempData'=>$tempData,
            'from'=>$from
        ]);
    }

    /**
     * Updates an existing NewUserBroadcastEmailTemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id,$from = '', $page = 1)
    {
        $model = $this->findModel($id);
        $selected      =   Yii::$app->session->get('selected');
        //print_r($selected);die;
        Yii::$app->session->set('selectedUsers', []);
        Yii::$app->session->set('previouslySelectedUsers', []);
        $selectedUsers = array();
        $totalPages = 0;
        if (!empty($selected)) {
            
            $userIds = json_decode($selected);
            foreach ($userIds as $id) {
                $user = User::findOne($id);
                //$selectedUsers[] = $user->email;
                $new_row = array(
                    'email'=>$user->email,
                    'id'=>$id
                );
                array_push($selectedUsers, $new_row);
            }
        }
        $users = Yii::$app->request->post('users');
        foreach ($users as $email) {
            $user = User::find()->where(['email' => $email])->one();
        }
        $userBulletin   =   new userBulletinModel();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $record = $this->findModel($model->id);
            if (!empty($record)) {
                $recordsToUpdate = \common\models\userBulletinModel::find()->where(['bullet_board_id' => $model->id])->all();

                // Delete each record
                foreach ($recordsToUpdate as $record) {
                    $record->has_seen = $model->no_of_time_bulletin_show;
                    $record->save();
                }
                //die;
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $email_template             =   new EmailTemplate();
        $tempData               =   EmailTemplate::find()->where(['template_type' =>'bulletin-template'])->all();
        $allBulletUser                =   \common\models\userBulletinModel::find()->where(['bullet_board_id'=>$id])->all();
        return $this->render('update', [
            'model' => $model,
            'selectedUsers'=>$selectedUsers,
            'tempData'=>$tempData,
            'allBulletUser'=>$allBulletUser
        ]);
    }


    public function actionCreateClient($selected = '')
    {
        $model = new NewUserBroadcastEmailTemplates();

        $emailBroadCastTEmplate = new NewUserBroadcastEmailTemplates();
        $users = Yii::$app->request->post('users');


        foreach ($users as $email) {
            $user = User::find()->where(['email' => $email])->one();
        }
        $listofUserID     =  Yii::$app->request->post('user_id');


        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $record = $this->findModel($model->id);
            
            if (!empty($record)) {
                $listofUserID     =  Yii::$app->request->post('user_id');
                $client_ids_array   =   array();
                foreach($listofUserID as $ids){
                    $client = Client::find()->where(['clientEmail' => $ids])->one();
                    $client_ids     =   array(
                        'id'=>$client['id'],
                        'clientType'=>$client['clientType'],
                        'email'=>$client['clientEmail']
                    );
                    
                    array_push($client_ids_array,$client_ids);
                } 
                $record->user_id =  implode(',',$client_ids_array);
                $record->save();
                $no_of_entries          =   count($client_ids_array);
                foreach($client_ids_array as $key=> $row){
                    $userBulletin   =   new userBulletinModel();
                    $userBulletin['user_id']            =   $row['id'];
                    $userBulletin['bullet_board_id']    =   $model->id;
                    $userBulletin['has_seen']           =   $model->no_of_time_bulletin_show;
                    $userBulletin['userType']           =   $row['clientType'];
                    $userBulletin['email']              =   $row['email'];
                    $userBulletin->save();
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        Yii::$app->session->set('selectedUsers', []);
        Yii::$app->session->set('previouslySelectedUsers', []);
        $from   =    $_GET['from'];
        $selectedUsers = array();
        $totalPages = 0;
        if (!empty($selected)) {
            if ($from == 'client') {
                $selectedClients = json_decode($selected);
                foreach ($selectedClients as $id) {
                    $client = Client::findOne($id);
                    
                    $new_row = array(
                        'email'=>$client->clientEmail,
                        'id'=>$id
                    );
                    array_push($selectedUsers, $new_row);
                }
            } else {
                $userIds = json_decode($selected);
                foreach ($userIds as $id) {
                    $user = User::findOne($id);
                    //$selectedUsers[] = $user->email;
                    $new_row = array(
                        'email'=>$user->email,
                        'id'=>$id
                    );
                    array_push($selectedUsers, $new_row);
                }
            }
        }
        $email_template             =   new EmailTemplate();
        $tempData               =   EmailTemplate::find()->select(['id', 'template_name'])->where(['template_type' =>'bulletin-template'])->all();
        

        return $this->render('create', [
            'model' => $model,
            'selectedUsers'=>$selectedUsers,
            'dummy'=>"cm",
            'tempData'=>$tempData,
            'from'=>$from
        ]);
    }
    public function actionBulletin()
    {
        $model = new NewUserBroadcastEmailTemplates();
       
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('form_bulletin', [
            'model' => $model,
        ]);
    }
    public function actionBupdate()
    {
        $model = new NewUserBroadcastEmailTemplates();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update_bulletin', [
            'model' => $model,
        ]);
    }
    

    /**
     * Deletes an existing NewUserBroadcastEmailTemplates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $userBulletinModel          =   new userBulletinModel();
        $recordsToDelete = userBulletinModel::find()->where(['bullet_board_id' => $id])->all();

        // Delete each record
        foreach ($recordsToDelete as $record) {
            $record->delete();
        }

        return $this->redirect(['index']);
    }

    public function actionBulkDeleteBulletin($selected){
        $bulletinArray          =   json_decode($selected);
        if(!empty($bulletinArray)){foreach ($bulletinArray as $obj) {
            $this->findModel($obj)->delete();

            $userBulletinModel          =   new userBulletinModel();
            $recordsToDelete = userBulletinModel::find()->where(['bullet_board_id' => $obj])->all();
            // Delete each record
            foreach ($recordsToDelete as $record) {
                $record->delete();
            }
        } }
        return $this->redirect(['index']);
    }

    public function actionAjaxBulkBulletinDelete(){
        $NewUserBroadcastEmailTemplates = new NewUserBroadcastEmailTemplates();

        $findAllBulletin = NewUserBroadcastEmailTemplates::find()->where(['!=', 'display_as', 'Onboarding'])->all();
        if(!empty($findAllBulletin)){foreach($findAllBulletin as $row){
            $userBulletinModel          =   new userBulletinModel();
            $recordsToDelete = UserBulletinModel::find()->where(['bullet_board_id' => $row->id])->andWhere(['!=', 'has_seen', 0])->all();

            // Delete each record
            //print_r($recordsToDelete);
            foreach ($recordsToDelete as $record) {
                $record->delete();
            }
        }}
        echo 'success';
        die();
    }
    /**
     * Finds the NewUserBroadcastEmailTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NewUserBroadcastEmailTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NewUserBroadcastEmailTemplates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
