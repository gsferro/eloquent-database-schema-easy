<?php

namespace Gsferro\DatabaseSchemaEasy\Facades;

use Illuminate\Support\Facades\Facade;

class DatabaseSchemaEasy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'databaseSchema';
    }
}