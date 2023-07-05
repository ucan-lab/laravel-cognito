<?php

declare(strict_types=1);

namespace Acme\Domain\Aws\CognitoIdentityProvider\AdminInitiateAuth;

use Acme\Domain\Aws\CognitoType\ChallengeName;
use Aws\Result;

final readonly class AdminInitiateAuthResult
{
    private function __construct(
        public string $session,
        public ?ChallengeName $challengeName,
        public array $challengeParameters,
        public string $accessToken,
        public ?int $expiresIn,
        public string $idToken,
        public array $newDeviceMetadata,
        public string $refreshToken,
        public string $tokenType,
    ) {
    }

    /**
     * @return static
     */
    public static function createForAws(Result $result): self
    {
        return new self(
            $result->get('Session') ?? '',
            $result->get('ChallengeName') ? ChallengeName::from($result->get('ChallengeName')) : null,
            $result->get('ChallengeParameters') ?? [],
            $result->get('AuthenticationResult')['AccessToken'] ?? '',
            $result->get('AuthenticationResult')['ExpiresIn'] ?? null,
            $result->get('AuthenticationResult')['IdToken'] ?? '',
            $result->get('AuthenticationResult')['NewDeviceMetadata'] ?? [],
            $result->get('AuthenticationResult')['RefreshToken'] ?? '',
            $result->get('AuthenticationResult')['TokenType'] ?? '',
        );
    }

    /**
     * @return static
     */
    public static function createForLocal(array $result): self
    {
        return new self(
            $result['Session'],
            $result['ChallengeName'],
            $result['ChallengeParameters'] ?? [],
            $result['AccessToken'] ?? '',
            $result['ExpiresIn'] ?? null,
            $result['IdToken'] ?? '',
            $result['NewDeviceMetadata'] ?? null,
            $result['RefreshToken'] ?? '',
            $result['TokenType'] ?? '',
        );
    }

    /**
     * 初期パスワードの変更が必要か
     */
    public function isNewPasswordRequired(): bool
    {
        return $this->challengeName === ChallengeName::NEW_PASSWORD_REQUIRED;
    }

    /**
     * SMS認証が有効になっているか
     */
    public function isSmsAuthEnabled(): bool
    {
        return $this->challengeName === ChallengeName::SMS_MFA;
    }

    /**
     * TOTP認証が有効になっているか
     */
    public function isTotpAuthEnabled(): bool
    {
        return $this->challengeName === ChallengeName::SOFTWARE_TOKEN_MFA;
    }

    /**
     * 認証に成功している場合は空です。
     * (MFAが未設定の場合も成功となります)
     */
    public function isSuccessInitiateAuth(): bool
    {
        return $this->challengeName === null; // MFA_SETUP - 多要素認証はオプションなので空文字が来る
    }
}
