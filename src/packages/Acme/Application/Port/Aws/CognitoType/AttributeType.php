<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws\CognitoType;

use InvalidArgumentException;

/**
 * Specifies whether the attribute is standard or custom.
 *
 * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-cognito-idp-2016-04-18.html#shape-attributetype
 * @link https://docs.aws.amazon.com/ja_jp/cognito/latest/developerguide/user-pool-settings-attributes.html
 */
final readonly class AttributeType
{
    /**
     * カスタム属性
     */
    private const CUSTOM_ATTRIBUTES = [];

    public function __construct(public array $attributes)
    {
    }

    /**
     * @param array $attributes [['Name' => 'xxx', 'Value' => 'yyy']]
     * @return static
     */
    public static function create(array $attributes): self
    {
        foreach ($attributes as $i => $attribute) {
            if (! isset($attribute['Name'])) {
                throw new InvalidArgumentException('Name は必須の属性です。');
            }

            if (! is_string($attribute['Name'])) {
                throw new InvalidArgumentException('Name は文字列を指定してください。');
            }

            if (isset($attribute['Value']) && ! is_string($attribute['Value'])) {
                throw new InvalidArgumentException('Value は文字列を指定してください。');
            }

            if (self::isCustomAttributes($attribute['Name'])) {
                $attributes[$i] = [
                    'Name' => 'custom:' . $attribute['Name'],
                    'Value' => $attribute['Value'],
                ];
            }
        }

        return new self($attributes);
    }

    /**
     * @param array $hashAttributes ['xxx' => 'yyy']
     * @return static
     */
    public static function createForHash(array $hashAttributes): self
    {
        $attributes = [];

        foreach ($hashAttributes as $name => $value) {
            if (self::isCustomAttributes($name)) {
                $attributes[] = [
                    'Name' => 'custom:' . $name,
                    'Value' => $value,
                ];
            } else {
                $attributes[] = ['Name' => $name, 'Value' => $value];
            }
        }

        return new self($attributes);
    }

    private static function isCustomAttributes(string $key): bool
    {
        return in_array($key, self::CUSTOM_ATTRIBUTES, true);
    }

    public function get(string $key): ?string
    {
        foreach ($this->attributes as $attribute) {
            if ($key === $attribute['Name'] || 'custom:' . $key === $attribute['Name']) {
                return $attribute['Value'];
            }
        }

        return null;
    }

    public function toHash(): array
    {
        $attributes = [];

        foreach ($this->attributes as $attribute) {
            $attributes[$attribute['Name']] = $attribute['Value'];
        }

        return $attributes;
    }
}
