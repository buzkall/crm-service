## Configuration

1. Run composer

    For development: ```composer install```

    For production or demo
    ```composer install --no-dev```

2. Create sqlite database file 
    ```
    touch database/crm_service.sqlite
    ```
   
3. Copy the environment file example and force app key generation

    ```
    cp .env.example .env
    php artisan key:generate 
    ```