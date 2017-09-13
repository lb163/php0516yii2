<?php $form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($article,'name');
echo $form->field($article,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($category,'id','name'));
echo $form->field($article,'intro')->textarea(['rows'=>5]);
echo $form->field($article,'status',['inline'=>true])->radioList(['-1'=>'删除','0'=>'隐藏','1'=>'正常'],['class'=>'label-group']);
echo $form->field($article,'sort')->input('number');
echo $form->field($article_detail,'content')->textarea(['rows'=>8]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();