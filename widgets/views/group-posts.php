<?php
use yii\widgets\ListView;
use yii\helpers\Url;
?>

<div class="col-sm-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= Yii::t('group', 'Group posts') ?></h3>
        </div>

        <div class="box-body">
            <?php if (!$hasGroups): ?>
                <div class="alert alert-warning">
                    <?= Yii::t('group', 'You haven\'t joined in any group yet') ?>
                </div>
            <?php endif ?>

            <?= ListView::widget([
                'dataProvider' => $postsDataProvider,
                'layout' => '{items}{pager}',
                'emptyText' => '<p>' . Yii::t('group', 'There are no posts') . '</p>',
                'options' => ['tag' => false],
                'itemOptions' => ['tag' => 'div', 'class' => 'post clearfix'],
                'itemView' => '@app/modules/group/views/default/_post',
                'viewParams' => [
                    'excerpt' => true,
                    'showGroupName' => true,
                ]
            ]) ?>
        </div>

        <div class="box-footer">
            <a href="<?= Url::to(['/group/default/index']) ?>"><?= Yii::t('group', 'More groups') ?></a>
        </div>
    </div>
</div>