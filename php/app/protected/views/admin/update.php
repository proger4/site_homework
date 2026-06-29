<?php
/**
 * @var AdminController $this
 * @var Comment $model
 */

$this->pageTitle = 'Edit Comment';
?>

<h1>Edit Comment #<?= (int)$model->id; ?></h1>

<div class="panel">
    <?php $form = $this->beginWidget('CActiveForm', [
        'action' => $this->createUrl('admin/update', ['id' => $model->id]),
        'method' => 'post',
    ]); ?>
        <?= $form->errorSummary($model); ?>

        <p>
            <?= $form->label($model, 'name'); ?>
            <?= $form->textField($model, 'name', ['maxlength' => 255]); ?>
        </p>

        <p>
            <?= $form->label($model, 'message'); ?>
            <?= $form->textArea($model, 'message'); ?>
        </p>

        <p>
            <?= $form->label($model, 'status'); ?>
            <?= $form->dropDownList($model, 'status', [
                Comment::STATUS_ACTIVE => 'active',
                Comment::STATUS_DELETED => 'deleted',
            ]); ?>
        </p>

        <p>
            <button type="submit">Save</button>
            <a class="button" href="<?= CHtml::encode($this->createUrl('admin/comments')); ?>">Back</a>
        </p>
    <?php $this->endWidget(); ?>
</div>
