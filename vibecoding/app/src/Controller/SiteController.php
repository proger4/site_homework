<?php

declare(strict_types=1);

namespace App\Controller;

use yii\web\Response;

final class SiteController extends WebController
{
    public function actionIndex(): string
    {
        $status = $this->runtime()->status(false);
        $exportState = $status['export_exists'] ? 'ready' : 'not generated yet';
        $aiState = $status['ai_mode'] === 'openrouter' ? 'OpenRouter key configured' : 'template fallback';
        $body = '<p>Yii2 web runtime with SQLite-backed keyword import/export integration points.</p>';
        $body .= '<table>';
        $body .= '<tr><th>SQLite DSN</th><td>' . self::e((string) $status['database']) . '</td></tr>';
        $body .= '<tr><th>Imported rows</th><td>' . (int) $status['keyword_import_rows'] . '</td></tr>';
        $body .= '<tr><th>Google Ads export</th><td>' . self::e($exportState) . '</td></tr>';
        $body .= '<tr><th>AI mode</th><td>' . self::e($aiState) . '</td></tr>';
        $body .= '</table>';

        return $this->page('Vibecoding Keyword Runtime', $body);
    }

    public function actionHealth(): Response
    {
        return $this->asJson($this->runtime()->status(false));
    }
}
