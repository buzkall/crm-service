## Configuration

1. Run composer

   For development or demo: ```composer install```

   For production
   ```composer install --no-dev```

2. Copy the environment file example and force app key generation

    ```
    cp .env.example .env
    php artisan key:generate 
    ```


3. Create sqlite database file and run migrations (can be changed to mysql. Using sqlite to simplify)
    ```
    touch database/database.sqlite
    ```
   
4. Run migrations and seed.
The seed parameter will create two dummy users: admin@mail.com, user@mail.com, their dummy passwords are in the DatabaseSeeder file
   (to be able to seed, the composer execution has to be the development one, without the --no-dev)

   ```
   php artisan migrate --seed
    ```

5. (optional) If no seeding has been done, configure Laravel Passport to handle API tokens
    ```
   php artisan passport:install
    ```
   
6. Link the storage public folder for the uploads in customers
    ```
   php artisan storage:link
    ```

Project has been tested using the Vagrant box [Homestead](https://laravel.com/docs/8.x/homestead).

But can also run locally using 'php artisan serve' or using Docker with [Laravel Sail](https://laravel.com/docs/8.x/sail)

## Test

To run tests, execute
```
php artisan test
```

There is an exported collection of all the endpoints in Postman in ./crm_service.postman_collection.json


![](<https://c.tenor.com/pPKOYQpTO8AAAAAd/monkey-developer.gif>)
