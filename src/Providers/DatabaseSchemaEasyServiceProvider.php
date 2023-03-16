<?php

namespace Gsferro\DatabaseSchemaEasy\Providers;

use Gsferro\DatabaseSchemaEasy\DatabaseSchemaEasy;
use Illuminate\Support\ServiceProvider;

class DatabaseSchemaEasyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('dbschemaeasy', function ($app, array $params) {
            return new DatabaseSchemaEasy(
                $params["table"], 
                $params["connection"] ?? null
            );
        });
    }
}
