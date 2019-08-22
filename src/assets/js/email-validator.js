/*!
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.2
 *
 * Yii2 email client validation library for `kartik-v/yii2-validators`
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var EmailValidator;
(function ($) {
    "use strict";
    EmailValidator = {
        validate: function (value, messages, options) {
            var delimiter = options.delimiter || ',', emails = value.split(delimiter),
                count = emails.length, i, lib = yii.validation, email; // jshint ignore:line
            if (count === 0) {
                return;
            }
            if (options.max && count > options.max) {
                lib.addMessage(messages, options.maxThresholdMessage, options.max);
            }
            if (options.min && count < options.min) {
                lib.addMessage(messages, options.minThresholdMessage, options.min);
            }
            for (i = 0; i < count; i++) {
                email = $.trim(emails[i]);
                lib.email(email, messages, options); // jshint ignore:line
            }
        }
    };
})(window.jQuery);