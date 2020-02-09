<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Csoportok</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php if ($groups): ?>
            <?php foreach($groups as $group): ?>
                <p><a href="<?= Url::to(['/group/default/view', 'id' => $group->id]) ?>"><?= $group->name ?></a></p>
            <?php endforeach ?>
        <?php else: ?>
            Még nem csatlakozott csoporthoz
        <?php endif ?>
    </div>
</div>