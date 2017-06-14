<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

$this->title = 'Isi data peserta';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-submit']); ?>

<?= $form->field($model2, 'username')->textInput(['readonly' => true]) ?>
                <?= $form->field($model2, 'firstname')->textInput(['autofocus' => true]) ?>
                   <?= $form->field($model2, 'lastname')->textInput() ?>
                                      <?= $form->field($model2, 'tempat_lahir')->textInput() ?>
                                      <?= $form->field($model2, 'tanggal_lahir')->widget(DatePicker::classname(), [
                                       'class' => 'form-control',
//'language' => 'ru',
'dateFormat' => 'yyyy-MM-dd',
]) ?>
                                                         <?= $form->field($model2, 'jabatan_dilamar')->textInput() ?>
                                                                            <?= $form->field($model2, 'pendidikan_terakhir')->dropDownList(['sd' => 'sd', 'smp' => 'smp', 'sma' => 'sma', 's1' => 's1', 's2'=>'s2', 's3'=>'s3'], ['prompt'=>'Select...']) ?>

                                                                            <?= $form->field($model2, 'tujuan_pemeriksaan')->dropDownList(['seleksi' => 'seleksi', 'promosi' => 'promosi', 'evaluasi' => 'evaluasi'],['prompt'=>'Select...']) ?>
                                                                            <?= $form->field($model2, 'tempat')->textInput(['readonly' => False]) ?>
                                                                            <?= $form->field($model2, 'tanggal_test')->widget(DatePicker::classname(), [
    //'language' => 'ru',
    'dateFormat' => 'yyyy-MM-dd',
]) ?>





                <div class="form-group">
                    <?= Html::submitButton('submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
