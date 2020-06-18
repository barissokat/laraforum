# Laraforum

This is an open source forum that was built and maintained from Laracasts.com. This project is for educational porpuses only.

## Installation

### Step 1
* To run this project, you must have PHP 7 installed.
* You should setup a host on your web server for your local domain. For this you could also configure Laravel Homestead or Valet. 
* If you want use Redis as your cache driver you need to install the Redis Server. You can either use homebrew on a Mac or compile from source (https://redis.io/topics/quickstart). 

Begin by cloning this repository to your machine, and installing all Composer dependencies.

 	git clone https://github.com/barissokat/laraforum
    cd forum && composer install && npm install
    cp .env.example .env
    php artisan key:generate
    npm run dev

### Step 2
Next, create a new database and reference its name and username/password within the project’s `.env` file. In the example below, we’ve named the database `"laraforum"`.

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laraforum
    DB_USERNAME=root
    DB_PASSWORD=

Then, migrate your database to create the required tables.

    php artisan migrate


### Step 3
Next, boot up a server and visit your forum.

1. Visit: http://localhost/register to register a new forum account.
2. Edit `config/laraforum.php`, and add any email address that should be marked as an administrator.
3. Visit: http://localhost/admin/channels to seed your forum with one or more channels.  

Then, clear your cache to get fresh channel table.

    php artisan cache:clear
