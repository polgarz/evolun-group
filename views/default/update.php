<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\group\user\models\User */

$this->title = 'Csoport adatainak módosítása: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Csoportok', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Módosítás';
$this->params['pageHeader'] = ['title' => $this->title];
?>

<?= $this->render('_form', [
    'model' => $model,
    'userList' => $userList,
    'groupCoordinatorList' => $groupCoordinatorList,
    'groupLinkList' => $groupLinkList,
]) ?>