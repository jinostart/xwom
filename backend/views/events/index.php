<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use common\models\Events;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EventsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '日历事件';
//$this->params['breadcrumbs'][] = $this->title;
?>
<!-- 面包屑 -->
<?= \Yii::$app->view->renderFile('@app/views/public/breadcrumb.php')?>
<!-- 面包屑 -->
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card" style="padding: 10px;">
                
<style>
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
    border: 1px solid #d6d6d6;
}

element.style {
    overflow: hidden scroll;
    /* height: 395px; */
}
</style>

<div class="events-index">
<p style="margin-bottom: 20px;margin-top: 5px;padding-top:10px;">
    <?= Html::button('新增日历事件', ['value' =>  Url::toRoute(['events/create', 'date' => date('Y-m-d'), 't'=>time()]), 'class' => 'btn btn-success', 'id' => 'modalButton']) ?>
</p>

<?php
    Modal::begin([
        'header' => '<h4>新增日历事件</h4>',
        'id' => 'modal',
        'size' => 'modal-lg',
    ]);

    echo "<div id='modalContent'></div>";

    Modal::end();
?>

<?= \yii2fullcalendar\yii2fullcalendar::widget([
    'events'=> $events,
    'options' => [
        'lang' => 'zh-cn',
    ],
]); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!-- 
    <//?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'headerOptions' => array('style'=>'width:10%;'),
            ],

            'title',
            // 'event_type',
            [
                'attribute' => 'event_type',
                'label' => '事件类型',
                'value'=>function ($model, $key, $index, $column) {
                    return Events::getEventTypeOption($model->event_type);
                },
                'filter'=> Events::getEventTypeOption(),
                'headerOptions' => array('style'=>'width:15%;'),
            ],
            //'description:ntext',
            //'library_id',
            //'user_id',
            'created_at:datetime',
            //'updated_at',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'layout'=>"{items}\n{summary}{pager}",
    ]); ?> -->
</div>
            </div>
        </div>
    </div>
</div> 

<?php
    $this->registerJs('$(function(){
        $(".fc-day, .fc-day-top").click(function(){
            var date = $(this).data("date");
            var d = new Date();

            $.get("'.Url::toRoute(["events/create"]).'", {"date": date, "t": d.getTime()}, function(data){
                $("#modal").modal("show")
                    .find("#modalContent")
                    .html(data);
            });
        });
        $("#modalButton").click(function(){
            $("#modal").modal("show")
                .find("#modalContent")
                .load($(this).attr("value"));
        });
    });');
?>
