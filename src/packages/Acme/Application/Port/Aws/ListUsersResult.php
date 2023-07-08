<?php

declare(strict_types=1);

namespace Acme\Application\Port\Aws;

use Aws\Result;

final readonly class ListUsersResult implements CognitoResult
{
    private function __construct(
        public string $paginationToken,
        public array $usernameList,
    ) {
    }

    /**
     * 件数が多いとメモリ使用量が増大するため最小限使用するパラメータのみとする
     *
     * @return static
     */
    public static function createForAws(Result $result): self
    {
        $usernameList = [];
        foreach ($result->get('Users') as $user) {
            $usernameList[] = $user['Username'];
        }

        return new self(
            $result->get('PaginationToken') ?? '',
            $usernameList,
        );
    }

    /**
     * @return static
     */
    public static function createForLocal(array $result): self
    {
        return new self(
            '',
            [],
        );
    }

    public function hasNext(): bool
    {
        return $this->paginationToken !== '';
    }
}
