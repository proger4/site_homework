<?php

declare(strict_types=1);

use yii\helpers\Html;

$this->title = 'Export Validation Error';
?>
<section class="page-head">
    <div>
        <h1>Export Validation Error</h1>
        <p class="muted">The CSV was generated but did not pass Google Ads format validation.</p>
    </div>
</section>

<section class="panel">
    <ul class="error-list">
        <?php foreach ($errors as $error): ?>
            <li><?= Html::encode($error) ?></li>
        <?php endforeach; ?>
    </ul>
    <?= Html::a('Back to preview', ['/preview'], ['class' => 'button button--secondary']) ?>
</section>
