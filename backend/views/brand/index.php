<?=\yii\bootstrap\Html::a('添加商品',['brand/add'],['class'=>'btn btn-warning'])?>
<table class="table table-striped table-bordered table-hover">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brand as $row):?>
        <tr data-id="<?=$row->id?>">
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td><?=$row->intro?></td>
            <td><img src="<?=$row->logo?>" class="img-circle" style="width: 80px"/></td>
            <td>
                <a href="javascript:;" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$row->id])?>"class="glyphicon glyphicon-pencil"></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
/*
 * @var $this \yii\web\View
 * */
$del_url = \yii\helpers\Url::to(['brand/del']);
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