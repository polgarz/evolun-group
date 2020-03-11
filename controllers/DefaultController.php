<?php

namespace evolun\group\controllers;

use Yii;
use evolun\group\models\Group;
use evolun\group\models\GroupCoordinator;
use evolun\group\models\GroupLink;
use evolun\group\models\GroupUser;
use evolun\group\models\GroupPost;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * A csoportok kontrollere
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow'   => true,
                        'roles'   => ['manageGroups'],
                    ],
                    [
                        'actions'    => ['update', 'delete', 'delete-group-post'],
                        'allow'      => true,
                        'roles'      => ['manageGroups'],
                        'roleParams' => function ($rule) {
                            $groups = Group::findOne(Yii::$app->request->get('id'));

                            if ($groups) {
                                return ['group' => $groups];
                            }
                        }
                    ],
                    [
                        'actions' => ['view', 'index', 'join', 'leave'],
                        'allow'   => true,
                        'roles'   => ['showGroups'],
                    ],
                ]
            ],
        ];
    }

    /**
     * A csoportok listája
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Group::find()->with('groupUsers')->orderBy('name'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * A csoport adatlapja
     * @param integer $id
     * @param integer $update_group_post ha egy kordinátor módosít egy posztot, akkor itt tároljuk a poszt id-ját
     * @return mixed
     * @throws NotFoundHttpException ha nem létező csoportot próbál behozni
     */
    public function actionView($id, $update_group_post = null)
    {
        $model = $this->findModel($id);

        $usersDataProvider = new ActiveDataProvider([
            'query' => $model->getGroupUsers()->joinWith('user'),
            'sort' => [
                'attributes' => [
                    'name' => [SORT_ASC => 'user.name', SORT_DESC => 'user.name']
                ],
                'defaultOrder' => ['name' => SORT_ASC],
            ],
            'pagination' => false,
        ]);

        $postsDataProvider = new ActiveDataProvider([
            'query' => $model->getGroupPosts()->with(['createdBy', 'group']),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 3
            ],
        ]);


        $postModel = new GroupPost(['group_id' => $id]);

        // ha van jogosultsaga egyaltalan a posztok szerkesztesehez
        if (Yii::$app->user->can('manageGroups', ['group' => $model])) {
            // megnezzuk, hogy kaptunk-e modositasra id-t, es letezik-e a poszt
            if ($update_group_post) {
                $exists = GroupPost::findOne(['id' => $update_group_post, 'group_id' => $id]);

                if ($exists) {
                    $postModel = $exists;
                }
            }

            if ($postModel->load(Yii::$app->request->post())) {
                if ($postModel->save()) {
                    Yii::$app->session->setFlash('success', 'You successfully created the post');

                    // ha ujkent vitte fel, akkor resetelni kell a modelt, hogy ne toltse vissza a szovegeket
                    if (!$update_group_post) {
                        $postModel = new GroupPost(['group_id' => $id]);
                    } else {
                        // ha modositas volt, akkor kiszedjuk a update_group_post-et, hogy ne mentsen ra meg egyszer
                        $this->redirect(['view', 'id' => $model->id]);
                    }
                } else {
                    Yii::$app->session->setFlash('danger', Yii::t('group', 'Something went wrong when you tried to create the post'));
                }
            }
        }

        return $this->render('view', [
            'model' => $model,
            'postModel' => $postModel,
            'usersDataProvider' => $usersDataProvider,
            'postsDataProvider' => $postsDataProvider,
        ]);
    }

    /**
     * Torol egy konkret posztot, ami a csoporthoz tartozik
     * @param  integer $id A csoport id-ja (jogosultsághoz)
     * @param  integer $post_id A poszt id-ja
     * @throws NotFoundHttpException ha nem található a poszt
     * @return mixed
     */
    public function actionDeleteGroupPost($id, $post_id)
    {
        $model = GroupPost::findOne($post_id);

        if (!$model) {
            throw new NotFoundHttpException('Nincs ilyen poszt!');
        } else {
            if ($model->delete()) {
                Yii::$app->session->setFlash('success', Yii::t('group', 'You successfully deleted the post'));
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('group', 'Something went wrong when you tried to delete the post'));
            }
        }

        return $this->redirect(['view', 'id' => $model->group_id]);
    }

    /**
     * Csatlakozás egy csoporthoz
     * @param  integer $id A csoport id-ja
     * @return mixed
     */
    public function actionJoin($id)
    {
        $model = $this->findModel($id);
        if ($model->addUser(Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('success', Yii::t('group', 'You successfully joined the group'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('group', 'Something went wrong when you tried to join the group'));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Kilépés egy csoportból
     * @param  integer $id A csoport id-ja
     * @return mixed
     */
    public function actionLeave($id)
    {
        $model = $this->findModel($id);
        if ($model->removeUser(Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('success', Yii::t('group', 'You successfully left the group'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('group', 'Something went wrong when you tried to leave the group'));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Csoport létrehozása, ha sikeres, a listára visz
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();
        $userModel = Yii::createObject(Yii::$app->user->identityClass);
        $userList = $userModel::find()
            ->orderBy('name ASC')
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // feltoltjuk a modelleket
            $groupCoordinatorList = [new GroupCoordinator(['group_id' => $model->id])];
            $count = count(Yii::$app->request->post('GroupCoordinator'));
            for ($i = 1; $i < $count; $i++) {
                $groupCoordinatorList[] = new GroupCoordinator(['group_id' => $model->id]);
            }

            // majd betoltjuk, validaljuk
            if (GroupCoordinator::loadMultiple($groupCoordinatorList, Yii::$app->request->post()) && GroupCoordinator::validateMultiple($groupCoordinatorList)) {
                // es mentjuk
                foreach ($groupCoordinatorList as $groupCoordinator) {
                    $groupCoordinator->link('group', $model);
                }
            }

            // feltoltjuk a modelleket
            $groupLinkList = [new GroupLink(['group_id' => $model->id])];
            $count = count(Yii::$app->request->post('GroupLink'));
            for ($i = 1; $i < $count; $i++) {
                $groupLinkList[] = new GroupLink(['group_id' => $model->id]);
            }

            // majd betoltjuk, validaljuk
            if (GroupLink::loadMultiple($groupLinkList, Yii::$app->request->post()) && GroupLink::validateMultiple($groupLinkList)) {
                // es mentjuk (ha meg van minden info.. ez azert van itt kezelve, mert ha a modelbe rakod a required-et, akkor egyaltalan nem enged menteni)
                foreach ($groupLinkList as $groupLink) {
                    if (!empty($groupLink->url) && !empty($groupLink->name)) {
                        $groupLink->link('group', $model);
                    }
                }
            }

            Yii::$app->session->setFlash('success', Yii::t('group', 'Create successful'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'userList' => $userList,
            'groupCoordinatorList' => [new GroupCoordinator],
            'groupLinkList' => [new GroupLink],
        ]);
    }

    /**
     * Módosít egy csoportot
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException ha nem található a csoport
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $userModel = Yii::createObject(Yii::$app->user->identityClass);
        $userList = $userModel::find()
            ->orderBy('name ASC')
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // torlunk minden kapcsolodo sort
            GroupCoordinator::deleteAll(['group_id' => $model->id]);
            // feltoltjuk a modelleket
            $groupCoordinatorList = [new GroupCoordinator(['group_id' => $model->id])];
            $count = count(Yii::$app->request->post('GroupCoordinator'));
            for ($i = 1; $i < $count; $i++) {
                $groupCoordinatorList[] = new GroupCoordinator(['group_id' => $model->id]);
            }

            // majd betoltjuk, validaljuk
            if (GroupCoordinator::loadMultiple($groupCoordinatorList, Yii::$app->request->post()) && GroupCoordinator::validateMultiple($groupCoordinatorList)) {
                // es mentjuk
                foreach ($groupCoordinatorList as $groupCoordinator) {
                    $groupCoordinator->link('group', $model);
                }
            }

            // torlunk minden kapcsolodo sort
            GroupLink::deleteAll(['group_id' => $model->id]);
            // feltoltjuk a modelleket
            $groupLinkList = [new GroupLink(['group_id' => $model->id])];
            $count = count(Yii::$app->request->post('GroupLink'));
            for ($i = 1; $i < $count; $i++) {
                $groupLinkList[] = new GroupLink(['group_id' => $model->id]);
            }

            // majd betoltjuk, validaljuk
            if (GroupLink::loadMultiple($groupLinkList, Yii::$app->request->post()) && GroupLink::validateMultiple($groupLinkList)) {
                // es mentjuk (ha meg van minden info.. ez azert van itt kezelve, mert ha a modelbe rakod a required-et, akkor egyaltalan nem enged menteni)
                foreach ($groupLinkList as $groupLink) {
                    if (!empty($groupLink->url) && !empty($groupLink->name)) {
                        $groupLink->link('group', $model);
                    }
                }
            }

            Yii::$app->session->setFlash('success', Yii::t('group', 'Update successful'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'userList' => $userList,
            'groupCoordinatorList' => GroupCoordinator::findAll(['group_id' => $model->id]) ?: [new GroupCoordinator()],
            'groupLinkList' => GroupLink::findAll(['group_id' => $model->id]) ?: [new GroupLink()],
        ]);
    }

    /**
     * Töröl egy csoportot
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException ha nem található a csoport
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('group', 'Delete successful'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('group', 'Delete unsuccesful'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Megkeres egy csoportot, ha nem található 404-et dob
     * @param integer $id
     * @return Group
     * @throws NotFoundHttpException ha nem található
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
