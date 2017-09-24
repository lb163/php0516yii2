<?php
use yii\web\JsExpression;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($goods,'name')->textInput();
echo $form->field($goods,'logo')->hiddenInput();

echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将上传文件的路径写入logo字段的隐藏域
        $("#goods-logo").val(data.fileUrl);
        //图片回显
        $('#img').attr("src",data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//==================
echo \yii\bootstrap\Html::img($goods->logo,['id'=>'img','height'=>'50px']);
echo $form->field($goods,'goods_category_id')->hiddenInput();
//=====================ztree==============
echo '<div><ul id="treeDemo" class="ztree"></ul></div>';
//=====================ztree==============
echo $form->field($goods,'brand_id')->dropDownList(\backend\models\Goods::getBrandOptions(),['prompt'=>'=请选择品牌=']);
echo $form->field($goods,'market_price')->textInput();
echo $form->field($goods,'shop_price')->textInput();
echo $form->field($goods,'stock')->textInput();
echo $form->field($goods,'sort')->textInput();
echo $form->field($goods,'is_on_sale',['inline'=>true])->radioList(['上架','未上架']);
echo $form->field($goods,'status',['inline'=>true])->radioList(['隐藏','显示']);
//echo $form->field($goods,'view_times');
echo $form->field($goods_intro,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        'initialFrameHeight' => '200',
        'lang' =>'en',
    ]
    ]);

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

//注册js文件 (需要在jquery后面加载)
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$goodsCategories = json_encode(\backend\models\GoodsCategory::getZNodes());
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {//事件回调函数
		        onClick: function(event, treeId, treeNode){
		             console.log(treeNode);
		             //获取当前点击节点的id,写入parent_id的值
		             $("#goods-goods_category_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$goodsCategories};
        
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        //展开全部节点
        zTreeObj.expandAll(true);
        //修改 根据当前分类的parent_id来选中节点
        //获取你需要选中的节点 
        var node = zTreeObj.getNodeByParam("id", "{$goods->goods_category_id}", null);
        zTreeObj.selectNode(node);
        
JS
));