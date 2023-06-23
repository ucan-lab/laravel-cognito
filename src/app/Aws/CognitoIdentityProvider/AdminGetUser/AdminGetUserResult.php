<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminGetUser;

use App\Aws\CognitoType\AttributeType;
use App\Aws\CognitoType\UserMFASetting;
use App\Aws\CognitoType\UserMFASettingList;
use App\Aws\CognitoType\UserStatus;
use Aws\Result;
use Carbon\Carbon;

final readonly class AdminGetUserResult
{
    /**
     * @param array $mFAOptions 非推奨オプション。userMFASettingListの方を参照する
     * @param UserMFASetting|null $preferredMfaSetting ユーザーの好みの優先MFA設定
     * @param AttributeType $userAttributes ユーザー属性
     * @param Carbon $userCreateDate ユーザー作成日
     * @param Carbon $userLastModifiedDate ユーザー最終更新日
     * @param UserMFASettingList $userMFASettingList ユーザーMFA設定リスト
     * @param UserStatus $userStatus ユーザーステータス
     */
    private function __construct(
        public bool $enabled,
        public array $mFAOptions,
        public ?UserMFASetting $preferredMfaSetting,
        public AttributeType $userAttributes,
        public Carbon $userCreateDate,
        public Carbon $userLastModifiedDate,
        public UserMFASettingList $userMFASettingList,
        public UserStatus $userStatus,
        public string $username,
    ) {
    }

    public function email(): string
    {
        return $this->userAttributes->get('email');
    }

    /**
     * @return static
     */
    public static function createForAws(Result $result): self
    {
        return new self(
            $result->get('Enabled'),
            $result->get('MFAOptions') ?? [],
            $result->get('PreferredMfaSetting') ? UserMFASetting::from($result->get('PreferredMfaSetting')) : null,
            AttributeType::create($result->get('UserAttributes')),
            Carbon::instance($result->get('UserCreateDate')),
            Carbon::instance($result->get('UserLastModifiedDate')),
            UserMFASettingList::create($result->get('UserMFASettingList') ?? []),
            UserStatus::from($result->get('UserStatus')),
            $result->get('Username'),
        );
    }

    /**
     * @return static
     */
    public static function createForMock(array $result): self
    {
        return new self(
            $result['Enabled'],
            $result['MFAOptions'],
            $result['PreferredMfaSetting'] ? UserMFASetting::from($result['PreferredMfaSetting']) : null,
            AttributeType::create($result['UserAttributes']),
            $result['UserCreateDate'],
            $result['UserLastModifiedDate'],
            UserMFASettingList::create($result['UserMFASettingList'] ?? []),
            UserStatus::from($result['UserStatus']),
            $result['Username'],
        );
    }
}
