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

`add_permissions` tells structure to automatically call `permissions()` method for all types that are added to it. This option is turned off by default, but it can be enabled by setting it to one of the two values: 

1. `StructureInterface::ADD_PERMISSIVE_PERMISSIONS` enables permissions and methods that check permissions are set to return `true` by default; 
2. `StructureInterface::ADD_RESTRICTIVE_PERMISSIONS` enables permissions and methods that check permissions are set to return `false` by default.

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
