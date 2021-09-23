<?php

namespace app\controllers;

use app\components\helpers\TasksHelper;
use yii\data\ActiveDataProvider;
use \yii\rest\ActiveController;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class TasksController extends ActiveController
{
    public $modelClass = 'app\models\Tasks';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    public $helper;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->helper = new TasksHelper();
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    public function prepareDataProvider(){
        return $this->helper->index();
    }

    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function actionCreate(){
        return $this->helper->create();
    }

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function actionUpdate($id){
        return $this->helper->update($id);
    }

    /**
     * Deletes a model.
     * @param mixed $id id of the model to be deleted.
     * @return Response|json the model id deleted
     * @throws ServerErrorHttpException on failure.
     */
    public function actionDelete($id){
        return $this->helper->delete($id);
    }
}
