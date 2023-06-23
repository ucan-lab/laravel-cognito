<?php

declare(strict_types=1);

namespace App\Aws\CognitoType;

final readonly class UserMFASettingList
{
    private function __construct(public array $attributes)
    {
    }

    /**
     * @return static
     */
    public static function create(array $attributes): self
    {
        return new self(array_map(static fn ($attribute) => UserMFASetting::from($attribute), $attributes));
    }

    /**
     * 多要素認証登録済みか
     */
    public function enabledMfa(): bool
    {
        return $this->attributes === [];
    }

    /**
     * TOTP認証が有効か
     */
    public function enabledTotp(): bool
    {
        return in_array(UserMFASetting::SOFTWARE_TOKEN_MFA, $this->attributes, true);
    }

    /**
     * SMS認証が有効か
     */
    public function enabledSms(): bool
    {
        return in_array(UserMFASetting::SMS_MFA, $this->attributes, true);
    }
}
