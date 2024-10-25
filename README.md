# RVTGTR


**Kas tika izmantots:**
Mājaslapai

    Laravel
    Node.js
    Vue.js
    MySQL

Priekš plugina 

 C++ ar JUCE framework


**lai mājaslapu palaistu:**
backenda setups
     
    cd laravelBackend    
    composer update
    cp .env.example .env 

    composer install

    php artisan key:generate   
    php artisan passport:keys    
    php artisan passport:client --personal    
    php artisan storage:link    

    composer require laravel/telescope --dev     

    php artisan telescope:install     
    php artisan migrate
    
    php artisan migrate:fresh --seed
    
    php artisan passport:client --personal

composer require laravel/breeze --devphp artisan breeze:install

env faila config:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rvtgtr
DB_USERNAME=root
DB_PASSWORD=
```
    frontend setup
    cd frontend        
    npm install

**palaišana**
    
    cd frontend    
    npm run dev
    
    cd laravelBackend    
    php artisan serve

>>>>>>> origin/main
