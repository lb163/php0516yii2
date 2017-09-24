<a href="<?= \yii\helpers\Url::to(['permission/add-role'])?>" class="btn btn-info">添加角色</a>
<table class="table table-striped table-bordered table-hover">
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($role as $row):?>
        <tr data_name="<?=$row->name?>">
            <td><?=$row->name?></td>
            <td><?=$row->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['permission/edit-role','name'=>$row->name])?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-pencil"></span></a>
                <a href="javascript:;" class="btn btn-default del_btn">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//注册JS代码.
$del_url = \yii\helpers\Url::to(['permission/role-del']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
        $(".del_btn").click(function() {
            if (confirm("确认删除?")){
                var tr = $(this).closest('tr');
                var name = tr.attr("data_name");
            $.post("{$del_url}",{name:name},function(data){
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