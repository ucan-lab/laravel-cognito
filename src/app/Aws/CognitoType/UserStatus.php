<?php

declare(strict_types=1);

namespace App\Aws\CognitoType;

enum UserStatus: string
{
    case UNCONFIRMED = 'UNCONFIRMED'; // ユーザーは作成されましたが、確認されていません。
    case CONFIRMED = 'CONFIRMED'; // ユーザーが確認されました。
    case ARCHIVED = 'ARCHIVED'; // ユーザーはもうアクティブではありません。
    case UNKNOWN = 'UNKNOWN'; // ユーザーのステータスが不明です。
    case RESET_REQUIRED = 'RESET_REQUIRED'; // ユーザーは確認されていますが、ユーザーはサインインする前にコードを要求し、パスワードをリセットする必要があります。
    case FORCE_CHANGE_PASSWORD = 'FORCE_CHANGE_PASSWORD'; // ユーザーは確認され、一時パスワードを使用してサインインできますが、最初のサインインでは、他の操作を行う前にパスワードを新しい値に変更する必要があります。
}
