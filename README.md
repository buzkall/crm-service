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
    touch database/crm_service.sqlite
    php artisan migrate
    ```


4. Configure Laravel Passport to handle API tokens

    ```
   php artisan passport:install
    ```

Project can run using 'php artisan serve'
   

## Test

To run tests, execute
    ```
    php artisan test
    ```