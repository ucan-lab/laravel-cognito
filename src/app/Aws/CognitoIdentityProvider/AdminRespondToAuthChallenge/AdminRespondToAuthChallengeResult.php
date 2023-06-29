<?php

declare(strict_types=1);

namespace App\Aws\CognitoIdentityProvider\AdminRespondToAuthChallenge;

use App\Aws\CognitoType\ChallengeName;
use Aws\Result;

final readonly class AdminRespondToAuthChallengeResult
{
    private function __construct(
        public string $session,
        public ?ChallengeName $challengeName,
        public array $challengeParameters,
        public string $accessToken,
        public int $expiresIn,
        public string $tokenType,
        public string $refreshToken,
        public string $idToken,
        public array $newDeviceMetadata,
    ) {
    }

    /**
     * @return static
     */
    public static function createForAws(Result $result): self
    {
        $authenticationResult = $result->get('AuthenticationResult');

        return new self(
            $result->get('Session') ?? '',
            $result->get('ChallengeName') ? ChallengeName::from($result->get('ChallengeName')) : null,
            $result->get('ChallengeParameters'),
            $authenticationResult['AccessToken'],
            $authenticationResult['ExpiresIn'],
            $authenticationResult['TokenType'],
            $authenticationResult['RefreshToken'],
            $authenticationResult['IdToken'],
            $authenticationResult['NewDeviceMetadata'] ?? [],
        );
    }

    /**
     * @return static
     */
    public static function createForLocal(array $result): self
    {
        return new self(
            $result['Session'],
            $result['ChallengeName'] ? ChallengeName::from($result['ChallengeName']) : null,
            $result['ChallengeParameters'],
            $result['AccessToken'],
            $result['ExpiresIn'],
            $result['TokenType'],
            $result['RefreshToken'],
            $result['IdToken'],
            $result['NewDeviceMetadata'],
        );
    }
}
