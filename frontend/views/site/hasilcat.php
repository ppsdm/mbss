<?php

use app\models\tao\Statements;
use app\models\tao\ResultsStorage;
use app\models\tao\VariablesStorage;
use Yii\helpers\Url;


/*

*/


 ?>


 <h2>Hasil Test</h2><br/>
<div class="body-content">

<?php

 if(isset($user)) {
  //echo '<br/><br/><br/><br/><br/>User ID : ' . $user->subject . '<hr/>';
  $results = ResultsStorage::find()->andWhere(['test_taker' => $user->subject])
  //->groupBy('delivery')
  //->orderBy('result_id DESC ')
//->groupBy('delivery')
//->max('result_id')
  ->All();

  foreach ($results as $result) {

$result_statement = Statements::find()->andWhere(['predicate'=> 'http://www.w3.org/2000/01/rdf-schema#label'])->andWhere(['subject' => $result->result_id ])->One();
     echo  '<div class="row"><a href="printstaffcat/'.explode("#i",$result->result_id)[1].'">';
     echo yii\bootstrap\Button::widget([
      'options' => ['class' => 'btn-lg'],
      'label' => $result_statement->object

     ]);


  echo '</a></div><br/>';



   //echo '<hr/>
   //echo '<br/>delivery id : ' . $result->delivery . '<br/>';





  }

 } else {
  echo '<br/><br/><br/><br/><br/>no USER with that login in Tao';
 }



?>

</div>
