<?php

/**
 * @property int $id Comment identifier.
 * @property string|null $name Guest display name.
 * @property string $message Comment text.
 * @property string $status Comment status: active or deleted.
 * @property string $created_at Creation datetime.
 * @property string|null $updated_at Last update datetime.
 * @method Comment active()
 */
class Comment extends CActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'comments';
    }

    public function rules()
    {
        return [
            ['message', 'required'],
            ['name, status', 'length', 'max' => 255],
            ['message', 'length', 'max' => 5000],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['id, name, message, status, created_at, updated_at', 'safe', 'on' => 'search'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'message' => 'Message',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function scopes()
    {
        return [
            'active' => [
                'condition' => 'status = :activeStatus',
                'params' => [':activeStatus' => self::STATUS_ACTIVE],
            ],
        ];
    }

    public function beforeValidate()
    {
        if ($this->status === null || $this->status === '') {
            $this->status = self::STATUS_ACTIVE;
        }

        return parent::beforeValidate();
    }

    public function beforeSave()
    {
        $now = date('Y-m-d H:i:s');

        if ($this->isNewRecord) {
            $this->created_at = $now;
        } else {
            $this->updated_at = $now;
        }

        return parent::beforeSave();
    }

    public function recent($limit = 50)
    {
        $this->getDbCriteria()->mergeWith([
            'order' => 'id DESC',
            'limit' => $limit,
        ]);

        return $this;
    }

    public function getIsDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }

    public static function createFromInput(array $attributes)
    {
        $model = new self();
        $model->attributes = $attributes;

        return $model->saveWithTransaction();
    }

    public function saveWithTransaction()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            if (!$this->save()) {
                $transaction->rollback();
                return $this;
            }

            $transaction->commit();
            return $this;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function markDeleted()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $this->status = self::STATUS_DELETED;

            if (!$this->save()) {
                $transaction->rollback();
                return false;
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->order = 'id DESC';

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
            'pagination' => ['pageSize' => 20],
        ]);
    }

    public static function recentActiveDataProvider($limit = 50)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'status = :status';
        $criteria->params = [':status' => self::STATUS_ACTIVE];
        $criteria->order = 'id DESC';
        $criteria->limit = $limit;

        return new CActiveDataProvider(__CLASS__, [
            'criteria' => $criteria,
            'pagination' => false,
        ]);
    }

    public function toRealtimePayload()
    {
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
