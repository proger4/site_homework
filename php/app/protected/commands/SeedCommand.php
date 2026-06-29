<?php

class SeedCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $db = Yii::app()->db;
        $count = (int)$db
            ->createCommand('SELECT COUNT(*) FROM comments')
            ->queryScalar();

        if ($count > 0) {
            echo "Comments seed skipped: table is not empty.\n";
            return;
        }

        $db->createCommand()->insert('comments', [
            'name' => 'Demo User',
            'message' => 'Welcome! This comment was created by the Yii seed command.',
            'status' => Comment::STATUS_ACTIVE,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => null,
        ]);

        echo "Comments seed completed.\n";
    }
}
