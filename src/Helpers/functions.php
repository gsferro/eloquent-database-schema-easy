<?php

use Gsferro\DatabaseSchemaEasy\DatabaseSchemaEasy;

if ( ! function_exists('dbSchemaEasy')) {
    /**
     * Initiate DatabaseSchemaEasy hook.
     *
     * @return DatabaseSchemaEasy
     */
    function dbSchemaEasy(string $table, ?string $connection = null)
    {
        return app()->make('dbschemaeasy', [
            'table'      => $table,
            'connection' => $connection
        ]);
    }
}

if ( ! function_exists('dbSchema')) {
    /**
     * Initiate DatabaseSchemaEasy hook.
     *
     * @return DatabaseSchemaEasy
     */
    function dbSchema(string $table, ?string $connection = null)
    {
        return dbSchemaEasy($table, $connection);
    }
}

if ( ! function_exists('databaseSchemaEasy')) {
    /**
     * Initiate DatabaseSchemaEasy hook.
     *
     * @return DatabaseSchemaEasy
     */
    function databaseSchemaEasy(string $table, ?string $connection = null)
    {
        return dbSchemaEasy($table, $connection);
    }
}

if ( ! function_exists('databaseSchema')) {
    /**
     * Initiate DatabaseSchemaEasy hook.
     *
     * @return DatabaseSchemaEasy
     */
    function databaseSchema(string $table, ?string $connection = null)
    {
        return dbSchemaEasy($table, $connection);
    }
}
