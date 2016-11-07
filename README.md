## php-background-proccess

#### Installation
	
	$ composer require dflourusso/php-background-proccess

#### Usage example

```php
$bg = new BackgroundProccess('teste', __DIR__ . '/tmp/background-proccess');

$bg->run(__DIR__ . '/bin/test_bg_task.php');
```

### Authors

- [Daniel Fernando Lourusso](http://dflourusso.com.br)