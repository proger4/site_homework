<?php

declare(strict_types=1);

namespace App\Controller;

use App\KeywordRuntime\KeywordRuntime;
use Yii;
use yii\web\Controller;
use yii\web\Response;

abstract class BaseController extends Controller
{
    public function runtime(): KeywordRuntime
    {
        return KeywordRuntime::createDefault((string) Yii::getAlias('@app'));
    }

    public function requireAdmin(): ?Response
    {
        if (!Yii::$app->user->isGuest) {
            return null;
        }

        return $this->redirect(['/auth/login']);
    }
}
