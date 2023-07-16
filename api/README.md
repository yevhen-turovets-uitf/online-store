# Test-shop | Backend

## Technologies

* PHP 8.1
* [Laravel 9](https://laravel.com)
* [REST API](https://ru.wikipedia.org/wiki/REST)
* [CORS](https://developer.mozilla.org/ru/docs/Web/HTTP/CORS)
* [PhpUnit](https://phpunit.de/)
* MySQL 5.7


## Install

The following sections describe dockerized environment.

Just keep versions of installed software to be consistent with the team and production environment (see [Pre-requisites](#pre-requisites) section).


Set your .env vars:
```bash
cp .env.example .env
```

Emails processing .env settings (you can use [mailtrap](https://mailtrap.io/) or your smtp credentials like user@gmail.com):
```dotenv
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_USERNAME=<mailtrap_key>
MAIL_PASSWORD=<mailtrap_password>
MAIL_PORT=587
MAIL_FROM_ADDRESS=admin@thread.com
MAIL_FROM_NAME="BSA Thread Admin"
```

Aws Storage processing .env settings (you can use [aws.amazon](https://us-east-1.console.aws.amazon.com/console/home)):
```dotenv
AWS_ACCESS_KEY_ID=<aws_key_id>
AWS_SECRET_ACCESS_KEY=<aws_secret>
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET==<aws_bucket_name>
```

Install composer dependencies and generate app key:
```bash
composer install
php artisan key:generate
php artisan jwt:secret
```

Database migrations install (set proper .env vars)
```bash
php artisan migrate
php artisan db:seed
```

Application server should be ready on `http://localhost:<APP_PORT>`


## Laravel IDE Helper

For ease of development, you can run the data generation function for the Laravel IDE Helper.
```bash
php artisan ide-helper:generate
php artisan ide-helper:models -N
php artisan ide-helper:meta
```

## OpenApi or Swagger

For openApi you can run the data generation function for the L5 Swagger.
```bash
php artisan l5-swagger:generate
```
Documentation is available at the following link (https://<host_url>/api/documentati    on)
## Debugging

To debug the application we highly recommend you to use xDebug, it is already pre-installed in dockerized environment, but you should setup your IDE.

You can debug your app with [Telescope](https://laravel.com/docs/9.x/telescope) tool which is installed already
