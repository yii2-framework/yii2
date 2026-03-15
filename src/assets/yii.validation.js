/**
 * Yii validation module.
 *
 * This JavaScript module provides the validation methods for the built-in validators.
 *
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */

// eslint-disable-next-line max-statements
yii.validation = (function ($) {
    var pub = {
        isEmpty: function (value) {
            return value === null || value === undefined || ($.isArray(value) && value.length === 0) || value === '';
        },

        addMessage: function (messages, message, value) {
            messages.push(message.replace(/\{value\}/g, value));
        },

        required: function (value, messages, options) {
            if (!isRequiredValid(value, options)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        // "boolean" is a reserved keyword in older versions of ES so it's quoted for IE < 9 support
        'boolean': function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (!isBooleanValid(value, options)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        string: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            validateStringValue(value, messages, options);
        },

        file: function (attribute, messages, options) {
            var files = getUploadedFiles(attribute, messages, options);
            $.each(files, function (i, file) {
                validateFile(file, messages, options);
            });
        },

        image: function (attribute, messages, options, deferredList) {
            var files = getUploadedFiles(attribute, messages, options);
            $.each(files, function (i, file) {
                validateFile(file, messages, options);

                // Skip image validation if FileReader API is not available
                if (typeof FileReader === "undefined") {
                    return;
                }

                var deferred = $.Deferred();
                pub.validateImage(file, messages, options, deferred, new FileReader(), new Image());
                deferredList.push(deferred);
            });
        },

        validateImage: function (file, messages, options, deferred, fileReader, image) {
            image.onload = function () {
                validateImageSize(file, image, messages, options);
                deferred.resolve();
            };

            image.onerror = function () {
                messages.push(options.notImage.replace(/\{file\}/g, file.name));
                deferred.resolve();
            };

            fileReader.onload = function () {
                image.src = this.result;
            };

            // Resolve deferred if there was error while reading data
            fileReader.onerror = function () {
                deferred.resolve();
            };

            fileReader.readAsDataURL(file);
        },

        number: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            validateNumberValue(value, messages, options);
        },

        range: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (!isRangeValid(value, options)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        regularExpression: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (!isRegularExpressionValid(value, options)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        email: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            var emailValidationResult = validateEmailValue(value, options);
            if (!emailValidationResult.valid) {
                pub.addMessage(messages, options.message, emailValidationResult.value);
            }
        },

        url: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            var urlValidationResult = validateUrlValue(value, options);
            if (!urlValidationResult.valid) {
                pub.addMessage(messages, options.message, urlValidationResult.value);
            }
        },

        trim: function ($form, attribute, options, value) {
            var $input = $form.find(attribute.input);
            if ($input.is(':checkbox, :radio')) {
                return value;
            }

            value = $input.val();
            if (shouldSkipTrim(value, options)) {
                return value;
            }

            value = trimValue(value, options);
            $input.val(value);

            return value;
        },

        captcha: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            // CAPTCHA may be updated via AJAX and the updated hash is stored in body data
            var hash = getCaptchaHash(options);
            var valueHash = getCaptchaValueHash(value, options.caseSensitive);
            if (isLooseNotEqual(valueHash, hash)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        compare: function (value, messages, options, $form) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            if (!isCompareValid(value, options, $form)) {
                pub.addMessage(messages, options.message, value);
            }
        },

        ip: function (value, messages, options) {
            if (options.skipOnEmpty && pub.isEmpty(value)) {
                return;
            }

            validateIpValue(value, messages, options);
        }
    };

    function isRequiredValid(value, options)
    {
        if (options.requiredValue !== undefined) {
            return options.strict ? value === options.requiredValue : isLooseEqual(value, options.requiredValue);
        }

        if (options.strict) {
            return value !== undefined;
        }

        var normalizedValue = isStringValue(value) ? trimString(value) : value;

        return !pub.isEmpty(normalizedValue);
    }

    function isBooleanValid(value, options)
    {
        if (options.strict) {
            return value === options.trueValue || value === options.falseValue;
        }

        return isLooseEqual(value, options.trueValue) || isLooseEqual(value, options.falseValue);
    }

    function validateStringValue(value, messages, options)
    {
        if (!isStringType(value)) {
            pub.addMessage(messages, options.message, value);

            return;
        }
        if (!isExpectedStringLength(value, options, messages)) {
            return;
        }

        addStringLengthMessages(value, messages, options);
    }

    function isStringType(value)
    {
        return typeof value === 'string';
    }

    function isExpectedStringLength(value, options, messages)
    {
        if (options.is === undefined || !isLooseNotEqual(value.length, options.is)) {
            return true;
        }

        pub.addMessage(messages, options.notEqual, value);

        return false;
    }

    function addStringLengthMessages(value, messages, options)
    {
        if (options.min !== undefined && value.length < options.min) {
            pub.addMessage(messages, options.tooShort, value);
        }
        if (options.max !== undefined && value.length > options.max) {
            pub.addMessage(messages, options.tooLong, value);
        }
    }

    function validateNumberValue(value, messages, options)
    {
        if (!isNumberPatternValid(value, options.pattern)) {
            pub.addMessage(messages, options.message, value);

            return;
        }

        addNumberRangeMessages(value, messages, options);
    }

    function isNumberPatternValid(value, pattern)
    {
        return typeof value !== 'string' || pattern.test(value);
    }

    function addNumberRangeMessages(value, messages, options)
    {
        if (options.min !== undefined && value < options.min) {
            pub.addMessage(messages, options.tooSmall, value);
        }
        if (options.max !== undefined && value > options.max) {
            pub.addMessage(messages, options.tooBig, value);
        }
    }

    function isRangeValid(value, options)
    {
        if (!options.allowArray && $.isArray(value)) {
            return false;
        }

        var values = $.isArray(value) ? value : [value];
        var inArray = values.every(function (singleValue) {
            return $.inArray(singleValue, options.range) !== -1;
        });
        var isNot = options.not === undefined ? false : options.not;

        return isNot !== inArray;
    }

    function isRegularExpressionValid(value, options)
    {
        var matches = options.pattern.test(value);

        return options.not ? !matches : matches;
    }

    function validateEmailValue(value, options)
    {
        var regexp = /^((?:"?([^"]*)"?\s)?)(?:\s+)?(?:(<?)((.+)@([^>]+))(>?))$/;
        var matches = regexp.exec(value);
        if (matches === null) {
            return {valid: false, value: value};
        }

        var localPart = matches[5];
        var domain = matches[6];
        var normalizedValue = value;
        if (options.enableIDN) {
            localPart = punycode.toASCII(localPart);
            domain = punycode.toASCII(domain);
            normalizedValue = matches[1] + matches[3] + localPart + '@' + domain + matches[7];
        }

        if (!isEmailLengthValid(localPart, domain)) {
            return {valid: false, value: normalizedValue};
        }

        var valid = options.pattern.test(normalizedValue) || (options.allowName && options.fullPattern.test(normalizedValue));

        return {valid: valid, value: normalizedValue};
    }

    function isEmailLengthValid(localPart, domain)
    {
        if (localPart.length > 64) {
            return false;
        }

        return (localPart + '@' + domain).length <= 254;
    }

    function validateUrlValue(value, options)
    {
        var normalizedValue = applyDefaultScheme(value, options.defaultScheme);
        if (!options.enableIDN) {
            return {valid: options.pattern.test(normalizedValue), value: normalizedValue};
        }

        var matches = /^([^:]+):\/\/([^\/]+)(.*)$/.exec(normalizedValue);
        if (matches === null) {
            return {valid: false, value: normalizedValue};
        }

        normalizedValue = matches[1] + '://' + punycode.toASCII(matches[2]) + matches[3];

        return {valid: options.pattern.test(normalizedValue), value: normalizedValue};
    }

    function applyDefaultScheme(value, defaultScheme)
    {
        if (defaultScheme && !/:\/\//.test(value)) {
            return defaultScheme + '://' + value;
        }

        return value;
    }

    function shouldSkipTrim(value, options)
    {
        var skipBecauseEmpty = options.skipOnEmpty && pub.isEmpty(value);
        var skipBecauseArray = options.skipOnArray && Array.isArray(value);

        return skipBecauseEmpty || skipBecauseArray;
    }

    function trimValue(value, options)
    {
        if (Array.isArray(value)) {
            return value.map(function (item) {
                return trimString(item, options);
            });
        }

        return trimString(value, options);
    }

    function getCaptchaHash(options)
    {
        var hash = $('body').data(options.hashKey);
        if (hash === null || hash === undefined) {
            return options.hash;
        }

        return hash[options.caseSensitive ? 0 : 1];
    }

    function getCaptchaValueHash(value, caseSensitive)
    {
        var normalizedValue = caseSensitive ? value : value.toLowerCase();
        var hash = 0;
        for (var i = normalizedValue.length - 1; i >= 0; --i) {
            hash += normalizedValue.charCodeAt(i) << i;
        }

        return hash;
    }

    function isCompareValid(value, options, $form)
    {
        var compareValue = getCompareValue(options, $form);
        var compareData = normalizeCompareValues(value, compareValue, options.type);

        return evaluateCompareResult(compareData.value, compareData.compareValue, options.operator);
    }

    function getCompareValue(options, $form)
    {
        if (options.compareAttribute === undefined) {
            return options.compareValue;
        }

        var $target = $('#' + options.compareAttribute);
        if (!$target.length) {
            $target = $form.find('[name="' + options.compareAttributeName + '"]');
        }

        return $target.val();
    }

    function normalizeCompareValues(value, compareValue, type)
    {
        if (type !== 'number') {
            return {value: value, compareValue: compareValue};
        }

        return {
            value: value ? parseFloat(value) : 0,
            compareValue: compareValue ? parseFloat(compareValue) : 0
        };
    }

    function evaluateCompareResult(value, compareValue, operator)
    {
        var compareMethod = getCompareMethod(operator);
        if (compareMethod === undefined) {
            return false;
        }

        return compareMethod(value, compareValue);
    }

    function getCompareMethod(operator)
    {
        var operators = {
            '==': function (left, right) {
                return isLooseEqual(left, right);
            },
            '===': function (left, right) {
                return left === right;
            },
            '!=': function (left, right) {
                return isLooseNotEqual(left, right);
            },
            '!==': function (left, right) {
                return left !== right;
            },
            '>': function (left, right) {
                return left > right;
            },
            '>=': function (left, right) {
                return left >= right;
            },
            '<': function (left, right) {
                return left < right;
            },
            '<=': function (left, right) {
                return left <= right;
            }
        };

        return operators[operator];
    }

    function validateIpValue(value, messages, options)
    {
        var parsedIp = parseIpValue(value, options.ipParsePattern);
        if (!validateIpSubnet(parsedIp, messages, options)) {
            return;
        }
        if (!validateIpNegation(parsedIp, messages, options)) {
            return;
        }

        validateIpVersion(parsedIp.value, messages, options);
    }

    function parseIpValue(value, ipParsePattern)
    {
        var matches = new RegExp(ipParsePattern).exec(value);
        if (!matches) {
            return {value: value, negation: null, cidr: null};
        }

        return {
            value: matches[2],
            negation: matches[1] || null,
            cidr: matches[4] || null
        };
    }

    function validateIpSubnet(parsedIp, messages, options)
    {
        if (options.subnet === true && parsedIp.cidr === null) {
            pub.addMessage(messages, options.messages.noSubnet, parsedIp.value);

            return false;
        }
        if (options.subnet === false && parsedIp.cidr !== null) {
            pub.addMessage(messages, options.messages.hasSubnet, parsedIp.value);

            return false;
        }

        return true;
    }

    function validateIpNegation(parsedIp, messages, options)
    {
        if (options.negation === false && parsedIp.negation !== null) {
            pub.addMessage(messages, options.messages.message, parsedIp.value);

            return false;
        }

        return true;
    }

    function validateIpVersion(value, messages, options)
    {
        if (isIpv6Address(value)) {
            validateIpv6Address(value, messages, options);

            return;
        }

        validateIpv4Address(value, messages, options);
    }

    function isIpv6Address(value)
    {
        return value.indexOf(':') !== -1;
    }

    function validateIpv6Address(value, messages, options)
    {
        if (!(new RegExp(options.ipv6Pattern)).test(value)) {
            pub.addMessage(messages, options.messages.message, value);
        }
        if (!options.ipv6) {
            pub.addMessage(messages, options.messages.ipv6NotAllowed, value);
        }
    }

    function validateIpv4Address(value, messages, options)
    {
        if (!(new RegExp(options.ipv4Pattern)).test(value)) {
            pub.addMessage(messages, options.messages.message, value);
        }
        if (!options.ipv4) {
            pub.addMessage(messages, options.messages.ipv4NotAllowed, value);
        }
    }

    function getUploadedFiles(attribute, messages, options)
    {
        // Skip validation if File API is not available
        if (typeof File === "undefined") {
            return [];
        }

        var fileInput = $(attribute.input, attribute.$form).get(0);

        // Skip validation if file input does not exist
        // (in case file inputs are added dynamically and no file input has been added to the form)
        if (typeof fileInput === "undefined") {
            return [];
        }

        return normalizeUploadedFiles(fileInput.files, messages, options);
    }

    function normalizeUploadedFiles(files, messages, options)
    {
        if (!files) {
            messages.push(options.message);

            return [];
        }
        if (files.length === 0) {
            if (!options.skipOnEmpty) {
                messages.push(options.uploadRequired);
            }

            return [];
        }
        if (options.maxFiles && options.maxFiles < files.length) {
            messages.push(options.tooMany);

            return [];
        }

        return files;
    }

    function validateFile(file, messages, options)
    {
        validateFileExtension(file, messages, options);
        validateFileMimeType(file, messages, options);
        validateFileSize(file, messages, options);
    }

    function validateFileExtension(file, messages, options)
    {
        if (!options.extensions || options.extensions.length === 0) {
            return;
        }

        if (!isValidFileExtension(file.name, options.extensions)) {
            messages.push(options.wrongExtension.replace(/\{file\}/g, file.name));
        }
    }

    function isValidFileExtension(filename, extensions)
    {
        var normalizedFilename = filename.toLowerCase();
        for (var index = 0; index < extensions.length; index++) {
            var extension = extensions[index].toLowerCase();
            var extensionLength = extension.length;
            if (extension === '' && normalizedFilename.indexOf('.') === -1) {
                return true;
            }
            if (normalizedFilename.substr(normalizedFilename.length - extensionLength - 1) === '.' + extension) {
                return true;
            }
        }

        return false;
    }

    function validateFileMimeType(file, messages, options)
    {
        if (!options.mimeTypes || options.mimeTypes.length === 0) {
            return;
        }

        if (!validateMimeType(options.mimeTypes, file.type)) {
            messages.push(options.wrongMimeType.replace(/\{file\}/g, file.name));
        }
    }

    function validateFileSize(file, messages, options)
    {
        if (options.maxSize && options.maxSize < file.size) {
            messages.push(options.tooBig.replace(/\{file\}/g, file.name));
        }
        if (options.minSize && options.minSize > file.size) {
            messages.push(options.tooSmall.replace(/\{file\}/g, file.name));
        }
    }

    function validateMimeType(mimeTypes, fileType)
    {
        for (var i = 0, len = mimeTypes.length; i < len; i++) {
            if (new RegExp(mimeTypes[i]).test(fileType)) {
                return true;
            }
        }

        return false;
    }

    function validateImageSize(file, image, messages, options)
    {
        addImageSizeMessageIfNeeded(options.minWidth, image.width, options.underWidth, file, messages, function (actual, expected) {
            return actual < expected;
        });
        addImageSizeMessageIfNeeded(options.maxWidth, image.width, options.overWidth, file, messages, function (actual, expected) {
            return actual > expected;
        });
        addImageSizeMessageIfNeeded(options.minHeight, image.height, options.underHeight, file, messages, function (actual, expected) {
            return actual < expected;
        });
        addImageSizeMessageIfNeeded(options.maxHeight, image.height, options.overHeight, file, messages, function (actual, expected) {
            return actual > expected;
        });
    }

    function addImageSizeMessageIfNeeded(limit, actualSize, messageTemplate, file, messages, isViolation)
    {
        if (limit && isViolation(actualSize, limit)) {
            messages.push(messageTemplate.replace(/\{file\}/g, file.name));
        }
    }

    /**
     * PHP: `trim($path, ' /')`, JS: `yii.helpers.trim(path, {chars: ' /'})`
     */
    function trimString(value, options = {skipOnEmpty: true, chars: null})
    {
        if (options.skipOnEmpty !== false && pub.isEmpty(value)) {
            return value;
        }

        var normalizedValue = String(value);
        if (options.chars || !String.prototype.trim) {
            return trimStringWithChars(normalizedValue, options.chars);
        }

        return normalizedValue.trim();
    }

    function trimStringWithChars(value, chars)
    {
        var trimChars = !chars
            ? ' \\s\xA0'
            : chars.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');

        return value.replace(new RegExp('^[' + trimChars + ']+|[' + trimChars + ']+$', 'g'), '');
    }

    function isStringValue(value)
    {
        return typeof value === 'string' || value instanceof String;
    }

    function isLooseEqual(left, right)
    {
        // eslint-disable-next-line eqeqeq
        return left == right;
    }

    function isLooseNotEqual(left, right)
    {
        // eslint-disable-next-line eqeqeq
        return left != right;
    }

    return pub;
}(jQuery));
