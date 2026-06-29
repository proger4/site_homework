<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Ads Preview';

$suggestionsByGroup = [];
foreach ($adminRows as $row) {
    $key = (string) $row['language'] . '|' . (string) $row['target_url'];
    $suggestionsByGroup[$key]['landing'] = $row['landing_page_suggestion'] ?? null;
    $suggestionsByGroup[$key]['ad_group'] = $row['ad_group_suggestion'] ?? null;
}
?>
<section class="page-head">
    <div>
        <h1>Ads Preview</h1>
        <p class="muted">Active and review-suggestion keywords grouped by language and target URL.</p>
    </div>
    <div class="actions">
        <?= Html::a('AI preview', ['/ai-preview'], ['class' => 'button button--secondary']) ?>
        <?= Html::a('Download export CSV', ['/export'], ['class' => 'button']) ?>
    </div>
</section>

<section class="group-list">
    <?php foreach ($groups as $group): ?>
        <?php $key = $group->language() . '|' . $group->targetUrl(); ?>
        <article class="panel">
            <h2><?= Html::encode(strtoupper($group->language())) ?> / <?= Html::encode($group->targetUrl()) ?></h2>
            <p class="muted">
                <?= count($group->rows()) ?> keyword(s)
                <?php if (!empty($suggestionsByGroup[$key]['landing'])): ?>
                    · landing_page_suggestion: <?= Html::encode((string) $suggestionsByGroup[$key]['landing']) ?>
                <?php endif; ?>
                <?php if (!empty($suggestionsByGroup[$key]['ad_group'])): ?>
                    · ad_group_suggestion: <?= Html::encode((string) $suggestionsByGroup[$key]['ad_group']) ?>
                <?php endif; ?>
            </p>
            <div class="chips">
                <?php foreach ($group->rows() as $row): ?>
                    <span><?= Html::encode($row->keywordText()) ?></span>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<section class="section-head">
    <h2>Future Google Ads CSV rows</h2>
    <p class="muted">These are the rows that `/export` will download.</p>
</section>

<section class="table-wrap">
    <table class="data-table">
        <thead>
        <tr>
            <?php foreach (['Campaign', 'Ad Group', 'Keyword', 'Final URL', 'Headline 1', 'Headline 2', 'Headline 3', 'Description 1', 'Description 2', 'Language'] as $column): ?>
                <th><?= Html::encode($column) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($exportRows as $row): ?>
            <?php $data = $row->toArray(); ?>
            <tr>
                <?php foreach ($data as $value): ?>
                    <td class="url-cell"><?= Html::encode($value) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
