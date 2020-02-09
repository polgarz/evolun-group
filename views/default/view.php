<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;
use yii\web\View;
use evolun\group\assets\SummernoteAsset;

/* @var $this yii\web\View */
/* @var $model evolun\group\user\models\User */

SummernoteAsset::register($this);

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Csoportok', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['pageHeader'] = ['title' => '&nbsp;'];

$this->registerJs("
    $('#summernote').summernote({
        placeholder: 'Bejegyzés szövege',
        minHeight: 100,
        maxHeight: 400,
        lang: 'hu-HU',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'table']],
        ]
    });
    ", View::POS_READY);
?>
<div class="row">
    <div class="col-lg-3 col-md-4">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <h3 class="profile-username text-center"><?= $model->name ?></h3>

                <p class="text-muted text-center"><?= $model->typeTitle ?></p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Tagok száma</b> <span class="pull-right"><?= count($model->groupUsers) ?></span>
                    </li>
                    <?php if (!empty($model->email)): ?>
                        <li class="list-group-item">
                            <b><?= $model->getAttributeLabel('email') ?></b> <a target="_blank" href="mailto:<?= $model->email ?>" class="pull-right"><?= StringHelper::truncate($model->email, 25) ?></a>
                        </li>
                    <?php endif ?>
                </ul>

                <?php if (Yii::$app->user->can('manageGroups', ['group' => $model])): ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <?= Html::a('<i class="fa fa-pencil"></i> Módosítás', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-block']) ?>
                        </div>
                        <div class="col-xs-6">
                            <?= Html::a('<i class="fa fa-trash"></i> Törlés', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger btn-block', 'data-confirm' => 'Biztosan törlöd ezt a csoportot? Minden hozzá tartozó adat törlődni fog!']) ?>
                        </div>
                    </div>
                    <br />
                <?php endif ?>

                <?php if (!in_array(Yii::$app->user->id, array_map(function($item) { return $item->user_id; }, $model->groupUsers))): ?>
                    <?= Html::a('<i class="fa fa-sign-in"></i> Csatlakozás a csoporthoz', ['join', 'id' => $model->id], ['class' => 'btn btn-success btn-block']) ?>
                <?php else: ?>
                    <?= Html::a('<i class="fa fa-sign-out"></i> Kilépés a csoportból', ['leave', 'id' => $model->id], ['class' => 'btn btn-warning btn-block']) ?>
                <?php endif ?>

            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Koordinátorok</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <ul class="users-list clearfix">
                    <?php foreach($model->groupCoordinators as $coordinator): ?>
                        <li>
                            <img src="<?= $coordinator->user->getThumbUploadUrl('image', 's') ?>" class="img-circle" alt="Profilkép">
                            <?php if (Yii::$app->user->can('showUsers')): ?>
                                <a class="users-list-name" href="<?= Url::to(['/user/default/view', 'id' => $coordinator->user_id]) ?>"><?= $coordinator->user->nickname ?></a>
                            <?php else: ?>
                                <div class="users-list-name"><?= $coordinator->user->nickname ?></div>
                            <?php endif ?>
                        </li>
                    <?php endforeach ?>
                </ul>
                <!-- /.users-list -->
            </div>
            <!-- /.box-body -->
        </div>

        <?php if ($model->groupLinks): ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Linkek</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <?php foreach($model->groupLinks as $link): ?>
                        <p><a href="<?= $link->url ?>" target="_blank"><?= $link->name ?></a></p>
                    <?php endforeach ?>
                </div>
                <!-- /.box-body -->
            </div>
        <?php endif ?>

        <?php if (!empty($model->description)): ?>
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Leírás</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <?= $model->description ?>
                    <!-- /.box-body -->
                </div>
            </div>
        <?php endif ?>

    </div>
    <!-- /.col -->
    <div class="col-lg-9 col-md-8">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#news" data-toggle="tab">Hírek, információk</a></li>
                <li><a href="#members" data-toggle="tab">Tagok</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="news">
                    <?= ListView::widget([
                        'dataProvider' => $postsDataProvider,
                        'layout' => '{items}{pager}',
                        'emptyText' => '<p>Nincsenek bejegyzések</p>',
                        'options' => ['tag' => false],
                        'itemOptions' => ['tag' => 'div', 'class' => 'post clearfix'],
                        'itemView' => '_post',
                        'viewParams' => [
                            'excerpt' => false,
                            'showGroupName' => false,
                        ]
                    ]) ?>

                    <?php if (Yii::$app->user->can('manageGroups', ['group' => $model])): ?>
                        <p><strong>Új bejegyzés írása</strong></p>
                        <!-- uj poszt -->
                        <div>
                            <?php $form = ActiveForm::begin(); ?>

                            <?= $form->field($postModel, 'title')->textInput(['placeholder' => $postModel->getAttributeLabel('title')])->label(false) ?>

                            <?= $form->field($postModel, 'content')->textArea(['rows' => 8, 'id' => 'summernote'])->label(false) ?>

                            <?php if (!Yii::$app->request->get('update_group_post')): ?>
                                <?= Html::submitButton('Közzététel', ['class' => 'btn btn-success']) ?>
                            <?php else: ?>
                                <?= Html::submitButton('Mentés', ['class' => 'btn btn-success']) ?>
                                <?= Html::a('Mégse', Url::current(['update_group_post' => null]), ['class' => 'btn btn-default']) ?>
                            <?php endif ?>

                            <?php ActiveForm::end(); ?>
                        </div>
                    <?php endif ?>
                </div>
                <div class="tab-pane" id="members">
                    <?= ListView::widget([
                        'dataProvider' => $usersDataProvider,
                        'layout' => '{items}{pager}',
                        'emptyText' => '<p>Nincsenek tagok</p>',
                        'itemOptions' => ['tag' => 'p'],
                        'itemView' => function($model) {
                            if (Yii::$app->user->can('showUsers')) {
                                return Html::a($model->user->name . ' (' . $model->user->nickname . ')', ['/user/default/view', 'id' => $model->user_id]);
                            } else {
                                return $model->user->name . ' (' . $model->user->nickname . ')';
                            }
                        },
                    ]) ?>
                </div>
                <!-- /.tab-pane -->
            </div>
        <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
