<?php

namespace Gsferro\DatabaseSchemaEasy\Providers;

use Gsferro\DatabaseSchemaEasy\DatabaseSchemaEasy;
use Illuminate\Support\ServiceProvider;

class DatabaseSchemaEasyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app()->bind('databaseSchema', function (string $table, ?string $connection = null) {
            return new DatabaseSchemaEasy($table, $connection);
        });
    }
}
