/**
 * Yii form widget.
 *
 * This is the JavaScript widget used by the yii\widgets\ActiveForm widget.
 *
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
// eslint-disable-next-line max-statements
(function ($) {

    $.fn.yiiActiveForm = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            if (typeof method === 'object' || !method) {
                return methods.init.apply(this, arguments);
            } else {
                $.error('Method ' + method + ' does not exist on jQuery.yiiActiveForm');
                return false;
            }
        }
    };

    var events = {
        /**
         * beforeValidate event is triggered before validating the whole form.
         * The signature of the event handler should be:
         *     function (event, messages, deferreds)
         * where
         *  - event: an Event object.
         *  - messages: an associative array with keys being attribute IDs and values being error message arrays
         *    for the corresponding attributes.
         *  - deferreds: an array of Deferred objects. You can use deferreds.add(callback) to add a new deferred validation.
         *
         * If the handler returns a boolean false, it will stop further form validation after this event. And as
         * a result, afterValidate event will not be triggered.
         */
        beforeValidate: 'beforeValidate',
        /**
         * afterValidate event is triggered after validating the whole form.
         * The signature of the event handler should be:
         *     function (event, messages, errorAttributes)
         * where
         *  - event: an Event object.
         *  - messages: an associative array with keys being attribute IDs and values being error message arrays
         *    for the corresponding attributes.
         *  - errorAttributes: an array of attributes that have validation errors. Please refer to attributeDefaults for the structure of this parameter.
         */
        afterValidate: 'afterValidate',
        /**
         * beforeValidateAttribute event is triggered before validating an attribute.
         * The signature of the event handler should be:
         *     function (event, attribute, messages, deferreds)
         * where
         *  - event: an Event object.
         *  - attribute: the attribute to be validated. Please refer to attributeDefaults for the structure of this parameter.
         *  - messages: an array to which you can add validation error messages for the specified attribute.
         *  - deferreds: an array of Deferred objects. You can use deferreds.add(callback) to add a new deferred validation.
         *
         * If the handler returns a boolean false, it will stop further validation of the specified attribute.
         * And as a result, afterValidateAttribute event will not be triggered.
         */
        beforeValidateAttribute: 'beforeValidateAttribute',
        /**
         * afterValidateAttribute event is triggered after validating the whole form and each attribute.
         * The signature of the event handler should be:
         *     function (event, attribute, messages)
         * where
         *  - event: an Event object.
         *  - attribute: the attribute being validated. Please refer to attributeDefaults for the structure of this parameter.
         *  - messages: an array to which you can add additional validation error messages for the specified attribute.
         */
        afterValidateAttribute: 'afterValidateAttribute',
        /**
         * beforeSubmit event is triggered before submitting the form after all validations have passed.
         * The signature of the event handler should be:
         *     function (event)
         * where event is an Event object.
         *
         * If the handler returns a boolean false, it will stop form submission.
         */
        beforeSubmit: 'beforeSubmit',
        /**
         * ajaxBeforeSend event is triggered before sending an AJAX request for AJAX-based validation.
         * The signature of the event handler should be:
         *     function (event, jqXHR, settings)
         * where
         *  - event: an Event object.
         *  - jqXHR: a jqXHR object
         *  - settings: the settings for the AJAX request
         */
        ajaxBeforeSend: 'ajaxBeforeSend',
        /**
         * ajaxComplete event is triggered after completing an AJAX request for AJAX-based validation.
         * The signature of the event handler should be:
         *     function (event, jqXHR, textStatus)
         * where
         *  - event: an Event object.
         *  - jqXHR: a jqXHR object
         *  - textStatus: the status of the request ("success", "notmodified", "error", "timeout", "abort", or "parsererror").
         */
        ajaxComplete: 'ajaxComplete',
        /**
         * afterInit event is triggered after yii activeForm init.
         * The signature of the event handler should be:
         *     function (event)
         * where
         *  - event: an Event object.
         */
        afterInit: 'afterInit'
    };

    // NOTE: If you change any of these defaults, make sure you update yii\widgets\ActiveForm::getClientOptions() as well
    var defaults = {
        // whether to encode the error summary
        encodeErrorSummary: true,
        // the jQuery selector for the error summary
        errorSummary: '.error-summary',
        // whether to perform validation before submitting the form.
        validateOnSubmit: true,
        // the container CSS class representing the corresponding attribute has validation error
        errorCssClass: 'has-error',
        // the container CSS class representing the corresponding attribute passes validation
        successCssClass: 'has-success',
        // the container CSS class representing the corresponding attribute is being validated
        validatingCssClass: 'validating',
        // the GET parameter name indicating an AJAX-based validation
        ajaxParam: 'ajax',
        // the type of data that you're expecting back from the server
        ajaxDataType: 'json',
        // the URL for performing AJAX-based validation. If not set, it will use the the form's action
        validationUrl: undefined,
        // whether to scroll to first visible error after validation.
        scrollToError: true,
        // offset in pixels that should be added when scrolling to the first error.
        scrollToErrorOffset: 0,
        // where to add validation class: container or input
        validationStateOn: 'container'
    };

    // NOTE: If you change any of these defaults, make sure you update yii\widgets\ActiveField::getClientOptions() as well
    var attributeDefaults = {
        // a unique ID identifying an attribute (e.g. "loginform-username") in a form
        id: undefined,
        // attribute name or expression (e.g. "[0]content" for tabular input)
        name: undefined,
        // the jQuery selector of the container of the input field
        container: undefined,
        // the jQuery selector of the input field under the context of the form
        input: undefined,
        // the jQuery selector of the error tag under the context of the container
        error: '.help-block',
        // whether to encode the error
        encodeError: true,
        // whether to perform validation when a change is detected on the input
        validateOnChange: true,
        // whether to perform validation when the input loses focus
        validateOnBlur: true,
        // whether to perform validation when the user is typing.
        validateOnType: false,
        // number of milliseconds that the validation should be delayed when a user is typing in the input field.
        validationDelay: 500,
        // whether to enable AJAX-based validation.
        enableAjaxValidation: false,
        // whether at least one client validator has a whenClient condition.
        hasWhenClient: false,
        // function (attribute, value, messages, deferred, $form), the client-side validation function.
        validate: undefined,
        // status of the input field, 0: empty, not entered before, 1: validated, 2: pending validation, 3: validating
        status: 0,
        // whether the validation is cancelled by beforeValidateAttribute event handler
        cancelled: false,
        // the value of the input
        value: undefined,
        // whether to update aria-invalid attribute after validation
        updateAriaInvalid: true
    };


    var submitDefer;

    var setSubmitFinalizeDefer = function ($form) {
        submitDefer = $.Deferred();
        $form.data('yiiSubmitFinalizePromise', submitDefer.promise());
    };

    // finalize yii.js $form.submit
    var submitFinalize = function ($form) {
        if (submitDefer) {
            submitDefer.resolve();
            submitDefer = undefined;
            $form.removeData('yiiSubmitFinalizePromise');
        }
    };

    var validateClientAttribute = function ($form, data, attribute, messages, deferreds) {
        attribute.$form = $form;
        attribute.cancelled = false;

        var $input = findInput($form, attribute);
        if (shouldSkipClientValidation($input) || !shouldValidateAttribute(data, attribute)) {
            return false;
        }

        var msg = getValidationMessages(messages, attribute.id);
        return runClientValidation($form, attribute, msg, deferreds);
    };

    var shouldSkipClientValidation = function ($input) {
        return isInputDisabled($input) || hasInvalidSelectMarkup($input);
    };

    var isInputDisabled = function ($input) {
        return $input.toArray().every(function (input) {
            return $(input).is(':disabled');
        });
    };

    var hasInvalidSelectMarkup = function ($input) {
        if (!isSingleSelectInput($input) || !isRequiredSingleSelect($input)) {
            return false;
        }

        return isSelectMissingEmptyOption($input[0].options);
    };

    var isSingleSelectInput = function ($input) {
        return $input.length && $input[0].tagName.toLowerCase() === 'select';
    };

    var isRequiredSingleSelect = function ($input) {
        var isRequired = $input.attr('required');
        var isMultiple = $input.attr('multiple');
        var size = parseInt($input.attr('size') || 1, 10);

        return isRequired && !isMultiple && size === 1;
    };

    var isSelectMissingEmptyOption = function (options) {
        if (!options || !options.length) {
            return true;
        }

        return options[0] && (options[0].value !== '' && options[0].text !== '');
    };

    var shouldValidateAttribute = function (data, attribute) {
        return data.submitting || attribute.status === 2 || attribute.status === 3;
    };

    var getValidationMessages = function (messages, attributeId) {
        if (messages[attributeId] === undefined) {
            messages[attributeId] = [];
        }

        return messages[attributeId];
    };

    var runClientValidation = function ($form, attribute, msg, deferreds) {
        var event = $.Event(events.beforeValidateAttribute);
        $form.trigger(event, [attribute, msg, deferreds]);
        if (event.result === false) {
            attribute.cancelled = true;
            return false;
        }

        if (attribute.validate) {
            attribute.validate(attribute, getValue($form, attribute), msg, deferreds, $form);
        }

        return !!attribute.enableAjaxValidation;
    };

    var completeValidation = function ($form, data, messages, deferreds, needAjaxValidation, submitting) {
        removeEmptyMessages(messages);
        if (shouldRunAjaxValidation(data, messages, needAjaxValidation)) {
            sendAjaxValidation($form, data, messages, submitting);
            return;
        }

        finalizeValidationWithoutAjax($form, data, messages, submitting);
    };

    var removeEmptyMessages = function (messages) {
        for (var messageId in messages) {
            if (messages[messageId].length === 0) {
                delete messages[messageId];
            }
        }
    };

    var shouldRunAjaxValidation = function (data, messages, needAjaxValidation) {
        return needAjaxValidation && ($.isEmptyObject(messages) || data.submitting);
    };

    var sendAjaxValidation = function ($form, data, messages, submitting) {
        $.ajax({
            url: data.settings.validationUrl,
            type: $form.attr('method'),
            data: $form.serialize() + buildAjaxValidationData($form, data.submitObject, data.settings.ajaxParam),
            dataType: data.settings.ajaxDataType,
            complete: function (jqXHR, textStatus) {
                currentAjaxRequest = null;
                $form.trigger(events.ajaxComplete, [jqXHR, textStatus]);
            },
            beforeSend: function (jqXHR, settings) {
                currentAjaxRequest = jqXHR;
                $form.trigger(events.ajaxBeforeSend, [jqXHR, settings]);
            },
            success: function (msgs) {
                var validationMessages = filterAjaxValidationMessages(data.attributes, msgs, messages);
                updateInputs($form, validationMessages, submitting);
            },
            error: function () {
                data.submitting = false;
                submitFinalize($form);
            }
        });
    };

    var buildAjaxValidationData = function ($form, $button, ajaxParamName) {
        var data = '&' + ajaxParamName + '=' + $form.attr('id');
        if ($button && $button.length && $button.attr('name')) {
            data += '&' + $button.attr('name') + '=' + $button.attr('value');
        }

        return data;
    };

    var filterAjaxValidationMessages = function (attributes, ajaxMessages, messages) {
        if (ajaxMessages === null || typeof ajaxMessages !== 'object') {
            return messages;
        }

        $.each(attributes, function () {
            if (!this.enableAjaxValidation || this.cancelled) {
                delete ajaxMessages[this.id];
            }
        });

        return $.extend(messages, ajaxMessages);
    };

    var finalizeValidationWithoutAjax = function ($form, data, messages, submitting) {
        if (data.submitting) {
            // delay callback so that the form can be submitted without problem
            window.setTimeout(function () {
                updateInputs($form, messages, submitting);
            }, 200);
            return;
        }

        updateInputs($form, messages, submitting);
    };


    var methods = {
        init: function (attributes, options) {
            return this.each(function () {
                var $form = $(this);
                if ($form.data('yiiActiveForm')) {
                    return;
                }

                var settings = $.extend({}, defaults, options || {});
                if (settings.validationUrl === undefined) {
                    settings.validationUrl = $form.attr('action');
                }

                $.each(attributes, function (i) {
                    attributes[i] = $.extend({value: getValue($form, this)}, attributeDefaults, this);
                    watchAttribute($form, attributes[i]);
                });

                $form.data('yiiActiveForm', {
                    settings: settings,
                    attributes: attributes,
                    submitting: false,
                    validated: false,
                    validate_only: false, // validate without auto submitting
                    options: getFormOptions($form)
                });

                /**
                 * Clean up error status when the form is reset.
                 * Note that $form.on('reset', ...) does work because the "reset" event does not bubble on IE.
                 */
                $form.on('reset.yiiActiveForm', methods.resetForm);

                if (settings.validateOnSubmit) {
                    $form.on('mouseup.yiiActiveForm keyup.yiiActiveForm', ':submit', function () {
                        $form.data('yiiActiveForm').submitObject = $(this);
                    });
                    $form.on('submit.yiiActiveForm', methods.submitForm);
                }
                var event = $.Event(events.afterInit);
                $form.trigger(event);
            });
        },

        // add a new attribute to the form dynamically.
        // please refer to attributeDefaults for the structure of attribute
        add: function (attribute) {
            var $form = $(this);
            attribute = $.extend({value: getValue($form, attribute)}, attributeDefaults, attribute);
            $form.data('yiiActiveForm').attributes.push(attribute);
            watchAttribute($form, attribute);
        },

        // remove the attribute with the specified ID from the form
        remove: function (id) {
            var $form = $(this),
                attributes = $form.data('yiiActiveForm').attributes,
                index = -1,
                attribute;
            $.each(attributes, function (i) {
                if (attributes[i]['id'] === id) {
                    index = i;
                    attribute = attributes[i];
                    return false;
                }
            });
            if (index >= 0) {
                attributes.splice(index, 1);
                unwatchAttribute($form, attribute);
            }

            return attribute;
        },

        // manually trigger the validation of the attribute with the specified ID
        validateAttribute: function (id) {
            var attribute = methods.find.call(this, id);
            if (attribute !== undefined) {
                validateAttribute($(this), attribute, true);
            }
        },

        // find an attribute config based on the specified attribute ID
        find: function (id) {
            var attributes = $(this).data('yiiActiveForm').attributes,
                result;
            $.each(attributes, function (i) {
                if (attributes[i]['id'] === id) {
                    result = attributes[i];
                    return false;
                }
            });
            return result;
        },

        destroy: function () {
            return this.each(function () {
                $(this).off('.yiiActiveForm');
                $(this).removeData('yiiActiveForm');
            });
        },

        data: function () {
            return this.data('yiiActiveForm');
        },

        // validate all applicable inputs in the form
        validate: function (forceValidate) {
            if (forceValidate) {
                $(this).data('yiiActiveForm').submitting = true;
            }

            var $form = $(this),
                data = $form.data('yiiActiveForm'),
                needAjaxValidation = false,
                messages = {},
                deferreds = deferredArray(),
                submitting = data.submitting;

            if (submitting) {
                var event = $.Event(events.beforeValidate);
                $form.trigger(event, [messages, deferreds]);

                if (event.result === false) {
                    data.submitting = false;
                    submitFinalize($form);
                    return;
                }
            }

            // client-side validation
            $.each(data.attributes, function () {
                if (validateClientAttribute($form, data, this, messages, deferreds)) {
                    needAjaxValidation = true;
                }
            });

            // ajax validation
            $.when.apply(this, deferreds).always(function () {
                completeValidation($form, data, messages, deferreds, needAjaxValidation, submitting);
            });
        },

        submitForm: function () {
            var $form = $(this),
                data = $form.data('yiiActiveForm');
            if (data.validated) {
                // Second submit's call (from validate/updateInputs)
                data.submitting = false;
                var event = $.Event(events.beforeSubmit);
                $form.trigger(event);
                if (event.result === false) {
                    data.validated = false;
                    submitFinalize($form);
                    return false;
                }
                updateHiddenButton($form);
                return true;   // continue submitting the form since validation passes
            } else {
                // First submit's call (from yii.js/handleAction) - execute validating
                setSubmitFinalizeDefer($form);

                if (data.settings.timer !== undefined) {
                    clearTimeout(data.settings.timer);
                }
                data.submitting = true;
                methods.validate.call($form);
                return false;
            }
        },

        resetForm: function () {
            var $form = $(this);
            var data = $form.data('yiiActiveForm');
            // Because we bind directly to a form reset event instead of a reset button (that may not exist),
            // when this function is executed form input values have not been reset yet.
            // Therefore we do the actual reset work through setTimeout.
            window.setTimeout(function () {
                $.each(data.attributes, function () {
                    // Without setTimeout() we would get the input values that are not reset yet.
                    this.value = getValue($form, this);
                    this.status = 0;
                    var $container = $form.find(this.container),
                        $input = findInput($form, this),
                        $errorElement = data.settings.validationStateOn === 'input' ? $input : $container;

                    $errorElement.removeClass(
                        data.settings.validatingCssClass + ' ' +
                        data.settings.errorCssClass + ' ' +
                        data.settings.successCssClass
                    );
                    $container.find(this.error).html('');
                });
                $form.find(data.settings.errorSummary).hide().find('ul').html('');
            }, 1);
        },

        /**
         * Updates error messages, input containers, and optionally summary as well.
         * If an attribute is missing from messages, it is considered valid.
         * @param messages array the validation error messages, indexed by attribute IDs
         * @param summary whether to update summary as well.
         */
        updateMessages: function (messages, summary) {
            var $form = $(this);
            var data = $form.data('yiiActiveForm');
            $.each(data.attributes, function () {
                updateInput($form, this, messages);
            });
            if (summary) {
                updateSummary($form, messages);
            }
        },

        /**
         * Updates error messages and input container of a single attribute.
         * If messages is empty, the attribute is considered valid.
         * @param id attribute ID
         * @param messages array with error messages
         */
        updateAttribute: function (id, messages) {
            var attribute = methods.find.call(this, id);
            if (attribute !== undefined) {
                var msg = {};
                msg[id] = messages;
                updateInput($(this), attribute, msg);
            }
        }
    };

    var watchAttribute = function ($form, attribute) {
        var $input = findInput($form, attribute);
        if (attribute.validateOnChange) {
            $input.on('change.yiiActiveForm', function () {
                validateAttribute($form, attribute, false);
            });
        }
        if (attribute.validateOnBlur) {
            $input.on('blur.yiiActiveForm', function () {
                if (attribute.status === 0 || attribute.status === 1) {
                    validateAttribute($form, attribute, true);
                }
            });
        }
        if (attribute.validateOnType) {
            $input.on('keyup.yiiActiveForm', function (e) {
                if ($.inArray(e.which, [16, 17, 18, 37, 38, 39, 40]) !== -1) {
                    return;
                }
                if (attribute.value !== getValue($form, attribute)) {
                    validateAttribute($form, attribute, false, attribute.validationDelay);
                }
            });
        }
    };

    var unwatchAttribute = function ($form, attribute) {
        findInput($form, attribute).off('.yiiActiveForm');
    };

    var hasValidationError = function ($form, attribute, data) {
        var $input = findInput($form, attribute),
            $container = $form.find(attribute.container),
            $errorElement = data.settings.validationStateOn === 'input' ? $input : $container;

        return $errorElement.hasClass(data.settings.errorCssClass);
    };

    var cancelActiveValidation = function (data) {
        if (currentAjaxRequest !== null) {
            currentAjaxRequest.abort();
        }
        if (data.settings.timer !== undefined) {
            clearTimeout(data.settings.timer);
        }
    };

    var validateAttribute = function ($form, attribute, forceValidate, validationDelay) {
        var data = $form.data('yiiActiveForm');
        var hasValueChanges = false;

        if (forceValidate) {
            attribute.status = 2;
        }
        $.each(data.attributes, function () {
            if (!isEqual(this.value, getValue($form, this))) {
                this.status = 2;
                forceValidate = true;
                hasValueChanges = true;
            }
        });
        if (hasValueChanges) {
            $.each(data.attributes, function () {
                if (!this.hasWhenClient || this.status === 2 || this.status === 3) {
                    return;
                }
                if (hasValidationError($form, this, data)) {
                    this.status = 2;
                    forceValidate = true;
                }
            });
        }
        if (!forceValidate) {
            return;
        }

        cancelActiveValidation(data);
        data.settings.timer = window.setTimeout(function () {
            if (data.submitting || $form.is(':hidden')) {
                return;
            }
            $.each(data.attributes, function () {
                if (this.status === 2) {
                    this.status = 3;

                    var $container = $form.find(this.container),
                        $input = findInput($form, this);

                    var $errorElement = data.settings.validationStateOn === 'input' ? $input : $container;

                    $errorElement.addClass(data.settings.validatingCssClass);
                }
            });
            methods.validate.call($form);
        }, validationDelay ? validationDelay : 200);
    };

    /**
     * Compares two value whatever it objects, arrays or simple types
     * @param val1
     * @param val2
     * @returns boolean
     */
    var isEqual = function (val1, val2) {
        // objects
        if (val1 instanceof Object) {
            return isObjectsEqual(val1, val2)
        }

        // arrays
        if (Array.isArray(val1)) {
            return isArraysEqual(val1, val2);
        }

        // simple types
        return val1 === val2;
    };

    /**
     * Compares two objects
     * @param obj1
     * @param obj2
     * @returns boolean
     */
    var isObjectsEqual = function (obj1, obj2) {
        if (!(obj1 instanceof Object) || !(obj2 instanceof Object)) {
            return false;
        }

        var keys1 = Object.keys(obj1);
        var keys2 = Object.keys(obj2);
        if (keys1.length !== keys2.length) {
            return false;
        }

        return keys1.every(function (key) {
            return obj2.hasOwnProperty(key) && obj1[key] === obj2[key];
        });
    };

    /**
     * Compares two arrays
     * @param arr1
     * @param arr2
     * @returns boolean
     */
    var isArraysEqual = function (arr1, arr2) {
        if (!Array.isArray(arr1) || !Array.isArray(arr2)) {
            return false;
        }

        if (arr1.length !== arr2.length) {
            return false;
        }
        for (var i = 0; i < arr1.length; i += 1) {
            if (arr1[i] !== arr2[i]) {
                return false;
            }
        }
        return true;
    };

    /**
     * Returns an array prototype with a shortcut method for adding a new deferred.
     * The context of the callback will be the deferred object so it can be resolved like ```this.resolve()```
     * @returns Array
     */
    var deferredArray = function () {
        var array = [];
        array.add = function (callback) {
            this.push(new $.Deferred(callback));
        };
        return array;
    };

    var buttonOptions = ['action', 'target', 'method', 'enctype'];

    /**
     * Returns current form options
     * @param $form
     * @returns object Object with button of form options
     */
    var getFormOptions = function ($form) {
        var attributes = {};
        for (var i = 0; i < buttonOptions.length; i++) {
            attributes[buttonOptions[i]] = $form.attr(buttonOptions[i]);
        }

        return attributes;
    };

    /**
     * Applies temporary form options related to submit button
     * @param $form the form jQuery object
     * @param $button the button jQuery object
     */
    var applyButtonOptions = function ($form, $button) {
        for (var i = 0; i < buttonOptions.length; i++) {
            var value = $button.attr('form' + buttonOptions[i]);
            if (value) {
                $form.attr(buttonOptions[i], value);
            }
        }
    };

    /**
     * Restores original form options
     * @param $form the form jQuery object
     */
    var restoreButtonOptions = function ($form) {
        var data = $form.data('yiiActiveForm');

        for (var i = 0; i < buttonOptions.length; i++) {
            $form.attr(buttonOptions[i], data.options[buttonOptions[i]] || null);
        }
    };

    /**
     * Updates the error messages and the input containers for all applicable attributes
     * @param $form the form jQuery object
     * @param messages array the validation error messages
     * @param submitting whether this method is called after validation triggered by form submission
     */
    var updateInputs = function ($form, messages, submitting) {
        var data = $form.data('yiiActiveForm');

        if (data === undefined) {
            return false;
        }

        var errorAttributes = collectErrorAttributes($form, data, data.attributes, messages, submitting);

        $form.trigger(events.afterValidate, [messages, errorAttributes]);

        if (submitting) {
            handleSubmittingValidationResult($form, data, messages, errorAttributes);
        } else {
            updatePendingAttributes($form, data.attributes, messages);
        }
        submitFinalize($form);
    };

    var collectErrorAttributes = function ($form, data, attributes, messages, submitting) {
        var errorAttributes = [];
        $.each(attributes, function () {
            collectErrorAttribute($form, data, this, messages, submitting, errorAttributes);
        });

        return errorAttributes;
    };

    var collectErrorAttribute = function ($form, data, attribute, messages, submitting, errorAttributes) {
        var $input = findInput($form, attribute);
        if ($input.is(':disabled') || attribute.cancelled) {
            return;
        }

        if (hasAttributeError($form, data, attribute, messages, submitting)) {
            errorAttributes.push(attribute);
        }
    };

    var hasAttributeError = function ($form, data, attribute, messages, submitting) {
        if (submitting) {
            return updateInput($form, attribute, messages);
        }

        if (attribute.status === 2 || attribute.status === 3 || messages[attribute.id] !== undefined) {
            return attrHasError($form, attribute, messages);
        }

        return hasValidationError($form, attribute, data);
    };

    var handleSubmittingValidationResult = function ($form, data, messages, errorAttributes) {
        updateSummary($form, messages);
        if (errorAttributes.length) {
            scrollToFirstError($form, errorAttributes, data.settings.scrollToError, data.settings.scrollToErrorOffset);
            data.submitting = false;
            return;
        }

        data.validated = true;
        if (!data.validate_only) {
            submitValidatedForm($form, data.submitObject);
        }
    };

    var updatePendingAttributes = function ($form, attributes, messages) {
        $.each(attributes, function () {
            if (!this.cancelled && (this.status === 2 || this.status === 3)) {
                updateInput($form, this, messages);
            }
        });
    };

    var scrollToFirstError = function ($form, errorAttributes, shouldScroll, scrollOffset) {
        if (!shouldScroll) {
            return;
        }

        var documentHeight = $(document).height();
        var top = getFirstErrorTop($form, errorAttributes, scrollOffset);
        top = top < 0 ? 0 : (top > documentHeight ? documentHeight : top);
        var windowTop = $(window).scrollTop();
        if (top < windowTop || top > windowTop + $(window).height()) {
            $(window).scrollTop(top);
        }
    };

    var getFirstErrorTop = function ($form, errorAttributes, scrollOffset) {
        var firstErrorSelector = $.map(errorAttributes, function (attribute) {
            return attribute.input;
        }).join(',');

        return $form.find(firstErrorSelector).first().closest(':visible').offset().top - scrollOffset;
    };

    var submitValidatedForm = function ($form, $submitObject) {
        if ($submitObject) {
            applyButtonOptions($form, $submitObject);
        }

        $form.submit();

        if ($submitObject) {
            restoreButtonOptions($form);
        }
    };

    /**
     * Updates hidden field that represents clicked submit button.
     * @param $form the form jQuery object.
     */
    var updateHiddenButton = function ($form) {
        var data = $form.data('yiiActiveForm');
        var $button = data.submitObject || $form.find(':submit:first');
        // TODO: if the submission is caused by "change" event, it will not work
        if ($button.length && $button.attr('type') === 'submit' && $button.attr('name')) {
            // simulate button input value
            var $hiddenButton = $('input[type="hidden"][name="' + $button.attr('name') + '"]', $form);
            if (!$hiddenButton.length) {
                $('<input>').attr({
                    type: 'hidden',
                    name: $button.attr('name'),
                    value: $button.attr('value')
                }).appendTo($form);
            } else {
                $hiddenButton.attr('value', $button.attr('value'));
            }
        }
    };

    /**
     * Updates the error message and the input container for a particular attribute.
     * @param $form the form jQuery object
     * @param attribute object the configuration for a particular attribute.
     * @param messages array the validation error messages
     * @return boolean whether there is a validation error for the specified attribute
     */
    var updateInput = function ($form, attribute, messages) {
        var data = $form.data('yiiActiveForm'),
            $input = findInput($form, attribute),
            hasError = attrHasError($form, attribute, messages);

        if (!$.isArray(messages[attribute.id])) {
            messages[attribute.id] = [];
        }

        attribute.status = 1;
        if ($input.length) {
            var $container = $form.find(attribute.container);
            var $error = $container.find(attribute.error);
            updateAriaInvalid($form, attribute, hasError);

            var $errorElement = data.settings.validationStateOn === 'input' ? $input : $container;

            if (hasError) {
                if (attribute.encodeError) {
                    $error.text(messages[attribute.id][0]);
                } else {
                    $error.html(messages[attribute.id][0]);
                }
                $errorElement.removeClass(data.settings.validatingCssClass + ' ' + data.settings.successCssClass)
                    .addClass(data.settings.errorCssClass);
            } else {
                $error.empty();
                $errorElement.removeClass(data.settings.validatingCssClass + ' ' + data.settings.errorCssClass + ' ')
                    .addClass(data.settings.successCssClass);
            }
            attribute.value = getValue($form, attribute);
        }

        $form.trigger(events.afterValidateAttribute, [attribute, messages[attribute.id]]);

        return hasError;
    };

    /**
     * Checks if a particular attribute has an error
     * @param $form the form jQuery object
     * @param attribute object the configuration for a particular attribute.
     * @param messages array the validation error messages
     * @return boolean whether there is a validation error for the specified attribute
     */
    var attrHasError = function ($form, attribute, messages) {
        var $input = findInput($form, attribute),
            hasError = false;

        if (!$.isArray(messages[attribute.id])) {
            messages[attribute.id] = [];
        }

        if ($input.length) {
            hasError = messages[attribute.id].length > 0;
        }

        return hasError;
    };

    /**
     * Updates the error summary.
     * @param $form the form jQuery object
     * @param messages array the validation error messages
     */
    var updateSummary = function ($form, messages) {
        var data = $form.data('yiiActiveForm'),
            $summary = $form.find(data.settings.errorSummary),
            $ul = $summary.find('ul').empty();

        if ($summary.length && messages) {
            $.each(data.attributes, function () {
                if ($.isArray(messages[this.id]) && messages[this.id].length) {
                    var error = $('<li/>');
                    if (data.settings.encodeErrorSummary) {
                        error.text(messages[this.id][0]);
                    } else {
                        error.html(messages[this.id][0]);
                    }
                    $ul.append(error);
                }
            });
            $summary.toggle($ul.find('li').length > 0);
        }
    };

    var getValue = function ($form, attribute) {
        var $input = findInput($form, attribute);
        var type = $input.attr('type');
        if (type === 'checkbox' || type === 'radio') {
            var $realInput = $input.filter(':checked');
            if ($realInput.length > 1) {
                var values = [];
                $realInput.each(function (index) {
                    values.push($($realInput.get(index)).val());
                });
                return values;
            }

            if (!$realInput.length) {
                $realInput = $form.find('input[type=hidden][name="' + $input.attr('name') + '"]');
            }

            return $realInput.val();
        } else {
            return $input.val();
        }
    };

    var findInput = function ($form, attribute) {
        var $input = $form.find(attribute.input);
        if ($input.length && $input[0].tagName.toLowerCase() === 'div') {
            // checkbox list or radio list
            return $input.find('input');
        } else {
            return $input;
        }
    };

    var updateAriaInvalid = function ($form, attribute, hasError) {
        if (attribute.updateAriaInvalid) {
            $form.find(attribute.input).attr('aria-invalid', hasError ? 'true' : 'false');
        }
    }

    var currentAjaxRequest = null;

}(window.jQuery));
