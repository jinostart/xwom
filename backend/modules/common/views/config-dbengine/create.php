<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\config\ConfigDbengine */

$this->title = Yii::t('app', 'Create Config Dbengine');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Config Dbengines'), 'url' => ['index']];

?>
<div class="layui-card-body">
    <div class="create config-dbengine-create">
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
