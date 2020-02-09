<?php

namespace evolun\group\models;

use Yii;
use evolun\group\Module;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property User $updatedBy
 * @property User $createdBy
 * @property GroupCoordinator[] $groupCoordinators
 * @property GroupLink[] $groupLinks
 * @property GroupUser[] $groupUsers
 */
class Group extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => BlameableBehavior::className()],
            ['class' => TimestampBehavior::className(), 'value' => new \yii\db\Expression('NOW()')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['description'], 'string'],
            [['email'], 'email'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['name', 'type', 'email'], 'string', 'max' => 255],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['updated_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Név',
            'type' => 'Típus',
            'description' => 'Leírás',
            'email' => 'Email cím',
            'created_at' => 'Létrehozva',
            'updated_at' => 'Utoljára módosítva',
            'created_by' => 'Létrehozta',
            'updated_by' => 'Utoljára módosította',
            'createdByName' => 'Létrehozta',
            'updatedByName' => 'Utoljára módosította',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
    }

    /**
     * Visszaadja annak a felhasználónak a nevét, aki létrehozta ezt a rekordot
     * @return string
     */
    public function getCreatedByName()
    {
        if ($this->createdBy) {
            return $this->createdBy->name;
        }
    }

    /**
     * Visszaadja annak a felhasználónak a nevét, aki utoljára módosította ezt a rekordot
     * @return string
     */
    public function getUpdatedByName()
    {
        if ($this->updatedBy) {
            return $this->updatedBy->name;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupCoordinators()
    {
        return $this->hasMany(GroupCoordinator::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupLinks()
    {
        return $this->hasMany(GroupLink::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupUsers()
    {
        return $this->hasMany(GroupUser::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupPosts()
    {
        return $this->hasMany(GroupPost::className(), ['group_id' => 'id']);
    }

    /**
     * Felhasználó hozzáadása a csoporthoz
     * @param User $user A felhasználó
     */
    public function addUser($user)
    {
        $model = new GroupUser(['user_id' => $user->id, 'group_id' => $this->id]);
        return $model->save();
    }

    /**
     * Felhasználó eltávolítása a csoportból
     * @param User $user A felhasználó
     */
    public function removeUser($user)
    {
        $model = GroupUser::findOne(['user_id' => $user->id, 'group_id' => $this->id]);
        if ($model) {
            return $model->delete();
        }

        return false;
    }

    /**
     * Visszaadja a típus szép nevét
     * @return string
     */
    public function getTypeTitle()
    {
        $module = Module::getInstance();
        if (isset($module->typeList[$this->type])) {
            return $module->typeList[$this->type];
        }
    }

    /**
     * Visszaadja a típus listát (modulból)
     * @return array
     */
    public function getTypeList()
    {
        $module = Module::getInstance();

        return $module->typeList;
    }
}
