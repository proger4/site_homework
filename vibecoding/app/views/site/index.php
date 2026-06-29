<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Vibecoding Keyword Runtime';
?>
<section class="page-head">
    <div>
        <h1>Vibecoding Keyword Runtime</h1>
        <p class="muted">Import keyword files, review cleanup decisions, preview ads, and download Google Ads CSV.</p>
    </div>
    <div class="actions">
        <?= Html::a('Start upload', ['/upload'], ['class' => 'button']) ?>
        <?= Html::a('Open admin', ['/admin/keywords'], ['class' => 'button button--secondary']) ?>
    </div>
</section>

<section class="stats">
    <div class="stat">
        <span>Imported rows</span>
        <strong><?= (int) $rowCount ?></strong>
    </div>
    <div class="stat">
        <span>Active groups</span>
        <strong><?= (int) $groupCount ?></strong>
    </div>
    <div class="stat">
        <span>Google Ads export</span>
        <strong><?= $exportExists ? 'ready' : 'not yet' ?></strong>
    </div>
    <div class="stat">
        <span>Admin login</span>
        <strong><?= Html::encode($adminLogin) ?></strong>
    </div>
</section>

<section class="workflow">
    <article>
        <span class="step">1</span>
        <h2>Upload or import samples</h2>
        <p>Load the bundled Google Ads, Search Console, and Ahrefs files, or upload one CSV/JSON file with a selected source.</p>
        <?= Html::a('Go to upload', ['/upload'], ['class' => 'link-button']) ?>
    </article>
    <article>
        <span class="step">2</span>
        <h2>Review admin data</h2>
        <p>See source, keyword, normalized keyword, language, volume, CPC, target URL, status, reasons, and suggestions.</p>
        <?= Html::a('Open admin table', ['/admin/keywords'], ['class' => 'link-button']) ?>
    </article>
    <article>
        <span class="step">3</span>
        <h2>Preview ads</h2>
        <p>Check language and target URL groups, template ad copy, review suggestions, and future export rows before download.</p>
        <?= Html::a('Open preview', ['/preview'], ['class' => 'link-button']) ?>
    </article>
    <article>
        <span class="step">4</span>
        <h2>Export Google Ads CSV</h2>
        <p>Download a validated CSV with Campaign, Ad Group, Keyword, Final URL, Headline 1-3, Description 1-2, and Language.</p>
        <?= Html::a('Download CSV', ['/export'], ['class' => 'link-button']) ?>
    </article>
</section>

<?php if ($isGuest): ?>
    <section class="notice">
        <strong>Admin area is protected.</strong>
        <?= Html::a('Login first', ['/login']) ?> to use upload, admin, preview, AI preview, and export.
    </section>
<?php endif; ?>
