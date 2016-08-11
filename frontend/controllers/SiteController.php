<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use app\models\UserExt;
use app\models\tao\Statements;
use app\models\tao\Models;
use app\models\tao\ResultsStorage;
use app\models\tao\VariablesStorage;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index2');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


    public function actionResult($id) {

     $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
$result_rdf = $tao_model->modeluri . 'i'. $id;
     echo 'result id :' . $result_rdf;
     echo '<hr/>';

    $result_statements = Statements::find()->andWhere(['subject' => $id])->All();
     foreach ($result_statements as $result_statement) {
      echo '<br/>=======================' . $result_statement->object;
     }

     $items = ["http://tao.ppsdm.com/ppsdm.rdf#i147080619178261339.item-3.0", "http://tao.ppsdm.com/ppsdm.rdf#i147080619178261339.item-5.0",
     "http://tao.ppsdm.com/ppsdm.rdf#i147080619178261339.item-8.0",
    "http://tao.ppsdm.com/ppsdm.rdf#i147080619178261339.item-10.0"];

   $pcas_item = ["http://tao.ppsdm.com/ppsdm.rdf#i147080619178261339.item-11.0"];
     //$items = ['http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-3.0','http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-1.0'];
    //$result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result->result_id])->groupBy('item, identifier')->All();
    $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
    ->andWhere(['in','call_id_item',$items])
    //->groupBy('item')
    ->groupBy('item, identifier')
    ->orderBy('variable_id ASC')
    //->OrWhere(['identifier' => 'SCORE'])
      //  ->OrWhere(['identifier' => 'RESPONSE'])
    //->OrWhere(['identifier' => 'LtiOutcome'])
    ->All();
    $score_array = [];
    foreach ($result_vars as $result_var) {

    //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
    if (strpos($result_var->identifier, 'RESPONSE') !== false) {
         //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
         $strpos = strpos($result_var->value, '{');
        $valuestring = substr($result_var->value, $strpos);
         $exploded_result_var = explode(';',$valuestring);
            $index = 0;
         foreach($exploded_result_var as $singular_result_var) {

            $ret = explode(':', $singular_result_var);
            if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                $value = explode(':', $exploded_result_var[$index + 1])[2];
                echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
            } else {
           //  echo '<br/>sasasa'. $singular_result_var;
            }

              $index++;
              }

    }
    if ($result_var->identifier == 'SCORE') {
//    echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

    $strpos = strpos($result_var->value, '{');
   $valuestring = substr($result_var->value, $strpos);
    $exploded_result_var = explode(';',$valuestring);
       $index = 0;
    foreach($exploded_result_var as $singular_result_var) {

       $ret = explode(':', $singular_result_var);
       if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

           $value = explode(':', $exploded_result_var[$index + 1])[2];
           echo '<br/>' . $result_var->call_id_item . '('.$exploded_result_var[$index]. ')  = ' . base64_decode($value);
           array_push($score_array, base64_decode($value));
       } else {
      //  echo '<br/>sasasa'. $singular_result_var;
       }

         $index++;
         }

    }



    }
    echo '<pre>';
print_r($score_array);
echo '</pre>';
echo '<hr/>';
//echo Url::to(['post/view', 'id' => 100]);
echo Html::a('Print Result', ['site/print', 'id' => $id], ['class' => 'profile-link']);

    }


    public function actionHasil()
    {
     //$
     $model = User::find()->andWhere(['username' => Yii::$app->user->identity->username])->One();


     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['object' => Yii::$app->user->identity->username])
     ->One();


     ob_start();
      ob_end_clean();

     return $this->render('hasil', [
        'model' => $model,
        'user' => $user
     ]);

    }
    public function actionDatapeserta()
    {

     $model = User::find()->andWhere(['username' => Yii::$app->user->identity->username])->One();
     $model2 = UserExt::find()->andWhere(['username' => Yii::$app->user->identity->username])->One();
          if (isset($model2)) {
//echo '<br/><br/><br/><br/>model 2 exzists';
          } else {
           $model2 = new UserExt();
           $model2->username = $model->username;
          }

             //if ($model2->load(Yii::$app->request->post()) && $model2->validate()) {
             if(Yii::$app->request->post()) {
                  //     echo '<pre>';
                       $model2->username = $_POST['UserExt']['username'];
                       $model2->firstname = $_POST['UserExt']['firstname'];
                       $model2->lastname = $_POST['UserExt']['lastname'];
                       $model2->tempat_lahir = $_POST['UserExt']['tempat_lahir'];
                       $model2->tanggal_lahir = $_POST['UserExt']['tanggal_lahir'];
                       $model2->jabatan_dilamar = $_POST['UserExt']['jabatan_dilamar'];
                       $model2->pendidikan_terakhir = $_POST['UserExt']['pendidikan_terakhir'];
                       $model2->tujuan_pemeriksaan = $_POST['UserExt']['tujuan_pemeriksaan'];
                       $model2->tempat = $_POST['UserExt']['tempat'];
                       $model2->tanggal_test = $_POST['UserExt']['tanggal_test'];

                     //print_r($model2);
                       if($model2->save()) {

                        Yii::$app->session->setFlash('success', 'data berhasil disimpan');
                       } else {
                        print_r($model2->getErrors());
                       }


          //    echo '</pre>';
             } else {
             }


     return $this->render('datapeserta', ['model' => $model, 'model2' => $model2]);



    }


    public function actionPrint($id)
    {
     $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();

     $result_rdf = $tao_model->modeluri . 'i'. $id;

     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();



     ob_start();
      ob_end_clean();
     //return $this->render('psikotes', ['model'=>$model]);

    }
}
