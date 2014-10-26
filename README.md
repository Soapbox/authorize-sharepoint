# Authorize-Sharepoint
[Authorize](http://github.com/soapbox/authorize) strategy for SharePoint authentication.

## Getting Started
- Install [Authorize](http://github.com/soapbox/authorize) into your application
to use this Strategy.
- Create you sharepoint application and obtain applicable keys

## Installation
Add the following to your `composer.json`
```
"require": {
	...
	"soapbox/authorize-sharepoint": "0.*",
	...
}
```

### app/config/app.php
Add the following to your `app.php`, note this will be removed in future
versions since it couples us with Laravel, and it isn't required for the library
to function
```
'providers' => array(
	...
	"SoapBox\AuthorizeSharepoint\AuthorizeSharepointServiceProvider",
	...
)
```

## Usage

### Login
```php

use SoapBox\Authroize\Authenticator;
use SoapBox\Authorize\Exceptions\InvalidStrategyException;
...
$settings = [
	'url'       => 'URL',
	'path'      => '/',
	'acs'       => 'acs/endpoint',
	'client_id' => 'CLIENT_ID',
	'secret'    => 'SECRET'
];

$strategy = new Authenticator('sharepoint', $settings);

$user = $strategy->authenticate($parameters);

```

### Endpoint
```php

use SoapBox\Authroize\Authenticator;
use SoapBox\Authorize\Exceptions\InvalidStrategyException;
...
$settings = [
	'url'       => 'URL',
	'path'      => '/',
	'acs'       => 'acs/endpoint',
	'client_id' => 'CLIENT_ID',
	'secret'    => 'SECRET'
];

$strategy = new Authenticator('sharepoint', $settings);
$user = $strategy->endpoint();

```
