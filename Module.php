<?php

namespace evolun\group;

use yii;

class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'evolun\group\controllers';

    /**
     * A csoport főtípusok listája (pl.: szakmai csoport,
     * fenntartásért felelős csoport, stb..)
     * kulcs -> elnevezés párok
     * @var array
     */
    public $typeList = [];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!Yii::$app->user->identity instanceof \evolun\user\models\User) {
            throw new \yii\base\InvalidConfigException('You have to install \'evolun-user\' to use this module');
        }

        $this->registerTranslations();

        if (empty($this->typeList)) {
            $this->typeList['general'] = Yii::t('group', 'General');
        }
    }

    public function registerTranslations()
    {
        if (!isset(Yii::$app->get('i18n')->translations['group'])) {
            Yii::$app->get('i18n')->translations['group*'] = [
                'class' => \yii\i18n\PhpMessageSource::className(),
                'basePath' => __DIR__ . '/messages',
                'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'group' => 'group.php',
                ]
            ];
        }
    }
}
