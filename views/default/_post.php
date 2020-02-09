<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\StringHelper;
?>
<div class="user-block">
    <?php if ($model->createdBy): ?>
        <img src="<?= $model->createdBy->getThumbUploadUrl('image', 's') ?>" class="img-circle img-bordered-sm" alt="Profilkép">
    <?php else: ?>
        <img src="https://via.placeholder.com/100x100?text=%3F" class="img-circle img-bordered-sm" alt="Profilkép">
    <?php endif ?>
    <span class="username">
        <?php if ($model->createdByName): ?>
            <a href="<?= Url::to(['/user/default/view', 'id' => $model->created_by]) ?>"><?= $model->createdByName ?></a>
        <?php else: ?>
            <span>Törölt felhasználó</span>
        <?php endif ?>

        <?php if (Yii::$app->user->can('manageGroups', ['group' => $model->group])): ?>
            <a href="<?= Url::to(['/group/default/delete-group-post', 'id' => $model->group_id, 'post_id' => $model->id]) ?>" class="pull-right btn-box-tool" data-confirm="Biztosan törlöd ezt a posztot?">
                <i class="fa fa-trash"></i>
            </a>
            <a href="<?= Url::to(['/group/default/view', 'id' => $model->group_id, 'update_group_post' => $model->id]) ?>" class="pull-right btn-box-tool">
                <i class="fa fa-pencil"></i>
            </a>
        <?php endif ?>
    </span>
    <span class="description">
        <?= Yii::$app->formatter->asDate($model->created_at, 'long') ?>

        <?php if ($showGroupName): ?>
            - <?= Html::a($model->group->name, ['/group/default/view', 'id' => $model->group_id]) ?>
        <?php endif ?>
    </span>
</div>
<!-- /.user-block -->
<h4><?= $model->title ?></h4>
<div>
    <?php if (!$excerpt): ?>
        <?= $model->content ?>
    <?php else: ?>
        <p><?= StringHelper::truncateWords(strip_tags($model->content), 20, '..') ?></p>
    <?php endif ?>
</div>