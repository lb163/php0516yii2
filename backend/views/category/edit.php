<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($category,'name')->textInput();
echo $form->field($category,'intro')->textInput();
echo $form->field($category,'sort')->textInput();
echo $form->field($category,'status',['inline'=>true])->radioList(['-1'=>'删除','0'=>'隐藏','1'=>'正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();