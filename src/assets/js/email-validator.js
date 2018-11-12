/*!
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   1.0.2
 *
 * Yii2 email client validation library for `kartik-v/yii2-validators`
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2018, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var EmailValidator;
(function ($) {
    "use strict";
    EmailValidator = {
        validate: function (value, messages, options) {
            var delimiter = options.delimiter || ',', emails = value.split(delimiter),
                count = emails.length, i, pub = yii.validation.pub, email; // jshint ignore:line
            if (count === 0) {
                return;
            }
            if (options.max && count > options.max) {
                pub.addMessage(messages, options.maxThresholdMessage, options.max);
            }
            if (options.min && count < options.min) {
                pub.addMessage(messages, options.minThresholdMessage, options.min);
            }
            for (i = 0; i < count; i++) {
                email = $.trim(emails[i]);
                yii.validation.email(email, messages, options); // jshint ignore:line
            }
        }
    };
})(window.jQuery);