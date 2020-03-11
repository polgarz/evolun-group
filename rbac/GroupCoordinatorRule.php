<?php
namespace evolun\group\rbac;

use yii\rbac\Rule;
use yii\helpers\ArrayHelper;

/**
 * Checks if authorID matches user passed via params
 */
class GroupCoordinatorRule extends Rule
{
    public $name = 'isGroupCoordinator';

    /**
     * Belso cache: a csoport kordinatorai
     * @var array
     */
    public $_groupCoordinators = [];

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if (isset($params['group'])) {
            if (!isset($this->_groupCoordinators[$params['group']->id])) {
                $this->_groupCoordinators[$params['group']->id] = ArrayHelper::getColumn($params['group']->groupCoordinators, 'user_id');
            }

            return in_array($user, $this->_groupCoordinators[$params['group']->id]);
        }

        return false;
    }
}
