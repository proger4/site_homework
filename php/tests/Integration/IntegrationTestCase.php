<?php

use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Yii::app()->db->createCommand()->delete('comments');
        Yii::app()->db->createCommand()->delete('users');
    }

    protected function ensureUser($username = 'admin', $password = 'admin123')
    {
        return Yii::app()->userRepository->ensureUser($username, $password);
    }
}
