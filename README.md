![Logo](logo.png)

Simple and efficient way to manipulate your database connection scheme, whatever it may be.

### Instalação:

```composer 
composer require gsferro/database-schema-easy
```

### Dependências:

Package | Versão
--------|-----------
PHP | ^8
Laravel | ^8
Doctrine/orm | ^2.14

### Uso:

- Instancie o serviço
    ```text 
    $schema = new DatabaseSchemaEasy(string $table, string $connection = null);
    ```

- Facade
  ```text
  $schema = dbschemaeasy(string $table)
  ```

### Métodos:

- `hasTable(): bool`: Verifica se a table exists
  ```php
  $schema->hasColumn('id');
  # true
  ```

- `getColumnListing(bool $includePrimaryKeys = true, bool $includeTimestamps = false): array` : Retorna todas as colunas da table
  ```php
  $schema->getColumnListing();
  /*
  [
    "id",
    "uuid",
    "pedido_status_id",
  ]
  */
  ```
- `hasColumn(string $column): bool`: Verifica se a coluna exists
  ```text
  $schema->hasColumn('id');
  # true
  ```

- `hasColumns(array $columns): bool`:Verifica se as colunas exists
  ```php 
  $schema->hasColumns(['id', 'uuid']);
  # true
  ```
  
- `hasColumnsTimestamps(): bool`: Verifica se a coluna exists
  ```php 
  $schema->hasColumnsTimestamps();
  # true
  ```

- `getTypeFromColumns(array $columns): array`: Pega todos os tipos das colunas
  ```php
  $schema->getTypeFromColumns(['id', 'uuid']);
  /*
  [
    "id" => "integer",
    "uuid" => "guid",
  ]
  */
  ```

- `getColumnType(string $column): string`:Retorna o type da colunas
  ```php
  $schema->getColumnType('id');
  # "integer"
  ```

- `getDoctrineColumn(string $column): array`:Retorna todas as informações da colunas
  ```php
  $schema->getDoctrineColumn('id');
  /*
  [
    "name" => "id",
    "type" => Doctrine\DBAL\Types\IntegerType {#4751},
    "default" => null,
    "notnull" => true,
    "length" => null,
    "precision" => 10,
    "scale" => 0,
    "fixed" => false,
    "unsigned" => false,
    "autoincrement" => true,
    "columnDefinition" => null,
    "comment" => null,
    "primary_key" => true,
  ]
  */
  ```

- `isPrimaryKey(string $column): bool`:Verifica se a coluna é pk
  ```php
  $schema->isPrimaryKey('id');
  # true
  ```

- `primaryKeys(): array`:Retorna as chaves pks
  ```php
  $schema->primaryKeys();
  /*
  [
    "id",
  ]
  */
  ```

- `hasModelWithTableName(string $table = null): bool|string`:Verifica se a table já existe model eloquent criada
  ```php
  $schema->hasModelWithTableName();
  # "App\Models\Pedidos"
  
  $schema->hasModelWithTableName('abc');
  # false
  ```

- `getAllTables(): array`: Todas as tabelas da conexão
  ```php
  $schema->getAllTables();
  /*
  [
    'pedidos',
    'pedido_status,
    ...
  ]
  */
  ```

- `hasForeinsKey(string $column, bool $fromStubReplace = false): bool|array`: Verifica se a coluna é uma foreing key e devolve os dados do relacionamento
  ```php 
   $schema->hasForeinsKey('pedido_status_id');
  /*
    [
    "table" => "pedido_status",
    "tableCamel" => Illuminate\Support\Stringable {#5069
    value: "pedidoStatus",
    },
    "related" => Illuminate\Support\Stringable {#5068
    value: "",
    },
    "relatedInstance" => false,
    "foreignKey" => "pedido_status_id",
    "ownerKey" => "id",
  ]
  */
  ``` 