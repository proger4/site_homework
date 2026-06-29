<?php

declare(strict_types=1);

namespace App\Controller;

use App\KeywordImport\Domain\KeywordSource;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

final class KeywordController extends BaseController
{
    public function actionUpload()
    {
        if (($redirect = $this->requireAdmin()) !== null) {
            return $redirect;
        }

        $message = null;
        $error = null;

        if (Yii::$app->request->isPost) {
            try {
                if (Yii::$app->request->post('import_samples') !== null) {
                    $result = $this->runtime()->importSamples();
                    $message = 'Imported sample files: ' . $result->rowCount() . ' rows.';
                } else {
                    $message = $this->handleUploadedFile();
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('upload', [
            'message' => $message,
            'error' => $error,
            'sources' => $this->sourceOptions(),
            'sampleFiles' => array_map('basename', $this->runtime()->sampleFiles()),
        ]);
    }

    public function actionAdmin()
    {
        if (($redirect = $this->requireAdmin()) !== null) {
            return $redirect;
        }

        $status = trim((string) Yii::$app->request->get('status', ''));
        $source = trim((string) Yii::$app->request->get('source', ''));
        $rows = $this->filterRows($this->runtime()->adminRows(), $status, $source);

        return $this->render('admin', [
            'rows' => $rows,
            'status' => $status,
            'source' => $source,
            'sources' => $this->sourceOptions(),
            'summary' => $this->statusSummary($this->runtime()->adminRows()),
        ]);
    }

    public function actionPreview()
    {
        if (($redirect = $this->requireAdmin()) !== null) {
            return $redirect;
        }

        $runtime = $this->runtime();

        return $this->render('preview', [
            'groups' => $runtime->groups(),
            'exportRows' => $runtime->previewRows(false),
            'adminRows' => $runtime->adminRows(),
        ]);
    }

    public function actionAiPreview()
    {
        if (($redirect = $this->requireAdmin()) !== null) {
            return $redirect;
        }

        $mode = (string) Yii::$app->request->get('mode', 'auto');
        $apiKey = Yii::$app->request->isPost
            ? (string) Yii::$app->request->post('openrouter_api_key', '')
            : (getenv('OPENROUTER_API_KEY') ?: '');
        $model = Yii::$app->request->isPost
            ? (string) Yii::$app->request->post('openrouter_model', 'openai/gpt-4.1-mini')
            : (getenv('OPENROUTER_MODEL') ?: 'openai/gpt-4.1-mini');
        $useAi = $mode !== 'template' && trim($apiKey) !== '';

        return $this->render('ai-preview', [
            'mode' => $useAi ? 'openrouter-requested' : 'template-fallback',
            'model' => $model,
            'exportRows' => $this->runtime()->previewRows($useAi, $apiKey, $model),
            'hasConfiguredKey' => $this->runtime()->hasOpenRouterKey(),
        ]);
    }

    public function actionExport()
    {
        if (($redirect = $this->requireAdmin()) !== null) {
            return $redirect;
        }

        $report = $this->runtime()->exportCsv();

        if ($report->hasErrors()) {
            return $this->render('export-error', [
                'errors' => $report->errors(),
            ]);
        }

        return Yii::$app->response->sendFile(
            $report->path(),
            'google_ads_import.csv',
            ['mimeType' => 'text/csv']
        );
    }

    private function handleUploadedFile(): string
    {
        $source = KeywordSource::fromString((string) Yii::$app->request->post('source', ''));
        $file = UploadedFile::getInstanceByName('keyword_file');

        if ($file === null) {
            throw new \RuntimeException('Choose a CSV or JSON file to import.');
        }

        $dir = (string) Yii::getAlias('@runtime/uploads');
        if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $path = $dir . '/' . date('YmdHis') . '-' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->name);

        if (!$file->saveAs($path)) {
            throw new \RuntimeException('Unable to save uploaded file.');
        }

        $result = $this->runtime()->importUploadedFile($path, $source);

        return 'Imported uploaded file: ' . $result->rowCount() . ' rows.';
    }

    /**
     * @return array<string, string>
     */
    private function sourceOptions(): array
    {
        return [
            KeywordSource::GOOGLE_ADS => 'Google Ads used keywords',
            KeywordSource::SEARCH_CONSOLE => 'Search Console queries',
            KeywordSource::AHREFS_ORGANIC => 'Ahrefs organic keywords',
            KeywordSource::AHREFS_PAID => 'Ahrefs paid competitor keywords',
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    private function filterRows(array $rows, string $status, string $source): array
    {
        return array_values(array_filter($rows, static function (array $row) use ($status, $source): bool {
            if ($status !== '' && ($row['status'] ?? '') !== $status) {
                return false;
            }

            if ($source !== '' && ($row['source'] ?? '') !== $source) {
                return false;
            }

            return true;
        }));
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, int>
     */
    private function statusSummary(array $rows): array
    {
        $summary = [];

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? 'unknown');
            $summary[$status] = ($summary[$status] ?? 0) + 1;
        }

        ksort($summary);

        return $summary;
    }
}
