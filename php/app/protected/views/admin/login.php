<?php
/**
 * @var AdminController $this
 * @var LoginForm $model
 */

$this->pageTitle = 'Admin Login';
?>

<h1>Admin Login</h1>

<div class="panel">
    <?php $form = $this->beginWidget('CActiveForm', [
        'action' => $this->createUrl('admin/login'),
        'method' => 'post',
    ]); ?>
        <?= $form->errorSummary($model); ?>

        <p>
            <?= $form->label($model, 'username'); ?>
            <?= $form->textField($model, 'username', ['autocomplete' => 'username']); ?>
        </p>

        <p>
            <?= $form->label($model, 'password'); ?>
            <?= $form->passwordField($model, 'password', ['autocomplete' => 'current-password']); ?>
        </p>

        <p>
            <button type="submit">Login</button>
        </p>
    <?php $this->endWidget(); ?>
</div>
