<h1>管理员列表</h1>
<a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-info">添加用户</a>
<table class="table table-hover table-responsive table-bordered table-view table-striped tab">
    <tr>
        <td>ID</td>
        <td>用户名</td>
        <td>邮箱</td>
        <td>状态</td>
        <td>操作</td>
    </tr>
        <?php foreach($admin as $row): ?>
            <tr data-id="<?=$row->id?>">
                <td><?=$row->id?></td>
                <td><?=$row->username?></td>
                <td><?=$row->email?></td>
                <td><?=$row->status==0?'隐藏':'正常'?></td>
                <td>
                    <a href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$row->id])?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="javascript:;" class="btn btn-default del_btn">
                        <span class="glyphicon glyphicon-trash"></a>
                </td>
            </tr>
        <?php endforeach; ?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页'
]);
$del_url = \yii\helpers\Url::to(['admin/del']);
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