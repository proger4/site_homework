<?php

class CommentController extends CController
{
    public function filters()
    {
        return ['accessControl', 'postOnly + delete'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['create'], 'users' => ['*']],
            ['allow', 'actions' => ['delete'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionCreate()
    {
        $model = new Comment();

        if (isset($_POST['Comment'])) {
            $model = Comment::createFromInput($_POST['Comment']);

            if (!$model->hasErrors()) {
                Yii::app()->webSocketNotifier->notifyCommentCreated($model);
                Yii::app()->user->setFlash('success', 'Comment added.');
                $this->redirect(['site/index']);
            }
        }

        $comments = Comment::model()->active()->recent()->findAll();

        $this->render('//site/index', [
            'model' => $model,
            'comments' => $comments,
        ]);
    }

    public function actionDelete($id)
    {
        $this->loadComment($id)->markDeleted();
        $this->redirect(['admin/comments']);
    }

    private function loadComment($id)
    {
        $model = Comment::model()->findByPk((int)$id);

        if ($model === null) {
            throw new CHttpException(404, 'Comment not found.');
        }

        return $model;
    }
}
