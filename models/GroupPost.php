<?php

namespace evolun\group\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group_post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $group_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Group $group
 * @property User $createdBy
 * @property User $updatedBy
 */
class GroupPost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_post';
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
            [['title', 'group_id'], 'required'],
            [['content'], 'string'],
            [['group_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Yii::$app->user->identityClass, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Bejegyzés címe',
            'content' => 'Szöveg',
            'group_id' => 'Group ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'createdByName' => 'Létrehozta',
            'updatedByName' => 'Utoljára módosította',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
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
    public function getCreatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'updated_by']);
    }
}
