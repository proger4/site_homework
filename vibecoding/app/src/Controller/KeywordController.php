<?php

declare(strict_types=1);

namespace App\Controller;

final class KeywordController extends WebController
{
    public function actionUpload(): string
    {
        return $this->page(
            'Keyword Upload',
            '<p>Upload routing is ready for the keyword importer. The pipeline can attach file parsing here.</p>'
        );
    }

    public function actionAdmin(): string
    {
        $rows = $this->runtime()->keywordRows();

        if ($rows === []) {
            return $this->page('Keyword Admin', '<p>No imported keywords yet.</p>');
        }

        $body = '<table><tr><th>Source</th><th>Keyword</th><th>Language</th><th>Status</th><th>Target URL</th></tr>';

        foreach ($rows as $row) {
            $body .= '<tr>';
            $body .= '<td>' . self::e((string) ($row['source'] ?? '')) . '</td>';
            $body .= '<td>' . self::e((string) ($row['keyword_text'] ?? '')) . '</td>';
            $body .= '<td>' . self::e((string) ($row['language'] ?? '')) . '</td>';
            $body .= '<td>' . self::e((string) ($row['status'] ?? '')) . '</td>';
            $body .= '<td>' . self::e((string) ($row['target_url'] ?? '')) . '</td>';
            $body .= '</tr>';
        }

        $body .= '</table>';

        return $this->page('Keyword Admin', $body);
    }

    public function actionPreview(): string
    {
        $groups = $this->runtime()->previewGroups();

        if ($groups === []) {
            return $this->page('Keyword Preview', '<p>No active keyword groups yet.</p>');
        }

        $body = '<table><tr><th>Language</th><th>Target URL</th><th>Keywords</th></tr>';

        foreach ($groups as $group) {
            $body .= '<tr>';
            $body .= '<td>' . self::e($group['language']) . '</td>';
            $body .= '<td>' . self::e($group['target_url']) . '</td>';
            $body .= '<td>' . self::e(implode(', ', $group['keywords'])) . '</td>';
            $body .= '</tr>';
        }

        $body .= '</table>';

        return $this->page('Keyword Preview', $body);
    }

    public function actionAiPreview(): string
    {
        $forceTemplate = \Yii::$app->request->get('mode') === 'template';
        $copies = $this->runtime()->aiCopies($forceTemplate);
        $status = $this->runtime()->status($forceTemplate);
        $body = '<p>Mode: <strong>' . self::e((string) $status['ai_mode']) . '</strong></p>';

        if ($copies === []) {
            return $this->page('AI Preview', $body . '<p>No keyword groups available for ad copy.</p>');
        }

        $body .= '<table><tr>';
        $body .= '<th>Generator</th><th>Language</th><th>Keyword</th><th>Headline 1</th><th>Description 1</th>';
        $body .= '</tr>';

        foreach ($copies as $copy) {
            $body .= '<tr>';
            $body .= '<td>' . self::e($copy['generator']) . '</td>';
            $body .= '<td>' . self::e($copy['language']) . '</td>';
            $body .= '<td>' . self::e($copy['keyword']) . '</td>';
            $body .= '<td>' . self::e($copy['headline_1']) . '</td>';
            $body .= '<td>' . self::e($copy['description_1']) . '</td>';
            $body .= '</tr>';
        }

        $body .= '</table>';

        return $this->page('AI Preview', $body);
    }

    public function actionExport(): string
    {
        $report = $this->runtime()->exportGoogleAdsCsv();
        $body = '<p>Export written: <code>' . self::e($report->path()) . '</code></p>';
        $body .= '<p>Rows exported: ' . $report->rowCount() . '</p>';

        if ($report->hasErrors()) {
            $body .= '<ul>';
            foreach ($report->errors() as $error) {
                $body .= '<li>' . self::e($error) . '</li>';
            }
            $body .= '</ul>';
        } else {
            $body .= '<p>Export looks valid.</p>';
        }

        return $this->page('Google Ads Export', $body);
    }
}
