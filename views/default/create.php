<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model evolun\group\user\models\User */

$this->title = 'Új csoport';
$this->params['pageHeader'] = ['title' => $this->title];
$this->params['breadcrumbs'][] = ['label' => 'Csoportok', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'userList' => $userList,
    'groupCoordinatorList' => $groupCoordinatorList,
    'groupLinkList' => $groupLinkList,
]) ?>