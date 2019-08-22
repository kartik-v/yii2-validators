<?php
/**
 * @package   yii2-validators
 * @author    Vuong Minh <vuongxuongminh@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */

namespace kartik\validators;

use yii\base\BootstrapInterface;
use yii\validators\Validator;

/**
 * Bootstrap for additional validators to `yii\validators\Validator::$builtInValidators`
 * for easier use and friendly syntax.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        Validator::$builtInValidators = array_merge(Validator::$builtInValidators, [
            'k-email' => 'kartik\validators\EmailValidator',
            'k-phone' => 'kartik\validators\PhoneValidator',
            'k-card' => 'kartik\validators\CardValidator',
            'k-json' => 'kartik\validators\JsonValidator',
        ]);
    }
}
