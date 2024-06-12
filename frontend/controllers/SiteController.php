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
use app\models\core\PcasResponseMap;
use app\models\core\PcasRangeMap;
use app\models\core\PcasIpaRef;
use app\models\core\ScaleRef;
use app\models\core\PcasGrafikRef;
use app\models\tao\ResultsStorage;
use app\models\tao\VariablesStorage;
use app\models\Adjustment;
use yii\helpers\Url;
use yii\helpers\Html;

use linslin\yii2\curl;



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

     $user = [];
     //$user['user'] = 'reno';


        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {


            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();

                    //print_r($user);


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


















    public function actionCfitstaff($id)
    {
        $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
        $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
        $result_rdf = $tao_model->modeluri . 'i'. $id;
   
        /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
        $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
        ->andWhere(['subject' => $result_storage->test_taker])
        ->One();
        $model = UserExt::find()->andWhere(['username' => $user->object])->One();
        if(!isset($model)) {
         $model = new UserExt;
        }
          $total_cfit = 0;
        $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];
   
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
        $cfit_score_array = [];
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
   
              $total_cfit = $total_cfit + base64_decode($value);
              array_push($cfit_score_array, base64_decode($value));
          }
            $index++;
            }
        }
        }
    }







    public function actionHasilcfit()
    {
     //$
     $model = User::find()->andWhere(['username' => Yii::$app->user->identity->username])->One();


     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['object' => Yii::$app->user->identity->username])
     ->One();


     ob_start();
      ob_end_clean();

     return $this->render('hasilcfit', [
        'model' => $model,
        'user' => $user
     ]);

    }

    public function actionHasilnew()
    {
     //$
    //  $model = User::find()->andWhere(['username' => Yii::$app->user->identity->username])->One();


     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     //buattest 
     ->andWhere(['object' => 'warid_satu'])
    //  ->andWhere(['object' => Yii::$app->user->identity->username])
     ->One();


     ob_start();
      ob_end_clean();

     return $this->render('hasilcat', [
        // 'model' => $model,
        'user' => $user
     ]);

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
























    public function actionTparesult($id)
    {
        $testdelivery_id = 'http://tao.ppsdm.com/ppsdm.rdf#i154511336540213107';

        $result_ids = ['1545113459700213108','1545113459700213108'];
        $all_scores = [];
        foreach($result_ids as $result_id) {

        

         $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
         $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $result_id])->One();
         $result_rdf = $tao_model->modeluri . 'i'. $id;
    
         /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
         $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
         ->andWhere(['subject' => $result_storage->test_taker])
         ->One();
         $model = UserExt::find()->andWhere(['username' => $user->object])->One();
         if(!isset($model)) {
          $model = new UserExt;
         }
           $total_cfit = 0;
      //   $items = [$result_rdf .".item-1.0",$result_rdf .".item-3.0",$result_rdf .".item-4.0",$result_rdf .".item-5.0"];
    
         //$items = ['http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-3.0','http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-1.0'];
         //$result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result->result_id])->groupBy('item, identifier')->All();
         $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
         //->andWhere(['in','call_id_item',$items])
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
         /*if (strpos($result_var->identifier, 'RESPONSE') !== false) {
             //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
             $strpos = strpos($result_var->value, '{');
            $valuestring = substr($result_var->value, $strpos);
             $exploded_result_var = explode(';',$valuestring);
                $index = 0;
             foreach($exploded_result_var as $singular_result_var) {
    
                $ret = explode(':', $singular_result_var);
                if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {
    
                    $value = explode(':', $exploded_result_var[$index + 1])[2];
    
                }
    
                  $index++;
                  }
    
         }
         */

         $subtest = ['TPA-Subtest1', 'TPA-Subtest2', 'TPA-Subtest3', 'TPA-Subtest4', 'TPA-Subtest5', 'TPA-Subtest6' ,'TPA-Subtest7', 'TPA-Subtest8', 'TPA-Subtest9'];
         if ($result_var->identifier == 'SCORE') {
         //    echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;
    
         $strpos = strpos($result_var->value, '{');
        $valuestring = substr($result_var->value, $strpos);
         $exploded_result_var = explode(';',$valuestring);
           $index = 0;
    
         foreach($exploded_result_var as $singular_result_var) {
    
           $ret = explode(':', $singular_result_var);
           if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {
            
            $i = 0;
            while (isset($subtest[$i])) {
            if (strpos($result_var->call_id_item, $subtest[$i]) !== false) {
              //  echo 'true ' . $subtest[$i] . '<br/>';
                if(!isset($score_array[$subtest[$i]])){
                    $score_array[$subtest[$i]] = 0;
                }

            
               $value = explode(':', $exploded_result_var[$index + 1])[2];
    
             // $score_array[$result_var->call_id_item] = base64_decode($value);
              $score_array[$subtest[$i]] = $score_array[$subtest[$i]] + base64_decode($value);
               //array_push($score_array, base64_decode($value));
            }
            $i++;
        }
           }
             $index++;
             }
         }
         }
echo '<pre>';

$score_array['nama'] = 'reno';


$score_array['total_verbal'] = $score_array['TPA-Subtest1'] + $score_array['TPA-Subtest2'] + $score_array['TPA-Subtest3'];
$score_array['total_kuantitatif'] = $score_array['TPA-Subtest4'] + $score_array['TPA-Subtest5'] + $score_array['TPA-Subtest6'];
$score_array['total_penalaran'] = $score_array['TPA-Subtest7'] + $score_array['TPA-Subtest8'] + $score_array['TPA-Subtest9'];

$score_array['score_verbal'] = ($score_array['total_verbal'] / 60 * 60) + 20;
$score_array['score_kuantitatif'] = ($score_array['total_kuantitatif'] / 45 * 60) + 20;
$score_array['score_penalaran'] = ($score_array['total_penalaran'] / 35 * 60) + 20;

$score_array['score_tpa'] = ($score_array['score_verbal'] + $score_array['score_kuantitatif'] + $score_array['score_penalaran'] ) / 3 * 10;

$all_scores[$result_id] = $score_array;
        }
        print_r($all_scores);
        echo json_encode($all_scores);
    }
    
    



public function actionCfitResult($id)
{


    /*** item-9, 10, 11, 12 */
	 $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
     $result_rdf = $tao_model->modeluri . 'i'. $id;

     /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
      $model = new UserExt;
     }
       $total_cfit = 0;
     $items = [$result_rdf .".item-9.0",$result_rdf .".item-10.0",$result_rdf .".item-11.0",$result_rdf .".item-12.0"];
     $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$items])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     ->All();
     $cfit_score_array = [];
     foreach ($result_vars as $result_var) {

      echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
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

            }

              $index++;
              }

     }
     if ($result_var->identifier == 'SCORE') {
       //  echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

     $strpos = strpos($result_var->value, '{');
     $valuestring = substr($result_var->value, $strpos);
     $exploded_result_var = explode(';',$valuestring);
       $index = 0;

     foreach($exploded_result_var as $singular_result_var) {

       $ret = explode(':', $singular_result_var);
       if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

           $value = explode(':', $exploded_result_var[$index + 1])[2];

           echo $total_cfit = $total_cfit + base64_decode($value);
           array_push($cfit_score_array, base64_decode($value));
       }
         $index++;
         }
     }
     }

     $pcas_item = [$result_rdf .".item-11.0"];

     $pcas_results = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$pcas_item])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     ->All();


     $pcas_score_array = [];
     foreach ($pcas_results as $result_var) {

     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
          $strpos = strpos($result_var->value, '{');
         $valuestring = substr($result_var->value, $strpos);
          $exploded_result_var = explode(';',$valuestring);
             $index = 0;
          foreach($exploded_result_var as $singular_result_var) {

             $ret = explode(':', $singular_result_var);
             if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                 $value = explode(':', $exploded_result_var[$index + 1])[2];
              //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                 if ($result_var->identifier != 'RESPONSE') {
                  $trimmed = trim(base64_decode($value), "[]");
                  $trimmed_items = explode(";", $trimmed);
                  $trimmed_array = [];
                  foreach($trimmed_items as $trimmed_item) {
                   $trimmed_trimmed = trim($trimmed_item, " ");
                   $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                   if (sizeof($trimmed_trimmed_items) > 1) {
                   $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                  }
                  }

                $pcas_score_array[$result_var->identifier] = $trimmed_array;
                }
             }

               $index++;
               }

     }
    }




     $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();


     #return $this->render('psikotes', ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values]);

return ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled


];


}

public function actionResultstaffcat($id) {
  $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
  $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
  $result_rdf = $tao_model->modeluri . 'i'. $id;


  $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
  ->andWhere(['subject' => $result_storage->test_taker])
  ->One();
  $model = UserExt::find()->andWhere(['username' => $user->object])->One();
  if(!isset($model)) {
   $model = new UserExt;
  }


  $total_cfit = 0;
  $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

  $result_vars = VariablesStorage::find()
  ->andWhere(['results_result_id' => $result_rdf])
  ->andWhere(['in','call_id_item',$items])

  ->groupBy('item, identifier')
  // ->orderBy('variable_id ASC')
  ->All();
  $cfit_score_array = [];
  // foreach ($result_vars as $result_var) {

  // //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
  // if (strpos($result_var->identifier, 'RESPONSE') !== false) {
  //     //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
  //     $strpos = strpos($result_var->value, '{');
  //    $valuestring = substr($result_var->value, $strpos);
  //     $exploded_result_var = explode(';',$valuestring);
  //        $index = 0;
  //     foreach($exploded_result_var as $singular_result_var) {

  //        $ret = explode(':', $singular_result_var);
  //        if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

  //            $value = explode(':', $exploded_result_var[$index + 1])[2];

  //        }

  //          $index++;
  //          }

  // }
  // if ($result_var->identifier == 'SCORE') {
  // //    echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

  // $strpos = strpos($result_var->value, '{');
  // $valuestring = substr($result_var->value, $strpos);
  // $exploded_result_var = explode(';',$valuestring);
  //   $index = 0;

  // foreach($exploded_result_var as $singular_result_var) {

  //   $ret = explode(':', $singular_result_var);
  //   if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

  //       $value = explode(':', $exploded_result_var[$index + 1])[2];

  //       $total_cfit = $total_cfit + base64_decode($value);
  //       array_push($cfit_score_array, base64_decode($value));
  //   }
  //     $index++;
  //     }
  // }
  // }






}

public function biodataProcessor($id,$model)
{
    $biodata = [];
    $biodata['nama'] = '';
    $biodata['jabatan'] = '';
    $biodata['prospek_jabatan'] = '';
    $biodata['tempat_lahir'] = '';
    $biodata['tanggal_lahir'] = '';
    $biodata['tujuan_test'] = '';
    $biodata['usia'] = '';
    $biodata['handphone'] = '';
    $biodata['email'] = '';
    $biodata['jenis_kelamin'] = '';
    $biodata['pendidikan_terakhir'] = '';
    $biodata['tanggal_test'] = '';

    $result_storage = ResultsStorage::find()->andWhere(['like', 'result_id', '%'. 'i'. $id.'%', false])->One();
//        $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;

    $total_cfit = 0;

//    return $id;
    $result_vars = VariablesStorage::find()
        ->andWhere(['like','results_result_id','i'.$id])
        ->andWhere(['like','call_id_item','.biodata_'])
        ->groupBy('item, identifier')
        ->orderBy('variable_id ASC')

        ->All();

       
    foreach ($result_vars as $result_var) {
        $biodata_keys = ['jenis_kelamin', 'tempat_lahir', 'email', 'handphone', 'tanggal_test', 'nama', 'tanggal_lahir', 'usia', 'pendidikan_terakhir','jabatan', 'prospek_jabatan','tujuan_test'];
//        if ($result_var->identifier == 'SCORE') {
            if (in_array($result_var->identifier,$biodata_keys)) {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;
                $strpos = strpos($result_var->value, '{');
                $valuestring = substr($result_var->value, $strpos);
                $exploded_result_var = explode(';',$valuestring);
                $index = 0;
                foreach($exploded_result_var as $singular_result_var) {

                    $ret = explode(':', $singular_result_var);
                    if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                        $value = explode(':', $exploded_result_var[$index + 1])[2];
                        $biodata[$result_var->identifier] = base64_decode($value);

                    }

                    $index++;
                }
        }
    }

return $biodata;
}
    public function apmProcessor($id,$debug)
    {
        $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
        $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
//        $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;

        $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
            ->andWhere(['subject' => $result_storage->test_taker])
            ->One();
        $model = UserExt::find()->andWhere(['username' => $user->object])->One();
        if(!isset($model)) {
            $model = new UserExt;
        }
        $total_cfit = 0;


        $result_vars = VariablesStorage::find()
            ->andWhere(['like','results_result_id','i'.$id])
            ->andWhere(['like','call_id_item','.apm_'])
            ->groupBy('item, identifier')
            ->orderBy('variable_id ASC')

            ->All();
        $cfit_score_array = [];
        foreach ($result_vars as $result_var) {


            if ($result_var->identifier == 'SCORE') {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

                $strpos = strpos($result_var->value, '{');
                $valuestring = substr($result_var->value, $strpos);
                $exploded_result_var = explode(';',$valuestring);
                $index = 0;

                foreach($exploded_result_var as $singular_result_var) {

                    $ret = explode(':', $singular_result_var);
                    if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

                        $value = explode(':', $exploded_result_var[$index + 1])[2];

                        $total_cfit = $total_cfit + base64_decode($value);
                        array_push($cfit_score_array, base64_decode($value));
                    }
                    $index++;
                }
            }
        }

        return $total_cfit;
    }

    public function cfitProcessor($id,$model)
    {

        $result_storage = ResultsStorage::find()->andWhere(['like', 'result_id', '%'. 'i'. $id.'%', false])->One();
        $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;

//        $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
//            ->andWhere(['subject' => $result_storage->test_taker])
//            ->One();
//        $model = UserExt::find()->andWhere(['username' => $user->object])->One();
//        if(!isset($model)) {
//            $model = new UserExt;
//        }
        $total_cfit = 0;
        $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

        $result_vars = VariablesStorage::find()
            ->andWhere(['like','results_result_id','i'.$id])
//            ->andWhere(['in','call_id_item',$items])
            ->andWhere(['like','call_id_item','.cfit_'])
            ->groupBy('item, identifier')
            ->orderBy('variable_id ASC')

            ->All();
        $cfit_score_array = [];
        foreach ($result_vars as $result_var) {


            /***
             * this part does not matter if we're not looking for the responses and just the score
             *
             *
             */
//     echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
            if (strpos($result_var->identifier, 'RESPONSE') !== false) {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
                $strpos = strpos($result_var->value, '{');
                $valuestring = substr($result_var->value, $strpos);
                $exploded_result_var = explode(';',$valuestring);
                $index = 0;
                foreach($exploded_result_var as $singular_result_var) {

                    $ret = explode(':', $singular_result_var);
                    if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                        $value = explode(':', $exploded_result_var[$index + 1])[2];

                    }

                    $index++;
                }

            }



            if ($result_var->identifier == 'SCORE') {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

                $strpos = strpos($result_var->value, '{');
                $valuestring = substr($result_var->value, $strpos);
                $exploded_result_var = explode(';',$valuestring);
                $index = 0;

                foreach($exploded_result_var as $singular_result_var) {

                    $ret = explode(':', $singular_result_var);
                    if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

                        $value = explode(':', $exploded_result_var[$index + 1])[2];

                        $total_cfit = $total_cfit + base64_decode($value);
                        array_push($cfit_score_array, base64_decode($value));
                    }
                    $index++;
                }
            }
        }

        return $total_cfit;
    }


public function discProcessor($id,$model)
{

    $result_storage = ResultsStorage::find()->andWhere(['like', 'result_id', '%'. 'i'. $id.'%', false])->One();
    $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;

    $pcas_results = VariablesStorage::find()
//        ->andWhere(['results_result_id' => $result_rdf])
        ->andWhere(['like','results_result_id','i'.$id])
        ->andWhere(['like','call_id_item','DISC'])
        //->groupBy('item')
        ->groupBy('item, identifier')
        ->orderBy('variable_id ASC')
        ->All();



    $pcas_score_array = [];
    $pcas_index = 1;
    foreach ($pcas_results as $result_var) {

        if ((strpos($result_var->identifier, 'RESPONSE') !== false) || (strpos($result_var->identifier, 'DISC') !== false)){
//            echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ';
            $strpos = strpos($result_var->value, '{');
            $valuestring = substr($result_var->value, $strpos);
            $exploded_result_var = explode(';',$valuestring);
            $index = 0;
            foreach($exploded_result_var as $singular_result_var) {

                $ret = explode(':', $singular_result_var);
                if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                    $value = explode(':', $exploded_result_var[$index + 1])[2];
//                    echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                    if ($result_var->identifier != 'RESPONSE') {
                        $trimmed = trim(base64_decode($value), "[]");
                        $trimmed_items = explode(";", $trimmed);
                        $trimmed_array = [];
                        foreach($trimmed_items as $trimmed_item) {
                            $trimmed_trimmed = trim($trimmed_item, " ");
                            $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                            if (sizeof($trimmed_trimmed_items) > 1) {
                                $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                            }
                        }



                        $pcas_score_array[$result_var->identifier] = $trimmed_array;
                    }
                }

                $index++;
            }

        }
    }


    $pcas_aspect_array = [];
    $pcas_aspect_array['a'] = 0;
    $pcas_aspect_array['b'] = 0;
    $pcas_aspect_array['c'] = 0;
    $pcas_aspect_array['d'] = 0;
    $pcas_aspect_array['e'] = 0;
    $pcas_aspect_array['f'] = 0;
    $pcas_aspect_array['g'] = 0;
    $pcas_aspect_array['h'] = 0;
    $pcas_aspect_array['i'] = 0;
    $pcas_aspect_array['j'] = 0;

//    echo 'pcas score array<pre>';
//    print_r($pcas_score_array);

    foreach ($pcas_score_array as $key => $item) {

        if (sizeof($item) > 1) {

            if (isset($item['M'][0])) {
                $pcas_aspect_array[strtolower($item['M'][0])]++;
            } else {

            }
            if (isset($item['L'][1]) && ($item['L'][1] != '_')) {
                $pcas_aspect_array[strtolower($item['L'][1])]++;
            } else {

            }


        } else {
            //  echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
        }
    }

    return $pcas_aspect_array;
}
public function actionManagerprintaws($id)
{
    $object = $this->actionManagerresultaws($id,false);

     $adjustments = [];
     $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
     foreach ($adjustmentModel as $adjustmodel){
         $adjustments[$adjustmodel->key] = $adjustmodel->value;
     }
 //
//     echo '<pre>';
//     print_r($object);
     return $this->render('mbss_aws_manager', ['id'=>$object['id'], 'adjustments' => $adjustments, 'biodata'=>$object['biodata'],'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], 'papikostik' => $object['papikostik'], "data" => $object]);

}


public function actionStaffprintawsdebug($id)
{
    $object = $this->actionStaffresultaws($id,true);

    $adjustments = [];
    $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
    foreach ($adjustmentModel as $adjustmodel){
        $adjustments[$adjustmodel->key] = $adjustmodel->value;
    }

//    echo '<pre>';
//    foreach($object as $ob) {
//        print_r($ob);
//    }


}
public function actionStaffprintaws($id)
{
    $object = $this->actionStaffresultaws($id,false);

    $adjustments = [];
    $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
    foreach ($adjustmentModel as $adjustmodel){
        $adjustments[$adjustmodel->key] = $adjustmodel->value;
    }


    return $this->render('mbss_aws_staff', ['id'=>$id, 'adjustments'=>$adjustments,'biodata'=>$object['biodata'],'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object]);

}

    public function actionStaff2024print($id)
    {
        $object = $this->actionStaff2024($id,false);

        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }


        return $this->render('mbss_aws_staff', ['id'=>$id, 'adjustments'=>$adjustments,'biodata'=>$object['biodata'],'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object]);

    }

    public function actionStaffresultbyemail($email, $deliveryId)
    {

        $resultController = new ResultController($deliveryId,$this);
        $id = $resultController->actionGetresultid($email, $deliveryId);
        $resultController->actionGetscore($email, $deliveryId);


        $object = $this->actionStaff2024($id,false);

        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

        $scores = $this->getScores($id, $object);
//        print_r($scores);

        return $this->render('mbss_aws_staff_portal', ['id'=>$id, 'adjustments'=>$adjustments,'biodata'=>$object['biodata'],'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object, 'scores' => $scores]);

    }

public function getScores($id, $object)
{

    $biodata = $object['biodata'];
    $model = $object['model'];;
    $cfit = $object['cfit'];
    $ipa_values = $object['ipa_values'];

    $res = [];

    $nama           = $biodata['nama'];
    $no             = $id;
    $jabatanlamar   = $biodata['prospek_jabatan'];

    $ttl            = $biodata['tempat_lahir'] .', ' . $biodata['tanggal_lahir'];
    $tujuan         = $biodata['tujuan_test'];
    $pendidikan     = $biodata['pendidikan_terakhir'];
    $tgltes         = $biodata['tanggal_test'];
    $tempattes      = $model->tempat;
    $ttd            = "Drs. Herry Sardjono, Psikolog";
    $himpsi         = "0101188383";

    $namaaspek = "GENERAL INTELLIGENCE";
    $judul     = "PSIKOGRAM HASIL ASSESSMENT / PEMERIKSAAN PSIKOLOGIS";

    $bobot1 = 19; // General Intelligence

    $bobot3 = 9; // Interpersonal Understanding
    $bobot4 = 9; // Stabilitas Emosi
    $bobot6 = 9; // Kepercayaan diri
    $bobot10 = 9; // Kemandirian bobot

    $bobot2 = 9; // Achievement motivation
    $bobot5 = 9; // Pengambilan resiko
    $bobot7 = 9; // Inisiatif
    $bobot8 = 9; // Kerjasama
    $bobot9 = 9; // Ketekunan

    $total_bobot =  $bobot1 + $bobot2 + $bobot3+ $bobot4+ $bobot5+ $bobot6+ $bobot7+ $bobot8+ $bobot9+ $bobot10;

    $total_min = 0;
    $total_pribadi = 0;
    $total_max = 0;

    $min1 = 1 * $bobot1;$min2 = 1 * $bobot2;$min3 = 1 * $bobot3;$min4 = 1 * $bobot4;$min5 = 1 * $bobot5;$min6 = 1 * $bobot6;$min7 = 1 * $bobot7;$min8 = 1 * $bobot8;$min9 = 1 * $bobot9;$min10 = 1 * $bobot10;
    $max1 = 7 * $bobot1;$max2 = 7 * $bobot2;$max3 = 7 * $bobot3;$max4 = 7 * $bobot4;$max5 = 7 * $bobot5;$max6 = 7 * $bobot6;$max7 = 7 * $bobot7;$max8 = 7 * $bobot8;$max9 = 7 * $bobot9;$max10 = 7 * $bobot10;

    $total_min = $min1 + $min2 + $min3 + $min4 + $min5 + $min6 + $min7 + $min8 + $min9 + $min10;
    $total_max = $max1 + $max2 + $max3 + $max4 + $max5 + $max6 + $max7 + $max8 + $max9 + $max10;



    $rating1 = $cfit->scaled;
    $rating2 = $ipa_values->trait_1;
    $rating3 = $ipa_values->trait_2;
    $rating4 = $ipa_values->trait_3;
    $rating5 = $ipa_values->trait_4;
    $rating6 = $ipa_values->trait_5;
    $rating7 = $ipa_values->trait_6;
    $rating8 = $ipa_values->trait_7;
    $rating9 = $ipa_values->trait_8;
    $rating10 = $ipa_values->trait_9;

    if(isset($adjustments['general_intelligence'])){
        $rating1 = $adjustments['general_intelligence'];
    }
    if(isset($adjustments['interpersonal_understanding'])){
        $rating3 = $adjustments['interpersonal_understanding'];
    }
    if(isset($adjustments['stabilitas_emosi'])){
        $rating4 = $adjustments['stabilitas_emosi'];
    }
    if(isset($adjustments['kepercayaan_diri'])){
        $rating6 = $adjustments['kepercayaan_diri'];
    }
    if(isset($adjustments['pengambilan_resiko'])){
        $rating5 = $adjustments['pengambilan_resiko'];
    }
    if(isset($adjustments['achievement_motivation'])){
        $rating2 = $adjustments['achievement_motivation'];
    }
    if(isset($adjustments['kemandirian'])){
        $rating10 = $adjustments['kemandirian'];
    }
    if(isset($adjustments['inisiatif'])){
        $rating7 = $adjustments['inisiatif'];
    }
    if(isset($adjustments['kerjasama'])){
        $rating8 = $adjustments['kerjasama'];
    }
    if(isset($adjustments['ketekunan'])){
        $rating9 = $adjustments['ketekunan'];
    }

    if(isset($adjustments['kepemimpinan'])){
        $rating11 = $adjustments['kepemimpinan'];
    }




    $rating1_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating1'])->One();
    if(null !== $rating1_adj) {
        $rating1 = $rating1_adj->value;
    }
    $rating2_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating2'])->One();
    if(null !== $rating2_adj) {
        $rating2 = $rating2_adj->value;
    }
    $rating3_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating3'])->One();
    if(null !== $rating3_adj) {
        $rating3 = $rating3_adj->value;
    }
    $rating4_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating4'])->One();
    if(null !== $rating4_adj) {
        $rating4 = $rating4_adj->value;
    }
    $rating5_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating5'])->One();
    if(null !== $rating5_adj) {
        $rating5 = $rating5_adj->value;
    }
    $rating6_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating6'])->One();
    if(null !== $rating6_adj) {
        $rating6 = $rating6_adj->value;
    }
    $rating7_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating7'])->One();
    if(null !== $rating7_adj) {
        $rating7 = $rating7_adj->value;
    }
    $rating8_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating8'])->One();
    if(null !== $rating8_adj) {
        $rating8 = $rating8_adj->value;
    }
    $rating9_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating9'])->One();
    if(null !== $rating9_adj) {
        $rating9 = $rating9_adj->value;
    }
    $rating10_adj = Adjustment::find()->andWhere(['test_id' => $id])->andWhere(['key' => 'rating10'])->One();
    if(null !== $rating10_adj) {
        $rating10 = $rating10_adj->value;
    }


    $pribadi1 = $rating1 * $bobot1 ;$pribadi2 =  $rating2 * $bobot2;$pribadi3 =  $rating3 * $bobot3;$pribadi4 =  $rating4 * $bobot4;$pribadi5 =  $rating5 * $bobot5;$pribadi6 =  $rating6 * $bobot6;$pribadi7 =  $rating7 * $bobot7;$pribadi8 =  $rating8 * $bobot8;$pribadi9 =  $rating9 * $bobot9;$pribadi10 =  $rating10 * $bobot10;
    $total_pribadi = $pribadi1 + $pribadi2 + $pribadi3 + $pribadi4 + $pribadi5 + $pribadi6 + $pribadi7 + $pribadi8 + $pribadi9 + $pribadi10;
    if ($rating1 == 1 ){ $rat11 = "grey";} else { $rat11 = "";}
    if ($rating1 == 2 ){ $rat12 = "grey";} else { $rat12 = "";}
    if ($rating1 == 3 ){ $rat13 = "grey";} else { $rat13 = "";}
    if ($rating1 == 4 ){ $rat14 = "grey";} else { $rat14 = "";}
    if ($rating1 == 5 ){ $rat15 = "grey";} else { $rat15 = "";}
    if ($rating1 == 6 ){ $rat16 = "grey";} else { $rat16 = "";}
    if ($rating1 == 7 ){ $rat17 = "grey";} else { $rat17 = "";}

    if ($rating2 == 1 ){ $rat21 = "grey";} else { $rat21 = "";}
    if ($rating2 == 2 ){ $rat22 = "grey";} else { $rat22 = "";}
    if ($rating2 == 3 ){ $rat23 = "grey";} else { $rat23 = "";}
    if ($rating2 == 4 ){ $rat24 = "grey";} else { $rat24 = "";}
    if ($rating2 == 5 ){ $rat25 = "grey";} else { $rat25 = "";}
    if ($rating2 == 6 ){ $rat26 = "grey";} else { $rat26 = "";}
    if ($rating2 == 7 ){ $rat27 = "grey";} else { $rat27 = "";}

    if ($rating3 == 1 ){ $rat31 = "grey";} else { $rat31 = "";}
    if ($rating3 == 2 ){ $rat32 = "grey";} else { $rat32 = "";}
    if ($rating3 == 3 ){ $rat33 = "grey";} else { $rat33 = "";}
    if ($rating3 == 4 ){ $rat34 = "grey";} else { $rat34 = "";}
    if ($rating3 == 5 ){ $rat35 = "grey";} else { $rat35 = "";}
    if ($rating3 == 6 ){ $rat36 = "grey";} else { $rat36 = "";}
    if ($rating3 == 7 ){ $rat37 = "grey";} else { $rat37 = "";}

    if ($rating4 == 1 ){ $rat41 = "grey";} else { $rat41 = "";}
    if ($rating4 == 2 ){ $rat42 = "grey";} else { $rat42 = "";}
    if ($rating4 == 3 ){ $rat43 = "grey";} else { $rat43 = "";}
    if ($rating4 == 4 ){ $rat44 = "grey";} else { $rat44 = "";}
    if ($rating4 == 5 ){ $rat45 = "grey";} else { $rat45 = "";}
    if ($rating4 == 6 ){ $rat46 = "grey";} else { $rat46 = "";}
    if ($rating4 == 7 ){ $rat47 = "grey";} else { $rat47 = "";}

    if ($rating5 == 1 ){ $rat51 = "grey";} else { $rat51 = "";}
    if ($rating5 == 2 ){ $rat52 = "grey";} else { $rat52 = "";}
    if ($rating5 == 3 ){ $rat53 = "grey";} else { $rat53 = "";}
    if ($rating5 == 4 ){ $rat54 = "grey";} else { $rat54 = "";}
    if ($rating5 == 5 ){ $rat55 = "grey";} else { $rat55 = "";}
    if ($rating5 == 6 ){ $rat56 = "grey";} else { $rat56 = "";}
    if ($rating5 == 7 ){ $rat57 = "grey";} else { $rat57 = "";}

    if ($rating6 == 1 ){ $rat61 = "grey";} else { $rat61 = "";}
    if ($rating6 == 2 ){ $rat62 = "grey";} else { $rat62 = "";}
    if ($rating6 == 3 ){ $rat63 = "grey";} else { $rat63 = "";}
    if ($rating6 == 4 ){ $rat64 = "grey";} else { $rat64 = "";}
    if ($rating6 == 5 ){ $rat65 = "grey";} else { $rat65 = "";}
    if ($rating6 == 6 ){ $rat66 = "grey";} else { $rat66 = "";}
    if ($rating6 == 7 ){ $rat67 = "grey";} else { $rat67 = "";}

    if ($rating7 == 1 ){ $rat71 = "grey";} else { $rat71 = "";}
    if ($rating7 == 2 ){ $rat72 = "grey";} else { $rat72 = "";}
    if ($rating7 == 3 ){ $rat73 = "grey";} else { $rat73 = "";}
    if ($rating7 == 4 ){ $rat74 = "grey";} else { $rat74 = "";}
    if ($rating7 == 5 ){ $rat75 = "grey";} else { $rat75 = "";}
    if ($rating7 == 6 ){ $rat76 = "grey";} else { $rat76 = "";}
    if ($rating7 == 7 ){ $rat77 = "grey";} else { $rat77 = "";}

    if ($rating8 == 1 ){ $rat81 = "grey";} else { $rat81 = "";}
    if ($rating8 == 2 ){ $rat82 = "grey";} else { $rat82 = "";}
    if ($rating8 == 3 ){ $rat83 = "grey";} else { $rat83 = "";}
    if ($rating8 == 4 ){ $rat84 = "grey";} else { $rat84 = "";}
    if ($rating8 == 5 ){ $rat85 = "grey";} else { $rat85 = "";}
    if ($rating8 == 6 ){ $rat86 = "grey";} else { $rat86 = "";}
    if ($rating8 == 7 ){ $rat87 = "grey";} else { $rat87 = "";}

    if ($rating9 == 1 ){ $rat91 = "grey";} else { $rat91 = "";}
    if ($rating9 == 2 ){ $rat92 = "grey";} else { $rat92 = "";}
    if ($rating9 == 3 ){ $rat93 = "grey";} else { $rat93 = "";}
    if ($rating9 == 4 ){ $rat94 = "grey";} else { $rat94 = "";}
    if ($rating9 == 5 ){ $rat95 = "grey";} else { $rat95 = "";}
    if ($rating9 == 6 ){ $rat96 = "grey";} else { $rat96 = "";}
    if ($rating9 == 7 ){ $rat97 = "grey";} else { $rat97 = "";}

    if ($rating10 == 1 ){ $rat101 = "grey";} else { $rat101 = "";}
    if ($rating10 == 2 ){ $rat102 = "grey";} else { $rat102 = "";}
    if ($rating10 == 3 ){ $rat103 = "grey";} else { $rat103 = "";}
    if ($rating10 == 4 ){ $rat104 = "grey";} else { $rat104 = "";}
    if ($rating10 == 5 ){ $rat105 = "grey";} else { $rat105 = "";}
    if ($rating10 == 6 ){ $rat106 = "grey";} else { $rat106 = "";}
    if ($rating10 == 7 ){ $rat107 = "grey";} else { $rat107 = "";}

    if ($total_pribadi > 449 ){ $bcg1 = "yellow";} else { $bcg1 = "";}
    if ($total_pribadi > 399 && $total_pribadi < 450 ){ $bcg2 = "yellow";} else { $bcg2 = "";}
    if ($total_pribadi > 349 && $total_pribadi < 400 ){ $bcg3 = "yellow";} else { $bcg3 = "";}
    if ($total_pribadi < 350 ){ $bcg4 = "yellow";} else { $bcg4 = "";}

//    print_r($total_pribadi);

    $res['total_pribadi'] = $total_pribadi;
    return $res;
}

public function papikostikProcessor($id, $debug) {

    $PAPIKOSTIK_ARRAY=[
        'e' => 0,
        'n' => 0,
        'a' => 0,
        'x' => 0,
        'b' => 0,
        'o' => 0,
        'z' => 0,
        'k' => 0,
        'w' => 0,
        'c' => 0,
        'l' => 0,
        'g' => 0,
        'r' => 0,
        'd' => 0,
        't' => 0,
        's' => 0,
        'i' => 0,
        'v' => 0,
        'f' => 0,
        'p' => 0,
    ];
    $PAPIKOSTIK_ARRAY_SCALED=[];


    $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
    $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();

    $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
        ->andWhere(['subject' => $result_storage->test_taker])
        ->One();
    $model = UserExt::find()->andWhere(['username' => $user->object])->One();
    if(!isset($model)) {
        $model = new UserExt;
    }
    $total_cfit = 0;


    $result_vars = VariablesStorage::find()
        ->andWhere(['like','results_result_id','i'.$id])
        ->andWhere(['like','call_id_item','.Papikostik_'])
        ->groupBy('item, identifier')
        ->orderBy('variable_id ASC')

        ->All();

    foreach ($result_vars as $result_var) {
        if (strpos($result_var->identifier, 'KOSTIK') !== false) {

            //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
            $strpos = strpos($result_var->value, '{');
            $valuestring = substr($result_var->value, $strpos);
            $exploded_result_var = explode(';', $valuestring);
            $index = 0;
            foreach ($exploded_result_var as $singular_result_var) {

                $ret = explode(':', $singular_result_var);
                if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                    $value = explode(':', $exploded_result_var[$index + 1])[2];
                    //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                    $SCORE_ARRAY[$result_var->call_id_item]['RESPONSE'][$result_var->identifier] = base64_decode($value);

                    if (isset(base64_decode($value)[0])) {

                        if (isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])])) {

                            if (strtolower(base64_decode($value)) == 'z46') {
                                if (isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))])) {
                                    $PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))] + 1;
                                } else {
                                    $PAPIKOSTIK_ARRAY[strtolower('o')] = 1;
                                }
                            } else if (strtolower(base64_decode($value)) == 'a46') {
                                //echo strtolower(base64_decode($value)) . '[]';
                                if (isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))])) {
                                    $PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))] + 1;
                                } else {
                                    $PAPIKOSTIK_ARRAY[strtolower('n')] = 1;
                                }
                            } else {

                                $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] + 1;
                            }
                        } else {
                            $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] = 1;
                        }
                    }
                }

                $index++;
            }

        }
    }

    foreach ($PAPIKOSTIK_ARRAY as $papikostik_key => $papikostik_value) {

        if($papikostik_key == 'z' || $papikostik_key == 'k') {
            $papikostik_scaled_int = ScaleRef::find()->andWhere(['scale_name' => 'papikostik_'.$papikostik_key])->andWhere(['unscaled' => $papikostik_value])->One();
            $papikostik_scaled = ScaleRef::find()->andWhere(['scale_name' => 'papikostik'])->andWhere(['unscaled' => $papikostik_scaled_int->scaled])->One();
        } else {
            $papikostik_scaled = ScaleRef::find()->andWhere(['scale_name' => 'papikostik'])->andWhere(['unscaled' => $papikostik_value])->One();
        }

        if (null !== $papikostik_scaled)
            $PAPIKOSTIK_ARRAY_SCALED[$papikostik_key] = $papikostik_scaled->scaled;
        else
            $PAPIKOSTIK_ARRAY_SCALED[$papikostik_key] = 99999;
    }

    return $PAPIKOSTIK_ARRAY_SCALED;
}

public function actionManagerresultaws($id,$debug)
{

    $apm_score = $this->apmProcessor($id,$debug);

    $PAPIKOSTIK_ARRAY_SCALED = $this->papikostikProcessor($id,$debug);

    $pcas_aspect_array = $this->discProcessor($id,$debug);

    $biodata = $this->biodataProcessor($id,$debug);


     $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
 //    $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;

     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
         ->andWhere(['subject' => $result_storage->test_taker])
         ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
         $model = new UserExt;
     }

     $total_apm_scaled = ScaleRef::find()->andWhere(['scale_name' => 'apm'])->andWhere(['unscaled' => $apm_score])->One();

     $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
     $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
     $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

     $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
     $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
     $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

     $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
     $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
     $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

     $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
     $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
     $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

     if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
     if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
     if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
     if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
     if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
     if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


     $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
     $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
     $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
     $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

     $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
         ->andWhere(['like','ds',$ds])
         ->andWhere(['like','dc' , $dc])
         ->andWhere(['like','is' , $is])
         ->andWhere(['like','ic' , $ic])
         ->andWhere(['like','sc' , $sc])
         ->andWhere(['like','d-pos' , $d_pos])
         ->andWhere(['like','i-pos' , $i_pos])
         ->andWhere(['like','s-pos', $s_pos])
         ->andWhere(['like','c-pos', $c_pos])
         ->All();


    if ($debug) {
        echo "<hr/>di = " . $di;
                    echo "<hr/>ds = " . $ds;
        echo "<hr/>dc = " . $dc;
        echo "<hr/>is = " . $is;
        echo "<hr/>ic = " . $ic;
        echo "<hr/>sc = " . $sc;
        echo "<hr/>d-pos = " . $d_pos;
        echo "<hr/>i-pos = " . $i_pos;
        echo "<hr/>s-pos = " . $s_pos;
        echo "<hr/>c-pos = " . $c_pos;
        echo "<hr/>d = " . $disc3_d->scaled;
        echo "<hr/>i = " . $disc3_i->scaled;
        echo "<hr/>s = " . $disc3_s->scaled;
        echo "<hr/>c = " . $disc3_c->scaled;
        print_r($grafik);
//            echo "<hr/>total unscaled cfit = " . $total_cfit_scaled->unscaled;
//            echo "<hr/>cfit score array = ". json_encode($cfit_score_array);
    }


     if(sizeof($grafik) == 1) {
//         if ($debug) {
//             echo 'size' .  sizeof($grafik);
//             echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
//         }

         $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
         //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
 //        echo '<br/>MULTIPLE GRAFIK<br/>';

         $grafs = [];
         foreach($grafik as $graf) {

             array_push($grafs, $graf->grafik);
         }


         $ranged_grafik = PcasRangeMap::find()
             ->andWhere(['in', 'grafik', $grafs])
             ->andWhere(['<=', 'dmin', $disc3_d->scaled])
             ->andWhere(['>', 'dmax', $disc3_d->scaled])
             ->andWhere(['<=', 'imin', $disc3_i->scaled])
             ->andWhere(['>', 'imax', $disc3_i->scaled])
             ->andWhere(['<=', 'smin', $disc3_s->scaled])
             ->andWhere(['>', 'smax', $disc3_s->scaled])
             ->andWhere(['<=', 'cmin', $disc3_c->scaled])
             ->andWhere(['>', 'cmax', $disc3_c->scaled])


             ->All();
 //        echo '<pre>';
 //        print_r($ranged_grafik);
         if(sizeof($ranged_grafik) == 1) {
 //              echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
             $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);

         } else if(sizeof($ranged_grafik) > 1) {
 //            echo '<br/>MULTIPLE RANGE GRAFIK<br/>';
             $ipa_values = new PcasIpaRef;
         } else {
             $ipa_values = new PcasIpaRef;
 //            echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';

         }
         //print_r($ranged_grafik);

     } else {
 //           echo '<br/>TIDAK ADA MATCHING GRAFIK';
         $ipa_values = new PcasIpaRef;

     }




     $disc_array = ['di' => $di,'ds' => $ds,
         'dc' => $dc,'is' => $is,'ic' => $ic,'sc' => $sc,'d-pos' => $d_pos,'i-pos' => $i_pos,'s-pos' => $s_pos,'c-pos' => $c_pos,
         'd' => $disc3_d->scaled,
         'i' => $disc3_i->scaled,
         's' => $disc3_s->scaled,
         'c' => $disc3_c->scaled


     ];

     return ['id'=>$id, 'model'=>$model,'biodata' => $biodata,'disc' => $disc_array,'grafik' => $grafik, 'cfit' => $total_apm_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values,
         'papikostik' => $PAPIKOSTIK_ARRAY_SCALED,
         'disc1_d' => $disc1_d->scaled,
         'disc1_i' => $disc1_i->scaled,
         'disc1_s' => $disc1_s->scaled,
         'disc1_c' => $disc1_c->scaled,
         'disc2_d' => $disc2_d->scaled,
         'disc2_i' => $disc2_i->scaled,
         'disc2_s' => $disc2_s->scaled,
         'disc2_c' => $disc2_c->scaled,
         'disc3_d' => $disc3_d->scaled,
         'disc3_i' => $disc3_i->scaled,
         'disc3_s' => $disc3_s->scaled,
         'disc3_c' => $disc3_c->scaled,
         'disc1_d_unscaled' => $pcas_aspect_array['a'],
         'disc1_i_unscaled' => $pcas_aspect_array['c'],
         'disc1_s_unscaled' => $pcas_aspect_array['e'],
         'disc1_c_unscaled' => $pcas_aspect_array['g'],
         'disc2_d_unscaled' => $pcas_aspect_array['b'],
         'disc2_i_unscaled' => $pcas_aspect_array['d'],
         'disc2_s_unscaled' => $pcas_aspect_array['f'],
         'disc2_c_unscaled' => $pcas_aspect_array['h'],
         'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
         'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
         'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
         'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],
     ];

}

public function grafikProcessor($pcas_aspect_array) {
    $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
    $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
    $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

    $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
    $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
    $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

    $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
    $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
    $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

    $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
    $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
    $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

    if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
    if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
    if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
    if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
    if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
    if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


    $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
    $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
    $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
    $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

    $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
        ->andWhere(['like','ds',$ds])
        ->andWhere(['like','dc' , $dc])
        ->andWhere(['like','is' , $is])
        ->andWhere(['like','ic' , $ic])
        ->andWhere(['like','sc' , $sc])
        ->andWhere(['like','d-pos' , $d_pos])
        ->andWhere(['like','i-pos' , $i_pos])
        ->andWhere(['like','s-pos', $s_pos])
        ->andWhere(['like','c-pos', $c_pos])
        ->All();

    return $grafik;
}

public function actionStaff2024($id) {

        $debug = 0;
        $user = Statements::find()
    ->join('INNER JOIN', 'results_storage', 'statements.subject = results_storage.test_taker')
        ->andWhere(['like', 'statements.predicate', '%generis.rdf#login%', false])
        ->andWhere(['like', 'results_storage.result_id', '%'. 'i'. $id.'%', false])
        ->one();


    $model = UserExt::find()->andWhere(['username' => $user->object])->One();
    if(!isset($model)) {
        $model = new UserExt;
    }

    $total_cfit = $this->cfitProcessor($id,$model);
    $pcas_aspect_array = $this->discProcessor($id,$model);
    $biodata = $this->biodataProcessor($id,$model);




//    $grafik = $this->grafikProcessor($pcas_aspect_array);

    $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
    $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
    $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

    $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
    $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
    $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

    $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
    $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
    $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

    $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
    $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
    $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

    if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
    if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
    if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
    if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
    if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
    if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


    $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
    $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
    $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
    $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

    $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
        ->andWhere(['like','ds',$ds])
        ->andWhere(['like','dc' , $dc])
        ->andWhere(['like','is' , $is])
        ->andWhere(['like','ic' , $ic])
        ->andWhere(['like','sc' , $sc])
        ->andWhere(['like','d-pos' , $d_pos])
        ->andWhere(['like','i-pos' , $i_pos])
        ->andWhere(['like','s-pos', $s_pos])
        ->andWhere(['like','c-pos', $c_pos])
        ->All();

    if(sizeof($grafik) == 1) {
        if ($debug) {
            echo 'size' .  sizeof($grafik);
            echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
        }

        $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
        //print_r($ipa_values);
    } else if(sizeof($grafik) > 1) {
//
        if ($debug) {
            echo '<br/>MULTIPLE GRAFIK<br/>';
            echo '<pre>';
            // print_r($grafik);
            echo '</pre>';
        }

        $grafs = [];
        foreach($grafik as $graf) {

            array_push($grafs, $graf->grafik);
        }


        $ranged_grafik = PcasRangeMap::find()
            ->andWhere(['in', 'grafik', $grafs])
            ->andWhere(['<=', 'dmin', $disc3_d->scaled])
            ->andWhere(['>', 'dmax', $disc3_d->scaled])
            ->andWhere(['<=', 'imin', $disc3_i->scaled]) //
            ->andWhere(['>', 'imax', $disc3_i->scaled])
            ->andWhere(['<=', 'smin', $disc3_s->scaled])
            ->andWhere(['>', 'smax', $disc3_s->scaled])
            ->andWhere(['<=', 'cmin', $disc3_c->scaled])
            ->andWhere(['>', 'cmax', $disc3_c->scaled])
            ->All();
        //    echo '<pre> ranbger dragif : ';
        //    print_r($ranged_grafik);
        if(sizeof($ranged_grafik) == 1) {
            if ($debug) {
                echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
            }
//
            $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);



        } else if(sizeof($ranged_grafik) > 1) {
            if ($debug) {
                echo '<br/>MULTIPLE RANGE GRAFIK a<br/>';
                foreach ($ranged_grafik as $graf) {
                    echo '<br/># matching grafik :  ' .$graf->grafik.')';
                }
            }
            $ipa_values = new PcasIpaRef;
        } else {
            $ipa_values = new PcasIpaRef;
//            echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';
        }
    } else {
        echo '<br/>TIDAK ADA MATCHING GRAFIK';
        $ipa_values = new PcasIpaRef;

    }
    $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();
//dd('here');
    if ($debug) {

        echo '<br/> d : ' . $d_pos;
        echo '<br/> i : ' . $i_pos;
        echo '<br/> s : ' . $s_pos;
        echo '<br/> c : ' . $c_pos;

        echo '<br/> di : ' . $di;
        echo '<br/> ds : ' . $ds;
        echo '<br/> dc : ' . $dc;
        echo '<br/> is : ' . $is;
        echo '<br/> ic : ' . $ic;
        echo '<br/> sc : ' . $sc;

        echo '<br/> D scaled: ' . $disc3_d->scaled;
        echo '<br/> I scaled: ' . $disc3_i->scaled;
        echo '<br/> S scaled: ' . $disc3_s->scaled;
        echo '<br/> C scaled: ' . $disc3_c->scaled;

    }

    return ['id'=>$id, 'model'=>$model, 'biodata' => $biodata, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values,
        'grafik' => $grafik,
        'ds' => $ds,
        'di' => $di,
        'dc' => $dc,
        'is' => $is,
        'ic' => $ic,
        'sc' => $sc,
        'd_pos' => $d_pos,
        'i_pos' => $i_pos,
        's_pos' => $s_pos,
        'c_pos' => $c_pos,
        'disc1_d' => $disc1_d->scaled,
        'disc1_i' => $disc1_i->scaled,
        'disc1_s' => $disc1_s->scaled,
        'disc1_c' => $disc1_c->scaled,
        'disc2_d' => $disc2_d->scaled,
        'disc2_i' => $disc2_i->scaled,
        'disc2_s' => $disc2_s->scaled,
        'disc2_c' => $disc2_c->scaled,
        'disc3_d' => $disc3_d->scaled,
        'disc3_i' => $disc3_i->scaled,
        'disc3_s' => $disc3_s->scaled,
        'disc3_c' => $disc3_c->scaled,
        'disc1_d_unscaled' => $pcas_aspect_array['a'],
        'disc1_i_unscaled' => $pcas_aspect_array['c'],
        'disc1_s_unscaled' => $pcas_aspect_array['e'],
        'disc1_c_unscaled' => $pcas_aspect_array['g'],
        'disc2_d_unscaled' => $pcas_aspect_array['b'],
        'disc2_i_unscaled' => $pcas_aspect_array['d'],
        'disc2_s_unscaled' => $pcas_aspect_array['f'],
        'disc2_c_unscaled' => $pcas_aspect_array['h'],
        'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
        'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
        'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
        'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],

    ];

}
    /***
     * @param $id
     * ini function untuk MBSS di cat
     */
public function actionStaffresultaws($id,$debug)
{

    $user = Statements::find()
        ->join('INNER JOIN', 'results_storage', 'statements.subject = results_storage.test_taker')
        ->andWhere(['like', 'statements.predicate', '%generis.rdf#login%', false])
        ->andWhere(['like', 'results_storage.result_id', '%'. 'i'. $id.'%', false])
        ->one();


    $model = UserExt::find()->andWhere(['username' => $user->object])->One();
    if(!isset($model)) {
        $model = new UserExt;
    }


    $total_cfit = $this->cfitProcessor($id,$model);
    $pcas_aspect_array = $this->discProcessor($id,$model);
    $biodata = $this->biodataProcessor($id,$model);

//    $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
//    $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
////    $result_rdf = 'https://cat.ppsdm.com/cat.rdf#i'. $id;
//
//    $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
//        ->andWhere(['subject' => $result_storage->test_taker])
//        ->One();
//    $model = UserExt::find()->andWhere(['username' => $user->object])->One();
//    if(!isset($model)) {
//        $model = new UserExt;
//    }

//    echo 'total cfit : ' . $total_cfit;
//    echo 'pcas aspect array<pre>';
//    print_r($pcas_aspect_array);

//    $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();


    $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
    $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
    $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

    $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
    $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
    $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

    $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
    $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
    $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

    $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
    $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
    $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

    if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
    if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
    if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
    if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
    if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
    if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


    $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
    $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
    $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
    $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

    $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
        ->andWhere(['like','ds',$ds])
        ->andWhere(['like','dc' , $dc])
        ->andWhere(['like','is' , $is])
        ->andWhere(['like','ic' , $ic])
        ->andWhere(['like','sc' , $sc])
        ->andWhere(['like','d-pos' , $d_pos])
        ->andWhere(['like','i-pos' , $i_pos])
        ->andWhere(['like','s-pos', $s_pos])
        ->andWhere(['like','c-pos', $c_pos])
        ->All();

    if(sizeof($grafik) == 1) {
        if ($debug) {
            echo 'size' .  sizeof($grafik);
            echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
        }

        $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
        //print_r($ipa_values);
    } else if(sizeof($grafik) > 1) {
//
        if ($debug) {
            echo '<br/>MULTIPLE GRAFIK<br/>';
            echo '<pre>';
            // print_r($grafik);
            echo '</pre>';
        }

        $grafs = [];
        foreach($grafik as $graf) {

            array_push($grafs, $graf->grafik);
        }


        $ranged_grafik = PcasRangeMap::find()
            ->andWhere(['in', 'grafik', $grafs])
            ->andWhere(['<=', 'dmin', $disc3_d->scaled])
            ->andWhere(['>', 'dmax', $disc3_d->scaled])
            ->andWhere(['<=', 'imin', $disc3_i->scaled]) //
            ->andWhere(['>', 'imax', $disc3_i->scaled])
            ->andWhere(['<=', 'smin', $disc3_s->scaled])
            ->andWhere(['>', 'smax', $disc3_s->scaled])
            ->andWhere(['<=', 'cmin', $disc3_c->scaled])
            ->andWhere(['>', 'cmax', $disc3_c->scaled])
            ->All();
    //    echo '<pre> ranbger dragif : ';
    //    print_r($ranged_grafik);
        if(sizeof($ranged_grafik) == 1) {
            if ($debug) {
                echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
            }
//
            $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);



        } else if(sizeof($ranged_grafik) > 1) {
            if ($debug) {
                            echo '<br/>MULTIPLE RANGE GRAFIK a<br/>';
                            foreach ($ranged_grafik as $graf) {
                                echo '<br/># matching grafik :  ' .$graf->grafik.')';
                            }
            }

            $ipa_values = new PcasIpaRef;
        } else {
            $ipa_values = new PcasIpaRef;
//            echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';


        }

    } else {
          echo '<br/>TIDAK ADA MATCHING GRAFIK';
        $ipa_values = new PcasIpaRef;

    }

    $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();

    if ($debug) {

        echo '<br/> d : ' . $d_pos;
        echo '<br/> i : ' . $i_pos;
        echo '<br/> s : ' . $s_pos;
        echo '<br/> c : ' . $c_pos;

        echo '<br/> di : ' . $di;
        echo '<br/> ds : ' . $ds;
        echo '<br/> dc : ' . $dc;
        echo '<br/> is : ' . $is;
        echo '<br/> ic : ' . $ic;
        echo '<br/> sc : ' . $sc;

        echo '<br/> D scaled: ' . $disc3_d->scaled;
        echo '<br/> I scaled: ' . $disc3_i->scaled;
        echo '<br/> S scaled: ' . $disc3_s->scaled;
        echo '<br/> C scaled: ' . $disc3_c->scaled;

    }

return ['id'=>$id, 'model'=>$model, 'biodata' => $biodata, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values,
'grafik' => $grafik,
'ds' => $ds,
'di' => $di,
'dc' => $dc,
'is' => $is,
'ic' => $ic,
'sc' => $sc,
'd_pos' => $d_pos,
'i_pos' => $i_pos,
's_pos' => $s_pos,
'c_pos' => $c_pos,
'disc1_d' => $disc1_d->scaled,
'disc1_i' => $disc1_i->scaled,
'disc1_s' => $disc1_s->scaled,
'disc1_c' => $disc1_c->scaled,
'disc2_d' => $disc2_d->scaled,
'disc2_i' => $disc2_i->scaled,
'disc2_s' => $disc2_s->scaled,
'disc2_c' => $disc2_c->scaled,
'disc3_d' => $disc3_d->scaled,
'disc3_i' => $disc3_i->scaled,
'disc3_s' => $disc3_s->scaled,
'disc3_c' => $disc3_c->scaled,
'disc1_d_unscaled' => $pcas_aspect_array['a'],
'disc1_i_unscaled' => $pcas_aspect_array['c'],
'disc1_s_unscaled' => $pcas_aspect_array['e'],
'disc1_c_unscaled' => $pcas_aspect_array['g'],
'disc2_d_unscaled' => $pcas_aspect_array['b'],
'disc2_i_unscaled' => $pcas_aspect_array['d'],
'disc2_s_unscaled' => $pcas_aspect_array['f'],
'disc2_c_unscaled' => $pcas_aspect_array['h'],
'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],

];
}

public function actionStaffresult($id)
{
	 $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
     $result_rdf = $tao_model->modeluri . 'i'. $id;


     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
      $model = new UserExt;
     }
       $total_cfit = 0;
     $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

     $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$items])
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')

     ->All();
     $cfit_score_array = [];
     foreach ($result_vars as $result_var) {


         /***
          * this part does not matter if we're not looking for the responses and just the score
          *
          *
          */
//     echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
         $strpos = strpos($result_var->value, '{');
        $valuestring = substr($result_var->value, $strpos);
         $exploded_result_var = explode(';',$valuestring);
            $index = 0;
         foreach($exploded_result_var as $singular_result_var) {

            $ret = explode(':', $singular_result_var);
            if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                $value = explode(':', $exploded_result_var[$index + 1])[2];

            }

              $index++;
              }

     }



     if ($result_var->identifier == 'SCORE') {
//         echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ' . $result_var->value;

     $strpos = strpos($result_var->value, '{');
     $valuestring = substr($result_var->value, $strpos);
     $exploded_result_var = explode(';',$valuestring);
       $index = 0;

     foreach($exploded_result_var as $singular_result_var) {

       $ret = explode(':', $singular_result_var);
       if ((sizeof($ret) > 2) && ($ret[2] == '"value"')) {

           $value = explode(':', $exploded_result_var[$index + 1])[2];

           $total_cfit = $total_cfit + base64_decode($value);
           array_push($cfit_score_array, base64_decode($value));
       }
         $index++;
         }
     }
     }

     $pcas_item = [$result_rdf .".item-11.0"];

     $pcas_results = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$pcas_item])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     ->All();


     $pcas_score_array = [];
     foreach ($pcas_results as $result_var) {

     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
          $strpos = strpos($result_var->value, '{');
         $valuestring = substr($result_var->value, $strpos);
          $exploded_result_var = explode(';',$valuestring);
             $index = 0;
          foreach($exploded_result_var as $singular_result_var) {

             $ret = explode(':', $singular_result_var);
             if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                 $value = explode(':', $exploded_result_var[$index + 1])[2];
                 echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                 if ($result_var->identifier != 'RESPONSE') {
                  $trimmed = trim(base64_decode($value), "[]");
                  $trimmed_items = explode(";", $trimmed);
                  $trimmed_array = [];
                  foreach($trimmed_items as $trimmed_item) {
                   $trimmed_trimmed = trim($trimmed_item, " ");
                   $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                   if (sizeof($trimmed_trimmed_items) > 1) {
                   $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                  }
                  }

                $pcas_score_array[$result_var->identifier] = $trimmed_array;
                }
             }

               $index++;
               }

     }
    }


     $pcas_aspect_array = [];
     $pcas_aspect_array['a'] = 0;
     $pcas_aspect_array['b'] = 0;
     $pcas_aspect_array['c'] = 0;
     $pcas_aspect_array['d'] = 0;
     $pcas_aspect_array['e'] = 0;
     $pcas_aspect_array['f'] = 0;
     $pcas_aspect_array['g'] = 0;
     $pcas_aspect_array['h'] = 0;
     $pcas_aspect_array['i'] = 0;
     $pcas_aspect_array['j'] = 0;

     foreach ($pcas_score_array as $key => $item) {

      if (sizeof($item) > 1) {


     $response_val = PcasResponseMap::find()->andWHere(['item' => $key])->One();
   //  echo '<br/>' .$key.' size: ' . sizeof($response_val);

      $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();



     $first_row_selection = (str_replace('choice_','',$item['choice_1'])) - 3;
     $second_row_selection =  (str_replace('choice_','',$item['choice_2'])) - 3;


     $first_row_disc_value = explode(',',$mapping->choice_1)[$first_row_selection];
   $second_row_disc_value = explode(',',$mapping->choice_2)[$second_row_selection];

      $pcas_aspect_array[$first_row_disc_value]++;
       $pcas_aspect_array[$second_row_disc_value]++;


     } else {
    //  echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
     }
     }

     $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();


     $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
     $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
     $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

     $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
     $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
     $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

     $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
     $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
     $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

     $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
     $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
     $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


     $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
     $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
     $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
     $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

     $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
     ->andWhere(['like','ds',$ds])
     ->andWhere(['like','dc' , $dc])
     ->andWhere(['like','is' , $is])
     ->andWhere(['like','ic' , $ic])
     ->andWhere(['like','sc' , $sc])
     ->andWhere(['like','d-pos' , $d_pos])
     ->andWhere(['like','i-pos' , $i_pos])
     ->andWhere(['like','s-pos', $s_pos])
     ->andWhere(['like','c-pos', $c_pos])
	 ->All();

     if(sizeof($grafik) == 1) {
      //echo 'size' .  sizeof($grafik);
      //echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
      //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
      //echo '<br/>MULTIPLE GRAFIK<br/>';

     $grafs = [];
     foreach($grafik as $graf) {

      array_push($grafs, $graf->grafik);
     }
     /*
      $grafs2 = ['139', '139.a'];

      print_r($grafs);
      echo '<br/>';
       print_r($grafs2);
       */


       $ranged_grafik = PcasRangeMap::find()
       ->andWhere(['in', 'grafik', $grafs])
       ->andWhere(['<=', 'dmin', $disc3_d->scaled])
         ->andWhere(['>', 'dmax', $disc3_d->scaled])
         ->andWhere(['<=', 'imin', $disc3_i->scaled])
           ->andWhere(['>', 'imax', $disc3_i->scaled])
           ->andWhere(['<=', 'smin', $disc3_s->scaled])
             ->andWhere(['>', 'smax', $disc3_s->scaled])
             ->andWhere(['<=', 'cmin', $disc3_c->scaled])
               ->andWhere(['>', 'cmax', $disc3_c->scaled])


       ->All();
	   //echo '<pre>';
	   //print_r($ranged_grafik);
     if(sizeof($ranged_grafik) == 1) {
    //  echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);
            /*echo '<br/> d : ' . $d_pos;
 echo '<br/> i : ' . $i_pos;
  echo '<br/> s : ' . $s_pos;
   echo '<br/> c : ' . $c_pos;

            echo '<br/> di : ' . $di;
            echo '<br/> ds : ' . $ds;
            echo '<br/> dc : ' . $dc;
            echo '<br/> is : ' . $is;
            echo '<br/> ic : ' . $ic;
            echo '<br/> sc : ' . $sc;

            echo '<br/> D scaled: ' . $disc3_d->scaled;
       echo '<br/> I scaled: ' . $disc3_i->scaled;
        echo '<br/> S scaled: ' . $disc3_s->scaled;
         echo '<br/> C scaled: ' . $disc3_c->scaled;
         */
     } else if(sizeof($ranged_grafik) > 1) {
      //echo '<br/>MULTIPLE RANGE GRAFIK<br/>';
         $ipa_values = new PcasIpaRef;
     } else {
         $ipa_values = new PcasIpaRef;
      //echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';


     }
     //print_r($ranged_grafik);

     } else {
   //   echo '<br/>TIDAK ADA MATCHING GRAFIK';
      $ipa_values = new PcasIpaRef;

     }

 echo "<hr/>total unscaled cfit = " . $total_cfit_scaled->unscaled;
    echo "<hr/>cfit score array = ". json_encode($cfit_score_array);

     //
//return ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values,
//'grafik' => $grafik,
//'ds' => $ds,
//'di' => $di,
//'dc' => $dc,
//'is' => $is,
//'ic' => $ic,
//'sc' => $sc,
//'d_pos' => $d_pos,
//'i_pos' => $i_pos,
//'s_pos' => $s_pos,
//'c_pos' => $c_pos,
//'disc1_d' => $disc1_d->scaled,
//'disc1_i' => $disc1_i->scaled,
//'disc1_s' => $disc1_s->scaled,
//'disc1_c' => $disc1_c->scaled,
//'disc2_d' => $disc2_d->scaled,
//'disc2_i' => $disc2_i->scaled,
//'disc2_s' => $disc2_s->scaled,
//'disc2_c' => $disc2_c->scaled,
//'disc3_d' => $disc3_d->scaled,
//'disc3_i' => $disc3_i->scaled,
//'disc3_s' => $disc3_s->scaled,
//'disc3_c' => $disc3_c->scaled,
//'disc1_d_unscaled' => $pcas_aspect_array['a'],
//'disc1_i_unscaled' => $pcas_aspect_array['c'],
//'disc1_s_unscaled' => $pcas_aspect_array['e'],
//'disc1_c_unscaled' => $pcas_aspect_array['g'],
//'disc2_d_unscaled' => $pcas_aspect_array['b'],
//'disc2_i_unscaled' => $pcas_aspect_array['d'],
//'disc2_s_unscaled' => $pcas_aspect_array['f'],
//'disc2_c_unscaled' => $pcas_aspect_array['h'],
//'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
//'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
//'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
//'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],
//
//];


}






public function actionStaffResultOld($id)
{
	 $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
     $result_rdf = $tao_model->modeluri . 'i'. $id;

     /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
      $model = new UserExt;
     }
       $total_cfit = 0;
     $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

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
     $cfit_score_array = [];
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

           $total_cfit = $total_cfit + base64_decode($value);
           array_push($cfit_score_array, base64_decode($value));
       }
         $index++;
         }
     }
     }

     $pcas_item = [$result_rdf .".item-11.0"];

     $pcas_results = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$pcas_item])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     ->All();


     $pcas_score_array = [];
     foreach ($pcas_results as $result_var) {

     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
          $strpos = strpos($result_var->value, '{');
         $valuestring = substr($result_var->value, $strpos);
          $exploded_result_var = explode(';',$valuestring);
             $index = 0;
          foreach($exploded_result_var as $singular_result_var) {

             $ret = explode(':', $singular_result_var);
             if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                 $value = explode(':', $exploded_result_var[$index + 1])[2];
              //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                 if ($result_var->identifier != 'RESPONSE') {
                  $trimmed = trim(base64_decode($value), "[]");
                  $trimmed_items = explode(";", $trimmed);
                  $trimmed_array = [];
                  foreach($trimmed_items as $trimmed_item) {
                   $trimmed_trimmed = trim($trimmed_item, " ");
                   $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                   if (sizeof($trimmed_trimmed_items) > 1) {
                   $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                  }
                  }

                $pcas_score_array[$result_var->identifier] = $trimmed_array;
                }
             }

               $index++;
               }

     }
    }


     $pcas_aspect_array = [];
     $pcas_aspect_array['a'] = 0;
     $pcas_aspect_array['b'] = 0;
     $pcas_aspect_array['c'] = 0;
     $pcas_aspect_array['d'] = 0;
     $pcas_aspect_array['e'] = 0;
     $pcas_aspect_array['f'] = 0;
     $pcas_aspect_array['g'] = 0;
     $pcas_aspect_array['h'] = 0;
     $pcas_aspect_array['i'] = 0;
     $pcas_aspect_array['j'] = 0;

     foreach ($pcas_score_array as $key => $item) {

      if (sizeof($item) > 1) {


     $response_val = PcasResponseMap::find()->andWHere(['item' => $key])->One();
   //  echo '<br/>' .$key.' size: ' . sizeof($response_val);

      $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();



     $first_row_selection = (str_replace('choice_','',$item['choice_1'])) - 3;
     $second_row_selection =  (str_replace('choice_','',$item['choice_2'])) - 3;


     $first_row_disc_value = explode(',',$mapping->choice_1)[$first_row_selection];
   $second_row_disc_value = explode(',',$mapping->choice_2)[$second_row_selection];

      $pcas_aspect_array[$first_row_disc_value]++;
       $pcas_aspect_array[$second_row_disc_value]++;


     } else {
    //  echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
     }
     }

     $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();


     $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
     $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
     $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

     $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
     $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
     $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

     $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
     $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
     $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

     $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
     $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
     $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


     $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
     $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
     $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
     $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

     $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
     ->andWhere(['like','ds',$ds])
     ->andWhere(['like','dc' , $dc])
     ->andWhere(['like','is' , $is])
     ->andWhere(['like','ic' , $ic])
     ->andWhere(['like','sc' , $sc])
     ->andWhere(['like','d-pos' , $d_pos])
     ->andWhere(['like','i-pos' , $i_pos])
     ->andWhere(['like','s-pos', $s_pos])
     ->andWhere(['like','c-pos', $c_pos])
	 ->All();

     if(sizeof($grafik) == 1) {
      //echo 'size' .  sizeof($grafik);
      //echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
      //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
      //echo '<br/>MULTIPLE GRAFIK<br/>';

     $grafs = [];
     foreach($grafik as $graf) {

      array_push($grafs, $graf->grafik);
     }
     /*
      $grafs2 = ['139', '139.a'];

      print_r($grafs);
      echo '<br/>';
       print_r($grafs2);
       */


       $ranged_grafik = PcasRangeMap::find()
       ->andWhere(['in', 'grafik', $grafs])
       ->andWhere(['<=', 'dmin', $disc3_d->scaled])
         ->andWhere(['>', 'dmax', $disc3_d->scaled])
         ->andWhere(['<=', 'imin', $disc3_i->scaled])
           ->andWhere(['>', 'imax', $disc3_i->scaled])
           ->andWhere(['<=', 'smin', $disc3_s->scaled])
             ->andWhere(['>', 'smax', $disc3_s->scaled])
             ->andWhere(['<=', 'cmin', $disc3_c->scaled])
               ->andWhere(['>', 'cmax', $disc3_c->scaled])


       ->All();
	   //echo '<pre>';
	   //print_r($ranged_grafik);
     if(sizeof($ranged_grafik) == 1) {
    //  echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);
            /*echo '<br/> d : ' . $d_pos;
 echo '<br/> i : ' . $i_pos;
  echo '<br/> s : ' . $s_pos;
   echo '<br/> c : ' . $c_pos;

            echo '<br/> di : ' . $di;
            echo '<br/> ds : ' . $ds;
            echo '<br/> dc : ' . $dc;
            echo '<br/> is : ' . $is;
            echo '<br/> ic : ' . $ic;
            echo '<br/> sc : ' . $sc;

            echo '<br/> D scaled: ' . $disc3_d->scaled;
       echo '<br/> I scaled: ' . $disc3_i->scaled;
        echo '<br/> S scaled: ' . $disc3_s->scaled;
         echo '<br/> C scaled: ' . $disc3_c->scaled;
         */
     } else if(sizeof($ranged_grafik) > 1) {
      //echo '<br/>MULTIPLE RANGE GRAFIK<br/>';
         $ipa_values = new PcasIpaRef;
     } else {
         $ipa_values = new PcasIpaRef;
      //echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';


     }
     //print_r($ranged_grafik);

     } else {
   //   echo '<br/>TIDAK ADA MATCHING GRAFIK';
      $ipa_values = new PcasIpaRef;

     }
     #return $this->render('psikotes', ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values]);

return ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values, 
'grafik' => $grafik,
'ds' => $ds,
'di' => $di,
'dc' => $dc,
'is' => $is,
'ic' => $ic,
'sc' => $sc,
'd_pos' => $d_pos,
'i_pos' => $i_pos,
's_pos' => $s_pos,
'c_pos' => $c_pos,
'disc1_d' => $disc1_d->scaled,
'disc1_i' => $disc1_i->scaled,
'disc1_s' => $disc1_s->scaled,
'disc1_c' => $disc1_c->scaled,
'disc2_d' => $disc2_d->scaled,
'disc2_i' => $disc2_i->scaled,
'disc2_s' => $disc2_s->scaled,
'disc2_c' => $disc2_c->scaled,
'disc3_d' => $disc3_d->scaled,
'disc3_i' => $disc3_i->scaled,
'disc3_s' => $disc3_s->scaled,
'disc3_c' => $disc3_c->scaled,
'disc1_d_unscaled' => $pcas_aspect_array['a'],
'disc1_i_unscaled' => $pcas_aspect_array['c'],
'disc1_s_unscaled' => $pcas_aspect_array['e'],
'disc1_c_unscaled' => $pcas_aspect_array['g'],
'disc2_d_unscaled' => $pcas_aspect_array['b'],
'disc2_i_unscaled' => $pcas_aspect_array['d'],
'disc2_s_unscaled' => $pcas_aspect_array['f'],
'disc2_c_unscaled' => $pcas_aspect_array['h'],
'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],


];


}




    public function actionStaffprint($id)
    {

        $object = $this->actionStaffresult($id);

        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

            //echo '<pre>';
          //  print_r($adjustments);
     return $this->render('psikotes', ['id'=>$id, 'adjustments' => $adjustments, 'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values']]);

    }


     public function actionManagerresult($id)
    {
  $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
     $result_rdf = $tao_model->modeluri . 'i'. $id;

     /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
      $model = new UserExt;
     }
       $total_cfit = 0;
   //  $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

     $items = ['APM-', 'Papikostik-', 'DISC-'];
     $pengolah_1 = ['APM-'];
     $pengolah_2 = ['Papikostik-'];
     $pengolah_3 = ['DISC-'];

     //$items = ['http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-3.0','http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-1.0'];
     //$result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result->result_id])->groupBy('item, identifier')->All();
     $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     //->andWhere(['like','call_id_item',$items])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     //->OrWhere(['identifier' => 'SCORE'])
      //  ->OrWhere(['identifier' => 'RESPONSE'])
     //->OrWhere(['identifier' => 'LtiOutcome'])
     ->All();
     $cfit_score_array = [];
     $PM_SCORE=0;
     $SCORE_ARRAY=[];
     $PAPIKOSTIK_ARRAY=[
    'e' => 0,
    'n' => 0,
    'a' => 0,
    'x' => 0,
    'b' => 0,
    'o' => 0,
    'z' => 0,
    'k' => 0,
    'w' => 0,
    'c' => 0,
    'l' => 0,
    'g' => 0,
    'r' => 0,
    'd' => 0,
    't' => 0,
    's' => 0,
    'i' => 0,
    'v' => 0,
    'f' => 0,
    'p' => 0,
     ];
          $PAPIKOSTIK_ARRAY_SCALED=[];
          $pcas_score_array = [];




        foreach ($result_vars as $result_var) {

$exist = false;
$match = false;
            foreach ($items as $key => $value) {
                 if(strpos($result_var->call_id_item, $value)) {
                    $exist = true;
                    $match = $value;
                 }
            }

           
                if ($match) {
                //     if (true) {
         //   echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
                    //echo $match . ' ++++++++++++++++++++';

  if (in_array($match,$pengolah_1)) {
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
                  //      echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                        $SCORE_ARRAY[$result_var->call_id_item]['RESPONSE'] = base64_decode($value);
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
            //       echo '<br/>' . $result_var->call_id_item . '('.$exploded_result_var[$index]. ')  = ' . base64_decode($value);
                   $total_cfit = $total_cfit + base64_decode($value);
                   array_push($cfit_score_array, base64_decode($value));
                              $SCORE_ARRAY[$result_var->call_id_item]['SCORE'] = base64_decode($value);
                              $PM_SCORE = $PM_SCORE + base64_decode($value);
               }
                 $index++;
                 }
            }
            }


            if (in_array($match,$pengolah_2)) {

            if (strpos($result_var->identifier, 'KOSTIK') !== false) {
                
                 //echo '<br/>_____' . $result_var->call_id_item . ' (' . $result_var->identifier . ') : ';// . $result_var->value;
                 $strpos = strpos($result_var->value, '{');
                $valuestring = substr($result_var->value, $strpos);
                 $exploded_result_var = explode(';',$valuestring);
                    $index = 0;
                 foreach($exploded_result_var as $singular_result_var) {

                    $ret = explode(':', $singular_result_var);
                    if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                        $value = explode(':', $exploded_result_var[$index + 1])[2];
                     //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                        $SCORE_ARRAY[$result_var->call_id_item]['RESPONSE'][$result_var->identifier] = base64_decode($value);

                                            if(isset(base64_decode($value)[0])) {

                        if(isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])])) {
                         
                            if (strtolower(base64_decode($value)) == 'z46') 
                            {
                                    if(isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))])) { 
                                    $PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode('o'))] + 1;
                                } else {
                                    $PAPIKOSTIK_ARRAY[strtolower('o')] = 1;
                                }
                            } else if (strtolower(base64_decode($value)) == 'a46') {
                                   //echo strtolower(base64_decode($value)) . '[]';
                                    if(isset($PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))])) { 
                                    $PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode('n'))] + 1;
                                } else {
                                    $PAPIKOSTIK_ARRAY[strtolower('n')] = 1;
                                }
                            } else {
                                
                            $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] = $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] + 1;
                            }
                        }
                        else {
                            $PAPIKOSTIK_ARRAY[strtolower(base64_decode($value)[0])] = 1;
                        }
                        }
                    }

                      $index++;
                      }

            }     

            }

  if (in_array($match,$pengolah_3)) {
     // echo 'MATCH<br/>';
     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
          $strpos = strpos($result_var->value, '{');
         $valuestring = substr($result_var->value, $strpos);
          $exploded_result_var = explode(';',$valuestring);
             $index = 0;
          foreach($exploded_result_var as $singular_result_var) {

             $ret = explode(':', $singular_result_var);
             if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                 $value = explode(':', $exploded_result_var[$index + 1])[2];
              //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                 if ($result_var->identifier != 'RESPONSE') {
                  $trimmed = trim(base64_decode($value), "[]");
                  $trimmed_items = explode(";", $trimmed);
                  $trimmed_array = [];
                  foreach($trimmed_items as $trimmed_item) {
                   $trimmed_trimmed = trim($trimmed_item, " ");
                   $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                   if (sizeof($trimmed_trimmed_items) > 1) {
                   $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                  }
                  }

                $pcas_score_array[$result_var->identifier] = $trimmed_array;
                }
             }

               $index++;
               }

     }
  } else {
      //echo '<br/>NO MATCH for : ' . $match;
  }

            } //if($match)) 

            } //foreach ($result_vars as $result_var)





     $pcas_aspect_array = [];
     $pcas_aspect_array['a'] = 0;
     $pcas_aspect_array['b'] = 0;
     $pcas_aspect_array['c'] = 0;
     $pcas_aspect_array['d'] = 0;
     $pcas_aspect_array['e'] = 0;
     $pcas_aspect_array['f'] = 0;
     $pcas_aspect_array['g'] = 0;
     $pcas_aspect_array['h'] = 0;
     $pcas_aspect_array['i'] = 0;
     $pcas_aspect_array['j'] = 0;
//echo 'before pcas_score_array for loop <br/>';
#print_r($pcas_score_array);
     foreach ($pcas_score_array as $key => $item) {
#echo 'inside pcas_score_array for loop <br/>';
      if (sizeof($item) > 1) {

//echo 'ITEM > 1<br/>';
     $response_val = PcasResponseMap::find()->andWHere(['item' => $key])->One();
     /*echo '<br/>' .$key.' size: ' . sizeof($response_val);
     echo '<br/>';
     print_r($item);
     echo '<br/>';
     */
     $quickconvert['a'] = 0;
     $quickconvert['b'] = 1;
     $quickconvert['c'] = 2;
     $quickconvert['d'] = 3;

     /* KODE DIBAWAH KHUSUS UNTUK CASE MBSS YANG MENGGUNAKAN DISC RESPONSE BERBEDA */
     if (array_key_exists('choice_1l', $item)) {
        $letter_selection_1 = str_replace('choice_1','',$item['choice_1l']);
        $letter_selection_2 = str_replace('choice_1','',$item['choice_1m']);
        /*echo $quickconvert[$letter_selection_1];
        echo '<br/>';
        echo $letter_selection_2;
        echo '<br/>';
        */
        $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();
        $first_row_disc_value = explode(',',$mapping->choice_1)[$quickconvert[$letter_selection_1]];
        $second_row_disc_value = explode(',',$mapping->choice_2)[$quickconvert[$letter_selection_2]];
        $pcas_aspect_array[$first_row_disc_value]++;
        $pcas_aspect_array[$second_row_disc_value]++;
        //first_row_selection = (str_replace('choice_','',$item['choice_1l'])) - 3;
        // echo 'EXISTS';
     } else {

  
      $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();
     $first_row_selection = (str_replace('choice_','',$item['choice_1'])) - 3;

     $second_row_selection =  (str_replace('choice_','',$item['choice_2'])) - 3;


  $first_row_disc_value = explode(',',$mapping->choice_1)[$first_row_selection];
     //   echo '<hr/>';
   $second_row_disc_value = explode(',',$mapping->choice_2)[$second_row_selection];
   /*echo $first_row_selection . ' : ' . $first_row_disc_value;
   echo '<br/>';
   echo $second_row_selection . ' : ' . $second_row_disc_value;
   echo '<hr/>';
   */

      $pcas_aspect_array[$first_row_disc_value]++;
       $pcas_aspect_array[$second_row_disc_value]++;


    }
     } else {
    //  echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
     }
     }




     //$total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();
          $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'apm'])->andWhere(['unscaled' => $PM_SCORE])->One();

     $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
     $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled ASC')->One();
     $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

     $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
     $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled ASC')->One();
     $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

     $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
     $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled ASC')->One();
     $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

     $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
     $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled ASC')->One();
     $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


     $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
     $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
     $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
     $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

     $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
     ->andWhere(['ds' => $ds])
     ->andWhere(['dc' => $dc])
     ->andWhere(['is' => $is])
     ->andWhere(['ic' => $ic])
     ->andWhere(['sc' => $sc])
     ->andWhere(['d-pos' => $d_pos])
     ->andWhere(['i-pos' => $i_pos])
     ->andWhere(['s-pos' => $s_pos])
     ->andWhere(['c-pos' => $c_pos])->All();

     $disc_array = ['di' => $di,'ds' => $ds,
'dc' => $dc,'is' => $is,'ic' => $ic,'sc' => $sc,'d-pos' => $d_pos,'i-pos' => $i_pos,'s-pos' => $s_pos,'c-pos' => $c_pos,
'd' => $disc3_d->scaled,
'i' => $disc3_i->scaled,
's' => $disc3_s->scaled,
'c' => $disc3_c->scaled


];

     if(sizeof($grafik) == 1) {
   //   echo 'size' .  sizeof($grafik);
     // echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
      //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
      //echo '<br/>MULTIPLE GRAFIK<br/>';

     $grafs = [];
     foreach($grafik as $graf) {

      array_push($grafs, $graf->grafik);
     }


       $ranged_grafik = PcasRangeMap::find()
       ->andWhere(['in', 'grafik', $grafs])
       ->andWhere(['<', 'dmin', $disc3_d->scaled])
         ->andWhere(['>', 'dmax', $disc3_d->scaled])
         ->andWhere(['<', 'imin', $disc3_i->scaled])
           ->andWhere(['>', 'imax', $disc3_i->scaled])
           ->andWhere(['<', 'smin', $disc3_s->scaled])
             ->andWhere(['>', 'smax', $disc3_s->scaled])
             ->andWhere(['<', 'cmin', $disc3_c->scaled])
               ->andWhere(['>', 'cmax', $disc3_c->scaled])


       ->All();
     if(sizeof($ranged_grafik) == 1) {
      //echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);
     } else if(sizeof($ranged_grafik) > 1) {
      //echo '<br/>MULTIPLE RANGE GRAFIK<br/>';
         $ipa_values = new PcasIpaRef;
     } else {
         $ipa_values = new PcasIpaRef;
      //echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';


     }
     //print_r($ranged_grafik);

     } else {
     // echo '<br/>TIDAK ADA MATCHING GRAFIK';
      $ipa_values = new PcasIpaRef;
     }




foreach ($PAPIKOSTIK_ARRAY as $papikostik_key => $papikostik_value) {
              
              if($papikostik_key == 'z' || $papikostik_key == 'k') {
  $papikostik_scaled_int = ScaleRef::find()->andWhere(['scale_name' => 'papikostik_'.$papikostik_key])->andWhere(['unscaled' => $papikostik_value])->One();
  $papikostik_scaled = ScaleRef::find()->andWhere(['scale_name' => 'papikostik'])->andWhere(['unscaled' => $papikostik_scaled_int->scaled])->One();
              } else {
                  $papikostik_scaled = ScaleRef::find()->andWhere(['scale_name' => 'papikostik'])->andWhere(['unscaled' => $papikostik_value])->One();
              }
      
        if (null !== $papikostik_scaled)
            $PAPIKOSTIK_ARRAY_SCALED[$papikostik_key] = $papikostik_scaled->scaled;
        else
            $PAPIKOSTIK_ARRAY_SCALED[$papikostik_key] = 99999;
}

/*
     ob_start();
      ob_end_clean();
     return $this->render('mbss_manager', ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values, 'papikostik' => $PAPIKOSTIK_ARRAY_SCALED]);
*/
return ['id'=>$id, 'model'=>$model,'disc' => $disc_array,'grafik' => $grafik, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values, 
'papikostik' => $PAPIKOSTIK_ARRAY_SCALED,
'disc1_d' => $disc1_d->scaled,
'disc1_i' => $disc1_i->scaled,
'disc1_s' => $disc1_s->scaled,
'disc1_c' => $disc1_c->scaled,
'disc2_d' => $disc2_d->scaled,
'disc2_i' => $disc2_i->scaled,
'disc2_s' => $disc2_s->scaled,
'disc2_c' => $disc2_c->scaled,
'disc3_d' => $disc3_d->scaled,
'disc3_i' => $disc3_i->scaled,
'disc3_s' => $disc3_s->scaled,
'disc3_c' => $disc3_c->scaled,
'disc1_d_unscaled' => $pcas_aspect_array['a'],
'disc1_i_unscaled' => $pcas_aspect_array['c'],
'disc1_s_unscaled' => $pcas_aspect_array['e'],
'disc1_c_unscaled' => $pcas_aspect_array['g'],
'disc2_d_unscaled' => $pcas_aspect_array['b'],
'disc2_i_unscaled' => $pcas_aspect_array['d'],
'disc2_s_unscaled' => $pcas_aspect_array['f'],
'disc2_c_unscaled' => $pcas_aspect_array['h'],
'disc3_d_unscaled' => $pcas_aspect_array['a'] - $pcas_aspect_array['b'],
'disc3_i_unscaled' => $pcas_aspect_array['c'] - $pcas_aspect_array['d'],
'disc3_s_unscaled' => $pcas_aspect_array['e'] - $pcas_aspect_array['f'],
'disc3_c_unscaled' => $pcas_aspect_array['g'] - $pcas_aspect_array['h'],
];


    }

    function actionManagerprint($id) {

        $object = $this->actionManagerresult($id);
        
        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }
     return $this->render('mbss_manager', ['id'=>$object['id'], 'adjustments' => $adjustments, 'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], 'papikostik' => $object['papikostik']]);

    }

    function actionManagerdebug($id){
    	    	$object = $this->actionManagerresult($id);

    	    	foreach($object as $obj => $value) {
    	    		echo $obj . ' = ' . sizeof($value);
    	    		echo '<br/>';
    	    	}
    	    	echo '<pre>';
    	    	print_r($object);
    }


    function actionManager2print($id) {

        $object = $this->actionManagerresult($id);
        
        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

     return $this->render('mbss_manager2', ['id'=>$object['id'], 'adjustments' => $adjustments, 'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], 'papikostik' => $object['papikostik'], "data" => $object]);

    }

    function actionDishubprint($id) {

        $object = $this->actionManagerresult($id);
     return $this->render('mbss_manager2', ['id'=>$object['id'], 'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], 'papikostik' => $object['papikostik']]);

    }

    public function actionInfomedia($id)
    {

        $object = $this->actionInfomediaresult($id);
      
        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
            

        }

     return $this->render('mbss_staff2', ['id'=>$id, 'adjustments'=>$adjustments,'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values']]);

     
            echo '<pre>';
            print_r($object);
            
    }


    public function actionCfitprint($id)
    {

        $object = $this->actionCfitresult($id);
    //     $adjustments = [];
    //     $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
    //     foreach ($adjustmentModel as $adjustmodel){
    //         $adjustments[$adjustmodel->key] = $adjustmodel->value;
    //     }

    //  return $this->render('mbss_staff2', ['id'=>$id, 'adjustments'=>$adjustments,'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object]);

    }



    public function actionStaffreport($id)
    {

        $object = $this->actionStaffresult($id);
        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

     return $this->render('mbss_staff2', ['id'=>$id, 'adjustments'=>$adjustments,'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object]);

    }


    public function actionPrintstaffcat($id) {
      $object = $this->actionResultstaffcat($id);
      $adjustments = [];
      $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
      foreach ($adjustmentModel as $adjustmodel){
          $adjustments[$adjustmodel->key] = $adjustmodel->value;
      }
    }
    public function actionStaff2print($id)
    {

        $object = $this->actionStaffresult($id);
        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $id])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

     return $this->render('mbss_staff2', ['id'=>$id, 'adjustments'=>$adjustments,'model'=>$object['model'], 'cfit' => $object['cfit'], 'pcas' => $object['pcas'], 'ipa_values' => $object['ipa_values'], "data" => $object]);

    }

    function actionStaffdebug($id){
                $object = $this->actionStaffresult($id);

                foreach($object as $obj => $value) {
                    echo $obj . ' = ' . json_encode($value);
                    echo '<br/>';
                }
                echo '<pre>';
//                 print_r($object);
                /*** untuk mencari iq*/
                echo '<h2/>IQ</h2>';
                echo 'CFIT Unscaled : ' . $object['cfit']['unscaled'];
                $iq = ScaleRef::find()->andWhere(['scale_name' => 'iq'])->andWhere(['<=','unscaled',$object['cfit']['unscaled']])->orderBy('unscaled DESC')->One();
                echo '<br/>IQ : ' . $iq->scaled;

				
    }

    function actionTpadebug($id){
        $object = $this->actionTparesult($id);

        foreach($object as $obj => $value) {
            echo $obj . ' = ' . sizeof($value);
            echo '<br/>';
        }
        echo '<pre>';
        print_r($object);
}



public function actionInfomediaResult($id)
{
	 $tao_model = Models::find()->andWhere(['modelid' => '1'])->One();
     $result_storage = ResultsStorage::find()->andWhere(['result_id' => $tao_model->modeluri . 'i'. $id])->One();
     $result_rdf = $tao_model->modeluri . 'i'. $id;

     /*$result_statements = Statements::find()->andWhere(['subject' => $id])->All();*/
     $user = Statements::find()->andWhere(['predicate' => 'http://www.tao.lu/Ontologies/generis.rdf#login'])
     ->andWhere(['subject' => $result_storage->test_taker])
     ->One();
     $model = UserExt::find()->andWhere(['username' => $user->object])->One();
     if(!isset($model)) {
      $model = new UserExt;
     }
       $total_cfit = 0;

       if ($id == '1545187130362413734') { //untuk user shogi
        $total_cfit = 9;
     } else if  ($id == '1545187137596613739') {
        $total_cfit = 14;
     }

 

     $items = [$result_rdf .".item-3.0",$result_rdf .".item-5.0",$result_rdf .".item-8.0",$result_rdf .".item-10.0"];

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
     $cfit_score_array = [];
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

           $total_cfit = $total_cfit + base64_decode($value);
           array_push($cfit_score_array, base64_decode($value));
       }
         $index++;
         }
         
     }

     
     }

     $pcas_item = [$result_rdf .".item-11.0"];

     $pcas_results = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$pcas_item])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     ->All();


     $pcas_score_array = [];
     foreach ($pcas_results as $result_var) {

     if (strpos($result_var->identifier, 'RESPONSE') !== false) {
          $strpos = strpos($result_var->value, '{');
         $valuestring = substr($result_var->value, $strpos);
          $exploded_result_var = explode(';',$valuestring);
             $index = 0;
          foreach($exploded_result_var as $singular_result_var) {

             $ret = explode(':', $singular_result_var);
             if ((sizeof($ret) > 2) && ($ret[2] == '"candidateResponse"')) {

                 $value = explode(':', $exploded_result_var[$index + 1])[2];
              //   echo '<br/>' . $result_var->call_id_item .'('.$result_var->identifier. ') = ' . base64_decode($value);
                 if ($result_var->identifier != 'RESPONSE') {
                  $trimmed = trim(base64_decode($value), "[]");
                  $trimmed_items = explode(";", $trimmed);
                  $trimmed_array = [];
                  foreach($trimmed_items as $trimmed_item) {
                   $trimmed_trimmed = trim($trimmed_item, " ");
                   $trimmed_trimmed_items = explode(" ", $trimmed_trimmed);
                   if (sizeof($trimmed_trimmed_items) > 1) {
                   $trimmed_array[$trimmed_trimmed_items[0]] = $trimmed_trimmed_items[1];
                  }
                  }

                $pcas_score_array[$result_var->identifier] = $trimmed_array;
                }
             }

               $index++;
               }

     }
    }


     $pcas_aspect_array = [];
     $pcas_aspect_array['a'] = 0;
     $pcas_aspect_array['b'] = 0;
     $pcas_aspect_array['c'] = 0;
     $pcas_aspect_array['d'] = 0;
     $pcas_aspect_array['e'] = 0;
     $pcas_aspect_array['f'] = 0;
     $pcas_aspect_array['g'] = 0;
     $pcas_aspect_array['h'] = 0;
     $pcas_aspect_array['i'] = 0;
     $pcas_aspect_array['j'] = 0;

     foreach ($pcas_score_array as $key => $item) {

      if (sizeof($item) > 1) {


     $response_val = PcasResponseMap::find()->andWHere(['item' => $key])->One();
   //  echo '<br/>' .$key.' size: ' . sizeof($response_val);

      $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();



     $first_row_selection = (str_replace('choice_','',$item['choice_1'])) - 3;
     $second_row_selection =  (str_replace('choice_','',$item['choice_2'])) - 3;


     $first_row_disc_value = explode(',',$mapping->choice_1)[$first_row_selection];
   $second_row_disc_value = explode(',',$mapping->choice_2)[$second_row_selection];

      $pcas_aspect_array[$first_row_disc_value]++;
       $pcas_aspect_array[$second_row_disc_value]++;


     } else {
    //  echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
     }
     }

    if ($id == '1545187134374513738') {
        $pcas_aspect_array['a'] = 5;
        $pcas_aspect_array['b'] = 4;
        $pcas_aspect_array['c'] = 8;
        $pcas_aspect_array['d'] = 4;
        $pcas_aspect_array['e'] = 4;
        $pcas_aspect_array['f'] = 8;
        $pcas_aspect_array['g'] = 4;
        $pcas_aspect_array['h'] = 7;
        $pcas_aspect_array['i'] = 3;
        $pcas_aspect_array['j'] = 1;
    }

     $total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();


     $disc1_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-d'])->andWhere(['<=','unscaled',$pcas_aspect_array['a']])->orderBy('unscaled DESC')->One();
     $disc2_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-d'])->andWhere(['>=','unscaled',$pcas_aspect_array['b']])->orderBy('unscaled DESC')->One();
     $disc3_d = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-d'])->andWhere(['<=','unscaled', ($pcas_aspect_array['a'] - $pcas_aspect_array['b'])])->orderBy('unscaled DESC')->One();

     $disc1_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-i'])->andWhere(['<=','unscaled', $pcas_aspect_array['c']])->orderBy('unscaled DESC')->One();
     $disc2_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-i'])->andWhere(['>=','unscaled', $pcas_aspect_array['d']])->orderBy('unscaled DESC')->One();
     $disc3_i = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-i'])->andWhere(['<=','unscaled', ($pcas_aspect_array['c'] - $pcas_aspect_array['d'])])->orderBy('unscaled DESC')->One();

     $disc1_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-s'])->andWhere(['<=','unscaled', $pcas_aspect_array['e']])->orderBy('unscaled DESC')->One();
     $disc2_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-s'])->andWhere(['>=','unscaled', $pcas_aspect_array['f']])->orderBy('unscaled DESC')->One();
     $disc3_s = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-s'])->andWhere(['<=','unscaled',($pcas_aspect_array['e'] - $pcas_aspect_array['f'])])->orderBy('unscaled DESC')->One();

     $disc1_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-1-c'])->andWhere(['<=','unscaled', $pcas_aspect_array['g']])->orderBy('unscaled DESC')->One();
     $disc2_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-2-c'])->andWhere(['>=','unscaled',$pcas_aspect_array['h']])->orderBy('unscaled DESC')->One();
     $disc3_c = ScaleRef::find()->andWhere(['scale_name' => 'pcas-3-c'])->andWhere(['<=','unscaled',($pcas_aspect_array['g'] - $pcas_aspect_array['h'])])->orderBy('unscaled DESC')->One();

if($disc3_d->scaled > $disc3_i->scaled) {$di = '>';} else if($disc3_d->scaled < $disc3_i->scaled) {$di = '<';} else {$di = '=';}
if($disc3_d->scaled > $disc3_s->scaled) {$ds = '>';} else if($disc3_d->scaled < $disc3_s->scaled) {$ds = '<';} else {$ds = '=';}
if($disc3_d->scaled > $disc3_c->scaled) {$dc = '>';} else if($disc3_d->scaled < $disc3_c->scaled) {$dc = '<';} else {$dc = '=';}
if($disc3_i->scaled > $disc3_s->scaled) {$is = '>';} else if($disc3_i->scaled < $disc3_s->scaled) {$is = '<';} else {$is = '=';}
if($disc3_i->scaled > $disc3_c->scaled) {$ic = '>';} else if($disc3_i->scaled < $disc3_c->scaled) {$ic = '<';} else {$ic = '=';}
if($disc3_s->scaled > $disc3_c->scaled) {$sc = '>';} else if($disc3_s->scaled < $disc3_c->scaled) {$sc = '<';} else {$sc = '=';}


     $d_pos = ($disc3_d->scaled >= 20) ? '1':'0';
     $i_pos = ($disc3_i->scaled >= 20) ? '1':'0';
     $s_pos= ($disc3_s->scaled >= 20) ? '1':'0';
     $c_pos= ($disc3_c->scaled >= 20) ? '1':'0';

     $grafik = PcasGrafikRef::find()->andWhere(['di' => $di])
     ->andWhere(['like','ds',$ds])
     ->andWhere(['like','dc' , $dc])
     ->andWhere(['like','is' , $is])
     ->andWhere(['like','ic' , $ic])
     ->andWhere(['like','sc' , $sc])
     ->andWhere(['like','d-pos' , $d_pos])
     ->andWhere(['like','i-pos' , $i_pos])
     ->andWhere(['like','s-pos', $s_pos])
     ->andWhere(['like','c-pos', $c_pos])
	 ->All();

     if(sizeof($grafik) == 1) {
      //echo 'size' .  sizeof($grafik);
      //echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
      //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
      //echo '<br/>MULTIPLE GRAFIK<br/>';

     $grafs = [];
     foreach($grafik as $graf) {

      array_push($grafs, $graf->grafik);
     }
     /*
      $grafs2 = ['139', '139.a'];

      print_r($grafs);
      echo '<br/>';
       print_r($grafs2);
       */


       $ranged_grafik = PcasRangeMap::find()
       ->andWhere(['in', 'grafik', $grafs])
       ->andWhere(['<=', 'dmin', $disc3_d->scaled])
         ->andWhere(['>', 'dmax', $disc3_d->scaled])
         ->andWhere(['<=', 'imin', $disc3_i->scaled])
           ->andWhere(['>', 'imax', $disc3_i->scaled])
           ->andWhere(['<=', 'smin', $disc3_s->scaled])
             ->andWhere(['>', 'smax', $disc3_s->scaled])
             ->andWhere(['<=', 'cmin', $disc3_c->scaled])
               ->andWhere(['>', 'cmax', $disc3_c->scaled])


       ->All();
	   //echo '<pre>';
	   //print_r($ranged_grafik);
     if(sizeof($ranged_grafik) == 1) {
    //  echo '<br/># matching grafik :  ' .$ranged_grafik[0]->grafik.')';
      $ipa_values = PcasIpaRef::findOne($ranged_grafik[0]->grafik);
            /*echo '<br/> d : ' . $d_pos;
 echo '<br/> i : ' . $i_pos;
  echo '<br/> s : ' . $s_pos;
   echo '<br/> c : ' . $c_pos;

            echo '<br/> di : ' . $di;
            echo '<br/> ds : ' . $ds;
            echo '<br/> dc : ' . $dc;
            echo '<br/> is : ' . $is;
            echo '<br/> ic : ' . $ic;
            echo '<br/> sc : ' . $sc;

            echo '<br/> D scaled: ' . $disc3_d->scaled;
       echo '<br/> I scaled: ' . $disc3_i->scaled;
        echo '<br/> S scaled: ' . $disc3_s->scaled;
         echo '<br/> C scaled: ' . $disc3_c->scaled;
         */
     } else if(sizeof($ranged_grafik) > 1) {
    //  echo '<br/>MULTIPLE RANGE GRAFIK<br/>';
         $ipa_values = new PcasIpaRef;
     } else {
         $ipa_values = new PcasIpaRef;
    //  echo '<br/>TIDAK ADA MATCHING RANGE GRAFIK';


     }
     //print_r($ranged_grafik);

     } else {
   //   echo '<br/>TIDAK ADA MATCHING GRAFIK';
      $ipa_values = new PcasIpaRef;

     }
     #return $this->render('psikotes', ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values]);

return ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values, 
'grafik' => $grafik,
'ds' => $ds,
'di' => $di,
'dc' => $dc,
'is' => $is,
'ic' => $ic,
'sc' => $sc,
'd_pos' => $d_pos,
'i_pos' => $i_pos,
's_pos' => $s_pos,
'c_pos' => $c_pos,

'disc3_d' => $disc3_d->scaled,
'disc3_i' => $disc3_i->scaled,
'disc3_s' => $disc3_s->scaled,
'disc3_c' => $disc3_c->scaled,


];


}



}
