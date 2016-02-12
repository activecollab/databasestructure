### DatabaseStructure Library

[![Build Status](https://travis-ci.org/activecollab/databasestructure.svg?branch=master)](https://travis-ci.org/activecollab/databasestructure)

…

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
abstract class Token extends \ActiveCollab\DatabaseObject\Object
{
    …
}
```

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
