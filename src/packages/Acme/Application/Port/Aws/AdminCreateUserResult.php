<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

use Acme\Application\Port\Aws\CognitoType\AttributeType;
use Aws\Result;
use Carbon\Carbon;

final readonly class AdminCreateUserResult implements CognitoResult
{
    private function __construct(
        public AttributeType $attributes,
        public bool $enabled,
        public array $mFAOptions,
        public Carbon $userCreateDate,
        public Carbon $userLastModifiedDate,
        public string $userStatus,
        public string $username,
    ) {
    }

    /**
     * @return static
     */
    public static function createForAws(Result $result): self
    {
        $user = $result->get('User');

        return new self(
            AttributeType::create($user['Attributes']),
            $user['Enabled'],
            $user['MFAOptions'] ?? [],
            Carbon::instance($user['UserCreateDate']),
            Carbon::instance($user['UserLastModifiedDate']),
            $user['UserStatus'],
            $user['Username'],
        );
    }

    /**
     * @return static
     */
    public static function createForMock(array $result): self
    {
        return new self(
            AttributeType::create($result['Attributes']),
            $result['Enabled'],
            $result['MFAOptions'],
            $result['UserCreateDate'],
            $result['UserLastModifiedDate'],
            $result['UserStatus'],
            $result['Username'],
        );
    }
}
