<?php

class UserIdentity extends CUserIdentity
{
    private $_id;

    public function authenticate()
    {
        $user = Yii::app()->userRepository->findByUsername($this->username);

        if ($user === null || !Yii::app()->userRepository->verifyPassword($user, $this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;

            return false;
        }

        $this->_id = (int)$user->id;
        $this->errorCode = self::ERROR_NONE;

        return true;
    }

    public function getId()
    {
        return $this->_id;
    }
}
