<?php

declare(strict_types=1);

use App\Asset\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title ?: 'Vibecoding Keywords') ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<header class="topbar">
    <div class="topbar__inner">
        <?= Html::a('Vibecoding Keywords', ['/'], ['class' => 'brand']) ?>
        <nav class="nav">
            <?= Html::a('Upload', ['/upload']) ?>
            <?= Html::a('Admin', ['/admin/keywords']) ?>
            <?= Html::a('Preview', ['/preview']) ?>
            <?= Html::a('AI Preview', ['/ai-preview']) ?>
            <?= Html::a('Export CSV', ['/export']) ?>
            <?php if (Yii::$app->user->isGuest): ?>
                <?= Html::a('Login', ['/login']) ?>
            <?php else: ?>
                <?= Html::a('Logout', ['/logout']) ?>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="page">
    <?= $content ?>
</main>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
