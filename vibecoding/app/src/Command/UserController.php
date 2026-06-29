<?php

declare(strict_types=1);

namespace App\Command;

use App\User\AdminUserRepository;
use Yii;
use yii\console\Controller;

final class UserController extends Controller
{
    public function actionEnsure(): int
    {
        $adminUsers = Yii::$app->get('adminUsers');

        if (!$adminUsers instanceof AdminUserRepository) {
            $this->stderr('Admin user repository is not configured.' . PHP_EOL);

            return self::EXIT_CODE_ERROR;
        }

        $user = $adminUsers->ensureDefaultUser();
        $this->stdout('Admin user ensured: ' . $user->username() . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }
}
