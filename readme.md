This is an api for the PHP programmers that want to work with World Of Tanks dossier
cache. It allows to convert dossier cache files into an object structure and then
convert it to an array or JSON.

Also, it is possible to aggregate different battle types within single tank, get 
total results and then compute efficiency for each item using different formulas.

Dossier cache contains statistics of every battle type for every tank that user
ever achieved. Whether you want information about random battles or maybe new
7/42 format -- it is all in there. Checkout `Stats\Battle` class.

Example
-------

There is an application that uses this API and displays dossier data in tabular
format.

Put some dossier files into `example/data` directory.

If you have PHP >= 5.4 you can start server using this command:

```
php -S localhost:8000
```

_You need to run this command from `example` directory._

Otherwise you will have to setup yout WEB-server to run the script.

Also, you need to have [Python][1] installed.

[1]: http://www.python.org

Using with Laravel
------------------

This API can be used with Laravel 4.1. You just need to add the service provider:

```php
'Kalnoy\Wot\Dossier\DossierServiceProvider',
```

And the facade:

```php
'Dossier' => 'Kalnoy\Wot\Dossier\Facades\Dossier.php',
```

And then you can convert the dossier using the `Dossier` facade:

```php
$dossier = Dossier::convert($file, $original);
```