<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */

namespace kartik\validators;

use Yii;
use yii\base\InvalidValueException;
use yii\validators\Validator;
use yii\base\Model;
use kartik\base\TranslationTrait;

/**
 * CardValidator validates standard debit and credit card number inputs using Luhn's checksum validation. It also
 * additionally validates the card holder name, expiry date and CVV entered.
 */
class CardValidator extends Validator
{
    use ValidatorTrait;
    use TranslationTrait;

    /**
     * Valid Card list
     * @var string
     */
    const ELECTRON = 'Visa Electron';
    const MAESTRO = 'Maestro';
    const FBF = 'Forbrugsforeningen';
    const DANKORT = 'Dankort';
    const VISA = 'Visa';
    const MASTERCARD = 'Mastercard';
    const AMEX = 'American Express';
    const CARTE_BLANCHE = 'Carte Blanche';
    const DINERS = 'Diners Club';
    const BC_GLOBAL = 'BC Global';
    const DISCOVER = 'Discover';
    const INSTA_PAY = 'Insta Payment';
    const JCB = 'JCB';
    const VOYAGER = 'Voyager';
    const KOREAN_LOCAL = 'Korean Local';
    const SOLO = 'Solo';
    const SWITCH_CARD = 'Switch Card';
    const LASER = 'Laser';
    const UNIONPAY = 'Union Pay';

    /**
     * @var array holds the configurations for each of the credit / debit cards including the card lengths and regex
     * patterns to check for valid card number prefixes
     */
    public $cards = [
        // Debit cards must come first, since they have more specific patterns than their credit-card equivalents.
        self::ELECTRON => [
            'pattern' => '/^(?:417500|4026\\d{2}|4917\\d{2}|4913\\d{2}|4508\\d{2}|4844\\d{2})\\d{10}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::MAESTRO => [
            'pattern' => '/^(5018|5020|5038|6304|6759|6761|6763)[0-9]{8,15}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::FBF => [
            'pattern' => '/^600[0-9]{13}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::DANKORT => [
            'pattern' => '/^5019[0-9]{12}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        // Credit cards
        self::VISA => [
            'pattern' => '/^4[0-9]{12}([0-9]{3})?$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::MASTERCARD => [
            'pattern' => '/^(5[0-5]|2[2-7])[0-9]{14}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::AMEX => [
            'pattern' => '/^3[47][0-9]{13}$/',
            'cvvLength' => [3, 4],
            'luhn' => true,
        ],
        self::CARTE_BLANCHE => [
            'pattern' => '/^389[0-9]{11}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::DINERS => [
            'pattern' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::BC_GLOBAL => [
            'pattern' => '/^(6541|6556)[0-9]{12}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::DISCOVER => [
            'pattern' => '/^65[4-9][0-9]{13}|64[4-9][0-9]{13}|6011[0-9]{12}|(622(?:12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|9[01][0-9]|92[0-5])[0-9]{10})$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::INSTA_PAY => [
            'pattern' => '/^63[7-9][0-9]{13}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::JCB => [
            'pattern' => '/^(3[0-9]{4}|2131|1800)[0-9]{11}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::VOYAGER => [
            'pattern' => '/^8699[0-9]{11}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::KOREAN_LOCAL => [
            'pattern' => '/^9[0-9]{15}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::SOLO => [
            'pattern' => '/^(6334[5-9][0-9]|6767[0-9]{2})\\d{10}(\\d{2,3})?$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::SWITCH_CARD => [
            'pattern' => '/^(4903|4905|4911|4936|6333|6759)[0-9]{12}|(4903|4905|4911|4936|6333|6759)[0-9]{14}|(4903|4905|4911|4936|6333|6759)[0-9]{15}|564182[0-9]{10}|564182[0-9]{12}|564182[0-9]{13}|633110[0-9]{10}|633110[0-9]{12}|633110[0-9]{13}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::LASER => [
            'pattern' => '/^(6304|6706|6709|6771)[0-9]{12,15}$/',
            'cvvLength' => [3],
            'luhn' => true,
        ],
        self::UNIONPAY => [
            'pattern' => '/^(62|88)[0-9]{14,17}$/',
            'cvvLength' => [3],
            'luhn' => false,
        ],
    ];

    /**
     * @var array list of allowed cards. If not set or empty, all cards set in [[cards]] will be allowed. For example,
     * to allow only VISA and MASTERCARD, set this to `[CardValidator::VISA, CardValidator::MASTERCARD]`.
     */
    public $allowedCards = [];

    /**
     * @var bool whether to auto update the card number as pure numeric digits and overwrite the model attribute
     */
    public $autoUpdateNumber = true;

    /**
     * @var bool whether to auto detect card type based on pattern detection in [[cards]] configuration and
     * auto update the [[typeValue]].
     */
    public $autoDetectType = true;

    /**
     * @var bool whether to auto update and store the auto detected type within the [[typeAttribute]]. Applicable only
     * when [[autoDetectType]] is set to `true`.
     */
    public $autoUpdateType = true;

    /**
     * @var bool whether to validate the card holder name
     */
    public $validateHolder = true;

    /**
     * @var bool whether to validate the card expiry date (year / month)
     */
    public $validateExpiry = true;

    /**
     * @var bool whether to validate the card CVV
     */
    public $validateCVV = true;

    /**
     * @var string the card type attribute
     */
    public $typeAttribute;

    /**
     * @var string the card holder attribute
     */
    public $holderAttribute;

    /**
     * @var int the card expiry year attribute
     */
    public $expiryYearAttribute;

    /**
     * @var int the card expiry month attribute
     */
    public $expiryMonthAttribute;

    /**
     * @var int the card CVV attribute
     */
    public $cvvAttribute;

    /**
     * @var string card type to check (overrides value set in [[typeAttribute]])
     */
    public $typeValue;

    /**
     * @var string card holder name to check (overrides value set in [[holderAttribute]])
     */
    public $holderValue;

    /**
     * @var int card expiry year to check (overrides value set in [[expiryYearAttribute]])
     */
    public $expiryYearValue;

    /**
     * @var int card expiry month to check (overrides value set in [[expiryMonthAttribute]])
     */
    public $expiryMonthValue;

    /**
     * @var int card cvv to check (overrides value set in [[cvvAttribute]])
     */
    public $cvvValue;

    /**
     * @var string the regular expression pattern to match for card holder name
     */
    public $holderPattern = '/^[a-z ,.\'-]+$/i';

    /**
     * @var string the message shown if the detected card type is invalid
     */
    public $invalidTypeMessage;

    /**
     * @var string the message shown if the detected card holder is invalid
     */
    public $invalidHolderMessage;

    /**
     * @var string the message shown if the detected card expiry year or month is invalid
     */
    public $invalidExpiryMessage;

    /**
     * @var string the message shown if the detected card CVV is invalid
     */
    public $invalidCVVMessage;

    /**
     * @var array the final list of cards based on allowedCards
     */
    protected $_cards = [];

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
     * Initialize settings
     * @throws \ReflectionException
     */
    protected function initSettings()
    {
        $this->initI18N();
        $this->setMsg('message', Yii::t('kvvalidator', '"{number}" is not a valid card number'));
        $this->setMsg('invalidTypeMessage', Yii::t('kvvalidator', 'Unsupported card number "{number}"'));
        $this->setMsg('invalidHolderMessage', Yii::t('kvvalidator', 'Invalid holder name "{holder}"'));
        $this->setMsg('invalidExpiryMessage', Yii::t('kvvalidator', 'Invalid expiry month/year "{expiry}"'));
        $this->setMsg('invalidCVVMessage', Yii::t('kvvalidator', 'Invalid CVV "{cvv}"'));
        if (!empty($this->allowedCards) && is_array($this->cards)) {
            foreach ($this->allowedCards as $card) {
                if (isset($this->cards[$card])) {
                    $this->_cards[$card] = $this->cards[$card];
                }
            }
        } else {
            $this->_cards = $this->cards;
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (!isset($model->$attribute)) {
            return;
        }
        $this->initValues($model, $attribute);
        $result = $this->validateValue($model->$attribute);
        if (!empty($result)) {
            if (isset($result[2])) {
                $attributes = (array)$result[2];
                foreach ($attributes as $attrib) {
                    $attr = $attrib . 'Attribute';
                    $this->addError($model, $this->$attr, $result[0], $result[1]);
                }
            } else {
                $this->addError($model, $attribute, $result[0], $result[1]);
            }
        }
    }

    /**
     * Gets expiry date
     * @return string
     */
    protected function getExpiryDate()
    {
        return $this->expiryMonthValue . '/' . $this->expiryYearValue;
    }

    /**
     * Validates a value.
     * A validator class can implement this method to support data validation out of the context of a data model.
     * @param mixed $value the data value to be validated.
     * @return array|null the error message and the array of parameters to be inserted into the error message.
     */
    protected function validateValue($value)
    {
        $number = preg_replace('/[^0-9]/', '', $value); // strip non numeric characters
        if (empty($this->typeValue) || empty($this->_cards) || empty($this->_cards[$this->typeValue])) {
            return [$this->invalidTypeMessage, ['number' => $number]];
        }
        if (!$this->validCard($number)) {
            return [$this->message, ['number' => $number]];
        }
        if ($this->validateHolder && !$this->isValidHolder()) {
            return [$this->invalidHolderMessage, ['holder' => $this->holderValue, 'holder']];
        }
        if ($this->validateExpiry && !$this->isValidExpiry()) {
            return [$this->invalidExpiryMessage, ['expiry' => $this->getExpiryDate()], ['expiryMonth', 'expiryYear']];
        }
        if ($this->validateCVV && !$this->isValidCVV()) {
            return [$this->invalidCVVMessage, ['cvv' => $this->cvvValue], 'cvv'];
        }
        return null;
    }

    /**
     * Gets value based on attribute property or the value property
     * @param Model $model the model instance
     * @param string $prop the attribute property
     * @param string $default the default value
     * @return mixed|null|string
     */
    protected function parseValue($model, $prop, $default = null)
    {
        $value = "{$prop}Value";
        if (isset($this->$value)) {
            return $this->$value;
        }
        $attribute = "{$prop}Attribute";
        if (isset($this->$attribute) && isset($model->{$this->$attribute})) {
            return $model->{$this->$attribute};
        }
        return $default;
    }

    /**
     * Gets card type
     * @param string $number
     * @return string
     */
    protected function getCardType($number)
    {
        foreach ($this->_cards as $type => $card) {
            if (isset($card['pattern']) && preg_match($card['pattern'], $number)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Gets the property of a card
     * @param string $prop the card property
     * @return mixed
     * @throws InvalidValueException
     */
    protected function getCardProp($prop)
    {
        if (isset($this->typeValue) && isset($this->_cards[$this->typeValue]) && isset($this->_cards[$this->typeValue][$prop])) {
            return $this->_cards[$this->typeValue][$prop];
        }
        throw new InvalidValueException('No card type detected. One of "typeValue" or "typeAttribute" must be set with a valid value.');
    }

    /**
     * Initializes related attribute values
     * @param Model $model the model instance
     * @param string $attribute the attribute
     */
    protected function initValues($model, $attribute)
    {
        $this->expiryMonthValue = $this->parseValue($model, 'expiryMonth', '');
        $this->expiryYearValue = $this->parseValue($model, 'expiryYear', '');
        $this->holderValue = $this->parseValue($model, 'holder');
        $this->cvvValue = $this->parseValue($model, 'cvv');
        $number = preg_replace('/[^0-9]/', '', $model->$attribute); // strip non numeric characters
        if ($this->autoDetectType && !isset($this->typeValue)) {
            $this->typeValue = $this->getCardType($number);
            if ($this->autoUpdateType && isset($this->typeAttribute)) {
                $model->{$this->typeAttribute} = $this->typeValue;
            }
        }
        if ($this->autoUpdateNumber) {
            $model->$attribute = $number;
        }
    }

    /**
     * Check if the card holder name is valid
     *
     * @return bool validation result
     */
    public function isValidHolder()
    {
        return !empty($this->holderValue) && preg_match($this->holderPattern, $this->holderValue);
    }

    /**
     * Validates if the card expiry is correct
     *
     * @return bool the validation result
     */
    public function isValidExpiry()
    {
        $month = $this->expiryMonthValue;
        $year = $this->expiryYearValue;
        $monthNow = intval(date('n'));
        $yearNow = intval(date('Y'));
        if (is_scalar($month)) {
            $month = intval($month);
        }
        if (is_scalar($year)) {
            $year = intval($year);
        }
        return is_integer($month) && $month >= 1 && $month <= 12 && is_integer($year) &&
            ($year > $yearNow || ($year === $yearNow && $month > $monthNow)) && $year < ($yearNow + 10);
    }

    /**
     * Check if the card CVV is valid
     * @return bool
     */
    public function isValidCVV()
    {
        return ctype_digit($this->cvvValue) && $this->validCVVLength();
    }

    /**
     * Luhn checksum check
     * @param string|int $number the card number
     * @return bool
     */
    public static function luhnCheck($number)
    {
        $cardNumber = strrev($number);
        $checkSum = 0;
        for ($i = 0; $i < strlen($cardNumber); $i++) {
            $currentNum = substr($cardNumber, $i, 1);
            if ($i % 2 == 1) {
                $currentNum *= 2;
            }
            if ($currentNum > 9) {
                $firstNum = $currentNum % 10;
                $secondNum = ($currentNum - $firstNum) / 10;
                $currentNum = $firstNum + $secondNum;
            }
            $checkSum += $currentNum;
        }
        return $checkSum % 10 == 0;
    }

    /**
     * Check if the card luhn checksum is valid
     * @param string|int $number the card number
     * @return bool
     */
    protected function validLuhn($number)
    {
        $luhn = $this->getCardProp('luhn');
        if (empty($luhn)) {
            return true;
        }
        return static::luhnCheck($number);
    }

    /**
     * Check if the card number is valid
     * @param string|int $number the card number
     * @return bool
     */
    protected function validCard($number)
    {
        $pattern = $this->getCardProp('pattern');
        return preg_match($pattern, $number) && $this->validLuhn($number);
    }

    /**
     * Check if the card CVV length is valid
     * @return bool
     */
    protected function validCVVLength()
    {
        if (empty($this->cvvValue)) {
            return false;
        }
        $cvvLength = $this->getCardProp('cvvLength');
        foreach ($cvvLength as $length) {
            if (strlen($this->cvvValue) === $length) {
                return true;
            }
        }
        return false;
    }
}
