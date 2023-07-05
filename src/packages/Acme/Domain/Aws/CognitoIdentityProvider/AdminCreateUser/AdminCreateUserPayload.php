<?php

declare(strict_types=1);

namespace Acme\Domain\Aws\CognitoIdentityProvider\AdminCreateUser;

use Acme\Domain\Aws\CognitoType\AttributeType;

final readonly class AdminCreateUserPayload
{
    /**
     * @param string $userPoolId 必須
     * @param string $username 必須。ユーザー名。ユーザープール内で一意。1 ～ 128 文字の UTF-8 文字列。ユーザーの作成後は、ユーザー名を変更できません。
     * @param string $messageAction RESEND or SUPPRESS
     * @param string|null $temporaryPassword 任意 ユーザーの一時パスワード。Cognitoのパスワードポリシーに準拠する。指定しない場合はCognito側で自動生成する
     * @param bool $forceAliasCreation 電話番号または電子メールアドレスが別ユーザーとして存在する場合に強制的に移行するか、エラーさせるか。デフォルトは false
     * @param array $clientMetadata AdminCreateUser APIがトリガーするカスタムワークフローの入力として提供する
     * @param array $desiredDeliveryMediums SMS or EMAIL ウェルカムメッセージの送信先を指定する。複数の値を指定可能、デフォルトは SMS
     * @param AttributeType $userAttributes ユーザー属性やカスタム属性を設定する
     * @param array $validationData ユーザーの検証データ
     */
    private function __construct(
        public string $userPoolId,
        public string $username,
        public string $messageAction,
        public ?string $temporaryPassword,
        public bool $forceAliasCreation,
        public array $clientMetadata,
        public array $desiredDeliveryMediums,
        public AttributeType $userAttributes,
        public array $validationData,
    ) {
    }

    /**
     * @return static
     */
    public static function create(
        string $username,
        string $email,
    ): self {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
            'SUPPRESS',
            null,
            false,
            [],
            [],
            AttributeType::createForHash(['email' => $email]),
            [],
        );
    }

    /**
     * @return static
     */
    public static function createForTemporaryPassword(
        string $username,
        string $temporaryPassword,
        string $email,
    ): self {
        return new self(
            config('services.cognito.user_pool_id'),
            $username,
            'SUPPRESS',
            $temporaryPassword,
            false,
            [],
            [],
            AttributeType::createForHash(['email' => $email]),
            [],
        );
    }
}
