<?php

declare(strict_types=1);

namespace App\Command;

use Yii;
use yii\console\Controller;

final class UserController extends Controller
{
    public function actionEnsure(): int
    {
        $user = Yii::$app->adminUsers->ensureDefaultUser();
        $this->stdout('Admin user ensured: ' . $user->username() . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }
}
