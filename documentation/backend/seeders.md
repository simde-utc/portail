# Seeders

Seeders are php scripts to fill development database in order to have data to deal with. It allows to test more functionnalities (Such as a "more articles" feature wich needs at least 11 articles).

There is a build-in solution in Laravel to create seeders. If you want to help us with seeders, we highly recommand you to read [the official  documentation on seeders](https://laravel.com/docs/master/seeding).

## Seeder realization
Before seeder realization, we prefer realizing a model factory. Because we need Factories for or tests. Factories uses `Faker`. You can find Faker's documentation [here](https://github.com/fzaninotto/Faker) and Laravel Factories' documentation [here](https://laravel.com/docs/master/database-testing#writing-factories).

Then you can use the factory for seeding. Try print log to `stdout` in order for developpers to know seeding progression. 

## Configuration

Seeders have their own configuration to help developpers adapt their developpment data with their needs. To add a new seeder in the configuration see the example below.

Let's say you want to add `new_seeder` configuration values and you have 2 of them. Inside the file `config/seeder.php` insert : 

```php
return [
    .
    .
    .
    'new_seeder' => [
        'config_value_1' => 12,
        'config_value_2' => "value",
    ],
];
```

You will now be able to retrieve these data in your code with the `config` function:
```php
config('seeder.new_seeder.config_value_1')
```