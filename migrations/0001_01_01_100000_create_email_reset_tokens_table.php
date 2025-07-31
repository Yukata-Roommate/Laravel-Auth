<?php

use YukataRm\Laravel\Db\Migration\CreateTableMigration;

use Illuminate\Database\Schema\Blueprint;

return new class extends CreateTableMigration
{
    /**
     * table name
     *
     * @var string
     */
    protected string $tableName = "email_reset_tokens";

    /**
     * create table callback
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @return void
     */
    protected static function create(Blueprint $table): void
    {
        $table->id();
        $table->foreignId("user_id")->constrained()->cascadeOnDelete();
        $table->string("email")->unique();
        $table->string("token")->unique();
        $table->timestamp("expired_at");
    }
};
