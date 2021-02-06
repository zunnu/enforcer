# Enforcer

Enforcer is a simple lightweight acl plugin for CakePHP 3.x

## Requirements
* CakePHP 3.x
* PHP 7.2 >

## Installing Using [Composer][composer]

`cd` to the root of your app folder (where the `composer.json` file is) and run the following command:

```
composer require zunnu/enforcer
```

Then load the plugin by using CakePHP's console:

```
./bin/cake plugin load Enforcer
```

Next create the tables:

```
./bin/cake migrations migrate -p Enforcer
```

## Usage
You will need to modify your `src/Controller/AppController.php` and load the Enforcer component in the `initialize()` function
```php
$this->loadComponent('Enforcer.Enforcer', [
    'unauthorizedRedirect' => [
        'plugin' => false,
        'controller' => 'Users',
        'action' => 'login',
        'prefix' => false
    ],
    'protectionMode' => 'everything' // everything | filters
]);
```

The `unauthorizedRedirect` will tell Enforcer where to redirect if the user has permission error.
The `protectionMode` will tell Enforcer how to handle permissions.

| protectionModes | README |
| ------ | ------ |
| everything | Enforcer will automaticly try to protect all public controller function |
| filters | Enforcer will protect the controllers where the protection is called from the `beforeFilter()` |

If the `protectionMode` **filters** is enabled you need to add the 

```php
public function beforeFilter(Event $event) {
    parent::beforeFilter($event);
    
    // permission load
    return $this->Enforcer->hasAccess($this->request, $this->Auth->user());
}
```

## Permissions
The migrations will create tree different groups.
You can add, modify or delete groups by going to
http://app-address/enforcer/admin/groups/index

| Groups | README |
| ------ | ------ |
| admin | All powerfull |
| user | Default user group |
| guest | Site visitors |

The default admin group should be able to access the permissions page.
You should be able to access the page using this url
http://app-url/enforcer/admin/permissions
<img src="https://imgur.com/N28gblK.png" alt="Enforcer permissions">
<br>
<img src="https://i.imgur.com/VkWzlgJ.png" alt="Enforcer permissions">

If the request is ajax the permission error will look like this:
<img src="https://i.imgur.com/gTD1lJC.png" alt="Enforcer permissions">

## Todos

 - User specific permissions
 - Groupped controllers. Like the user only has access to billing

## License

Licensed under [The MIT License][mit].

[cakephp]:http://cakephp.org
[composer]:http://getcomposer.org
[mit]:http://www.opensource.org/licenses/mit-license.php
