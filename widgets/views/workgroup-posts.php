<?php
use yii\widgets\ListView;
use yii\helpers\Url;
?>

<div class="col-sm-6">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Csoport hírek, információk</h3>
        </div>

        <div class="box-body">
            <?php if (!$hasGroups): ?>
                <div class="alert alert-warning">
                    Még egy csoporthoz sem csatlakoztál, csatlakozz egyhez, hogy mindig értesülj a téged érintő információkról.
                </div>
            <?php endif ?>

            <?= ListView::widget([
                'dataProvider' => $postsDataProvider,
                'layout' => '{items}{pager}',
                'emptyText' => '<p>Nincsenek bejegyzések</p>',
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
            <a href="<?= Url::to(['/group/default/index']) ?>">További csoportok</a>
        </div>
    </div>
</div>