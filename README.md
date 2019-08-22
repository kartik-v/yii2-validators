yii2-validators
===============

[![Stable Version](https://poser.pugx.org/kartik-v/yii2-validators/v/stable)](https://packagist.org/packages/kartik-v/yii2-validators)
[![Unstable Version](https://poser.pugx.org/kartik-v/yii2-validators/v/unstable)](https://packagist.org/packages/kartik-v/yii2-validators)
[![License](https://poser.pugx.org/kartik-v/yii2-validators/license)](https://packagist.org/packages/kartik-v/yii2-validators)
[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-validators/downloads)](https://packagist.org/packages/kartik-v/yii2-validators)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-validators/d/monthly)](https://packagist.org/packages/kartik-v/yii2-validators)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-validators/d/daily)](https://packagist.org/packages/kartik-v/yii2-validators)

This extension adds new model validator components for Yii2 frameworkor and/or enhances existing Yii2 model validators. 

The `EmailValidator` extends the [yii\validators\EmailValidator](https://www.yiiframework.com/doc/api/2.0/yii-validators-emailvalidator)
component to support multiple email inputs for Yii2 framework. In addition this validator allows setting the `multiple` property and
setting the `min` and `max` number of email addresses allowed. This is useful for adding validation rules to your attributes
in the model and also adds enhanced client validation support for ActiveForm inputs at runtime via Javascript.

The `PhoneValidator` extends the [yii\validators\Validator](https://www.yiiframework.com/doc/api/2.0/yii-validators-validator) component
using [libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) based on Google's
[libphonenumber](https://github.com/googlei18n/libphonenumber) library.

The `CardValidator` extends the [yii\validators\Validator](https://www.yiiframework.com/doc/api/2.0/yii-validators-validator) component.
It validates standard debit and credit card number inputs using Luhn's checksum validation. It also helps auto detect the card types and
additionally validating the card holder name, expiry date and CVV entered.

The `JsonValidator` extends the [yii\validators\Validator](https://www.yiiframework.com/doc/api/2.0/yii-validators-validator) component.
It validates JSON input in text area fields. The validator also helps to overwrite the JSON input as a pretty format via `prettify` setting.
It includes both server and client validations.

## Demo
You can see detailed [documentation and demos](http://demos.krajee.com/validators) on usage of this extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

> NOTE: Check the [composer.json](https://github.com/kartik-v/yii2-validators/blob/master/composer.json) for this extension's requirements and dependencies. Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

Either run

```
$ php composer.phar require kartik-v/yii2-validators "@dev"
```

or add

```
"kartik-v/yii2-validators": "@dev"
```

to the ```require``` section of your `composer.json` file.

## Usage

### EmailValidator

This class extends the `yii\validators\EmailValidator` class to offer multiple email validation support. The `EmailValidator` class
can be easily used via the alias `k-email` in your model validation rules. For example in your model you can use this as shown below:

```php
use yii\db\ActiveRecord;

class EmailModel extends ActiveRecord {
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['to', 'cc', 'bcc'], 'k-email', 'allowName' => true, 'enableIDN' => true, 'max' => 5],
        ];
    }
}
```

In your view where you render the model inputs with ActiveForm - you may set `enableClientValidation` to control whether you need client side validation.

```php
// views/email/send.php

use yii\widgets\ActiveForm;

$form = ActiveForm::begin(['enableClientValidation' => true]);
echo $form->field($model, 'to')->textInput();
echo $form->field($model, 'cc')->textInput();
echo $form->field($model, 'bcc')->textInput();
// other fields
ActiveForm::end();
```

### PhoneValidator

This class extends the `yii\validators\Validator` class to validate phone numbers using
[libphonenumber-for-php](https://github.com/giggsey/libphonenumber-for-php) based on Google's
[libphonenumber](https://github.com/googlei18n/libphonenumber) library. The `PhoneValidator` class
can be easily used via the alias `k-phone` in your model validation rules. For example in your 
model you can use this as shown below:

```php
use yii\db\ActiveRecord;

class ContactModel extends ActiveRecord {
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['phone1'], 'k-phone', 'countryValue' => 'US'],
            [['phone2'], 'k-phone', 'countryAttribute' => 'country', 'applyFormat' => false],
        ];
    }
}
```

### CardValidator

The `CardValidator` extends the [yii\validators\Validator](https://www.yiiframework.com/doc/api/2.0/yii-validators-validator) component.
It validates standard debit and credit card number inputs using Luhn's checksum validation. It also helps auto detect the card types and
additionally validating the card holder name, expiry date and CVV entered. The `CardValidator` class
can be easily used via the alias `k-card` in your model validation rules. For example in your 
model you can use this as shown below:

```php
use yii\db\ActiveRecord;

class PaymentModel extends ActiveRecord {
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                ['card_number'], 
                'k-card', 
                'typeAttribute' => 'card_type', 
                'holderAttribute' => 'holderName',
                'expiryYearAttribute' => 'expiryYear',
                'expiryMonthAttribute' => 'expiryMonth',
                'cvvAttribute' => 'cvv',
            ],
        ];
    }
}
```

### JsonValidator

The `JsonValidator` extends the [yii\validators\Validator](https://www.yiiframework.com/doc/api/2.0/yii-validators-validator) component.
It validates JSON input in text area fields. The validator also helps to overwrite the JSON input as a pretty format via `prettify` setting.
It includes both server and client validations. The `JsonValidator` class can be easily used via the alias `k-json` in your model validation 
rules. For example in your model you can use this as shown below:

```php
use yii\db\ActiveRecord;

class ProductModel extends ActiveRecord {
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                ['products_list_json'], 
                'k-json', 
                'prettify' => true, // set this to false if you do not wish to prettify the json input
            ],
        ];
    }
}
```

## License

**yii2-validators** is released under the BSD-3-Clause License. See the bundled `LICENSE.md` for details.