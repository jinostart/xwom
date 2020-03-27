<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\xpaper\models\XpNews */

$this->title = Yii::t('app', 'Create Xp News');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Xp News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="layui-card-body">
    <div class="create xp-news-create">
            <div class="layui-fluid layui-card" style="padding: 30px 30px;">
            <div class="layui-row">
        <!--<h3><?= Html::encode($this->title) ?></h3>-->
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

            </div>
       </div>
    </div>
</div>