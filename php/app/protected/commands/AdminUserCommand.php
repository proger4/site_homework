<?php

class AdminUserCommand extends CConsoleCommand
{
    public function actionEnsure()
    {
        $login = getenv('ADMIN_LOGIN') ?: 'admin';
        $password = getenv('ADMIN_PASSWORD') ?: 'admin123';

        $user = Yii::app()->userRepository->ensureUser($login, $password);

        echo 'Admin user ensured: ' . $user->username . "\n";
    }
}
