<?php

declare(strict_types=1);

namespace App\User;

use yii\base\Component;
use yii\db\Connection;

final class AdminUserRepository extends Component
{
    public ?Connection $db = null;

    public function init(): void
    {
        parent::init();
        $this->db = $this->db ?? \Yii::$app->db;
        $this->initialize();
    }

    public function ensureDefaultUser(): AdminIdentity
    {
        return $this->ensureUser(
            getenv('ADMIN_LOGIN') ?: 'admin',
            getenv('ADMIN_PASSWORD') ?: 'admin123'
        );
    }

    public function ensureUser(string $username, string $password): AdminIdentity
    {
        $user = $this->findByUsername($username);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($user === null) {
            $this->db->createCommand()->insert('users', [
                'username' => $username,
                'password_hash' => $hash,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ])->execute();

            return $this->requiredIdentityById((int) $this->db->getLastInsertID());
        }

        if (!password_verify($password, $user->passwordHash())) {
            $this->db->createCommand()->update('users', [
                'password_hash' => $hash,
                'updated_at' => date('Y-m-d H:i:s'),
            ], ['id' => $user->getId()])->execute();

            return $this->requiredIdentityById($user->getId());
        }

        return $user;
    }

    public function findIdentityById(int $id): ?AdminIdentity
    {
        $row = $this->db->createCommand('SELECT * FROM users WHERE id = :id', [':id' => $id])->queryOne();

        return is_array($row) ? $this->identityFromRow($row) : null;
    }

    public function findByUsername(string $username): ?AdminIdentity
    {
        $row = $this->db
            ->createCommand('SELECT * FROM users WHERE username = :username', [':username' => $username])
            ->queryOne();

        return is_array($row) ? $this->identityFromRow($row) : null;
    }

    public function validateCredentials(string $username, string $password): ?AdminIdentity
    {
        $user = $this->findByUsername($username);

        if ($user === null || !password_verify($password, $user->passwordHash())) {
            return null;
        }

        return $user;
    }

    private function initialize(): void
    {
        $this->db->createCommand(
            'CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at TEXT NOT NULL,
                updated_at TEXT
            )'
        )->execute();
    }

    private function requiredIdentityById(int $id): AdminIdentity
    {
        $identity = $this->findIdentityById($id);

        if ($identity === null) {
            throw new \RuntimeException('Unable to load admin user after save.');
        }

        return $identity;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function identityFromRow(array $row): AdminIdentity
    {
        return new AdminIdentity(
            (int) $row['id'],
            (string) $row['username'],
            (string) $row['password_hash']
        );
    }
}
