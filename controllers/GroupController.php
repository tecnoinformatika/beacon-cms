<?php
namespace app\controllers;

use app\behaviors\AliasBehavior;
use app\commands\RbacController;
use dezmont765\yii2bundle\components\Alert;
use app\filters\AuthKeyFilter;
use app\filters\FilterJson;
use app\filters\GroupLayout;
use app\filters\GroupManageLayout;
use app\models\Beacons;
use app\models\BeaconsSearch;
use app\models\ClientUsers;
use app\models\Groups;
use app\models\GroupSearch;
use app\models\Users;
use Yii;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * GroupController implements the CRUD actions for Groups model.
 * @mixin AuthKeyFilter
 */
class GroupController extends MainController
{
    public $defaultAction = 'list';
    public $layout = 'main';


    public function behaviors() {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [RbacController::admin],
                    ],
                ],
            ],
            'layout' => [
                'class' => GroupLayout::className(),
                'only' => ['list', 'create']
            ],
            'manage-layout' => [
                'class' => GroupManageLayout::className(),
                'except' => ['list', 'create']
            ],
            'json-filter' => [
                'class' => FilterJson::className(),
                'only' => ['beacons-list']
            ],

        ];
        return $behaviors;
    }


    /**
     * Lists all Groups models.
     * @return mixed
     */
    public function actionList() {
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('group-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Groups model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('group-view', [
            'model' => $this->findModel(Groups::className(), $id)
        ]);
    }


    public function actionMassDelete() {
        if(isset($_POST['keys'])) {
            foreach($_POST['keys'] as $key) {
                $model = $this->findModel(Groups::className(), $key);
                if($model) {
                    if($model->delete()) {
                        Alert::addSuccess("Items has been successfully deleted");
                    }
                }
            }
        }
    }


    public function actionAsAjax($id) {
        $model = $this->findModel(Groups::className(), $id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $model->toArray();
    }


    /**
     * Creates a new Groups model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Groups();
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else {
            return $this->render('group-form', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Updates an existing Groups model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel(Groups::className(), $id);
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else {
            return $this->render('group-form', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing Groups model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel(Groups::className(), $id)->delete();
        return $this->redirect(['list']);
    }


    public function actionGetSelectionList() {
        /** @var Groups $model_class */
        $value = Yii::$app->request->getQueryParam('value');
        $query = Groups::find();
        $query->filterWhere(['like', 'name', $value]);
        $user = Users::getLogged(true);
        if($user->role == RbacController::user) {
            $query->joinWith(['users' => function (ActiveQuery $query) use ($user) {
                $query->andFilterWhere([Users::tableName() . '.id' => $user->id]);
            }]);
        }
        $models = $query->all();
        $model_array = [];
        foreach($models as $model) {
            $model_array[] = ['id' => $model->id, 'text' => $model->name];
        }
        echo json_encode(['more' => false, 'results' => $model_array]);
    }


    public function actionGetSelectionById() {
        self::selectionById(Groups::className(), 'name');
    }


    public function actionBeacons($id) {
        $searchModel = new BeaconsSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search(null, $id);
        return $this->render('/beacon/beacon-list', [
            'searchModel' => new BeaconsSearch(),
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionGetAlias() {
        /**@var $model Groups |AliasBehavior */
        $value = Yii::$app->request->getQueryParam('value');
        $model = new Groups();
        $model->name = $value;
        $model->getAlias();
        return json_encode(['success' => true, 'alias' => $model->alias]);
    }


    public function actionCreateBeacon($id) {
        $group = $this->findModel(Groups::className(), $id);
        $model = new Beacons();
        $model->groupToBind = $group->id;
        if($model->load(Yii::$app->request->post())) {
            if($model->save()) {
                return $this->redirect(['beacons', 'id' => $group->id]);
            }
        }
        return $this->render('/beacon/beacon-form', [
            'model' => $model,
            'group' => $group
        ]);
    }



}
