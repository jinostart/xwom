<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\AppAsset;
use backend\grid\GridView;
use backend\widgets\Pjax; 
use backend\modules\common\models\WomPlan;
AppAsset::register($this); 
//$this->registerJs($this->render('js/index.js'));
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\common\models\WomPlanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Wom Plans');
$this->params['breadcrumbs'][] = $this->title
?>
<!-- 面包屑 -->
<?= \Yii::$app->view->renderFile('@app/views/public/breadcrumb.php')?>
<!-- 面包屑 -->
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-body ">
                            <form class="layui-form layui-col-space5">
                                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                             </form>
                        </div>
                        <div class="layui-card-header">
                            <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
                            <?= Html::Button('<i class="layui-icon"></i>添加',
                                    [
                                       'onclick' => 'xadmin.open("添加", "'.Url::toRoute(['create']).'",500,550)',
                                       'data-target' => '#create-modal',
                                       'class' => 'layui-btn',
                                       'id' => 'modalButton',
   
                                    ]
                                ) 
                            ?>
                            <!--<?//= Html::a(Yii::t('app', '<i class= layui-icon></i>添加 Wom Plan'), ['create'], ['class' => 'layui-btn layui-default-add']) ?>-->
                        </div>
                        <div class="layui-card-body layui-table-body layui-table-main">
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'options' => ['class' => 'layui-table-box ','style'=>'overflow:auto', 'id' => 'grid'],
                //'layout'=> '{items}<div class="layui-table-page"><div id="layui-table-page1"><div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-1">{pager}</div></div></div>',
                'layout'=> '{items}<div style="margin: 10px 0 0 10px;">{pager}</div>',
		'tableOptions'=> ['class'=>'layui-table','style'=>'width: 100%; '],
		'pager' => [
                        //'options'=>['class'=>'hidden'],//关闭自带分页
			'options'=>['class'=>'layuipage pull-left'],
				'prevPageLabel' => '上一页',
				'nextPageLabel' => '下一页    ',
				'firstPageLabel'=>'首页    ',
				'lastPageLabel'=>'尾页',
				'maxButtonCount'=>5,
                ],
                //GridView控制行样式 rowOptions属性
                
                //'showFooter'=>true,//显示底部（就是多了一栏），默认是关闭的
//                'filterModel' => $searchModel,
		'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],//渲染行号
			[
				'class' => 'backend\grid\CheckboxColumn',//复选框列
				'checkboxOptions' => ['lay-skin'=>'primary','lay-filter'=>'choose'],
				'headerOptions' => ['width'=>'20','style'=> 'text-align: center;'],
				'contentOptions' => ['style'=> 'text-align: center;']
			],
                     //'id',
                     'title',
                     //'desc',
                     'start_at:date',
                     'end_at:date',
                    [
                        'attribute'=>'status',
                        'label'=>'日程状态', 
                        'value'=>'status0.name',
                        'filter'=> \backend\modules\common\models\WomPlanStatus::find()
                                      ->select(['name','id'])
                                      ->orderBy('position')
                                      ->indexBy('id')
                                      ->column(),
                        'contentOptions'=>
                                    function($model)
                                    {
                                            return ($model->status!=3)?['class'=>'layui-btn-danger','width'=>'100px']:['width'=>'100px'];
                                    }
                    ],
                    [
                        'attribute'=>'time_status',
                        'label'=>'时间状态', 
                        'value'=>'timeStatus.name',
                        'filter'=> \backend\modules\common\models\WomPlantimeStatus::find()
                                      ->select(['name','id'])
                                      ->orderBy('position')
                                      ->indexBy('id')
                                      ->column(),
                        'contentOptions'=>
                                    function($model)
                                    {
                                            return ($model->status!=3)?['class'=>'layui-btn-danger','width'=>'100px']:['width'=>'100px'];
                                    }
                        
                    ],
                    [
                        'attribute'=>'admin_id',
                        'label'=>'处理人', 
                        'value'=>'userInfo.real_name',
                        'filter'=> \common\models\User::find()
                                      ->select(['real_name','user_id'])
                                      ->orderBy('user_id')
                                      ->indexBy('user_id')
                                      ->column(),
                    ],
                     'created_at:datetime',
                    [
                        'attribute'=>'created_id',
                        'label'=>'创建人', 
                        'value'=>'createdInfo.real_name',
                    ],
                     'updated_at:date',
                    [
                        'attribute'=>'updated_id',
                        'label'=>'最后修改人',
                        'value'=>'updatedInfo.real_name',
                    ],

            [
            'header' => '<div class="layui-table-cell">操作</div>',
				'class' => 'yii\grid\ActionColumn',
				'headerOptions' => [
					'width' => '20%'
				],
                                'template' =>'<div class="layui-table-cell"> {view} {update} {delete} {approve} </div>',
				'buttons' => [
                                        'view' => function ($url, $model, $key){
                                            //return Html::a('查看', Url::to(['view','id'=>$model->id]), ['class' => "layui-btn layui-btn-xs layui-default-view"]);
                                            return Html::Button(Yii::t('app', 'View'),
                                                    [
                                                    'onclick' => 'xadmin.open("'.Yii::t('app', 'View').'", "'.$url.'",500,550)',
                                                    'data-target' => '#view-modal',
                                                    'class' => 'layui-btn layui-btn-xs layui-default-view',
                                                    'id' => 'modalButton',
                                                    ]
                                                ); 
                                                    
                                        },
                                        'update' => function ($url, $model, $key) {
                                            //return Html::a('修改', Url::to(['update','id'=>$model->id]), ['class' => "layui-btn layui-btn-normal layui-btn-xs layui-default-update"]);
                                            return Html::Button(Yii::t('app', 'Update'),
                                                    [
                                                    'onclick' => 'xadmin.open("'.Yii::t('app', 'Update').'", "'.$url.'",500,550)',
                                                    'data-target' => '#update-modal',
                                                    'class' => 'layui-btn layui-btn-normal layui-btn-xs layui-default-update',
                                                    'id' => 'modalButton',
                                                    ]
                                                );  
                                                    
                                        },

                                        'delete'=>function($url,$model,$key)
                                            {
                                                $options=[
                                                    'title'=>Yii::t('app', 'Delete'),
                                                    'aria-label'=>Yii::t('app','Delete'),
                                                    'data-confirm'=>Yii::t('app','Are you sure you want to delete this item?'),
                                                    'data-method'=>'post',
                                                    'data-pjax'=>'0',
                                                    'class'=>'layui-btn layui-btn-danger layui-btn-xs layui-default-delete'
                                                    ];
                                                return Html::a(Yii::t('app', 'Delete'),$url,$options); 
                                            },
                                        //接手
                                        'approve'=>function($url,$model,$key)
                                        {
                                                $options=[
                                                        'title'=>Yii::t('app', 'rove task'),
                                                        'aria-label'=>Yii::t('app','rove task'),
                                                        'data-confirm'=>Yii::t('app','Are you sure you want to rove this item?'),
                                                        'data-method'=>'post',
                                                        'data-pjax'=>'0',
                                                        'class'=>'layui-btn layui-btn-normal layui-btn-xs layui-default-approve'
                                                                ];
                                                if($model->status ==1){
                                                  return Html::a('接手任务', $url, $options); 
                                                } else {
                                                   return Html::a('已委派',$url,$options);

                                                }

                                        },
				]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>   
                        </div>

                    </div>
                </div>
            </div>
        </div> 

    <script>
      layui.use(['laydate','form'], function(){
        var laydate = layui.laydate;
        var  form = layui.form;


        // 监听全选
        form.on('checkbox(checkall)', function(data){

          if(data.elem.checked){
            $('tbody input').prop('checked',true);
          }else{
            $('tbody input').prop('checked',false);
          }
          form.render('checkbox');
        }); 
        
        //执行一个laydate实例
        laydate.render({
          elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#end' //指定元素
        });


      });
      /*用户-删除*/
      function delAll (argument) {
        var ids = [];

        // 获取选中的id 
        $('tbody input').each(function(index, el) {
            if($(this).prop('checked')){
               ids.push($(this).val())
            }
        });
  
        layer.confirm('确认要删除吗？'+ids.toString(),function(index){
            //捉到所有被选中的，发异步进行删除
            layer.msg('删除成功', {icon: 1});
            $(".layui-form-checked").not('.header').parents('tr').remove();
        });
      }
    </script>