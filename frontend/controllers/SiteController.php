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
                    //return $this->goHome();
                    $this->redirect(

                    'http://127.0.0.1:8090/ppsdm/tao/tao/Users/ppsdmAdd?' . http_build_query(['param' => $model])

                    /*, [
                    'data'=>[
                    'method' => 'post',
                    'confirm' => 'Are you sure?',
                    'params'=>['MyParam1'=>'100', 'MyParam2'=>true],
                    ]

                   ]
*/

                   );

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
           $total_cfit = 0;
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
           $total_cfit = $total_cfit + base64_decode($value);
           array_push($cfit_score_array, base64_decode($value));
       }
         $index++;
         }
    }

    }

echo '</br>';
    $pcas_item = [$result_rdf .".item-11.0"];
      //$items = ['http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-3.0','http://127.0.0.1:8090/tao/ppsdm.rdf#i147076498436978.item-1.0'];
     //$result_vars = VariablesStorage::find()->andWhere(['results_result_id' => $result->result_id])->groupBy('item, identifier')->All();
     $pcas_results = VariablesStorage::find()->andWhere(['results_result_id' => $result_rdf])
     ->andWhere(['in','call_id_item',$pcas_item])
     //->groupBy('item')
     ->groupBy('item, identifier')
     ->orderBy('variable_id ASC')
     //->OrWhere(['identifier' => 'SCORE'])
       //  ->OrWhere(['identifier' => 'RESPONSE'])
     //->OrWhere(['identifier' => 'LtiOutcome'])
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

echo '<hr/>';
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

echo '<pre>';
print_r($pcas_score_array);

foreach ($pcas_score_array as $key => $item) {
 //echo '<br/>';
 //print_r($key);
 if (sizeof($item) > 1) {

$response_val = PcasResponseMap::find()->andWHere(['item' => $key])->One();
//echo $pcas_score;
echo '<br/>' .$key.' size: ' . sizeof($response_val);

 $mapping = PcasResponseMap::find()->andWHere(['item' => $key])->One();


$first_row_selection = (str_replace('choice_','',$item['choice_1'])) - 3;
$second_row_selection =  (str_replace('choice_','',$item['choice_2'])) - 3;

$first_row_disc_value = explode(',',$mapping->choice_1)[$first_row_selection];
$second_row_disc_value = explode(',',$mapping->choice_2)[$second_row_selection];

 $pcas_aspect_array[$first_row_disc_value]++;
  $pcas_aspect_array[$second_row_disc_value]++;

} else {
 echo '<br/>WARNING : ADA SOAL PCAS NOT ANSWERED';
}
}
echo 'sasafdadadsa</pre>';
echo '<hr/>';
$total_cfit_scaled = ScaleRef::find()->andWhere(['scale_name' => 'cfit-to-6'])->andWhere(['unscaled' => $total_cfit])->One();
echo '<pre>CFIT total unscaled = '.$total_cfit.'<br/>scaled = ' . $total_cfit_scaled->scaled . '<br/>';
print_r($cfit_score_array);


echo '<br/>pcas aspect';

print_r($pcas_aspect_array);
echo '<br/>A - B = ' . ($pcas_aspect_array['a'] - $pcas_aspect_array['b']);
echo '<br/>C - D = ' . ($pcas_aspect_array['c'] - $pcas_aspect_array['d']);
echo '<br/>E - F = ' . ($pcas_aspect_array['e'] - $pcas_aspect_array['f']);
echo '<br/>G - H = ' . ($pcas_aspect_array['g'] - $pcas_aspect_array['h']);

echo '<hr/>';

//select * from scale_ref where scale_name = 'pcas-1-d' AND unscaled <= '8' order by unscaled desc LIMIT 1


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

echo '<br/>scale-40 disc 3 d : ' . $disc3_d->scaled;
echo '<br/>scale-40 disc 3 i : ' . $disc3_i->scaled;
echo '<br/>scale-40 disc 3 s : ' . $disc3_s->scaled;
echo '<br/>scale-40 disc 3 c : ' . $disc3_c->scaled;



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

     echo '<br/>d pos : ' . $d_pos;
     echo '<br/>i pos : ' . $i_pos;
     echo '<br/>s pos : ' . $s_pos;
     echo '<br/>c pos : ' . $c_pos;

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

if(sizeof($grafik) == 1) {
 echo 'size' .  sizeof($grafik);
 echo '<br/># matching grafik : ' . sizeof($grafik) . ' ( ' .$grafik[0]->grafik.')';
 $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
 //print_r($ipa_values);
} else if(sizeof($grafik) > 1) {
 echo '<br/>MULTIPLE GRAFIK<br/>';

 $grafs = ['139', '139.a'];


  $ranged_grafik = PcasRangeMap::find()
  ->andWhere(['in', 'grafik', $grafs])
  ->andWhere(['>', 'dmin', $disc3_d->scaled])
    ->andWhere(['<', 'dmax', $disc3_d->scaled])
    ->andWhere(['>', 'imin', $disc3_i->scaled])
      ->andWhere(['<', 'imax', $disc3_i->scaled])
      ->andWhere(['>', 'smin', $disc3_s->scaled])
        ->andWhere(['<', 'smax', $disc3_s->scaled])
        ->andWhere(['>', 'cmin', $disc3_c->scaled])
          ->andWhere(['<', 'cmax', $disc3_c->scaled])

   // ->andWhere(['between', $disc3_i->scaled, 'imin', 'imax'])
     // ->andWhere(['between', $disc3_s->scaled, 'smin', 'smax'])
       // ->andWhere(['between', $disc3_c->scaled, 'cmin', 'cmax'])
  ->All();

print_r($ranged_grafik);

} else {
 echo '<br/>TIDAK ADA MATCHING GRAFIK';
 $ipa_values = new PcasIpaRef;
}


echo '</pre>';
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

     $result_statements = Statements::find()->andWhere(['subject' => $id])->All();
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
     ->andWhere(['ds' => $ds])
     ->andWhere(['dc' => $dc])
     ->andWhere(['is' => $is])
     ->andWhere(['ic' => $ic])
     ->andWhere(['sc' => $sc])
     ->andWhere(['d-pos' => $d_pos])
     ->andWhere(['i-pos' => $i_pos])
     ->andWhere(['s-pos' => $s_pos])
     ->andWhere(['c-pos' => $c_pos])->All();

     if(sizeof($grafik) == 1) {

      $ipa_values = PcasIpaRef::findOne($grafik[0]->grafik);
      //print_r($ipa_values);
     } else if(sizeof($grafik) > 1) {
     // echo '<br/>MULTIPLE GRAFIK';
      $ipa_values = new PcasIpaRef;
     } else {
     // echo '<br/>TIDAK ADA MATCHING GRAFIK';
      $ipa_values = new PcasIpaRef;
     }


     ob_start();
      ob_end_clean();
     return $this->render('psikotes', ['id'=>$id, 'model'=>$model, 'cfit' => $total_cfit_scaled, 'pcas' => $pcas_aspect_array, 'ipa_values' => $ipa_values]);

    }
}
