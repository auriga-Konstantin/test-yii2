<?php

namespace app\components\helpers;

use app\models\Tasks;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use Yii;

class TasksHelper
{
    public $modelClass = 'app\models\Tasks';
    public $viewAction = 'view';

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    public function index(){
        return Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => Tasks::find()->where(['parent_id' => null])
        ]);
    }

    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function create(){
        $request = Yii::$app->getRequest()->getBodyParams();
        $model = new $this->modelClass();
        $model->name = $request['name'];
        if(isset($request['parent_id'])){
            $model->parent_id = $request['parent_id'];
        }
        if(isset($request['description'])){
            $model->description = $request['description'];
        }
        $model->date_add = date('Y-m-d H:i:s');
        $model->date_modify = date('Y-m-d H:i:s');
        $model->save();
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function update($id){
        $model = Tasks::findOne($id);
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->date_modify = date('Y-m-d H:i:s');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }
        return $model;
    }

    /**
     * Deletes a model.
     * @param mixed $id id of the model to be deleted.
     * @return Response|json the model id deleted
     * @throws ServerErrorHttpException on failure.
     */
    public function delete($id){
        $model = Tasks::findOne($id);
        if(empty($model)){
            throw new ServerErrorHttpException('A task with this ID-' . $id . ' does not exist.');
        }
        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }
        if(Tasks::deleteAll(['parent_id' => $model->id])){
            throw new ServerErrorHttpException('Failed to delete the children object for unknown reason.');
        }
        return ['status' => true, 'deleted_item_id' => $id];
    }

}