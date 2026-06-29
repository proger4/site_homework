<?php

class m250000_000002_create_users_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'pk',
            'username' => 'varchar(255) NOT NULL',
            'password_hash' => 'varchar(255) NOT NULL',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NULL',
        ]);

        $this->createIndex('idx_users_username_unique', 'users', 'username', true);

        return true;
    }

    public function down()
    {
        $this->dropIndex('idx_users_username_unique', 'users');
        $this->dropTable('users');

        return true;
    }
}
