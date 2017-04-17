# DatabaseStructure Library

[![Build Status](https://travis-ci.org/activecollab/databasestructure.svg?branch=master)](https://travis-ci.org/activecollab/databasestructure)

…

## Fields

Boolean fields with names that start with `is_`, `has_`, `had_`, `was_`, `were_` and `have_` also get a short getter. For example, if field name is `is_awesome`, builder will product two getters: `getIsAwesome()` and `isAwesome()`.

### Password Field

Password field is field meant for storing password hashes. By default, it sets `password` as field name. It is similar to `StringField` (uses `VARCHAR` columns), but it can't have default value (doh!), and it does not have methods for easy indexing (you can still add an index by yourself, if you wish).

```php
<?php

namespace MyApp;

use ActiveCollab\DatabaseStructure\Field\Scalar\PasswordField;

new PasswordField(); // Use default name (password).
new PasswordField('psswd_hash'); // Specify field name. 
```

### JSON Field

JSON field add a JSON field to the type. It will be automatically serialized and deserialized on reads and writes:

```php
$this->addType('stats_snapshots')->addFields([
    new JsonField('stats')
]);
```

System supports value extraction from JSON fields. These values are extracted by MySQL automatically, and they can be stored and indexed. 

There are two ways of adding extractors. First is by constructing extractor instance by yourself, and adding it:

```php
$execution_time_extractor = (new FloatValueExtractor('execution_time', '$.exec_time', 0))
    ->storeValue()
    ->addIndex();

$this->addType('stats_snapshots')->addFields([
    new DateField('day'),
    (new JsonField('stats'))
        ->addValueExtractor($execution_time_extractor)
]);
```

Second is by calling `extractValue` method, which uses provided arguments to construct the appropriate extractor, configure it and add it to the field. Method arguments:

1. `field_name` - Name of the generated field,
1. `expression` - Expression used to extract the value from JSON. See [https://dev.mysql.com/doc/refman/5.7/en/json-search-functions.html#function_json-extract](JSON_EXTRACT()) MySQL function for details,
1. `default_value` - Value that will be used if `expression` returns `NULL`,
1. `extractor_type` - Class name of the extractor implementation that should be used. Default is `ValueExtractor` (string value extractor), but there are also extractors for int, float, bool, date, and date and time values,
1. `is_stored` - Should the value be permanently stored, or should it be virtual (calculated on the fly on read). Value is stored by default,
1. `is_indexed` - Should the value be indexed. Index on the generated field is added when `TRUE`. `FALSE` by default.

Example:

```php
$this->addType('stats_snapshots')->addFields([
    new DateField('day'),
    (new JsonField('stats'))
        ->extractValue('plan_name', '$.plan_name', 'Unknown', ValueExtractor::class, true, true)
        ->extractValue('number_of_active_users', '$.users.num_active', 0, IntValueExtractor::class, true)
        ->extractValue('is_used_on_day', '$.is_used_on_day', null, BoolValueExtractor::class, false),
]);
```

Getter methods are automatically added for all generated fields:

```php
$snapshot = $pool->getById(StatsSnapshot::class, 1);
print $snapshot->getPlanName() . "\n";
print $snapshot->getNumberOfActiveUsers() . "\n";
print ($snapshot->isUsedOnDay() ? 'yes' : 'no') . "\n";
```

Note that values of generated fields can't be set directly. This code will raise an exception:

```php
$snapshot = $pool->getById(StatsSnapshot::class, 1);
$snapshot->setFieldValue('number_of_active_users', 123);  // Exception!
```

## Associations

### Belongs To

#### Programming to an Interface

Belongs To association supports "programming to an interface" approach. This means that you can set so they accept (and return) instances that implement a specific interface:

```php
<?php

namespace MyApp;

use ActiveCollab\DatabaseStructure\Association\BelongsToAssociation;

(new BelongsToAssociation('author'))->accepts(AuthorInterface::class);
```

### Has Many

```php
<?php

namespace App;

use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;
use ActiveCollab\DatabaseStructure\Field\Composite\NameField;
use ActiveCollab\DatabaseStructure\Structure;

class HasManyExampleStructure extends Structure
{
    public function configure()
    {
        $this->addType('writers')->addFields([
            (new NameField('name', ''))->required(),
        ])->addAssociations([
            new HasManyAssociation('books'),
        ]);
    }
}
```

Method that this association will add to `Writer` model are:

* `getBooksFinder(): FinderInterface` - Prepare a book finder instance for this writer, with all the defaults set (ordering for example). Use it like you would use any other finder: extend it with extra conditions, use it to count records, fetch all, or first record etc, 
* `getBooks(): ?iterable` - Return all books that belong to the writer. When no books are found, this method returns `NULL`,
* `getBookIds(): ?iterable` - Return a list of all book ID-s that belong to the writer. When no books are found, this method returns `NULL`,
* `countBooks(): int` - Return a total number of books.

#### Attributes

Has many association also adds following attributes to the model:

* `books` - Set associated books by providing their instances. These instances can be persisted to the database, or they can be new instances. If new, they will be saved when parent writer object is saved,
* `book_ids` - Set associated books by providing their ID-s.

```php
<?php

namespace App;

// Set books using an attribute:
$writer = $pool->produce(Writer::class, [
    'name' => 'Leo Tolstoy',
    'books' => [$book1, $book2, $book3],
]);

// Or, using ID-s:
$writer = $pool->produce(Writer::class, [
    'name' => 'Leo Tolstoy',
    'book_ids' => [1, 2, 3, 4],
]);
```
#### Programming to an Interface

Has Many association support "programming to an interface" approach. This means that you can set so it accepts (and return) instances that implement a specific interface:

Example:

```php
<?php

namespace MyApp;

use ActiveCollab\DatabaseStructure\Association\HasManyAssociation;

(new HasManyAssociation('books'))->accepts(BookInterface::class);
```

### Has One

#### Programming to an Interface

Has One association support "programming to an interface" approach. This means that you can set so it accepts (and return) instances that implement a specific interface:

Example:

```php
<?php

namespace MyApp;

use ActiveCollab\DatabaseStructure\Association\HasOneAssociation;

(new HasOneAssociation('book'))->accepts(BookInterface::class);
```

### Has Many Via

### Has and Belongs to Many

## Structure Options

Structure object support config option setting via `setConfig()` method. This method can be called during object configuration, of after it has been created:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->setConfig('option_name', 'value');
    }
}
```

Following options are available:

1. `add_permissions` - Add CRUD permission checks to objects. [More…](#add_permissions),
1. `base_class_doc_block_properties` - Specify an array of properties to be added as `@property` elements to DocBlock section of generated classes. [More…](#class_doc_block_properties).
1. `base_class_extends` - Specify which class should built objects extend (`ActiveCollab\DatabaseObject\Object` is default),

### `add_permissions`

This option tells structure to automatically call `permissions()` method for all types that are added to it. This option is turned off by default, but it can be enabled by setting it to one of the two values: 

1. `StructureInterface::ADD_PERMISSIVE_PERMISSIONS` enables permissions and methods that check permissions are set to return `true` by default; 
2. `StructureInterface::ADD_RESTRICTIVE_PERMISSIONS` enables permissions and methods that check permissions are set to return `false` by default.

Example:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->setConfig(‘add_permissions’, StructureInterface::ADD_RESTRICTIVE_PERMISSIONS);
    }
}
```

### `base_class_doc_block_properties`

Some editors read `@property` from DocBlock section of the class and know which properties are available via magic methods, which type they are and offer various features based on that info (like code completion, type checking etc). Use `base_class_doc_block_properties` to specify a list of properties that will be added to the class. Example of the config:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->setConfig(‘base_class_doc_block_properties’, [
            'jobs' => '\\ActiveCollab\\JobsQueue\\Dispatcher'
        ]);
    }
}
```

what it builds:

```php
<?php

namespace Application\Structure\Namespace\Base;

/**
 * @property \ActiveCollab\JobsQueue\Dispatcher $jobs
 *
 * …
 */
abstract class Token extends \ActiveCollab\DatabaseObject\Entity\Entity
{
}
```

### `deprecate_long_bool_field_getter`

Set to true if you want to have log boolean field getters to be marked as deprecated, when there's a short getter (`isAwesome()` vs `getIsAwesome()`).

### `header_comment`

Add a comment that will be included at the header of all auto-generated files. This option is useful if you need to include licensing information in your source code.

## Behaviours

Behaviours are interfaces and interface implementations that types and fields add to resulting object classes. These behaviours can do all sort of things: let you element position in collections, store additional bits of information on object level, check user permissions and more.

### Permissions Behaviour

When applied, permissions behaviour adds `ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface` to object classes, which add four methods that check user permissions over a given object:

1. `canCreate($user)`
2. `canView($user)`
3. `canEdit($user)`
4. `canDelete($user)`

All four methods accept only one argument, and that argument needs to be instance that implements `\ActiveCollab\User\UserInterface` interface.

There are two default implementations that can be added as implementations of `PermissionsInterface`:

1. `ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\PermissiveImplementation` is set to return `true` by default,
2. `ActiveCollab\DatabaseStructure\Behaviour\PermissionsInterface\RestrictiveImplementation` is set to return `false` by default.

**Note:** Generated code does not enforce these checks prior to doing CRUD operations. It’s up to the application that includes DatabaseStructure library to enforce that these restrictions are applied (in ACL or controller layer for example).

Structure can be configured to apply permissions behaviour to types automatically (see `add_permissions` structure option). In a situation when you have structure set to automatically add permissions behaviour to types, but you want to turn it off for a particular type, just call `permissions(false)` again:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->setConfig(‘add_permissions’, StructureInterface::ADD_RESTRICTIVE_PERMISSIONS);
        
        $this->addType(‘reverted_elements’)->addFields([
            …
        ])->permissions(false);
    }
}
```

### Protected Fields Behaviour

This behaviour adds a simple list of proteected fields to the object (accessible using `getProtectedFields()` method). It's up to the rest of the system to decide what to do with this list, but most common scenario is to disable set of these fields when objects are added using POST or updated using PUT requests:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->addType('elements')->protectFields('created_at', 'created_by_id')->unprotectFields('created_by_id'); // will record ['created_at']
    }
}
```

`protectFields` ignores empty fields values, and it can be called multiple times:

```php
class MyStructure extends Structure
{
    public function configure()
    {
        $this->addType('elements')->protectFields('field_1', 'field_2')->protectFields('', '')->protectFields('field_2', 'field_3'); // will only record ['field_1', 'field_2', 'field_3']
    }
}
```

## To Do

1. Add `ChildInterface`, and make sure that `ParentField` adds it to models that include it.
1. Associations should automatically add connection fields to the list of fields to be serialized
1. Association cascading options and tests
