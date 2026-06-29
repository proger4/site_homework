<?php

declare(strict_types=1);

namespace App\Console;

use yii\console\Controller;

final class KeywordController extends Controller
{
    /** @var string|null */
    public $apiKey;

    /** @var string|null */
    public $model;

    /**
     * @param string $actionID
     * @return array<int, string>
     */
    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['apiKey', 'model']);
    }

    public function actionDbInit(): int
    {
        return $this->command()->initDatabase();
    }

    public function actionImportSamples(): int
    {
        return $this->command()->importSamples();
    }

    public function actionExportSamples(?string $targetPath = null): int
    {
        return $this->command()->exportSamples($targetPath);
    }

    public function actionAiPreview(): int
    {
        $args = [];

        if (is_string($this->apiKey) && $this->apiKey !== '') {
            $args[] = '--apiKey=' . $this->apiKey;
        }

        if (is_string($this->model) && $this->model !== '') {
            $args[] = '--model=' . $this->model;
        }

        return $this->command()->aiPreview($args);
    }

    public function actionValidateExport(?string $targetPath = null): int
    {
        return $this->command()->validateExport($targetPath);
    }

    public function actionSmoke(): int
    {
        return $this->command()->smoke();
    }

    private function command(): KeywordCommand
    {
        return (new KeywordCommandFactory(dirname(__DIR__, 2)))->create();
    }
}
