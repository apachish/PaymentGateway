<p align="center"><img src="resources/images/payment.png?raw=true"></p>



# Laravel Payment Gateway




Add badges from somewhere like: [shields.io](https://shields.io/)

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
[![GPLv3 License](https://img.shields.io/badge/License-GPL%20v3-yellow.svg)](https://opensource.org/licenses/)
[![AGPL License](https://img.shields.io/badge/license-AGPL-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)


This is a Laravel Package for Payment Gateway Integration. This package supports `Laravel 8+`.

<!--[Donate me](https://apachish.ir/donate) if you like this package :sunglasses: :bowtie:-->


> This packages works with multiple drivers, and you can create custom drivers if you can't find them in the [current drivers list](#list-of-available-drivers) (below list).
<!--
- [داکیومنت فارسی][link-fa]
- [English documents][link-en]
- [中文文档][link-zh]-->

# List of contents

- [Laravel Payment Gateway](#laravel-payment-gateway)
- [List of contents](#list-of-contents)
- [List of available drivers](#list-of-available-drivers)
  - [Install](#install)
  - [Configure](#configure)
  - [How to use](#how-to-use)
      - [Working with invoices](#working-with-invoices)
      - [Purchase invoice](#purchase-invoice)
      - [Pay invoice](#pay-invoice)
      - [Verify payment](#verify-payment)
      - [Useful methods](#useful-methods)
      - [Create custom drivers:](#create-custom-drivers)
      - [Events](#events)
  - [Change log](#change-log)
  - [Contributing](#contributing)
  - [Security](#security)
  - [Credits](#credits)
  - [License](#license)

# List of available drivers

- [behpardakht (mellat)](http://www.behpardakht.com/) :heavy_check_mark:



> you can create your own custom drivers if it does not exist in the list, read the `Create custom drivers` section.

## Install

Via Composer

``` bash
$ composer require apachish/payment-driver
```

## Publish Vendor Files

- **publish configuration files:**
``` bash
php artisan vendor:publish --tag=payment-driver-config
```

 - **publish views for customization:**
``` bash
php artisan vendor:publish --tag=payment-driver-views
```

## Configure

If you are using `Laravel 8` or higher then you don't need to add the provider and alias. (Skip to b)

a. In your `config/app.php` file add these two lines.

```php
// In your providers array.
'providers' => [
    ...
    Apachish\Payment\Driver\Provider\PaymentServiceProvider::class,
],

// In your aliases array.
'aliases' => [
    ...
    'Payment' => Apachish\Payment\driver\Facade\Payment::class,
],
```

In the config file you can set the `default driver` to use for all your payments. But you can also change the driver at runtime.

Choose what gateway you would like to use in your application. Then make that as default driver so that you don't have to specify that everywhere. But, you can also use multiple gateways in a project.

```php
// Eg. if you want to use mellat.
'default' => 'mellat',
```

Then fill the credentials for that gateway in the drivers array.

```php
'drivers' => [
    'mellat' => [
        // Fill in the credentials here.
        'terminalId	' => '',
        'userName' => '',
        'userPassword' => '',
        'callbackUrl' => 'http://yoursite.com/path/to',
        'description' => 'payment in '.config('app.name'),
    ],
    ...
]
```

## How to use

your `Invoice` holds your payment details, so initially we'll talk about `Invoice` class.

#### Working with invoices




## Feedback

If you have any feedback, please reach out to us at apachish@gmail.com



## Credits

- [Shahriar Pahlevansadegh](https://apachish.ir)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/shetabit/payment.svg?style=flat-square
[ico-download]: https://img.shields.io/packagist/dt/shetabit/payment.svg?color=%23F18&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/shetabit/payment.svg?label=Code%20Quality&style=flat-square

[link-fa]: README-FA.md
[link-en]: README.md
[link-zh]: README-ZH.md
[link-packagist]: https://packagist.org/packages/shetabit/payment
[link-code-quality]: https://scrutinizer-ci.com/g/shetabit/payment
[link-author]: https://github.com/khanzadimahdi
[link-contributors]: ../../contributors
