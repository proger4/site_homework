<?php

declare(strict_types=1);

namespace App\Command;

use App\KeywordExport\GoogleAdsExportValidator;
use App\KeywordRuntime\KeywordRuntime;
use Yii;
use yii\console\Controller;

final class KeywordController extends Controller
{
    public function actionDbInit(): int
    {
        $runtime = $this->runtime();
        $runtime->storage()->initialize();
        Yii::$app->adminUsers->ensureDefaultUser();

        $this->stdout('SQLite database ready: ' . $runtime->storage()::defaultDsn() . PHP_EOL);
        $this->stdout('Rows stored: ' . $runtime->storage()->rowCount() . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }

    public function actionImportSamples(): int
    {
        $result = $this->runtime()->importSamples();

        $this->stdout('Imported rows: ' . $result->rowCount() . PHP_EOL);
        foreach ($result->countsBySource() as $source => $count) {
            $this->stdout('- ' . $source . ': ' . $count . PHP_EOL);
        }
        $this->stdout('Rows stored in SQLite: ' . $this->runtime()->storage()->rowCount() . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }

    public function actionExportSamples(?string $targetPath = null): int
    {
        $report = $this->runtime()->exportCsv($targetPath);

        $this->stdout('Export written: ' . $report->path() . PHP_EOL);
        $this->stdout('Rows exported: ' . $report->rowCount() . PHP_EOL);

        if ($report->hasErrors()) {
            foreach ($report->errors() as $error) {
                $this->stderr($error . PHP_EOL);
            }

            return self::EXIT_CODE_ERROR;
        }

        $this->stdout('Export looks valid.' . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }

    public function actionAiPreview(?string $apiKey = null, ?string $model = null): int
    {
        $rows = $this->runtime()->previewRows(
            $apiKey !== null || $this->runtime()->hasOpenRouterKey(),
            $apiKey ?: (getenv('OPENROUTER_API_KEY') ?: null),
            $model ?: (getenv('OPENROUTER_MODEL') ?: null)
        );

        $this->stdout('language | keyword | headline_1 | description_1' . PHP_EOL);
        $this->stdout('---------|---------|------------|--------------' . PHP_EOL);

        foreach ($rows as $row) {
            $data = $row->toArray();
            $this->stdout(implode(' | ', [
                $data['Language'],
                $data['Keyword'],
                $data['Headline 1'],
                $data['Description 1'],
            ]) . PHP_EOL);
        }

        return self::EXIT_CODE_NORMAL;
    }

    public function actionValidateExport(?string $targetPath = null): int
    {
        $path = $targetPath ?? $this->runtime()->exportPath();

        if (!is_file($path)) {
            $this->stderr('Export file not found: ' . $path . PHP_EOL);

            return self::EXIT_CODE_ERROR;
        }

        $rows = $this->readExportRows($path);
        $errors = (new GoogleAdsExportValidator())->validate($rows);

        if ($errors !== []) {
            foreach ($errors as $error) {
                $this->stderr($error . PHP_EOL);
            }

            return self::EXIT_CODE_ERROR;
        }

        $this->stdout('Export looks valid.' . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }

    public function actionSmoke(): int
    {
        $this->actionDbInit();
        $result = $this->runtime()->importSamples();

        if ($result->rowCount() !== 8 || $this->runtime()->storage()->rowCount() !== 8) {
            $this->stderr('Smoke failed: expected 8 imported rows.' . PHP_EOL);

            return self::EXIT_CODE_ERROR;
        }

        if (count($this->runtime()->groups()) < 1) {
            $this->stderr('Smoke failed: expected active keyword groups.' . PHP_EOL);

            return self::EXIT_CODE_ERROR;
        }

        if ($this->actionExportSamples() !== self::EXIT_CODE_NORMAL) {
            return self::EXIT_CODE_ERROR;
        }

        $this->stdout('Smoke passed.' . PHP_EOL);

        return self::EXIT_CODE_NORMAL;
    }

    private function runtime(): KeywordRuntime
    {
        return KeywordRuntime::createDefault((string) Yii::getAlias('@app'));
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function readExportRows(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new \RuntimeException('Unable to open export: ' . $path);
        }

        $header = fgetcsv($handle, 0, ',', '"', '\\');
        $rows = [];

        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (!is_array($header)) {
                break;
            }

            $rows[] = array_combine($header, array_pad($data, count($header), ''));
        }

        fclose($handle);

        return array_filter($rows, 'is_array');
    }
}
