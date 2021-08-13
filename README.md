# Session Component

[![Latest Stable Version](https://img.shields.io/packagist/v/pollen-solutions/session.svg?style=for-the-badge)](https://packagist.org/packages/pollen-solutions/session)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen Solutions **Session** Component provides utilities to store and query informations through HTTP Request User's session.

## Installation

```bash
composer require pollen-solutions/session
```

## Basic Usage

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();
try {
    $session->start();
} catch (RuntimeException $e) {
    unset($e);
}

$session->set('key1', 'value1');
$session->set('key2', 'value2');

var_dump($session->all());
```

## Base General API

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();

// Start session (with exception catching for best practice).
try {
    $session->start();
} catch (RuntimeException $e) {
    // throwing error.
    throw $e;
    // or mute the error.
    // unset($e);
}

// Sets data.
$session->set('key1', 'value1');
$session->set('key2', 'value2');

// Checks if data key exists.
$session->has('key1');

// Gets data value.
$session->get('key1', 'defaultValue');

// Returns all data values.
$session->all();

// Counts datas.
$session->count();

// Deletes a data by its key.
$session->remove('key1');

// Clear all existing datas.
$session->clear();
```

## Base Flash API

A simple way to set flash messages in an HTTP request and to display them after a page redirect.

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();

try {
    $session->start();
} catch (RuntimeException $e) {
    unset($e);
}

// Retrieve the flashBag instance.
$session->flash();

// Sets a flash data.
$session->flash()->set('key1', 'value1');
$session->flash()->set('key2', 'value2');

// Alternative method to set flash data.
$session->flash([
    'key1' => 'value1',
    'key2' => 'value2'
]);

// Checks if flash data exists by its key.
$session->flash()->has('key1');

// Gets a flash data value without removing it.
$session->flash()->peek('key1');

// Gets a flash data value with a fallback value and without removing it.
$session->flash()->read('key1', 'defaultValue1');

// Gets all flash data values.
$session->flash()->peekAll();

// Alternative method to gets all flash data values
$session->flash()->readAll();

// Gets a flash data value and removing it.
$session->flash()->get('key1');

// Gets a flash data value with a fallback value and removing it.
$session->flash('key1', 'defaultValue');

// Gets all flash data value and removing them.
$session->flash()->all();

// Counts flash datas.
$session->flash()->count();

// Removes a flash data by its key.
$session->flash()->remove('key1');

// Clear all flash datas.
$session->flash()->clear();
```

## CSRF Protection

Session provides a protection system against CSRF attacks.

### Set Token ID

Ideally, use a string of at least 32 characters.

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();
$session->setTokenID('example_token_id');
```

### Basic token verification process

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();
$session->setTokenID('example_token_id');

$token = $session->getToken();

var_dump($session->verifyToken($token));
```

### Custom token verification process

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();

$token = $session->getToken('custom_token_id');

var_dump($session->verifyToken($token, 'custom_token_id'));
```

### Form workflow

1. Create a CSRF token.

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();

$csrf_token = $session->getToken();
```

2. Submit a form with CSRF token.

```html
<form method="post">
    <input type="hidden" name="token" value="{{ csrf_token }}">
    <button type="submit">Submit</button>
</form>
```

3. Catch and verify CSRF token submission.

```php
use Pollen\Session\SessionManager;
use Pollen\Http\Request;

$session = new SessionManager();
$request = Request::createFromGlobals();
$token = $request->request->get('token');

var_dump($session->verifyToken($token));
```

## Advanced usage

### Access to the session through an HTTP Request

```php
use Pollen\Session\SessionManager;
use Pollen\Http\Request;

$session = new SessionManager();
try {
    $session->start();
} catch (RuntimeException $e) {
    unset($e);
}

$session->set('key1', 'value1');
$session->set('key2', 'value2');

$request = Request::createFromGlobals();
$request->setSession($session->processor());

var_dump($request->getSession()->all());
```

### Attribute Key Bag

```php
use Pollen\Session\SessionManager;

$session = new SessionManager();
try {
    $session->start();
} catch (RuntimeException $e) {
    unset($e);
}

// Registers an attribute key bag.
$keyBag = $session->addAttributeKeyBag('specialKey');

// Sets data for key
$keyBag->set('test1', 'value1');
$keyBag->set('test2', 'value2');

// Alternate dot syntax allowed
$keyBag->set('test3.childs', ['child1', 'child2', 'child3']);

// Get data
var_dump($keyBag->all());
var_dump($session->get('specialKey'));
```