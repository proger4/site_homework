<?php

require_once __DIR__ . '/IntegrationTestCase.php';

class UserAuthenticationIntegrationTest extends IntegrationTestCase
{
    /**
     * @dataProvider validCredentialProvider
     */
    public function testIdentityAuthenticatesSeededUser($username, $password)
    {
        $user = $this->ensureUser($username, $password);
        $identity = new UserIdentity($username, $password);

        $this->assertTrue($identity->authenticate());
        $this->assertSame(CUserIdentity::ERROR_NONE, $identity->errorCode);
        $this->assertSame((string)$user->id, (string)$identity->getId());
    }

    public function validCredentialProvider()
    {
        return [
            'default admin' => ['admin', 'admin123'],
            'custom admin' => ['owner', 'owner-secret'],
        ];
    }

    /**
     * @dataProvider invalidCredentialProvider
     */
    public function testIdentityRejectsInvalidCredentials($seedUsername, $seedPassword, $loginUsername, $loginPassword)
    {
        $this->ensureUser($seedUsername, $seedPassword);
        $identity = new UserIdentity($loginUsername, $loginPassword);

        $this->assertFalse($identity->authenticate());
        $this->assertSame(CUserIdentity::ERROR_PASSWORD_INVALID, $identity->errorCode);
        $this->assertNull($identity->getId());
    }

    public function invalidCredentialProvider()
    {
        return [
            'wrong password' => ['admin', 'admin123', 'admin', 'wrong-password'],
            'missing user' => ['admin', 'admin123', 'missing', 'admin123'],
            'empty password' => ['admin', 'admin123', 'admin', ''],
        ];
    }

    /**
     * @dataProvider passwordRotationProvider
     */
    public function testEnsureUserRotatesPasswordWhenEnvDefaultChanges($username, $oldPassword, $newPassword)
    {
        $this->ensureUser($username, $oldPassword);
        $updatedUser = $this->ensureUser($username, $newPassword);

        $this->assertTrue(Yii::app()->userRepository->verifyPassword($updatedUser, $newPassword));
        $this->assertFalse(Yii::app()->userRepository->verifyPassword($updatedUser, $oldPassword));
        $this->assertSame(1, (int)User::model()->count());
    }

    public function passwordRotationProvider()
    {
        return [
            'default login password changes' => ['admin', 'admin123', 'better-secret'],
        ];
    }
}
