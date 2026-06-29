<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'AI Preview';
?>
<section class="page-head">
    <div>
        <h1>AI Preview</h1>
        <p class="muted">Generate ad copy with OpenRouter when a key is provided, otherwise use deterministic template fallback.</p>
    </div>
    <div class="actions">
        <?= Html::a('Template mode', ['/ai-preview', 'mode' => 'template'], ['class' => 'button button--secondary']) ?>
        <?= Html::a('Export CSV', ['/export'], ['class' => 'button']) ?>
    </div>
</section>

<section class="panel panel--narrow">
    <h2>OpenRouter token for this preview</h2>
    <p class="muted">The token is used only for this request. It is not stored, logged by the app, rendered back, or exported.</p>
    <form method="post">
        <label for="openrouter-model">Model</label>
        <input id="openrouter-model" name="openrouter_model" type="text" value="<?= Html::encode($model) ?>">

        <label for="openrouter-api-key">OpenRouter API key</label>
        <input id="openrouter-api-key" name="openrouter_api_key" type="password" autocomplete="off" placeholder="<?= $hasConfiguredKey ? 'Configured in .env' : 'Optional' ?>">

        <button class="button" type="submit">Generate AI preview</button>
    </form>
</section>

<section class="notice">
    Mode: <strong><?= Html::encode($mode) ?></strong>. If OpenRouter fails, the PHP generator returns template fallback rows.
</section>

<section class="table-wrap">
    <table class="data-table">
        <thead>
        <tr>
            <?php foreach (['Keyword', 'Language', 'Headline 1', 'Headline 2', 'Headline 3', 'Description 1', 'Description 2', 'Final URL'] as $column): ?>
                <th><?= Html::encode($column) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($exportRows as $row): ?>
            <?php $data = $row->toArray(); ?>
            <tr>
                <td><?= Html::encode($data['Keyword']) ?></td>
                <td><?= Html::encode($data['Language']) ?></td>
                <td><?= Html::encode($data['Headline 1']) ?></td>
                <td><?= Html::encode($data['Headline 2']) ?></td>
                <td><?= Html::encode($data['Headline 3']) ?></td>
                <td><?= Html::encode($data['Description 1']) ?></td>
                <td><?= Html::encode($data['Description 2']) ?></td>
                <td class="url-cell"><?= Html::encode($data['Final URL']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
