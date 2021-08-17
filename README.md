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
   The seed parameter will create two dummy users: admin@mail.com, user@mail.com, password is in the DatabaseSeeder file
    ```
    touch database/crm_service.sqlite
    php artisan migrate --seed
    ```


4. Configure Laravel Passport to handle API tokens

    ```
   php artisan passport:install
    ```

Project can run using 'php artisan serve' or using Docker with Laravel Sail

## Test

To run tests, execute
```
php artisan test
```

There is an exported collection of all the endpoints in Postman in ./crm_service.postman_collection.json  