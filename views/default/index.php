<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('group', 'Groups');
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-default">

    <?php if (Yii::$app->user->can('manageGroups')): ?>
        <div class="box-header">
            <div class="box-tools pull-left">
                <?= Html::a('<i class="fa fa-plus"></i> ' . Yii::t('group', 'New group'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    <?php endif ?>

    <div class="box-body table-responsive">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'showHeader' => false,
            'tableOptions' => ['class' => 'table table-hover'],
            'layout' => '{items}{summary}{pager}',
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $row = '<strong>' . $model->name . '</strong>';
                        $row .= '<div class="text-muted">' . Yii::t('group', 'Members: {members}', ['members' => count($model->groupUsers)]) . '</div>';
                        $row .= '<div class="text-muted">' . StringHelper::truncateWords($model->description, 15) . '</div>';

                        return Html::a($row, ['view', 'id' => $model->id], ['class' => 'col-link text-default']);
                    },
                ],
            ],
        ]); ?>
    </div>
</div>
