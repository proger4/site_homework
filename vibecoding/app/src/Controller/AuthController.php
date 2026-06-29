<?php

declare(strict_types=1);

namespace App\Controller;

use App\User\AdminUserRepository;
use Yii;
use yii\web\Response;

final class AuthController extends BaseController
{
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/admin/keywords']);
        }

        $error = null;

        if (Yii::$app->request->isPost) {
            $username = trim((string) Yii::$app->request->post('username', ''));
            $password = (string) Yii::$app->request->post('password', '');
            $adminUsers = Yii::$app->get('adminUsers');
            $identity = $adminUsers instanceof AdminUserRepository
                ? $adminUsers->validateCredentials($username, $password)
                : null;

            if ($identity !== null) {
                Yii::$app->user->login($identity);

                return $this->redirect(['/admin/keywords']);
            }

            $error = 'Incorrect login or password.';
        }

        return $this->render('login', [
            'error' => $error,
            'defaultLogin' => getenv('ADMIN_LOGIN') ?: 'admin',
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(['/login']);
    }
}
