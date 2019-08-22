<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */
namespace kartik\validators;

use kartik\base\AssetBundle;
/**
 * Asset bundle for JsonValidator
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class JsonValidatorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\validators\ValidationAsset'
    ];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/json-validator']);
        parent::init();
    }
}
