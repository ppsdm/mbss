<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>

        <p class="lead">You have successfully login.</p>
<?php if(Yii::$app->user->isGuest) {
  echo '<p><a class="btn btn-lg btn-success" href="'. Url::toRoute(['site/login']).'">Klik Untuk Login</a></p>';

} else {

 echo '<p><a class="btn btn-lg btn-success" href="../site/datapeserta">Mulai dengan isi data peserta</a></p>';

}

?>

    </div>

    <div class="body-content">

        <div class="row">

        </div>

    </div>
</div>
