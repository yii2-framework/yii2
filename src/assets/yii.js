/**
 * Yii JavaScript module.
 *
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */

/**
 * yii is the root module for all Yii JavaScript modules.
 * It implements a mechanism of organizing JavaScript code in modules through the function "yii.initModule()".
 *
 * Each module should be named as "x.y.z", where "x" stands for the root module (for the Yii core code, this is "yii").
 *
 * A module may be structured as follows:
 *
 * ```javascript
 * window.yii.sample = (function($) {
 *     var pub = {
 *         // whether this module is currently active. If false, init() will not be called for this module
 *         // it will also not be called for all its child modules. If this property is undefined, it means true.
 *         isActive: true,
 *         init: function() {
 *             // ... module initialization code goes here ...
 *         },
 *
 *         // ... other public functions and properties go here ...
 *     };
 *
 *     // ... private functions and properties go here ...
 *
 *     return pub;
 * })(window.jQuery);
 * ```
 *
 * Using this structure, you can define public and private functions/properties for a module.
 * Private functions/properties are only visible within the module, while public functions/properties
 * may be accessed outside of the module. For example, you can access "yii.sample.isActive".
 *
 * You must call "yii.initModule()" once for the root module of all your modules.
 */
// eslint-disable-next-line max-statements
window.yii = (function ($) {
    var pub = {
        /**
         * List of JS or CSS URLs that can be loaded multiple times via AJAX requests.
         * Each item may be represented as either an absolute URL or a relative one.
         * Each item may contain a wildcard matching character `*`, that means one or more
         * any characters on the position. For example:
         *  - `/css/*.css` will match any file ending with `.css` in the `css` directory of the current web site
         *  - `http*://cdn.example.com/*` will match any files on domain `cdn.example.com`, loaded with HTTP or HTTPS
         *  - `/js/myCustomScript.js?realm=*` will match file `/js/myCustomScript.js` with defined `realm` parameter
         */
        reloadableScripts: [],
        /**
         * The selector for clickable elements that need to support confirmation and form submission.
         */
        clickableSelector: 'a, button, input[type="submit"], input[type="button"], input[type="reset"], ' +
            'input[type="image"]',
        /**
         * The selector for changeable elements that need to support confirmation and form submission.
         */
        changeableSelector: 'select, input, textarea',

        /**
         * @return string|undefined the CSRF parameter name. Undefined is returned if CSRF validation is not enabled.
         */
        getCsrfParam: function () {
            return $('meta[name=csrf-param]').attr('content');
        },

        /**
         * @return string|undefined the CSRF token. Undefined is returned if CSRF validation is not enabled.
         */
        getCsrfToken: function () {
            return $('meta[name=csrf-token]').attr('content');
        },

        /**
         * Sets the CSRF token in the meta elements.
         * This method is provided so that you can update the CSRF token with the latest one you obtain from the server.
         * @param name the CSRF token name
         * @param value the CSRF token value
         */
        setCsrfToken: function (name, value) {
            $('meta[name=csrf-param]').attr('content', name);
            $('meta[name=csrf-token]').attr('content', value);
        },

        /**
         * Updates all form CSRF input fields with the latest CSRF token.
         * This method is provided to avoid cached forms containing outdated CSRF tokens.
         */
        refreshCsrfToken: function () {
            var token = pub.getCsrfToken();
            if (token) {
                $('form input[name="' + pub.getCsrfParam() + '"]').val(token);
            }
        },

        /**
         * Displays a confirmation dialog.
         * The default implementation simply displays a js confirmation dialog.
         * You may override this by setting `yii.confirm`.
         * @param message the confirmation message.
         * @param ok a callback to be called when the user confirms the message
         * @param cancel a callback to be called when the user cancels the confirmation
         */
        confirm: function (message, ok, cancel) {
            var confirmHandler = window.confirm;
            if (confirmHandler.call(window, message)) {
                if (ok) {
                    ok();
                }
            } else {
                if (cancel) {
                    cancel();
                }
            }
        },

        /**
         * Handles the action triggered by user.
         * This method recognizes the `data-method` attribute of the element. If the attribute exists,
         * the method will submit the form containing this element. If there is no containing form, a form
         * will be created and submitted using the method given by this attribute value (e.g. "post", "put").
         * For hyperlinks, the form action will take the value of the "href" attribute of the link.
         * For other elements, either the containing form action or the current page URL will be used
         * as the form action URL.
         *
         * If the `data-method` attribute is not defined, the `href` attribute (if any) of the element
         * will be assigned to `window.location`.
         *
         * Starting from version 2.0.3, the `data-params` attribute is also recognized when you specify
         * `data-method`. The value of `data-params` should be a JSON representation of the data (name-value pairs)
         * that should be submitted as hidden inputs. For example, you may use the following code to generate
         * such a link:
         *
         * ```php
         * use yii\helpers\Html;
         * use yii\helpers\Json;
         *
         * echo Html::a('submit', ['site/foobar'], [
         *     'data' => [
         *         'method' => 'post',
         *         'params' => [
         *             'name1' => 'value1',
         *             'name2' => 'value2',
         *         ],
         *     ],
         * ]);
         * ```
         *
         * @param $e the jQuery representation of the element
         * @param event Related event
         */
        handleAction: function ($e, event) {
            var context = createActionContext($e, event);
            validateActionParams(context.params, context.areValidParams);
            if (context.usePjax) {
                context.pjaxOptions = createPjaxOptions(context.$e, event);
            }

            if (context.method === undefined) {
                handleActionWithoutMethod(context);
                return;
            }

            handleActionWithMethod(context);
        },

        getQueryParams: function (url) {
            var queryString = getQueryString(url);
            if (queryString === null) {
                return {};
            }

            return $.grep(queryString.split('&'), function (value) {
                return value !== '';
            }).reduce(function (params, pair) {
                appendQueryParam(params, pair);

                return params;
            }, {});
        },

        initModule: function (module) {
            if (module.isActive !== undefined && !module.isActive) {
                return;
            }
            if ($.isFunction(module.init)) {
                module.init();
            }
            $.each(module, function () {
                if ($.isPlainObject(this)) {
                    pub.initModule(this);
                }
            });
        },

        init: function () {
            initCsrfHandler();
            initRedirectHandler();
            initAssetFilters();
            initDataMethods();
        },

        /**
         * Returns the URL of the current page without params and trailing slash. Separated and made public for testing.
         * @returns {string}
         */
        getBaseCurrentUrl: function () {
            return window.location.protocol + '//' + window.location.host;
        },

        /**
         * Returns the URL of the current page. Used for testing, you can always call `window.location.href` manually
         * instead.
         * @returns {string}
         */
        getCurrentUrl: function () {
            return window.location.href;
        }
    };

    var conflictActionParams = ['submit', 'reset', 'elements', 'length', 'name', 'acceptCharset',
        'action', 'enctype', 'method', 'target'];
    var actionHelperAttribute = 'data-yii-action-helper';

    function createActionContext($e, event)
    {
        var $form = $e.attr('data-form') ? $('#' + $e.attr('data-form')) : $e.closest('form');
        var params = $e.data('params');

        return {
            $e: $e,
            event: event,
            $form: $form,
            method: $e.data('method'),
            action: $e.attr('href'),
            isValidAction: $e.attr('href') && $e.attr('href') !== '#',
            params: params,
            areValidParams: params && $.isPlainObject(params),
            usePjax: isPjaxEnabled($e),
            pjaxOptions: {}
        };
    }

    function isPjaxEnabled($e)
    {
        var pjax = $e.data('pjax');

        return pjax !== undefined && pjax !== 0 && pjax !== false && $.support.pjax;
    }

    function validateActionParams(params, areValidParams)
    {
        if (!areValidParams) {
            return;
        }

        $.each(conflictActionParams, function (index, param) {
            if (Object.prototype.hasOwnProperty.call(params, param)) {
                console.error("Parameter name '" + param + "' conflicts with a same named form property. " +
                    "Please use another name.");
            }
        });
    }

    function createPjaxOptions($e, event)
    {
        return {
            container: getPjaxContainer($e),
            push: !!$e.data('pjax-push-state'),
            replace: !!$e.data('pjax-replace-state'),
            scrollTo: $e.data('pjax-scrollto'),
            pushRedirect: $e.data('pjax-push-redirect'),
            replaceRedirect: $e.data('pjax-replace-redirect'),
            skipOuterContainers: $e.data('pjax-skip-outer-containers'),
            timeout: $e.data('pjax-timeout'),
            originalEvent: event,
            originalTarget: $e
        };
    }

    function getPjaxContainer($e)
    {
        var container = $e.data('pjax-container');
        if (container !== undefined && container.length) {
            return container;
        }

        var closestContainerId = $e.closest('[data-pjax-container]').attr('id');
        if (closestContainerId) {
            return '#' + closestContainerId;
        }

        return 'body';
    }

    function handleActionWithoutMethod(context)
    {
        if (context.isValidAction) {
            if (context.usePjax) {
                $.pjax.click(context.event, context.pjaxOptions);
            } else {
                window.location.assign(context.action);
            }

            return;
        }

        if (context.$e.is(':submit') && context.$form.length) {
            submitActionForm(context.$form, context.usePjax, context.pjaxOptions);
        }
    }

    function handleActionWithMethod(context)
    {
        var formState = prepareActionForm(context);
        setActiveFormSubmitObject(context.$form, context.$e);
        appendActionParams(context.$form, context.params, context.areValidParams);
        submitActionForm(context.$form, context.usePjax, context.pjaxOptions);
        restoreActionForm(context.$form, formState);
    }

    function prepareActionForm(context)
    {
        var state = {
            newForm: !context.$form.length,
            oldMethod: undefined,
            oldAction: undefined,
            oldActionExists: false,
            actionModified: false
        };

        if (state.newForm) {
            context.$form = createActionForm(context);

            return state;
        }

        state.oldMethod = context.$form.attr('method');
        context.$form.attr('method', context.method);
        var normalizedMethod = normalizeSubmitMethod(context.$form, context.method, true);
        appendCsrfField(context.$form, normalizedMethod, true);
        if (context.isValidAction) {
            state.actionModified = true;
            state.oldActionExists = context.$form[0].hasAttribute('action');
            state.oldAction = context.$form.attr('action');
            context.$form.attr('action', context.action);
        }

        return state;
    }

    function createActionForm(context)
    {
        var action = context.isValidAction ? context.action : pub.getCurrentUrl();
        var method = context.method;
        var $form = $('<form/>', {method: method, action: action});
        var target = context.$e.attr('target');
        if (target) {
            $form.attr('target', target);
        }

        method = normalizeSubmitMethod($form, method);
        appendCsrfField($form, method);
        $form.hide().appendTo('body');

        return $form;
    }

    function normalizeSubmitMethod($form, method, markAsActionHelper)
    {
        if (/(get|post)/i.test(method)) {
            return method;
        }

        appendHiddenInput($form, '_method', method, markAsActionHelper);
        $form.attr('method', 'post');

        return 'post';
    }

    function appendCsrfField($form, method, markAsActionHelper)
    {
        if (!/post/i.test(method)) {
            return;
        }

        var csrfParam = pub.getCsrfParam();
        if (csrfParam) {
            appendHiddenInput($form, csrfParam, pub.getCsrfToken(), markAsActionHelper);
        }
    }

    function setActiveFormSubmitObject($form, $e)
    {
        var activeFormData = $form.data('yiiActiveForm');
        if (activeFormData) {
            // Remember the element triggered the form submission. This is used by yii.activeForm.js.
            activeFormData.submitObject = $e;
        }
    }

    function appendActionParams($form, params, areValidParams)
    {
        if (!areValidParams) {
            return;
        }

        $.each(params, function (name, value) {
            appendHiddenInput($form, name, value, true);
        });
    }

    function submitActionForm($form, usePjax, pjaxOptions)
    {
        if (usePjax) {
            $form.off('submit.yiiPjaxSubmit').on('submit.yiiPjaxSubmit', function (e) {
                $.pjax.submit(e, pjaxOptions);
            });
        }

        $form.trigger('submit');
    }

    function restoreActionForm($form, formState)
    {
        $.when($form.data('yiiSubmitFinalizePromise')).done(function () {
            $form.off('submit.yiiPjaxSubmit');
            if (formState.newForm) {
                $form.remove();

                return;
            }

            if (formState.actionModified) {
                if (formState.oldActionExists) {
                    $form.attr('action', formState.oldAction);
                } else {
                    $form.removeAttr('action');
                }
            }
            $form.attr('method', formState.oldMethod);
            removeActionParams($form);
        });
    }

    function removeActionParams($form)
    {
        $('input[type="hidden"][' + actionHelperAttribute + '="true"]', $form).remove();
    }

    function appendHiddenInput($form, name, value, markAsActionHelper)
    {
        var attributes = {name: name, value: value, type: 'hidden'};
        if (markAsActionHelper) {
            attributes[actionHelperAttribute] = 'true';
        }

        $form.append($('<input/>').attr(attributes));
    }

    function getQueryString(url)
    {
        var querySeparatorPosition = url.indexOf('?');
        if (querySeparatorPosition < 0) {
            return null;
        }

        return url.substring(querySeparatorPosition + 1).split('#')[0];
    }

    function appendQueryParam(params, pair)
    {
        var pairSeparatorPosition = pair.indexOf('=');
        var name = decodeURIComponent(getQueryParamName(pair, pairSeparatorPosition).replace(/\+/g, '%20'));
        if (!name.length) {
            return;
        }

        var value = getQueryParamValue(pair, pairSeparatorPosition);
        setQueryParamValue(params, name, value);
    }

    function getQueryParamName(pair, pairSeparatorPosition)
    {
        return pairSeparatorPosition < 0 ? pair : pair.substring(0, pairSeparatorPosition);
    }

    function getQueryParamValue(pair, pairSeparatorPosition)
    {
        return pairSeparatorPosition < 0 ? '' : decodeURIComponent(pair.substring(pairSeparatorPosition + 1).replace(/\+/g, '%20'));
    }

    function setQueryParamValue(params, name, value)
    {
        if (params[name] === undefined) {
            params[name] = value || '';

            return;
        }

        appendQueryParamValue(params, name, value);
    }

    function appendQueryParamValue(params, name, value)
    {
        if (!$.isArray(params[name])) {
            params[name] = [params[name]];
        }

        params[name].push(value || '');
    }

    function initCsrfHandler()
    {
        // automatically send CSRF token for all AJAX requests
        $.ajaxPrefilter(function (options, originalOptions, xhr) {
            if (!options.crossDomain && pub.getCsrfParam()) {
                xhr.setRequestHeader('X-CSRF-Token', pub.getCsrfToken());
            }
        });
        pub.refreshCsrfToken();
    }

    function initRedirectHandler()
    {
        // handle AJAX redirection
        $(document).ajaxComplete(function (event, xhr) {
            var url = xhr && xhr.getResponseHeader('X-Redirect');
            if (url) {
                window.location.assign(url);
            }
        });
    }

    function initAssetFilters()
    {
        /**
         * Used for storing loaded scripts and information about loading each script if it's in the process of loading.
         * A single script can have one of the following values:
         *
         * - `undefined` - script was not loaded at all before or was loaded with error last time.
         * - `true` (boolean) -  script was successfully loaded.
         * - object - script is currently loading.
         *
         * In case of a value being an object the properties are:
         * - `xhrList` - represents a queue of XHR requests sent to the same URL (related with this script) in the same
         * small period of time.
         * - `xhrDone` - boolean, acts like a locking mechanism. When one of the XHR requests in the queue is
         * successfully completed, it will abort the rest of concurrent requests to the same URL until cleanup is done
         * to prevent possible errors and race conditions.
         * @type {{}}
         */
        var loadedScripts = {};

        $('script[src]').each(function () {
            var url = getAbsoluteUrl(this.src);
            loadedScripts[url] = true;
        });

        $.ajaxPrefilter('script', function (options, originalOptions, xhr) {
            handleScriptAjaxPrefilter(options, xhr, loadedScripts);
        });

        $(document).ajaxComplete(function () {
            var styleSheets = [];
            $('link[rel=stylesheet]').each(function () {
                var url = getAbsoluteUrl(this.href);
                if (isReloadableAsset(url)) {
                    return;
                }

                if ($.inArray(url, styleSheets) === -1) {
                    styleSheets.push(url);
                } else {
                    $(this).remove();
                }
            });
        });
    }

    function initDataMethods()
    {
        var handler = function (event) {
            var actionData = getDataMethodActionData($(this));
            if (shouldSkipDataMethod(actionData)) {
                return true;
            }

            executeDataMethodAction(this, actionData, event);
            event.stopImmediatePropagation();

            return false;
        };

        // handle data-confirm and data-method for clickable and changeable elements
        $(document).on('click.yii', pub.clickableSelector, handler)
            .on('change.yii', pub.changeableSelector, handler);
    }

    function handleScriptAjaxPrefilter(options, xhr, loadedScripts)
    {
        if (options.dataType === 'jsonp') {
            return;
        }

        var url = getAbsoluteUrl(options.url);
        if (shouldAbortScriptRequest(loadedScripts, url)) {
            xhr.abort();

            return;
        }

        ensureScriptRequestData(loadedScripts, url);
        attachScriptRequestHandlers(xhr, loadedScripts);
        registerScriptRequest(xhr, loadedScripts, url);
    }

    function shouldAbortScriptRequest(loadedScripts, url)
    {
        var forbiddenRepeatedLoad = loadedScripts[url] === true && !isReloadableAsset(url);
        var cleanupRunning = loadedScripts[url] !== undefined && loadedScripts[url]['xhrDone'] === true;

        return forbiddenRepeatedLoad || cleanupRunning;
    }

    function ensureScriptRequestData(loadedScripts, url)
    {
        if (loadedScripts[url] === undefined || loadedScripts[url] === true) {
            loadedScripts[url] = {
                xhrList: [],
                xhrDone: false
            };
        }
    }

    function attachScriptRequestHandlers(xhr, loadedScripts)
    {
        xhr.done(function (data, textStatus, jqXHR) {
            var scriptData = loadedScripts[jqXHR.yiiUrl];
            // If multiple requests were successfully loaded, perform cleanup only once
            if (shouldSkipScriptSuccessCleanup(scriptData)) {
                return;
            }

            scriptData.xhrDone = true;
            abortPendingScriptRequests(scriptData.xhrList);
            loadedScripts[jqXHR.yiiUrl] = true;
        }).fail(function (jqXHR, textStatus) {
            if (textStatus === 'abort') {
                return;
            }

            var scriptData = loadedScripts[jqXHR.yiiUrl];
            if (!hasScriptRequestList(scriptData)) {
                return;
            }

            delete scriptData.xhrList[jqXHR.yiiIndex];
            if (areAllScriptRequestsFailed(scriptData.xhrList)) {
                delete loadedScripts[jqXHR.yiiUrl];
            }
        });
    }

    function shouldSkipScriptSuccessCleanup(scriptData)
    {
        return scriptData === true || !scriptData || scriptData.xhrDone === true;
    }

    function hasScriptRequestList(scriptData)
    {
        return !!scriptData && scriptData !== true && typeof scriptData === 'object' && !!scriptData.xhrList;
    }

    function abortPendingScriptRequests(xhrList)
    {
        for (var i = 0, len = xhrList.length; i < len; i++) {
            var singleXhr = xhrList[i];
            if (singleXhr && singleXhr.readyState !== XMLHttpRequest.DONE) {
                singleXhr.abort();
            }
        }
    }

    function areAllScriptRequestsFailed(xhrList)
    {
        for (var i = 0, len = xhrList.length; i < len; i++) {
            if (xhrList[i]) {
                return false;
            }
        }

        return true;
    }

    function registerScriptRequest(xhr, loadedScripts, url)
    {
        // Use prefix for custom XHR properties to avoid possible conflicts with existing properties
        xhr.yiiIndex = loadedScripts[url]['xhrList'].length;
        xhr.yiiUrl = url;

        loadedScripts[url]['xhrList'][xhr.yiiIndex] = xhr;
    }

    function getDataMethodActionData($element)
    {
        return {
            $element: $element,
            method: $element.data('method'),
            message: $element.data('confirm'),
            form: $element.data('form')
        };
    }

    function shouldSkipDataMethod(actionData)
    {
        return actionData.method === undefined && actionData.message === undefined && actionData.form === undefined;
    }

    function executeDataMethodAction(context, actionData, event)
    {
        if (isConfirmationMessageEnabled(actionData.message)) {
            $.proxy(pub.confirm, context)(actionData.message, function () {
                pub.handleAction(actionData.$element, event);
            });

            return;
        }

        pub.handleAction(actionData.$element, event);
    }

    function isConfirmationMessageEnabled(message)
    {
        return message !== undefined && message !== false && message !== '';
    }

    function isReloadableAsset(url)
    {
        for (var i = 0; i < pub.reloadableScripts.length; i++) {
            var rule = getAbsoluteUrl(pub.reloadableScripts[i]);
            var match = new RegExp("^" + escapeRegExp(rule).split('\\*').join('.+') + "$").test(url);
            if (match === true) {
                return true;
            }
        }

        return false;
    }

    // https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex/6969486#6969486
    function escapeRegExp(str)
    {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    /**
     * Returns absolute URL based on the given URL
     * @param {string} url Initial URL
     * @returns {string}
     */
    function getAbsoluteUrl(url)
    {
        return url.charAt(0) === '/' ? pub.getBaseCurrentUrl() + url : url;
    }

    return pub;
}(window.jQuery));

window.jQuery(function () {
    window.yii.initModule(window.yii);
});
