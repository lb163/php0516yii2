<h1>菜单列表</h1>
<table class="table table-hover table-responsive table-condensed">
    <tr>
        <th>名称</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach($model as $row):?>
        <tr data-id="<?=$row->id?>">
            <td><?=$row->name?></td>
            <td><?=$row->sort?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['carte/edit','id'=>$row->id])?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-pencil"></span></a>
                <a href="javascript:;" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
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