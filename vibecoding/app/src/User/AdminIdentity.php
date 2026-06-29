<?php

declare(strict_types=1);

namespace App\User;

use yii\web\IdentityInterface;

final class AdminIdentity implements IdentityInterface
{
    private int $id;
    private string $username;
    private string $passwordHash;

    public function __construct(int $id, string $username, string $passwordHash)
    {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
    }

    public static function findIdentity($id): ?self
    {
        return \Yii::$app->adminUsers->findIdentityById((int) $id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        return null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return hash('sha256', $this->id . ':' . $this->passwordHash);
    }

    public function validateAuthKey($authKey): bool
    {
        return hash_equals($this->getAuthKey(), (string) $authKey);
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }
}
