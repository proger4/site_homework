<?php
/**
 * @var CController $this
 * @var string $content
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/app.css'); ?>
    <title><?= CHtml::encode($this->pageTitle ?: Yii::app()->name); ?></title>
</head>
<body>
<header>
    <a href="<?= CHtml::encode($this->createUrl('site/index')); ?>">Comments</a>
    <a href="<?= CHtml::encode($this->createUrl('admin/comments')); ?>">Admin</a>
    <?php if (!Yii::app()->user->isGuest): ?>
        <a href="<?= CHtml::encode($this->createUrl('admin/logout')); ?>">Logout</a>
    <?php endif; ?>
</header>
<main>
    <?= $content; ?>
</main>
</body>
</html>
