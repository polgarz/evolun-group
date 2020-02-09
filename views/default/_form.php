<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use unclead\multipleinput\TabularInput;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\TabularColumn;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin(); ?>
    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Alapadatok</h3>
        </div>

        <div class="box-body">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList($model->typeList) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true])->hint('A csoport központi email címe') ?>

        </div>
    </div>

    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Kordinátorok</h3>
        </div>

        <div class="box-body">

            <?= TabularInput::widget([
                'models' => $groupCoordinatorList,
                'addButtonPosition' => MultipleInput::POS_FOOTER,
                'form' => $form,
                'columns' => [
                    [
                        'name' => 'user_id',
                        'title' => 'Koordinátorok',
                        'type'  => TabularColumn::TYPE_DROPDOWN,
                        'items' => ArrayHelper::map($userList, 'id', function($model) {
                            return $model->name . ($model->nickname ? ' (' . $model->nickname . ')' : '');
                        }),
                        'options' => ['prompt' => '- Válassz - '],
                        'attributeOptions' => [
                            'validateOnChange' => true,
                        ],
                        'enableError' => true
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="box box-default">

        <div class="box-header">
            <h3 class="box-title">Linkek</h3>
        </div>

        <div class="box-body">

            <?= TabularInput::widget([
                'models' => $groupLinkList,
                'addButtonPosition' => MultipleInput::POS_FOOTER,
                'form' => $form,
                'columns' => [
                    [
                        'name' => 'name',
                        'title' => 'Név',
                        'type'  => TabularColumn::TYPE_TEXT_INPUT,
                        'attributeOptions' => [
                            'validateOnChange' => true,
                        ],
                        'enableError' => true
                    ],
                    [
                        'name' => 'url',
                        'title' => 'Url',
                        'type'  => TabularColumn::TYPE_TEXT_INPUT,
                        'attributeOptions' => [
                            'validateOnChange' => true,
                        ],
                        'enableError' => true
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>

