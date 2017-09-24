<h2>商品列表</h2>
<?php
$form=\yii\bootstrap\ActiveForm::begin([
    'layout'=>'inline','method'=>'get','action'=>['goods/index']
]);
echo $form->field($model,'name')->textInput(['placeholder'=>'商品名']);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号']);
echo $form->field($model,'min')->textInput(['placeholder'=>'$0']);
echo $form->field($model,'max')->textInput(['placeholder'=>'$999']);
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-default']);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table table-bordered table-hover">
    <tr>
        <th>ID</th>
        <th>货号</th>
        <th>商品名称</th>
        <th>市场价格</th>
        <th>库存</th>
        <th>LOGO图片</th>
        <th>操作</th>
    </tr>
    <?php foreach ($goods as $row):?>
        <tr data-id="<?=$row->id?>">
            <td><?=$row->id?></td>
            <td><?=$row->sn?></td>
            <td><?=$row->name?></td>
            <td><?=$row->stock?></td>
            <td><?=$row->market_price?></td>
            <td><img src="<?=$row->logo?>" class="img-circle" style="width: 80px"/></td>
            <td>
                <a href="javascript:;" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$row->id])?>" class="btn btn-default "><span class="glyphicon glyphicon-pencil"></a>
                <a href="<?=\yii\helpers\Url::to(['goods/add'])?>" class="btn btn-default ">
                    <span class="glyphicon glyphicon-plus"></span>
                    <a href="<?=\yii\helpers\Url::to(['goods/gallery','id'=>$row->id])?>" class="btn btn-default "><span class="glyphicon glyphicon-expand"></a>
                </a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);

$del_url = \yii\helpers\Url::to(['goods/del']);
//注册js代码
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $(".del_btn").click(function(){
        if(confirm('确定要删除吗?')){
            var tr = $(this).closest('tr');
            var id = tr.attr("data-id");
            $.post("{$del_url}",{id:id},function(data){
                if(data == 'success'){
                    alert('删除成功');
                    tr.hide('slow');
                }else{
                    alert('删除失败');
                }
            });
        }
    });
JS
));