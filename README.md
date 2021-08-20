## Configuration

1. Run composer

   For development: ```composer install```

   For production or demo
   ```composer install --no-dev```

2. Copy the environment file example and force app key generation

    ```
    cp .env.example .env
    php artisan key:generate 
    ```


3. Create sqlite database file and run migrations (can be changed to mysql. Using sqlite to simplify)
    ```
    php artisan migrate --seed
    ```

4. Configure Laravel Passport to handle API tokens
    ```
   php artisan passport:install
    ```
   
5. Run migrations and seed.
The seed parameter will create two dummy users: admin@mail.com, user@mail.com, their dummy passwords are in the DatabaseSeeder file

   ```
   php artisan migrate --seed
    ```
   
6. Link the storage public folder for the uploads in customers
    ```
   php artisan storage:link
    ```

Project can run using 'php artisan serve' or using Docker with Laravel Sail

## Test

To run tests, execute
```
php artisan test
```

There is an exported collection of all the endpoints in Postman in ./crm_service.postman_collection.json  