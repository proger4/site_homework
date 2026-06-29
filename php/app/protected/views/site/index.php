<?php
/**
 * @var CController $this
 * @var Comment $model
 * @var Comment[] $comments
 */

$this->pageTitle = 'Comments';
?>

<h1>Comments</h1>

<?php if (Yii::app()->user->hasFlash('success')): ?>
    <div class="flash"><?= CHtml::encode(Yii::app()->user->getFlash('success')); ?></div>
<?php endif; ?>

<div class="panel">
    <?php $form = $this->beginWidget('CActiveForm', [
        'action' => $this->createUrl('comment/create'),
        'method' => 'post',
    ]); ?>
        <?= $form->errorSummary($model); ?>

        <p>
            <?= $form->label($model, 'name'); ?>
            <?= $form->textField($model, 'name', ['maxlength' => 255, 'placeholder' => 'Guest']); ?>
        </p>

        <p>
            <?= $form->label($model, 'message'); ?>
            <?= $form->textArea($model, 'message', ['placeholder' => 'Write a comment...']); ?>
        </p>

        <p>
            <button type="submit">Post comment</button>
        </p>
    <?php $this->endWidget(); ?>
</div>

<h2>Feed</h2>

<?php foreach ($comments as $comment): ?>
    <article class="comment">
        <div class="meta">
            #<?= (int)$comment->id; ?>,
            <?= CHtml::encode($comment->name ?: 'Guest'); ?>,
            <?= CHtml::encode($comment->created_at); ?>
        </div>
        <div><?= nl2br(CHtml::encode($comment->message)); ?></div>
    </article>
<?php endforeach; ?>
