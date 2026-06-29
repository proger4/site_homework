<?php

/**
 * @property int $id User identifier.
 * @property string $username Login name.
 * @property string $password_hash Yii-compatible password hash.
 * @property string $created_at Creation datetime.
 * @property string|null $updated_at Last update datetime.
 */
class User extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            ['username, password_hash', 'required'],
            ['username', 'length', 'max' => 255],
            ['username', 'unique'],
            ['password_hash', 'length', 'max' => 255],
            ['id, username, password_hash, created_at, updated_at', 'safe', 'on' => 'search'],
        ];
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
}
