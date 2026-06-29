<?php

class m250000_000001_create_comments_table extends CDbMigration
{
    public function up()
    {
        $this->createTable('comments', [
            'id' => 'pk',
            'name' => 'varchar(255) NULL',
            'message' => 'text NOT NULL',
            'status' => "varchar(255) NOT NULL DEFAULT 'active'",
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NULL',
        ]);

        $this->createIndex('idx_comments_status_id', 'comments', 'status, id');

        return true;
    }

    public function down()
    {
        $this->dropIndex('idx_comments_status_id', 'comments');
        $this->dropTable('comments');

        return true;
    }
}
