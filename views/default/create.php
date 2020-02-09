<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\group\user\models\User */

$this->title = Yii::t('group', 'New group');
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = ['label' => Yii::t('group', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'userList' => $userList,
    'groupCoordinatorList' => $groupCoordinatorList,
    'groupLinkList' => $groupLinkList,
]) ?>