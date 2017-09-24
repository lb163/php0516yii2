<a href="<?= \yii\helpers\Url::to(['permission/add'])?>" class="btn btn-info">添加权限</a>
<table class="table table-responsive table-bordered">
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($permissions as $permission):?>
        <tr data_name="<?=$permission->name?>">
            <td><?=$permission->name?></td>
            <td><?=$permission->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['permission/edit','name'=>$permission->name])?>" class="btn btn-default">
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
$del_url = \yii\helpers\Url::to(['permission/del']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
        $(document).ready( function () {
            $('#table_id_example').DataTable();
        } );
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
