# Laraforum

This is an open source forum that was built and maintained from Laracasts.com. This project is for educational porpuses only.

## Installation

### Step 1.
> To run this project, you must hav PHP 7 installed as a prerequisite.

Begin by cloning this repository to your machine, and installing all Composer dependencies.

 	git clone https://github.com/barissokat/laraforum`
    cd forum && composer install
    php artisan key:generate
    cp .env.example .env

### Step 2.
Next, create a new database and reference its name and username/password within the project’s `.env` file. In the example below, we’ve named the database `"laraforum"`.

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=laraforum
    DB_USERNAME=root
    DB_PASSWORD=

Then, migrate your database to create the required tables.

    php artisan migrate


### Step 3.
Finally, add one or more channels. Login with the following credentials:

```
email: admin@example.com
password: admin
```

now visit: http://localhost/admin/channels and add at least one channel.

### Step 4.
Use your forum! Visit http://localhost to create a new account and publish your first thread.
