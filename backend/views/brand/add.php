<?php
use yii\web\JsExpression;
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($brand,'name')->textInput();
echo $form->field($brand,'intro')->textarea();
//echo $form->field($brand,'file')->fileInput();
//echo \yii\bootstrap\Html::img($brand->logo,['class'=>'img-circle','style'=>"width:120px"]);
echo $form->field($brand,'logo')->hiddenInput();
//=================uploadifive插件
//外部TAG
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
        $("#brand-logo").val(data.fileUrl);
        //图片回显
        $('#img').attr("src",data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//==================
echo \yii\bootstrap\Html::img($brand->logo,['id'=>'img']);
echo $form->field($brand,'sort')->textInput();
echo $form->field($brand,'status',['inline'=>true])->radioList(['隐藏','显示']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();