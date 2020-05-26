# Laraforum
---
This is an open source forum that was built and maintained from Laracasts.com. This project is for educational porpuses only.

## Installation
---
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

reCAPTCHA is a Google tool to help prevent forum spam. You'll need to create a free account.

https://www.google.com/recaptcha/intro/v3.html

Choose reCAPTCHA V2, and specify your local and production domain name, as illustrated in the image below.

![recaptcha](/recaptcha.jpg)

Once submitted, you'll see two important keys that should be referenced in your `.env` file.

    RECAPTCHA_KEY=
    RECAPTCHA_SECRET=

### Step 4.
Until an administration portal is available, manually insert any number of `channels` (think of these as forum categories) into the `channels` table in your database.

Once finished, clear your server cache, and you’re all set to go!

    php artisan cache:clear

### Step 5.
Use your forum! Visit http://localhost to create a new account and publish your first thread.
