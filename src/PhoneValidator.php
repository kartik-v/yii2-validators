<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */

namespace kartik\validators;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Yii;
use yii\validators\Validator;
use kartik\base\TranslationTrait;

/**
 * PhoneValidator validates phone numbers for given country and formats. The country codes and attributes value should
 * be ISO 3166-1 alpha-2 codes.
 */
class PhoneValidator extends Validator
{
    use TranslationTrait;
    use ValidatorTrait;

    /**
     * @var string the model attribute name that stores the country code value.
     */
    public $countryAttribute;

    /**
     * @var string the value for country code. This will override any value set in [[countryAttribute]]. This is the
     * [ISO 3166-1](http://www.iso.org/iso/country_names_and_code_elements) two letter country code. If the number is
     * passed in an international format (e.g. +44 117 496 0123), then the region code is not needed, and can be null.
     * Failing that, the library will use this value to work out the phone number based on rules loaded for that region.
     */
    public $countryValue;

    /**
     * @var boolean whether country code is mandatory for parsing the phone number
     */
    public $countryRequired = true;

    /**
     * @var boolean whether to apply the parsed phone format and update the attribute
     */
    public $applyFormat = true;

    /**
     * @var string the format to use when applying the format
     * @see [[PhoneNumberFormat]]
     */
    public $format = PhoneNumberFormat::INTERNATIONAL;

    /**
     * @var  string the message to show if an invalid phone number is parsed.
     */
    public $message;

    /**
     * @var string the message to show if country value is empty and [[countryRequired]] is `true`.
     */
    public $countryRequiredMessage;

    /**
     * @var string the message to show when a [[NumberParseException]] is thrown during phone number parsing.
     */
    public $parseExceptionMessage;

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function init()
    {
        $this->_msgCat = 'kvvalidator';
        parent::init();
        $this->initSettings();
    }

    /**
     * Initializes the validator settings
     * @throws \ReflectionException
     */
    protected function initSettings()
    {
        $this->initI18N();
        $this->setMsg('message', Yii::t('kvvalidator', '"{value}" does not seem to be a valid phone number.'));
        $this->setMsg('countryRequiredMessage', Yii::t('kvvalidator', 'Country is required for phone validation.'));
        $this->setMsg('parseExceptionMessage',
            Yii::t('kvvalidator', 'Unexpected or unrecognized phone number format.'));
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $country = '';
        if (isset($this->countryValue)) {
            $country = $this->countryValue;
        } elseif (isset($this->countryAttribute)) {
            $country = $model->{$this->countryAttribute};
        }
        if (empty($country) && $this->countryRequired) {
            $this->addError($model, $attribute, $this->countryRequiredMessage, ['{value}' => $model->$attribute]);
            return;
        }
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($model->$attribute, $country);
            if (!$phoneUtil->isValidNumber($numberProto)) {
                $this->addError($model, $attribute, $this->message, ['{value}' => $model->$attribute]);
            } elseif ($this->applyFormat) {
                $model->$attribute = $phoneUtil->format($numberProto, $this->format);
            }
        } catch (NumberParseException $e) {
            $this->addError($model, $attribute, $this->parseExceptionMessage, ['{value}' => $model->$attribute]);
        }
    }
}
