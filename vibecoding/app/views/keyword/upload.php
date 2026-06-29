<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Upload Keywords';
?>
<section class="page-head">
    <div>
        <h1>Upload Keywords</h1>
        <p class="muted">Import the task sample files or upload a CSV/JSON file for the current working dataset.</p>
    </div>
    <div class="actions">
        <?= Html::a('Admin table', ['/admin/keywords'], ['class' => 'button button--secondary']) ?>
        <?= Html::a('Preview ads', ['/preview'], ['class' => 'button button--secondary']) ?>
    </div>
</section>

<?php if ($message !== null): ?>
    <div class="flash"><?= Html::encode($message) ?></div>
<?php endif; ?>
<?php if ($error !== null): ?>
    <div class="flash flash--error"><?= Html::encode($error) ?></div>
<?php endif; ?>

<section class="grid-two">
    <article class="panel">
        <h2>Import bundled sample files</h2>
        <p class="muted">This loads the exact task fixtures and replaces the current keyword rows in SQLite.</p>
        <ul class="file-list">
            <?php foreach ($sampleFiles as $file): ?>
                <li><?= Html::encode($file) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="post">
            <button class="button" name="import_samples" value="1" type="submit">Import sample files</button>
        </form>
    </article>

    <article class="panel">
        <h2>Upload one CSV or JSON</h2>
        <p class="muted">Choose the source type so CSV and JSON still pass through the shared import interface.</p>
        <form method="post" enctype="multipart/form-data">
            <label for="source">Source</label>
            <select id="source" name="source">
                <?php foreach ($sources as $value => $label): ?>
                    <option value="<?= Html::encode($value) ?>"><?= Html::encode($label) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="keyword-file">Keyword file</label>
            <input id="keyword-file" name="keyword_file" type="file" accept=".csv,.json,text/csv,application/json">

            <button class="button" type="submit">Upload and import</button>
        </form>
    </article>
</section>
