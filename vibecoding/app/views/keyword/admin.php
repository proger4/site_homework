<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Admin Keywords';
$statusOptions = [
    '' => 'All statuses',
    'active' => 'Active',
    'review_suggestion' => 'Review suggestions',
    'excluded' => 'Excluded',
    'merged_duplicate' => 'Merged duplicates',
];
?>
<section class="page-head">
    <div>
        <h1>Admin Keywords</h1>
        <p class="muted">All imported rows with deterministic cleanup status, reasons, and review suggestions.</p>
    </div>
    <div class="actions">
        <?= Html::a('Upload', ['/upload'], ['class' => 'button button--secondary']) ?>
        <?= Html::a('Preview', ['/preview'], ['class' => 'button']) ?>
    </div>
</section>

<section class="stats stats--compact">
    <?php foreach ($summary as $name => $count): ?>
        <div class="stat">
            <span><?= Html::encode($name) ?></span>
            <strong><?= (int) $count ?></strong>
        </div>
    <?php endforeach; ?>
</section>

<section class="filters">
    <form method="get">
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach ($statusOptions as $value => $label): ?>
                <option value="<?= Html::encode($value) ?>" <?= $status === $value ? 'selected' : '' ?>>
                    <?= Html::encode($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="source">Source</label>
        <select id="source" name="source">
            <option value="">All sources</option>
            <?php foreach ($sources as $value => $label): ?>
                <option value="<?= Html::encode($value) ?>" <?= $source === $value ? 'selected' : '' ?>>
                    <?= Html::encode($label) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="button button--secondary" type="submit">Apply filters</button>
    </form>
</section>

<section class="table-wrap">
    <table class="data-table">
        <thead>
        <tr>
            <th>source</th>
            <th>keyword/query</th>
            <th>normalized_keyword</th>
            <th>language</th>
            <th>volume</th>
            <th>cpc</th>
            <th>target_url</th>
            <th>status</th>
            <th>removal_reason</th>
            <th>landing_page_suggestion</th>
            <th>ad_group_suggestion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr class="row-<?= Html::encode((string) $row['status']) ?>">
                <td><?= Html::encode((string) $row['source']) ?></td>
                <td><?= Html::encode((string) $row['keyword_text']) ?></td>
                <td><?= Html::encode((string) $row['normalized_keyword']) ?></td>
                <td><?= Html::encode((string) $row['language']) ?></td>
                <td><?= (int) $row['volume'] ?></td>
                <td><?= Html::encode((string) $row['cpc']) ?></td>
                <td class="url-cell"><?= Html::encode((string) $row['target_url']) ?></td>
                <td><span class="badge badge--<?= Html::encode((string) $row['status']) ?>"><?= Html::encode((string) $row['status']) ?></span></td>
                <td><?= Html::encode((string) ($row['removal_reason'] ?? '')) ?></td>
                <td class="url-cell"><?= Html::encode((string) ($row['landing_page_suggestion'] ?? '')) ?></td>
                <td><?= Html::encode((string) ($row['ad_group_suggestion'] ?? '')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
