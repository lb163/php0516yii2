<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($brand,'name')->textInput();
echo $form->field($brand,'intro')->textarea();
echo $form->field($brand,'file')->fileInput();
echo \yii\bootstrap\Html::img($brand->logo,['class'=>'img-circle','style'=>"width:120px"]);
echo $form->field($brand,'sort')->textInput();
echo $form->field($brand,'status',['inline'=>true])->radioList(['隐藏','显示']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();