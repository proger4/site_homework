<?php

class UserRepository extends CApplicationComponent
{
    public function findByUsername($username)
    {
        return User::model()->findByAttributes(['username' => $username]);
    }

    public function ensureUser($username, $password)
    {
        $user = $this->findByUsername($username);

        if ($user === null) {
            $user = new User();
            $user->username = $username;
            $user->password_hash = $this->hashPassword($password);

            return $this->saveUser($user);
        }

        // Local startup keeps the demo admin in sync when ADMIN_PASSWORD changes.
        if (!$this->verifyPassword($user, $password)) {
            $user->password_hash = $this->hashPassword($password);

            return $this->saveUser($user);
        }

        return $user;
    }

    public function verifyPassword(User $user, $password)
    {
        if ($user->password_hash === null || $user->password_hash === '') {
            return false;
        }

        if (class_exists('CPasswordHelper')) {
            return CPasswordHelper::verifyPassword($password, $user->password_hash);
        }

        return hash_equals($user->password_hash, crypt($password, $user->password_hash));
    }

    public function hashPassword($password)
    {
        if (class_exists('CPasswordHelper')) {
            return CPasswordHelper::hashPassword($password);
        }

        $salt = substr(strtr(base64_encode($this->randomBytes(16)), '+', '.'), 0, 22);

        return crypt($password, '$2y$13$' . $salt . '$');
    }

    private function saveUser(User $user)
    {
        if (!$user->save()) {
            throw new CException('Unable to save user: ' . var_export($user->getErrors(), true));
        }

        return $user;
    }

    private function randomBytes($length)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }

        return openssl_random_pseudo_bytes($length);
    }
}
