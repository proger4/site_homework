<?php

class WebSocketNotifier extends CApplicationComponent
{
    public $host = '127.0.0.1';
    public $port = '3001';

    public function notifyCommentCreated(Comment $comment)
    {
        $payload = [
            'type' => 'comment.created',
            'comment' => $comment->toRealtimePayload(),
        ];

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => CJSON::encode($payload),
                'ignore_errors' => true,
                'timeout' => 1,
            ],
        ]);

        $result = @file_get_contents($this->getBroadcastUrl(), false, $context);

        if ($result === false) {
            Yii::log('Unable to notify websocket server.', CLogger::LEVEL_WARNING, __METHOD__);
        }
    }

    private function getBroadcastUrl()
    {
        return 'http://' . $this->host . ':' . $this->port . '/broadcast';
    }
}
