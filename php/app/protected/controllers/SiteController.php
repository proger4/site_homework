<?php

class SiteController extends CController
{
    public $layout = 'main';

    public function actionIndex()
    {
        $model = new Comment();
        $comments = Comment::model()->active()->recent()->findAll();

        $this->render('index', [
            'model' => $model,
            'comments' => $comments,
        ]);
    }

    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;

        if ($error) {
            $this->renderText(CHtml::encode($error['message']));
            return;
        }

        $this->renderText('Error');
    }
}
