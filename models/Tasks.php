<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name
 * @property string\null $description
 * @property int $status
 * @property int|null $parent_id
 * @property datetime $date_add
 * @property datetime $date_modify
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'parent_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'status' => 'Status',
            'parent_id' => 'Parent ID',
            'date_add' => 'Date add',
            'date_modify' => 'Date modify',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'description',
            'status',
            'parent_id',
            'date_add',
            'date_modify',
            'subtasks' => function (){
                return $this->getChildren($this->id);
            }
        ];
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return Tasks subtasks for main task
     */
    private function getChildren($id){
        return Tasks::find()->where(['parent_id' => $id])->all();
    }
}
