<?php
/**
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.3
 */

namespace kartik\validators;

use yii\base\Model;

/**
 * Trait ValidatorTrait
 *
 * @package kartik\validators
 *
 * @author  Kartik Visweswaran <kartikv2@gmail.com>
 * @since   1.0
 */
trait ValidatorTrait
{
    /**
     * Sets message property
     * @param string $type
     * @param string $msg
     */
    protected function setMsg($type, $msg)
    {
        if (!isset($this->$type)) {
            $this->$type = $msg;
        }
    }
}