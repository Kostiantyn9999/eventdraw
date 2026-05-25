<?php
namespace client\controllers;


use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use common\models\LoginFormClient;
use common\models\Client;
use common\components\MomentusClient;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                    'actions' => ['logout', 'index', 'account-code', 'ajax-set-account-code', 'ajax-get-org-name', 'ajax-get-organizations'],
                    'allow' => true,
                    'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }



    public function actionUser()
    {
        return $this->render('user');
    }

    /**
     * Account Code (Momentus OrgCode) management page.
     */
    public function actionAccountCode()
    {
        $clientId = Yii::$app->user->identity->clientid;
        $client = Client::findOne($clientId);
        if (!$client) {
            throw new \yii\web\NotFoundHttpException('Client not found.');
        }
        return $this->render('account-code', [
            'client' => $client,
        ]);
    }

    /**
     * AJAX POST: save the Account Code (OrgCode) for the current client.
     */
    public function actionAjaxSetAccountCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $clientId = Yii::$app->user->identity->clientid;
        $client = Client::findOne($clientId);
        if (!$client) {
            throw new BadRequestHttpException('Client not found.');
        }

        $code = trim((string) Yii::$app->request->post('account_code', ''));
        $client->momentusOrgCode = $code !== '' ? $code : null;

        if ($client->save(false, ['momentusOrgCode'])) {
            return ['ok' => true, 'account_code' => $client->momentusOrgCode];
        }
        return ['ok' => false, 'errors' => $client->errors];
    }

    /**
     * AJAX GET: return available organizations from Momentus.
     */
    public function actionAjaxGetOrganizations()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $search = trim((string) Yii::$app->request->get('search', ''));
        $mc = MomentusClient::create();
        return ['ok' => true, 'organizations' => $mc->getOrganizations($search !== '' ? $search : null)];
    }

    /**
     * AJAX GET: return the organisation name for a given org code from Momentus.
     */
    public function actionAjaxGetOrgName()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $code = trim((string) Yii::$app->request->get('code', ''));
        if ($code === '') {
            return ['ok' => false, 'name' => null];
        }
        $mc = MomentusClient::create(['orgCode' => $code]);
        $name = $mc->getOrganizationName($code);
        return ['ok' => true, 'name' => $name];
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginFormClient();


        if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
