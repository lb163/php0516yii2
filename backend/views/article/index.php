<!--<a href="--><?//=\yii\helpers\Url::to(['article/add'])?><!--"  class="btn btn-primary">添加文章</a>-->
<h2>文章列表</h2>
<?php
$form=\yii\bootstrap\ActiveForm::begin([
    'layout'=>'inline','method'=>'get','action'=>['article/index']
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'标题']);
echo $form->field($model,'intro')->textInput(['placeholder'=>'简介']);
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-default']);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table table-striped table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>文章分类</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($article as $row):?>
            <tr data_id="<?=$row->id?>">
                <td><?=$row->id?></td>
                <td><?=$row->name?></td>
                <td><?=$row->intro?></td>
                <td><?=$row->articleCategory->name?></td>
                <td><?=$row->status==0?'隐藏':'正常'?></td>
                <td><?= date('Y-m-d H:i:s',$row->create_time);?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['article/edit','id'=>$row->id])?>" class="btn btn-default ">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a href="javascript:;" class="btn btn-default del_btn">
                        <span class="glyphicon glyphicon-trash"></span>
                    </a>
                    <a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-default ">
                        <span class="glyphicon glyphicon-plus"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
//注册JS代码.
$del_url = \yii\helpers\Url::to(['article/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
        $(".del_btn").click(function() {
            if (confirm("确认删除?")){
                var tr = $(this).closest('tr');
                var id = tr.attr("data_id");
            $.post("{$del_url}",{id:id},function(data){
                if(data == 'success'){
                    alert('删除成功');
                    tr.hide('slow');
                }else{
                    alert('删除失败');
                }
            });
            }
        })
JS
));