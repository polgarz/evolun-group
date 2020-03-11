<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\group\user\models\User */

$this->title = Yii::t('group', 'Update group: {name}', ['name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('group', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('group', 'Update');
$this->params['pageHeader'] = ['title' => $this->title];
?>

<?= $this->render('_form', [
    'model' => $model,
    'userList' => $userList,
    'groupCoordinatorList' => $groupCoordinatorList,
    'groupLinkList' => $groupLinkList,
]);
