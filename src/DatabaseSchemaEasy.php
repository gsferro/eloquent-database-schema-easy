<?php

namespace Gsferro\DatabaseSchemaEasy;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

class DatabaseSchemaEasy
{
    private Connection $connection;
    private Builder $builder;
    private AbstractSchemaManager $manager;

    public function __construct(private string $table, ?string $connection = null)
    {
        $this->connection = DB::connection($connection);
        $this->builder    = $this->connection->getSchemaBuilder();
        $this->manager    = $this->connection->getDoctrineSchemaManager();
        
        throw_if(!$this->hasTable(),
            ModelNotFoundException::class,
            "Table [ {$this->table} ] not exists in database using connection [ {$this->connection->getDriverName()} ]"
        );
    }

    /**
     * Verifica se a table exists
     *
     * @return bool
     */
    public function hasTable(): bool
    {
        return $this->builder->hasTable($this->table);
    }

    /**
     * Retorna todas as colunas da table
     *
     * @param bool $includePrimaryKeys true
     * @return array
     */
    public function getColumnListing(bool $includePrimaryKeys = true, bool $includeTimestamps = false): array
    {
        $columnListing = $this->builder->getColumnListing($this->table);
        $columnsPk     = $includePrimaryKeys
            ? $columnListing
            : array_diff($columnListing, $this->primaryKeys());

        $columnsTimes = $includeTimestamps
            ? $columnsPk
            : array_diff($columnsPk, [
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

        return array_values($columnsTimes);
    }

    /**
     * Verifica se a coluna exists
     * 
     * @param string $column
     * @return bool
     */
    public function hasColumn(string $column): bool
    {
        return $this->builder->hasColumn($this->table, $column);
    }
    
    /**
     * Verifica se a coluna exists
     * 
     * @return bool
     */
    public function hasColumnsTimestamps(): bool
    {
        return $this->hasColumns([
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * Verifica se as colunas exists
     * 
     * @param array $columns
     * @return bool
     */
    public function hasColumns(array $columns): bool
    {
        return $this->builder->hasColumns($this->table, $columns);
    }

    /**
     * Pega todos os tipos das colunas
     *
     * @param array $columns
     * @return array
     */
    public function getTypeFromColumns(array $columns): array
    {
        $types = [];
        foreach ($columns as $column) {
            $types[ $column ] = $this->getColumnType($column);
        }
        return $types;
    }

    /**
     * Retorna o type da colunas
     *
     * @param string $column
     * @return string
     */
    public function getColumnType(string $column): string
    {
        return $this->builder->getColumnType($this->table, $column);
    }

    /**
     * Retorna todas as informações da colunas
     *
     * @param string $column
     * @return array
     */
    public function getDoctrineColumn(string $column): array
    {
        return $this->connection
                ->getDoctrineColumn($this->table, $column)
                ->toArray() + [
                    'primary_key' => $this->isPrimaryKey($column)
                ];
    }

    /**
     * Verifica se a coluna é pk
     *
     * @param string $column
     * @return bool
     */
    public function isPrimaryKey(string $column): bool
    {
        return in_array($column, $this->primaryKeys());
    }

    /**
     * Retorna as chaves pks
     *
     * @return array
     */
    public function primaryKeys(): array
    {
        return $this->manager
            ->listTableDetails($this->table)
            ->getPrimaryKey()
            ->getColumns();
    }

    /**
     * Verifica se a table já existe model eloquent criada
     * retorna a model ou false
     *
     * @param string|null $table
     * @return bool|string
     */
    public function hasModelWithTableName(string $table = null): bool|string
    {
        $path   = app_path('Models') . '/*.php';
        $models = collect(glob($path))->map(fn($file) => basename($file, '.php'))->toArray();

        foreach ($models as $model) {
            $instance = "App\Models\\$model";
            $getTable    = (new $instance)->getTable();
            if ($getTable == ($table ?? $this->table)) {
                return $instance;
            }
        }

        return false;
    }

    /**
     * Todas as tabelas da conexão
     *
     * @return array
     */
    public function getAllTables(): array
    {
       return $this->manager->listTableNames();
    }

    /**
     * Verifica se a coluna é uma foreing key e devolve os dados do relacionamento
     *
     * @param string $column
     * @param bool $fromStubReplace
     * @return bool|array
     */
    public function hasForeinsKey(string $column, bool $fromStubReplace = false): bool|array
    {
        $foreignKeys = $this->manager->listTableForeignKeys($this->table);

        $keys = !$fromStubReplace
            ? [ // todo talvez nem precise
                // belongsto
                'table',
                'tableCamel',
                'related',
                'relatedInstance',
                'foreignKey',
                'ownerKey',
            ]
            : [
                // belongsto
                '/\{{ table }}/',
                '/\{{ tableCamel }}/',
                '/\{{ related }}/',
                '/\{{ relatedInstance }}/',
                '/\{{ foreignKey }}/',
                '/\{{ ownerKey }}/',
            ];

        foreach ($foreignKeys as $foreingnKey) {
            if (in_array($column, $foreingnKey->getLocalColumns())) {
                $tableName       = $foreingnKey->getForeignTableName();
                // TODO quando não houver, criar?
                $relatedInstance = $this->hasModelWithTableName($tableName);

                return array_combine($keys, [
                    // belongsto
                    $tableName,
                    Str::of($tableName)->camel(),
                    Str::of($relatedInstance)->replace('App\Models\\', ''),
                    $relatedInstance,
                    $column,
                    current($foreingnKey->getForeignColumns()),
                    
                    // hasmany
                ]);
            }
        }

        return false;
    }
}
