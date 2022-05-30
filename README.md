## Requirements

- PHP >= 7.4.19

## Usage

1. Clone project.
2. Create .env file, copy content from .env.example to .env file and config in .env:

- Config Database
``` bash
DB_CONNECTION=mongo
DB_HOST=database_server_ip
DB_PORT=27017
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

- Config Email
``` bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_email_address
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"
```

 - Config login with Google
``` bash
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URL=your_url
```

- Config login with Facebook
``` bash
FACEBOOK_CLIENT_ID=your_client_id
FACEBOOK_CLIENT_SECRET=your_client_secret
FACEBOOK_REDIRECT_URL=your_url
```

- Config Google Drive
``` bash
USER_FILESYSTEM_CLOUD=google
USER_GOOGLE_DRIVE_CLIENT_ID=
USER_GOOGLE_DRIVE_CLIENT_SECRET=
USER_GOOGLE_DRIVE_REFRESH_TOKEN=
USER_GOOGLE_DRIVE_FOLDER_ID=

ADMIN_FILESYSTEM_CLOUD=google
ADMIN_GOOGLE_DRIVE_CLIENT_ID=
ADMIN_GOOGLE_DRIVE_CLIENT_SECRET=
ADMIN_GOOGLE_DRIVE_REFRESH_TOKEN=
ADMIN_GOOGLE_DRIVE_FOLDER_ID=
```
- Config payment by VNPAY
``` bash
VNP_TMN_CODE=your_code
VNP_HASHSECRET=your_code_secret
VNP_URL=your_url
VNP_RETURN_URL=your_url
```

- Config payment by MOMO
``` bash
MOMO_PARTNER_NAME=example
MOMO_PARTNER_CODE=your_partner_code
MOMO_ACCESS_KEY=your_access_key
MOMO_SECRET_KEY=your_secret_key
MOMO_STORE_ID=null
MOMO_IPN_URL=http://example.com/ipn
MOMO_REDIRECT_URL=http://api.example.com/redirect
MOMO_URL_CREATE_PAYMENT=http://example.com/create
MOMO_URL_CONFIRM_PAYMENT=http://example.com/confirm
```

3. Run
``` bash
$ composer install
$ php artisan key:generate
$ php artisan jwt:secret
$ php artisan db:seed
$ php artisan storage:link
$ php artisan route:clear
$ php artisan config:clear
$ php artisan db:seed --class=GroupEmailTemplateTableSeeder
$ php artisan db:seed --class=EmailTemplateTableSeeder
$ php artisan db:seed --class=NotificationPopupGroupTableSeeder
$ php artisan db:seed --class=NotificationPopupTableSeeder

```

4. Local development server
- Run
``` bash
$ php artisan serve
```
- Login with default admin acount email: admin@zent.vn and password: 123456

## Dev additional
Move to root of project and run below command line:
``` bash
composer require --dev squizlabs/php_codesniffer
cp pre-commit .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
```

## Credits

[Zent Software](https://zent.edu.vn/).
