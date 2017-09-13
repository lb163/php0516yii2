<?php
/* @var $this yii\web\View */
?>
<h2>商品分类列表</h2>
<?=\yii\bootstrap\Html::a('添加分类',['goods_category/add'],['class'=>'btn btn-info'])?>
<!--<a href="--><?//=\yii\helpers\Url::to(['goods_category/add'])?><!--" class="btn btn-info">添加分类</a>-->
<table class="table table-responsive table-bordered">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr>
            <td><?=$model['id']?></td>
            <td><?=str_repeat('— ',$model['depth']).$model['name']?></td>
            <td><?=\yii\bootstrap\Html::a('修改',['edit','id'=>$model['id']],['class'=>'btn btn-xs btn-warning'])?>
                <?=\yii\bootstrap\Html::a('删除',['del','id'=>$model['id']],['class'=>'btn btn-xs btn-danger'])?></td>
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
