<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($admin,'username');
echo $form->field($admin,'password')->passwordInput();
echo $form->field($admin,'email')->textInput();
echo $form->field($admin,'status',['inline'=>true])->radioList(['0'=>'隐藏','1'=>'正常']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();