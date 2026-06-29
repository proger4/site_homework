<?php

class AdminController extends CController
{
    public $layout = 'main';

    public function filters()
    {
        return ['accessControl'];
    }

    public function accessRules()
    {
        return [
            ['allow', 'actions' => ['login'], 'users' => ['*']],
            ['allow', 'actions' => ['index', 'comments', 'update', 'delete', 'logout'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect(['admin/comments']);
        }

        $model = new LoginForm();

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            if ($model->validate() && $model->login()) {
                $this->redirect(['admin/comments']);
            }
        }

        $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(['site/index']);
    }

    public function actionIndex()
    {
        $this->redirect(['admin/comments']);
    }

    public function actionComments()
    {
        $model = new Comment('search');
        $model->unsetAttributes();

        if (isset($_GET['Comment'])) {
            $model->attributes = $_GET['Comment'];
        }

        $this->render('comments', [
            'model' => $model,
            'wsUrl' => $this->buildWebSocketUrl(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->loadComment($id);

        if (isset($_POST['Comment'])) {
            $model->attributes = $_POST['Comment'];

            if (!$model->saveWithTransaction()->hasErrors()) {
                $this->redirect(['admin/comments']);
            }
        }

        $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest) {
            $this->loadComment($id)->markDeleted();
            $this->redirect(['admin/comments']);
        }

        throw new CHttpException(400, 'Delete requires POST.');
    }

    private function loadComment($id)
    {
        $model = Comment::model()->findByPk((int)$id);

        if ($model === null) {
            throw new CHttpException(404, 'Comment not found.');
        }

        return $model;
    }

    private function buildWebSocketUrl()
    {
        $host = getenv('WS_PUBLIC_HOST') ?: 'localhost';
        $port = getenv('WS_PORT') ?: '3001';

        return 'ws://' . $host . ':' . $port;
    }
}
