/*!
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.2
 *
 * Yii2 json client validation library for `kartik-v/yii2-validators`
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var JsonValidator;
(function ($) {
    'use strict';
    JsonValidator = {
        validate: function (value, messages, options) {
            var obj, exception = '', msg = options.message, lib = yii.validation; // jshint ignore:line
            if (value === '' || value === null) {
                return;
            }
            try {
                obj = JSON.parse(value);
            } catch (err) {
                exception = err.message;
            }
            if (!obj || exception) {
                lib.addMessage(messages, msg.replace('{exception}', exception), value);
            }
        }
    };
})(window.jQuery);