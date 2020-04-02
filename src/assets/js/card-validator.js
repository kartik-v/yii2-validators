/*!
 * @package   yii2-validators
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   1.0.2
 *
 * Yii2 card client validation library for `kartik-v/yii2-validators`
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var CardValidator;
(function ($) {
    "use strict";
    CardValidator = {
        cards: {},
        validate: function (value, messages, options) {

            const lib = yii.validation;
            this.cards = options.cards;
            value = value.split(' ').join('');

            let cardType = this.getCardType(value);
            if (!cardType) {
                lib.addMessage(messages, this.prepareMessage(options.invalidTypeMessage, value));
                return;
            }
            if (this.cards[cardType].luhn && !(this.validateLuhn(value))) {
                lib.addMessage(messages, this.prepareMessage(options.invalidMessage, value));
            }
        },
        validateLuhn: function (value) {
            value = value.split("").reverse().join("");
            let checkSum = 0;
            for (let i = 0; i < value.length; i++) {
                let currentNum = parseInt(value.charAt(i));
                if (i % 2 === 1) {
                    currentNum *= 2;
                }
                if (currentNum > 9) {
                    let firstNum = currentNum % 10;
                    let secondNum = (currentNum - firstNum) / 10;
                    currentNum = firstNum + secondNum;
                }
                checkSum += currentNum;
            }
            return (checkSum % 10) === 0;
        },

        getCardType: function (value) {
            for (let cardType in this.cards) {
                let exp = eval(unescape(this.cards[cardType].pattern));
                if (exp.test(value)) {
                    return cardType;
                }
            }
            return false;
        },
        prepareMessage: function (str, replace) {
            return str.replace('{number}', replace);
        }
    };
})(window.jQuery);