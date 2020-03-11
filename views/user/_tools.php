<?php
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\bootstrap\ActiveForm;
?>

<?php if (Yii::$app->user->can('manageUsers')): ?>
    <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('user', 'New volunteer'), ['create'], ['class' => 'btn btn-success pull-left', 'style' => 'margin-right: 10px']) ?>
<?php endif ?>

<?php if (Yii::$app->user->can('showGroups')): ?>
    <div class="btn-group pull-left" style="margin-right: 5px">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php if ($searchModel->validate() && $searchModel->group): ?>
                <?= $searchModel->groupList[$searchModel->group] ?>
            <?php else: ?>
                <?= Yii::t('user', 'Groups') ?>
            <?php endif ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><a href="javascript:;" onclick="$('#usersearch-group').val('');$('#user-search-form').submit();"><?= Yii::t('user', 'All groups') ?></a></li>
            <?php foreach ($searchModel->groupList as $id => $name): ?>
                <li><a href="javascript:;" onclick="$('#usersearch-group').val('<?= $id ?>');$('#user-search-form').submit();"><?= $name ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['index'], 'id' => 'user-search-form']); ?>
    <div class="input-group">
        <?= $form->field($searchModel, 'searchString', ['options' => ['tag' => false], 'inputOptions' => ['placeholder' => Yii::t('user', 'Search by email, name, and others')]])->label(false) ?>
        <div class="input-group-btn">
            <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?= $form->field($searchModel, 'group', ['options' => ['tag' => false]])->hiddenInput()->label(false)->hint(false)->error(false) ?>
<?php ActiveForm::end(); ?>

