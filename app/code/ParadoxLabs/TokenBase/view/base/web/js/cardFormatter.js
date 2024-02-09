/**
 * Copyright © 2015-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
 */

/*jshint jquery:true*/
define([
    "jquery",
    'Magento_Payment/js/model/credit-card-validation/credit-card-number-validator'
], function($, cardNumberValidator) {
    "use strict";

    /**
     * Implements CC formatting solution by NateS
     * https://stackoverflow.com/a/48273550/2336164
     */
    $.widget('mage.tokenbaseCardFormatter', {
        options: {
            ccInputSelector: '[autocomplete="cc-number"]',
            separator: ' ',
            ccTypeSelector: '',
            ccTypeContainer: ''
        },

        _create: function() {
            var ccInput = this.element.find(this.options.ccInputSelector);

            if (ccInput.length > 0) {
                ccInput.on('keydown', this.handleKeydown.bind(this))
                       .on('input paste', this.formatCc.bind(this));
            }

            if (this.options.ccTypeSelector !== '' && this.options.ccTypeContainer !== '') {
                ccInput.on('input change keyup paste', this.detectCcType.bind(this));
            }
        },

        /**
         * Track cursor through the separator for deletes, etc.
         */
        handleKeydown: function(e) {
            var ccInput = this.element.find(this.options.ccInputSelector)[0];
            var cursor = ccInput.selectionStart;

            if (ccInput.selectionEnd !== cursor) {
                return;
            }

            if (e.which === 46 && ccInput.value[cursor] === this.options.separator) {
                ccInput.selectionStart++;
            } else if (e.which === 8 && cursor && ccInput.value[cursor - 1] === this.options.separator) {
                ccInput.selectionEnd--;
            }
        },

        /**
         * Format the CC number element with nice separators.
         */
        formatCc: function() {
            var ccInput = this.element.find(this.options.ccInputSelector)[0];
            var value   = ccInput.value;
            var cursor  = ccInput.selectionStart;

            if (value.substring(0,4) === "XXXX") {
                return;
            }

            var matches = value.substring(0, cursor).match(/[^0-9]/g);
            if (matches) {
                cursor -= matches.length;
            }

            value = value.replace(/[^0-9]/g, "").substring(0, 19);

            // AmEx cards get separated different, because they're special.
            var typeIsAmEx = false;
            if (value.substring(0,2) === "34" || value.substring(0,2) === "37") {
                typeIsAmEx = true;
            }

            var formatted = "";
            for (var i=0, n=value.length; i<n; i++) {
                if (this.shouldSeparate(i, typeIsAmEx)) {
                    if (formatted.length <= cursor) {
                        cursor++;
                    }

                    formatted += this.options.separator;
                }

                formatted += value[i];
            }

            if (formatted === ccInput.value) {
                return;
            }

            ccInput.value = formatted;
            ccInput.selectionEnd = cursor;
        },

        /**
         * Determine whether a card number should be separated at the given index.
         *
         * @param int i
         * @param bool isAmEx
         * @returns bool
         */
        shouldSeparate: function(i, isAmEx) {
            // Separate AmEx cards at 4 and 10 rather than quadruplets.
            if (isAmEx === true) {
                return i === 4 || i === 10;
            }

            return i && i % 4 === 0;
        },

        /**
         * Detect CC type as number is entered, and update active type.
         */
        detectCcType: function() {
            this.updateCcType(null);

            var cc_no = this.element.find(this.options.ccInputSelector).val().replace(/\D/g,'');
            var result = cardNumberValidator(cc_no);

            if ((result.isPotentiallyValid || result.isValid) && result.card !== null) {
                this.updateCcType(result.card.type);
            }
        },

        /**
         * Update active CC type to the selected value.
         *
         * @param string creditCardType
         */
        updateCcType: function (creditCardType) {
            this.element.find(this.options.ccTypeSelector).val(creditCardType).trigger('change');

            var typeContainer = $(this.element).find(this.options.ccTypeContainer);
            typeContainer.find('._active').removeClass('_active');
            typeContainer.find('[data-type=' + creditCardType + ']').addClass('_active');
        }
    });

    return $.mage.tokenbaseCardFormatter;
});
