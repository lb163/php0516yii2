<h2>文章列表</h2>
<table class="table table-bordered table-hover">
    <tr>
        <td>ID</td>
        <td>名称</td>
        <td>操作</td>
    </tr>
    <?php foreach ($category as $row):?>
        <tr data-id="<?=$row->id?>">
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td>
                <a href="javascript:;" class="btn btn-default del_btn"><span class="glyphicon glyphicon-trash"></span></a>
                <a href="<?=\yii\helpers\Url::to(['category/edit','id'=>$row->id])?>" class="glyphicon glyphicon-pencil btn btn-default"></a>
                <a href="<?=\yii\helpers\Url::to(['category/add'])?>"><span class="glyphicon glyphicon-list btn btn-default"></span></a>
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
$del_url = \yii\helpers\Url::to(['category/del']);
//注册js代码
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    $(".del_btn").click(function(){
        if(confirm('确定要删除吗?')){
            var tr = $(this).closest('tr');
            var id = tr.attr("data-id");
            console.log(tr);
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