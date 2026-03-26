<?php

declare(strict_types=1);

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yiiunit\framework\helpers\providers;

/**
 * Data provider for {@see \yiiunit\framework\helpers\HtmlFormTest} test cases.
 *
 * Provides representative input/output pairs for form-related HTML helper methods.
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */
final class HtmlFormProvider
{
    /**
     * @phpstan-return array<string, array{string, string}>
     */
    public static function beginFormSimulateViaPost(): array
    {
        return [
            'DELETE simulated via POST' => [
                <<<HTML
                <form action="/foo" method="post">%A<input type="hidden" name="_method" value="DELETE">
                HTML,
                'DELETE',
            ],
            'GET passthrough' => [
                <<<HTML
                <form action="/foo" method="GET">
                HTML,
                'GET',
            ],
            'GETFOO simulated via POST' => [
                <<<HTML
                <form action="/foo" method="post">%A<input type="hidden" name="_method" value="GETFOO">
                HTML,
                'GETFOO',
            ],
            'POST passthrough' => [
                <<<HTML
                <form action="/foo" method="POST">
                HTML,
                'POST',
            ],
            'POSTFOO simulated via POST' => [
                <<<HTML
                <form action="/foo" method="post">%A<input type="hidden" name="_method" value="POSTFOO">
                HTML,
                'POSTFOO',
            ],
            'POSTFOOPOST simulated via POST' => [
                <<<HTML
                <form action="/foo" method="post">%A<input type="hidden" name="_method" value="POSTFOOPOST">
                HTML,
                'POSTFOOPOST',
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, string}>
     */
    public static function beginForm(): array
    {
        return [
            'default action and method' => [
                <<<HTML
                <form action="/test" method="post">
                HTML,
                '',
                'post',
            ],
            'explicit action and GET method' => [
                <<<HTML
                <form action="/example" method="get">
                HTML,
                '/example',
                'get',
            ],
            'GET with query parameters preserved' => [
                <<<HTML
                <form action="/example" method="get">
                <input type="hidden" name="id" value="1">
                <input type="hidden" name="title" value="&lt;">
                HTML,
                '/example?id=1&title=%3C',
                'get',
            ],
            'GET with empty query parameter' => [
                <<<HTML
                <form action="/foo" method="GET">
                <input type="hidden" name="p" value="">
                HTML,
                '/foo?p',
                'GET',
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, string|null}>
     */
    public static function a(): array
    {
        return [
            'empty URL' => [
                <<<HTML
                <a href="/test">something</a>
                HTML,
                'something',
                '',
            ],
            'internationalized domain URL' => [
                <<<HTML
                <a href="http://www.быстроном.рф">http://www.быстроном.рф</a>
                HTML,
                'http://www.быстроном.рф',
                'http://www.быстроном.рф',
            ],
            'relative URL' => [
                <<<HTML
                <a href="/example">something</a>
                HTML,
                'something',
                '/example',
            ],
            'without URL' => [
                <<<HTML
                <a>something<></a>
                HTML,
                'something<>',
                null,
            ],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string, array<string, mixed>}>
     */
    public static function img(): array
    {
        return [
            'custom alt and width' => [
                <<<HTML
                <img src="/example" width="10" alt="something">
                HTML,
                '/example',
                [
                    'alt' => 'something',
                    'width' => 10,
                ],
            ],
            'empty src' => [
                <<<HTML
                <img src="/test" alt="">
                HTML,
                '',
                [],
            ],
            'empty srcset array' => [
                <<<HTML
                <img src="/base-url" srcset="" alt="">
                HTML,
                '/base-url',
                ['srcset' => []],
            ],
            'fractional pixel density descriptors' => [
                <<<HTML
                <img src="/base-url" srcset="/example-1.42x 1.42x,/example-2.0x 2.0x,/example-3.99999x 3.99999x" alt="">
                HTML,
                '/base-url',
                [
                    'srcset' => [
                        '1.42x' => '/example-1.42x',
                        '2.0x' => '/example-2.0x',
                        '3.99999x' => '/example-3.99999x',
                    ],
                ],
            ],
            'multiple srcset pixel density descriptors' => [
                <<<HTML
                <img src="/base-url" srcset="/example-1x 1x,/example-2x 2x,/example-3x 3x,/example-4x 4x,/example-5x 5x" alt="">
                HTML,
                '/base-url',
                [
                    'srcset' => [
                        '1x' => '/example-1x',
                        '2x' => '/example-2x',
                        '3x' => '/example-3x',
                        '4x' => '/example-4x',
                        '5x' => '/example-5x',
                    ],
                ],
            ],
            'multiple srcset width descriptors' => [
                <<<HTML
                <img src="/base-url" srcset="/example-100w 100w,/example-500w 500w,/example-1500w 1500w" alt="">
                HTML,
                '/base-url',
                [
                    'srcset' => [
                        '100w' => '/example-100w',
                        '500w' => '/example-500w',
                        '1500w' => '/example-1500w',
                    ],
                ],
            ],
            'simple src' => [
                <<<HTML
                <img src="/example" alt="">
                HTML,
                '/example',
                [],
            ],
            'single srcset width descriptor' => [
                <<<HTML
                <img src="/base-url" srcset="/example-9001w 9001w" alt="">
                HTML,
                '/base-url',
                ['srcset' => ['9001w' => '/example-9001w']],
            ],
            'srcset as string' => [
                <<<HTML
                <img src="/base-url" srcset="/example-1x 1x,/example-2x 2x,/example-3x 3x" alt="">
                HTML,
                '/base-url',
                ['srcset' => '/example-1x 1x,/example-2x 2x,/example-3x 3x'],
            ],
        ];
    }
}
