<?php

namespace frontend\controllers;

use app\models\Adjustment;
use app\models\tao\ResultsStorage;
//use app\models\taoResultServer;
use app\models\tao\VariablesStorage;
use app\models\TaoResult;
use phpDocumentor\Reflection\Types\Object_;
use stdClass;
use Yii;
use yii\db\Query;






class ResultController extends \yii\web\Controller
{
    public function getTaoValue($result_var)
    {

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
        return base64_decode($value);
//        return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));

    }

    public function actionGetresultid($email, $deliveryId)
    {
        $email_encoded = base64_encode($email);


        $results = VariablesStorage::find()->leftJoin('results_storage', 'variables_storage.results_result_id = results_storage.result_id')
//            ->leftJoin(['tao_result' => 'portaldb.tao_result'], 'results_storage.delivery_id like :delivery', [':delivery' => '%'.$deliveryId.'%'])
            ->andWhere('variables_storage.value like :value', [':value' => '%'.$email_encoded.'%'])
            ->andWhere('results_storage.delivery like :delivery', [':delivery' => '%'.$deliveryId.'%'])
            ->orderBy(['results_storage.result_id' => SORT_DESC])
            ->One();
        $resultId = $results->results_result_id;
//        print_r(sizeof($results));
        $res = explode('#',$resultId);
return ltrim($res[1],'i');
//        $taoResult = TaoResult::find()->andWhere('result_id = :result_id', [':result_id' => $resultId])->one();
    }

    public function actionGetscore($email, $deliveryId)
    {

        $resultId = $this->actionGetresultid($email,$deliveryId);
//        return $resultId;
        $siteController = new SiteController($resultId, $this);


        $object = $siteController->actionStaff2024($resultId);

        $adjustments = [];
        $adjustmentModel = Adjustment::find()->andWhere(['test_id' => $resultId])->All();
        foreach ($adjustmentModel as $adjustmodel){
            $adjustments[$adjustmodel->key] = $adjustmodel->value;
        }

        $scores = $siteController->getScores($resultId, $object);


//        $result_storage = ResultsStorage::find()->andWhere(['delivery' => ])->All();
//        $query = new Query();
//        $rows = $query->select('*')
//            ->from('variables_storage')
//            ->where(['like','results_result_id','https://cat.ppsdm.com/cat.rdf#i1632793525704665350'])
////            ->andWhere(['identifier' => 'email'])
//            ->all(Yii::$app->taodb);

        $result_vars = VariablesStorage::find()->andWhere(['results_result_id' => 'https://cat.ppsdm.com/cat.rdf#i'. $resultId])
//            ->andWhere(['in','call_id_item',$items])
            ->groupBy('item, identifier')
            ->orderBy('variable_id ASC')->all(Yii::$app->taodb);



//        echo '<pre/>';

//        $serializedData = 'O:46:"taoResultServer_models_classes_OutcomeVariable":7:{s:13:"normalMaximum";N;s:13:"normalMinimum";N;s:5:"value";s:12:"Y29tcGxldGVk";s:10:"identifier";s:16:"completionStatus";s:11:"cardinality";s:6:"single";s:8:"baseType";s:10:"identifier";s:5:"epoch";s:21:"0.34452800 1632794190";}';


        foreach ($result_vars as $result) {
            if ($result->identifier == 'email') {
//                echo $result['identifier'] . ' => ' . $result['value'] . '<br/><br/>';
                try {

//                    $serializedData = $result['value'];
//                    $parsedData = $this->casttoclass($serializedData,'stdClass');
//                    $parsedData = unserialize($result['value']);

$taoResult = TaoResult::find()->andWhere('result_id = :result_id', [':result_id' => $resultId])->one();
if ($taoResult == null) {
    $taoResult = new TaoResult();
}
                    $taoResult->score = $scores['total_pribadi'];
                    $taoResult->result_id = $resultId;
                    $taoResult->delivery_id = $deliveryId;
                    $taoResult->status = 'completed';
                    $taoResult->email = $this->getTaoValue($result);
                    if ($taoResult->save()) {
                        // Model was saved successfully
//                        echo 'MODEL SAVED';
//                        echo base64_encode($taoResult->email);
                    } else {
                        // There were validation errors or an error occurred while saving
                    }



//                    $correctResponse = $parsedData->correctResponse;
                    // Continue with the rest of your code using $correctResponse
                    // ...
                } catch (Exception $e) {
                    // Handle the error, such as logging or displaying a message
                    echo "Error: Unable to unserialize the object. " . $e->getMessage();
                }
//print_r($parsedData);
//                $correctResponse = $parsedData['correctResponse'];
// Access the values in the parsed data
//                $correctResponse = $parsedData->correctResponse;
//                $candidateResponse = $parsedData->candidateResponse;
//                $identifier = $parsedData->identifier;
//                $cardinality = $parsedData->cardinality;
//                $baseType = $parsedData->baseType;
//                $epoch = $parsedData->epoch;
//
//// Print the values
//                echo "Correct Response: " . $correctResponse . "\n";
//                echo "Candidate Response: " . $candidateResponse . "\n";
//                echo "Identifier: " . $identifier . "\n";
//                echo "Cardinality: " . $cardinality . "\n";
//                echo "Base Type: " . $baseType . "\n";
//                echo "Epoch: " . $epoch . "\n";
            } else {
//                echo $result['identifier'] . '<br/>';
            }
        }
//        print_r($rows);
//        return $this->render('index');
    }

}
