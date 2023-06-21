<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /**
     * Aws Cognito
     */
    'cognito' => [
        /**
         * AWS Cognito に実際に接続するか(ローカルでCognitoを動作させたくない時に無効化する)
         */
        'enabled' => env('AWS_COGNITO_ENABLED', false),

        /**
         * Aws Cognito Use IAM Auth
         */
        'use_iam_auth' => env('AWS_COGNITO_USE_IAM_AUTH', false),

        /**
         * Aws Cognito Credentials
         */
        'key' => env('AWS_COGNITO_ACCESS_KEY_ID'),
        'secret' => env('AWS_COGNITO_SECRET_ACCESS_KEY'),
        'region' => env('AWS_COGNITO_DEFAULT_REGION', 'ap-northeast-1'),

        /**
         * Aws Cognito Identity Provider Version
         *
         * https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.CognitoIdentityProvider.CognitoIdentityProviderClient.html
         * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html
         */
        'version' => '2016-04-18',

        /**
         * Aws Cognito Http
         *
         * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html#config-http
         */
        'http' => [
            /**
             * 接続タイムアウト時間 (秒)
             * 未設定時: 0 (無期限)
             *
             * ネットワークの問題によりサーバーとの通信ができない場合に、エラーになるまでの時間。
             * http.timeout より大きな値を指定することはできない。
             */
            'connect_timeout' => env('AWS_COGNITO_HTTP_CONNECT_TIMEOUT', 5),

            /**
             * タイムアウト時間 (秒)
             * 未設定時: 0 (無期限)
             *
             * リクエストを開始してからこの時間経過するまでにレスポンスが受信が完了しなければエラーとする。
             */
            'timeout' => env('AWS_COGNITO_HTTP_TIMEOUT', 10),
        ],

        /**
         * Aws Cognito User Pool ID
         *
         * https://docs.aws.amazon.com/ja_jp/cognito/latest/developerguide/cognito-user-identity-pools.html
         */
        'user_pool_id' => env('AWS_COGNITO_USER_POOL_ID', 'ap-northeast-1_*********'),

        /**
         * Aws Cognito App Client ID
         *
         * https://docs.aws.amazon.com/ja_jp/cognito/latest/developerguide/user-pool-settings-client-apps.html
         */
        'app_client_id' => env('AWS_COGNITO_APP_CLIENT_ID', '*********************'),

        /**
         * Aws Cognito App Client Secret
         */
        'app_client_secret' => env('AWS_COGNITO_APP_CLIENT_SECRET', '*********************************'),
    ],

];
