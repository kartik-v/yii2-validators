<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */

namespace kartik\validators;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Json;
use yii\validators\Validator;
use kartik\base\TranslationTrait;

/**
 * JsonValidator will help parse a valid JSON input format and also gives an option to prettify the json input
 */
class JsonValidator extends Validator
{
    use TranslationTrait;
    use ValidatorTrait;

    /**
     * @var bool whether to prettify the json output (only via server mode)
     */
    public $prettify = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_msgCat = 'kvvalidator';
        parent::init();
        $this->initI18N();
        $this->setMsg('message', Yii::t('kvvalidator', 'Invalid JSON input for "{attribute}". {exception}'));
    }

    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if ($value === '' || $value === null) {
            return;
        }
        $out = $this->validateJson($value);
        if (isset($out['data'])) {
            if ($this->prettify) {
                $model->$attribute = Json::encode($out['data'], JSON_PRETTY_PRINT);
            }
        } else {
            $this->addError($model, $attribute, $this->message, ['exception' => $out['message']]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $out = $this->validateJson($value);
        if (!isset($out['data'])) {
            return [$this->message, ['exception' => $out['message']]];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        JsonValidatorAsset::register($view);
        $options = $this->getClientOptions($model, $attribute);
        $opts = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return "JsonValidator.validate(value, messages, {$opts});";
    }

    /**
     * @inheritdoc
     */
    public function getClientOptions($model, $attribute)
    {
        /**
         * @var Model $model
         */
        $label = $model->getAttributeLabel($attribute);
        $options = [
            'message' => $this->formatMessage($this->message, [
                'attribute' => $label,
            ]),
        ];
        return $options;
    }

    /**
     * Validate JSON Input
     * @param string $value
     * @return array output data and message
     */
    protected function validateJson($value)
    {
        try {
            return ['data' => Json::decode($value), 'message' => 'success'];
        } catch (InvalidArgumentException $e) {
            return ['data' => null, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['data' => null, 'message' => $e->getMessage()];
        }
    }
}
