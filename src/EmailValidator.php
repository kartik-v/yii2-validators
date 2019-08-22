<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */
namespace kartik\validators;

use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\validators\EmailValidator as YiiEmailValidator;
use yii\validators\PunycodeAsset;
use kartik\base\TranslationTrait;

/**
 * EmailValidator validates that the attribute value contains a list of valid emails separated by a delimiter.
 */
class EmailValidator extends YiiEmailValidator
{
    use TranslationTrait;
    use ValidatorTrait;
    
    /**
     * @var boolean whether multiple email addresses are supported (separated by the [[delimiter]])
     */
    public $multiple = true;
    /**
     * @var string the delimiter to separate multiple emails (applicable only when [[multiple]] is `true`)
     */
    public $delimiter = ',';
    /**
     * @var int minimum number of emails required (applicable only when [[multiple]] is `true`). Setting this to `0` or
     * `NULL` will mean no minimum limit.
     */
    public $min = 0;
    /**
     * @var int maximum number of emails required (applicable only when [[multiple]] is `true`). Setting this to `0` or
     * `NULL` will mean no maximum limit.
     */
    public $max = 0;
    /**
     * @var string the message when the number of emails are below the [[min]] threshold
     */
    public $minThresholdMessage;
    /**
     * @var string the message when the number of emails are above the [[max]] threshold
     */
    public $maxThresholdMessage;
    /**
     * @inheritdoc
     */
    public $message;

    /**
     * @inheritdoc
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $isMsgSet = isset($this->message);
        $this->_msgCat = 'kvvalidator';
        parent::init();
        $this->initSettings($isMsgSet);
    }

    /**
     * Initializes settings
     * @param boolean $isMsgSet whether [[message]] property is set
     * @throws \ReflectionException
     */
    protected function initSettings($isMsgSet = false)
    {
        if (!$this->multiple) {
            return;
        }
        $this->initI18N();
        $this->setMsg(
            'minThresholdMessage',
            Yii::t('kvvalidator', 'At least {value, number} {value, plural, one{email is} other{emails are}} required.')
        );
        $this->setMsg(
            'maxThresholdMessage',
            Yii::t('kvvalidator', 'A maximum of {value, number} {value, plural, one{email is} other{emails are}} allowed.')
        );
        if (!$isMsgSet) {
            $this->message = Yii::t('kvvalidator', '"{value}" is not a valid email address.');
        }
    }

    /**
     * @inheritdoc
     * @throws \yii\base\NotSupportedException
     */
    public function validateAttribute($model, $attribute)
    {
        if (!$this->multiple) {
            parent::validateAttribute($model, $attribute);
            return;
        }
        $emails = empty($model->$attribute) ? [] : explode($this->delimiter, $model->$attribute);
        if (empty($emails)) {
            return;
        }
        $count = count($emails);
        if (!empty($this->min) && $count < $this->min) {
            $this->addError($model, $attribute, $this->minThresholdMessage, ['{value}' => $this->min]);
            return;
        }
        if (!empty($this->max) && $count > $this->max) {
            $this->addError($model, $attribute, $this->maxThresholdMessage, ['{value}' => $this->max]);
            return;
        }
        foreach ($emails as $email) {
            $email = trim($email);
            if (!empty($email) && !empty($this->validateValue($email))) {
                $this->addError($model, $attribute, $this->message, ['{value}' => $email]);
                return;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        if (!$this->multiple) {
            return parent::clientValidateAttribute($model, $attribute, $view);
        }
        /**
         * @var Model $model
         */
        EmailValidatorAsset::register($view);
        if ($this->enableIDN) {
            PunycodeAsset::register($view);
        }
        $options = array_replace_recursive($this->getClientOptions($model, $attribute), [
            'delimiter' => $this->delimiter,
            'min' => $this->min,
            'max' => $this->max,
            'message' => $this->message,
            'minThresholdMessage' => $this->minThresholdMessage,
            'maxThresholdMessage' => $this->maxThresholdMessage,
        ]);
        return 'EmailValidator.validate(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}
