<?php

require_once __DIR__ . '/IntegrationTestCase.php';

class CommentIntegrationTest extends IntegrationTestCase
{
    /**
     * @dataProvider validCommentProvider
     */
    public function testCreateFromInputPersistsValidComment(array $attributes, $expectedName)
    {
        $comment = Comment::createFromInput($attributes);

        $this->assertFalse($comment->hasErrors());
        $this->assertFalse($comment->isNewRecord);
        $this->assertSame($expectedName, $comment->name);
        $this->assertSame(Comment::STATUS_ACTIVE, $comment->status);
        $this->assertNotEmpty($comment->created_at);
        $this->assertSame(1, (int)Comment::model()->active()->count());
    }

    public function validCommentProvider()
    {
        return [
            'name and message' => [
                ['name' => 'Guest', 'message' => 'Hello from tests'],
                'Guest',
            ],
            'message only' => [
                ['message' => 'Anonymous comment'],
                null,
            ],
        ];
    }

    /**
     * @dataProvider invalidCommentProvider
     */
    public function testCreateFromInputRejectsInvalidComment(array $attributes, $expectedErrorAttribute)
    {
        $comment = Comment::createFromInput($attributes);

        $this->assertTrue($comment->hasErrors($expectedErrorAttribute));
        $this->assertTrue($comment->isNewRecord);
        $this->assertSame(0, (int)Comment::model()->count());
    }

    public function invalidCommentProvider()
    {
        return [
            'missing message' => [
                ['name' => 'Guest'],
                'message',
            ],
            'empty message' => [
                ['name' => 'Guest', 'message' => ''],
                'message',
            ],
            'invalid status' => [
                ['name' => 'Guest', 'message' => 'Hello', 'status' => 'spam'],
                'status',
            ],
        ];
    }

    /**
     * @dataProvider softDeleteProvider
     */
    public function testMarkDeletedKeepsRowAndHidesItFromActiveFeed(array $attributes)
    {
        $comment = Comment::createFromInput($attributes);

        $this->assertTrue($comment->markDeleted());
        $this->assertSame(Comment::STATUS_DELETED, $comment->status);
        $this->assertSame(1, (int)Comment::model()->count());
        $this->assertSame(0, (int)Comment::model()->active()->count());
        $this->assertNotEmpty($comment->updated_at);
    }

    public function softDeleteProvider()
    {
        return [
            'regular comment' => [
                ['name' => 'Guest', 'message' => 'Delete me'],
            ],
        ];
    }
}
