<?php

use yii\db\Migration;

/**
 * Class m191010_210511_rbac_init
 */
class m191010_210511_rbac_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');

        $manageGroups = $auth->createPermission('manageGroups');
        $manageGroups->description = 'Hozzáadhat, módosíthat, és törölhet csoportokat';
        $auth->add($manageGroups);

        $showGroups = $auth->createPermission('showGroups');
        $showGroups->description = 'Megtekintheti a csoportokat';
        $auth->add($showGroups);

        // add the rule
        $rule = new \evolun\group\rbac\GroupCoordinatorRule;
        $auth->add($rule);

        $manageOwnGroups = $auth->createPermission('manageOwnGroups');
        $manageOwnGroups->description = 'Kezelheti azt a csoportot, ahol koordinátor';
        $manageOwnGroups->ruleName = $rule->name;
        $auth->add($manageOwnGroups);

        $auth->addChild($admin, $showGroups);
        $auth->addChild($admin, $manageGroups);
        $auth->addChild($manageOwnGroups, $manageGroups);
        $auth->addChild($admin, $manageOwnGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $manageGroups = $auth->getPermission('manageGroups');
        $auth->remove($manageGroups);
        $showGroups = $auth->getPermission('showGroups');
        $auth->remove($showGroups);
        $manageOwnGroups = $auth->getPermission('manageOwnGroups');
        $auth->remove($manageOwnGroups);
        $rule = $auth->getRule('isGroupCoordinator');
        $auth->remove($rule);
    }
}
