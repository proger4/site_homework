<?php

declare(strict_types=1);

namespace App\Controller;

use Yii;
use yii\web\Response;

final class SiteController extends BaseController
{
    public function actionIndex(): string
    {
        $runtime = $this->runtime();

        return $this->render('index', [
            'rowCount' => $runtime->storage()->rowCount(),
            'groupCount' => count($runtime->groups()),
            'exportExists' => is_file($runtime->exportPath()),
            'isGuest' => Yii::$app->user->isGuest,
            'adminLogin' => getenv('ADMIN_LOGIN') ?: 'admin',
        ]);
    }

    public function actionHealth(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $runtime = $this->runtime();

        return [
            'status' => 'ok',
            'database' => $runtime->storage()::defaultDsn(),
            'keyword_import_rows' => $runtime->storage()->rowCount(),
            'active_groups' => count($runtime->groups()),
            'export_exists' => is_file($runtime->exportPath()),
            'ai_mode' => $runtime->hasOpenRouterKey() ? 'openrouter' : 'template-fallback',
        ];
    }
}
