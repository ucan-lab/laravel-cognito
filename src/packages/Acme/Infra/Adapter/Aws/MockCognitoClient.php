<?php

declare(strict_types=1);

namespace Acme\Infra\Adapter\Aws;

use Acme\Application\Port\Aws\AdminCreateUserPayload;
use Acme\Application\Port\Aws\AdminCreateUserResult;
use Acme\Application\Port\Aws\AdminDeleteUserPayload;
use Acme\Application\Port\Aws\AdminDeleteUserResult;
use Acme\Application\Port\Aws\AdminGetUserPayload;
use Acme\Application\Port\Aws\AdminGetUserResult;
use Acme\Application\Port\Aws\AdminInitiateAuthPayload;
use Acme\Application\Port\Aws\AdminInitiateAuthResult;
use Acme\Application\Port\Aws\AdminRespondToAuthChallengePayload;
use Acme\Application\Port\Aws\AdminRespondToAuthChallengeResult;
use Acme\Application\Port\Aws\AdminSetUserPasswordPayload;
use Acme\Application\Port\Aws\AdminSetUserPasswordResult;
use Acme\Application\Port\Aws\AdminUserGlobalSignOutPayload;
use Acme\Application\Port\Aws\AdminUserGlobalSignOutResult;
use Acme\Application\Port\Aws\CognitoClient;
use Acme\Application\Port\Aws\ListUsersPayload;
use Acme\Application\Port\Aws\ListUsersResult;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class MockCognitoClient implements CognitoClient
{
    private const FORCE_CHANGE_PASSWORD = '64c3e414-70e7-49ec-8ff9-aee5c930ae79'; // パスワード強制変更

    private const MFA_SETUP = 'cfb98568-0d0a-48e0-a870-81f49af2297c'; // 多要素認証登録

    private const SMS_MFA = '616dbc3a-da9d-4bd7-8564-97ec374a36a6'; // SMS認証

    private const SOFTWARE_TOKEN_MFA = '05a28437-aa96-4171-94fd-21229ac21189'; // TOTP認証
    private array $userList;

    public function __construct()
    {
        $this->userList = [
            self::FORCE_CHANGE_PASSWORD => [
                'Session' => 'AYABeET8VlBlhfXPnp1pshuf6IsAHQABAAdTZXJ2aWNlABBDb2duaXRvVXNlclBvb2xzAAEAB2F3cy1rbXMAUGFybjphd3M6a21zOmFwLW5vcnRoZWFzdC0xOjM0NjM3NzU0NDkyNzprZXkvZDNhY2NlYmQtNTdhOC00NWE0LTk1ZmEtYzc2YzY5ZDIwYTRkALgBAgEAePZZnC4WFmlF02bVD7JImpVw_X4vigfhMFizLpHK-pJkAV6uCVOxGWOdOCoP1CyDtfEAAAB-MHwGCSqGSIb3DQEHBqBvMG0CAQAwaAYJKoZIhvcNAQcBMB4GCWCGSAFlAwQBLjARBAwTPOgurB5yqZuPCI4CARCAO9NEYX5r97-voGpVyyaE5sgZ2VrDoh2EhT2lrGb_a4GUmjuDzRpswkQW2d38obqyPL-8ANsmaRV0icolAgAAAAAMAAAQAAAAAAAAAAAAAAAAAGOVyC09m4FiMxmqRuDOHmL_____AAAAAQAAAAAAAAAAAAAAAQAAANUz9by6N_NvNX45uzXXJ9qzQQhSazcmHhDNwQTSU-pr0NCcasFyckS6XA-zeGzQmH27VHgH2mkZT5SG4Wke63yC26816Mw7w0V7jYywxoA07Gxvlx-yVpVqAKv66Z42dB-YO1Vuchrw8On-FxK2B-z--fxapMZ1NRt-ZjPeNeTFXdTtqx3KCIaD0vVqvB277JkuWIJv58x5G2LqWjGb_d9VP0J1rJSeWpSVCRFHbCnO0q4gEBzwpOV9aw2c_C0IkJfw5Iyb_2xBf21tNCc45_9zurhxFXCvP3R46xTCynUpLgAS6QkQ',
                'ChallengeName' => 'NEW_PASSWORD_REQUIRED',
                'ChallengeParameters' => [
                    'USER_ID_FOR_SRP' => '616dbc3a-da9d-4bd7-8564-97ec374a36a6',
                ],
                'AccessToken' => '',
                'ExpiresIn' => null,
                'IdToken' => '',
                'NewDeviceMetadata' => [],
                'RefreshToken' => '',
                'TokenType' => '',
            ],
            self::MFA_SETUP => [
                'Session' => '',
                'ChallengeName' => '',
                'ChallengeParameters' => [],
                'AccessToken' => 'eyJraWQiOiIwNGx4Y2JhMlN5aUVHekxmekk0STQ5ZTJHY2Eza0o0bVlUR0pRck03Rm9FPSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiI2OTlhN2NiYS02NmExLTQ1MzAtOGU0Yy0wY2MwYmUwNWRlMGQiLCJpc3MiOiJodHRwczpcL1wvY29nbml0by1pZHAuYXAtbm9ydGhlYXN0LTEuYW1hem9uYXdzLmNvbVwvYXAtbm9ydGhlYXN0LTFfMktJZVp0WDJzIiwiY2xpZW50X2lkIjoiN2JlbXYzOHRudTBiNmFvb2EwOGw2bWZic24iLCJvcmlnaW5fanRpIjoiMjQ5NGE2NTYtNzY1Zi00MDc5LTlkZGQtYmM4NDE3MzQ1MWY3IiwiZXZlbnRfaWQiOiIwYWU1Y2NkMy02NGZiLTQ4OGUtYWQ1Zi05Nzc1Y2ZhOTdmMjkiLCJ0b2tlbl91c2UiOiJhY2Nlc3MiLCJzY29wZSI6ImF3cy5jb2duaXRvLnNpZ25pbi51c2VyLmFkbWluIiwiYXV0aF90aW1lIjoxNjgyMjcyNDcxLCJleHAiOjE2ODIyNzYwNzEsImlhdCI6MTY4MjI3MjQ3MSwianRpIjoiYjZlMjBjNTktNmVhMC00NWVlLTllNGItMTBmMDJjNWFkM2Q1IiwidXNlcm5hbWUiOiI2MTZkYmMzYS1kYTlkLTRiZDctODU2NC05N2VjMzc0YTM2YTYifQ.GEgsGLfkfeRYDmrTOqFJP1BJIC0yLdm5lkoN3nmwGCUaKW3wrusoSYhXnAuqWXK7-Dbu2HskzpHrWFm1CuojuYcf5MvJnpG8jTD4gdgb7iv7JdSgmsuI0m7EHb2KsXKCg4YIqz46wCmex6PTE4U8T2YsZ9Ohfdn-fl2Yx1jdR49mY7VNqe0noHT9D7FBw_mVQFahyLmL0r1dd5L226AuFzIsdH6N5cDw7_Pna-4QJLqXyYm2yR4mBmMbkmU9UQVr_VgzxWVD4dIyNpl_A1b7etazBgL7RZO5wSw0Glfbp51T6usRPJUEVDW3P9qpm7KPINIYsackl3cq3R-vZB8leA',
                'ExpiresIn' => 3600,
                'IdToken' => 'eyJraWQiOiJDbUpIVHd0blwvYXhqSkh2SE9STTFJOURVdGhxYit1dllhWXl0TUJYM0dnQT0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiI2OTlhN2NiYS02NmExLTQ1MzAtOGU0Yy0wY2MwYmUwNWRlMGQiLCJjdXN0b206bG9naW5faWQiOiJhZG1pbiIsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5hcC1ub3J0aGVhc3QtMS5hbWF6b25hd3MuY29tXC9hcC1ub3J0aGVhc3QtMV8yS0llWnRYMnMiLCJjb2duaXRvOnVzZXJuYW1lIjoiNjE2ZGJjM2EtZGE5ZC00YmQ3LTg1NjQtOTdlYzM3NGEzNmE2Iiwib3JpZ2luX2p0aSI6IjI0OTRhNjU2LTc2NWYtNDA3OS05ZGRkLWJjODQxNzM0NTFmNyIsImF1ZCI6IjdiZW12Mzh0bnUwYjZhb29hMDhsNm1mYnNuIiwiZXZlbnRfaWQiOiIwYWU1Y2NkMy02NGZiLTQ4OGUtYWQ1Zi05Nzc1Y2ZhOTdmMjkiLCJ0b2tlbl91c2UiOiJpZCIsImF1dGhfdGltZSI6MTY4MjI3MjQ3MSwibmFtZSI6IuOCouODieODn-ODi-OBleOCkyIsImV4cCI6MTY4MjI3NjA3MSwiaWF0IjoxNjgyMjcyNDcxLCJqdGkiOiI1OWJkYWQ1ZC0zNzk4LTQyZGEtYjczOC1iOTUxYzVmOWZiYjMifQ.SrHVciZLfX1rGcdWdlRfHGdB4bUndg2i5TeTP1xEeTv1bSbsvyZAld0hNsHB3O-gqwsID4meJLlrphgaUCXMtCYPDf5YUXkUdcMs_jBqwrfEmDPSrfBS1j9dSXXxbm2dkrkOfmA-5T6LK2Z0pwFS7Ze53Icd_-j2ktQyHaethN8dLFMHU7iX5BikhRsMtpaLOCekrNgwxSSd4jtUyBNNMX51_LL0_KQaZK3KXo5NhHgf8Pa7VpkXFwAbSel5baoIc9wkGgTk43b_NwQ7X3JAqpvaVDxZWkmLL4koj4VAh1ahlB_nvfLos8B2jPnTQBbTcBiczq7E1lTnSzjCIg7O8w',
                'NewDeviceMetadata' => [],
                'RefreshToken' => 'eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.AGqkzyZU5Jv1KODnXWpl2A4arpQppTpT_UceXfIryt1IZGaqMuq6RWBH-g7Ezil97h-fsIOVSGgrw75ToybPkw-fDDSr5D6CAId6_Te3nwWKCJAvoHjqIIJDKg1FZDAqO-pxnEMKcueGw5ACIqVW_ecDRX6yGQ7ZbHrE8NC1ikYrPEjoptjh-KtdUPRzXptLhlNtLV4uduEY6pGHSdkfAFLUD44AVK8gJiKxeXYHZNZiM_Wss-kge6eUDI9YxNhsLUohNgq79lY4urM81vd-_aClVfXQ5Vrv_lOgKJgbT7k1iUFIfBmTe9Fh2Exne6-rdj8k8Y-NwWinSJ1T_WyUSg.MrjGJZ8kk_MvKnwl.8VnkJonuMXR-YArYSTIX6HnXBrD9PEzNsbrWu_lUmficB3VEH76IqH6UkTgbXiiFUDSWRcW0rSIS1AevicHzhx21d4oOGfTPnL3uQmuv6vahl9BdZCDdCSr2vC5KWfy4oMJxL3mAmomO6TtMnA5VWgTLqKgv0yvyrzE-qmh5CPwojclyCZk4Psn9zz1a6WtGO5DLkOFldfO2aN-d9r1hYWopHSyX1ggBHwJs-wHztIPRJj0A5JRWw23e3p24OmAXeDKi51XgpVWBlAHtiR5cgccucKVVqhNBtpnEXe5mf_1gqUIFz-2kVuOBPmMCRW15iH3e7YjD-7E4K7qrRL2mQgF7mJHIVBlg_CohLKTP3fJEEUGZp2bPmVIpibpZzQe7PXWVYH1JrEkkpEFocjg7ihafOLzBR7fKgTQSgcknmOH0zi8YirtFgO4lA4fxROQirgPwG78c1Ggxtg8xQgbIPVM8aNOpgvrAjqg3O51FE4Eny8xSw7Z96IaD0OgUm-MglaTN9vuHU5g5xmzQnuyA7I6UyKxt9ncI8VHjDIxdpthgCCWcMTBPcngN6ZFXM4PiACHHeAhVCUU0TwywDgbETjNm7IFvis-PFXxCS_kJiolxiF5YN7WS1wCmNfzLWTMe3npUuJIpVTlkdKoGpzdtPgD0KaNOvL4eJnHbuFaeeWuuM-LmBrEoXdr84B2RGTgYpE5GpAcmqJ_gmsG1Wu25rVmOUvsTu0qxV284Lw3L76fKZC6F9nCGGVSdq3G80qVO4Cfl9UxPt5poNJnCl8nPEsBn3jBLQ1r5pWFSdylhj81XrJCZtm0kKarh47GuTGxXeAiU9WcA25Kuq0JonxhjKi2uVxdkQX6gU50I34GM6uIWERx4hkx9zqxLttJEjiBCMFzU-zkrNtis2KxMoUZZqCZFJRUDdd1Hcv9dq6l5qAQyPmnTklq9hZXObLd7B70HVKETyq2jykjvIUX1W8NrsoN0sBGDv9OG-Ts9H_14JwnchBguycJdHvtDPhivfsOdPgKsTni3DYsYr4a3nJ3MjwjUJVGez5zoV1cg1F503BcdiOEumNdl2A05pPkBTUik9BvTPzzA76Dl5bJwIPo4HJZPBFXbs0K28qGSJdTWwn44fnQzFSz6uDB_5GujryJIW4iK8FZm0-LHpwFhGYnt0kmpZQu77OEENmEWjEQQ3iW4NRa2WX7H6SpaSovamuq6OHn0TQhDtdx3IzcB-Nd7Ty6S8yXkLShAdk4ryQontGkrP4V86FJ740ik7pjlZMRBvqqFYnh8a1Hfuiz5hEUiLh1ExjoOBNW8sPwTlsUhJcbNk5PZ140KNpVbqs0YE29Y0McvlzA-Ub9Z.Fyj7kDhvLjDMum_faJQ8zA',
                'TokenType' => 'Bearer',
            ],
            self::SMS_MFA => [
                'Session' => 'AYABeC-FzcHcaCXQGLmozjWlABsAHQABAAdTZXJ2aWNlABBDb2duaXRvVXNlclBvb2xzAAEAB2F3cy1rbXMAUGFybjphd3M6a21zOmFwLW5vcnRoZWFzdC0xOjM0NjM3NzU0NDkyNzprZXkvZDNhY2NlYmQtNTdhOC00NWE0LTk1ZmEtYzc2YzY5ZDIwYTRkALgBAgEAePZZnC4WFmlF02bVD7JImpVw_X4vigfhMFizLpHK-pJkAYlhWV5Ie99SOFLjRNrKEXIAAAB-MHwGCSqGSIb3DQEHBqBvMG0CAQAwaAYJKoZIhvcNAQcBMB4GCWCGSAFlAwQBLjARBAxx6oq_mpv4CwC6F-ACARCAO9fwtC0V496BnwjcSVJbvLKIRrW4EzEe6RXQA0fqKCwd_yhAuw0LczVHqzFB9K-Iro8oJ_mT1T-rmHgWAgAAAAAMAAAQAAAAAAAAAAAAAAAAAKm3cgst7mEH0bS1aKrkn2H_____AAAAAQAAAAAAAAAAAAAAAQAAAf8EW8pBFDtwABOartxNv33dguWq-QIBAsrwxpU5XXWq4igj4wIR6PcoysUi0Znlf6AT55wHyZZTGTsrVQOnJh5guhXWQ79AHg8t34XCTQs7CQAD8boEly7u8UeB2EXAszTTGXuVgU8v2-kqln2nHx_adgLjTu-IkRlsHVhWorC9312l-HOMU5pCSS9sGzdbDesCg_3swOmEtek3GX9MkrPpPTFV5-G_6A08mg8HwJYIXoUGpWP8FIH1PZT0HYKKfOE3YaFMqSrHiS-Ro70MdFqG0NcT5v6tkQ3x7c-ji6B2oLjiqqIcjXnfilyVSZ9G1Wm-nVwhP5D-vuUonTMiWW5tLqVRak82alNOazn4CP56KUrmPy0Qlwf-GU-dXV2I0Dwr3Qg7gpKf0Y2VdZ9MEaB-7dkEcf_Vp19Zj0ydSiV809gWK-WJWrl6jHgur4m1VnkDIWekrMRhMsm-1B6AxUmD0sWsSK7TUWfKBq_Gjw1dx-zd-UHp3g3gVytxVaeaXN9h9A9FKd7_Nthas13U6dLdLPuoNd6oNs59DuESK9OPRWuhKXHUu_UFPciBs6Zki_4vWPywYk7aR0Ch8ZI55BPtaQK6p2Vp1MM2vRXsBOHt6rINHmaZvSpcMf4laR-bXFjWUroiitTA160vGJp9rbUWKu-K7xCqfy4FoxsSPl1GFfeQ2Xvu0J-dnroal7CfbA',
                'ChallengeName' => 'SMS_MFA',
                'ChallengeParameters' => [
                    'CODE_DELIVERY_DELIVERY_MEDIUM' => 'SMS',
                    'CODE_DELIVERY_DESTINATION' => '+*********6308',
                    'USER_ID_FOR_SRP' => '76373a0a-e894-4b6a-a247-36bdcabcfb82',
                ],
                'AccessToken' => 'eyJraWQiOiJFNjRWR094Y2dNUTQ5TUhpUGZVSVRLN0d1XC9Sb3RGaTlyVTYwUXFYYitYYz0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJhNTM4Y2Y3OS00OWUwLTQ2NjAtODFhYi1jNGRkMTY1NTIwOWYiLCJpc3MiOiJodHRwczpcL1wvY29nbml0by1pZHAuYXAtbm9ydGhlYXN0LTEuYW1hem9uYXdzLmNvbVwvYXAtbm9ydGhlYXN0LTFfbTBFRFpwUHZhIiwiY2xpZW50X2lkIjoiNW82dmxzcGFubWJwbzQwbzhsbTFzM2oxOCIsIm9yaWdpbl9qdGkiOiJhZmI2MDE3MC0zMjBlLTRhZTctOTEyMS0wMzllZTJhYTFiZDQiLCJldmVudF9pZCI6IjQ1NmQwNWQzLWE0OTAtNGViMi05NWI1LTE2MzgwNDQ2Zjc3YyIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYXdzLmNvZ25pdG8uc2lnbmluLnVzZXIuYWRtaW4iLCJhdXRoX3RpbWUiOjE2Nzg5NzIzMTYsImV4cCI6MTY3ODk3NTkxNiwiaWF0IjoxNjc4OTcyMzE2LCJqdGkiOiIyZmQzMWVkOC0zNTFmLTRkYzgtYjU0ZS02ZDUwZmEzZTg3ZmQiLCJ1c2VybmFtZSI6Ijc2MzczYTBhLWU4OTQtNGI2YS1hMjQ3LTM2YmRjYWJjZmI4MiJ9.n17RGLmcqsyASQodg56aRbgCUX1ZIgCscTMuXih6A1DuN0LEDe17aCLIGjdlnvL9R-k_XvaVvFQh7whCRmbLNq4rZUBzoE2I6RFCMhAkCOrVr1d3wvZoz6v5bZMuFsTaDaASuJu333iAuvsbXu7XjLlSy-xrGRHaWVK6B8AWj45JPCngyvoxgnssFcQUZ3nHCljkyIDL1bdE1sV_CPsKxcOE36GJFjX14-P286UHCVM1ppT9IVMKxYNy1hA6jlOf2-oSP7YzivK1-pbBmAloUwbRGEy7Axued_etEfQy08qzD9seINALS0DGFCW9rb6_bdAUb7C3CXnKMrRg91hueQ',
                'ExpiresIn' => null,
                'IdToken' => '',
                'NewDeviceMetadata' => [],
                'RefreshToken' => '',
                'TokenType' => '',
            ],
            self::SOFTWARE_TOKEN_MFA => [
                'Session' => 'AYABeC-n_kAv3Z2Z5vOAQ5W0GHIAHQABAAdTZXJ2aWNlABBDb2duaXRvVXNlclBvb2xzAAEAB2F3cy1rbXMAUGFybjphd3M6a21zOmFwLW5vcnRoZWFzdC0xOjM0NjM3NzU0NDkyNzprZXkvZDNhY2NlYmQtNTdhOC00NWE0LTk1ZmEtYzc2YzY5ZDIwYTRkALgBAgEAePZZnC4WFmlF02bVD7JImpVw_X4vigfhMFizLpHK-pJkAeAd8uaMEbyTrrfiHRbU0jIAAAB-MHwGCSqGSIb3DQEHBqBvMG0CAQAwaAYJKoZIhvcNAQcBMB4GCWCGSAFlAwQBLjARBAzejyUwS26Dw98J7gICARCAO-XvIlqy19jxGevaqfMzYUCuzSPkE_Hsg2GBf2Z4-bu2qBq_J2r_ZGzCtaJ1z0pwYDkeoyyxOeD1ZLf1AgAAAAAMAAAQAAAAAAAAAAAAAAAAAO_iehXKUci00SDU_GF5AsX_____AAAAAQAAAAAAAAAAAAAAAQAAAQFul8nl5GtL53mPZ9CKdnV8KVYz0A4hD64NyjfI_AcNup5OfcaakK3IkG6gMqNGSra8jxkS4WzFbfLgcnjD-Zdo8b8dLyCBobXvhDmXZJTLw0nK2E9axdDhk9CdjL0c0vrtTXPz8K6IG7EjTuCsnhY5_3BmTjaIwsBUHKbdpKioxkzXC0Nyf53AiSwiqlAtDk5uqeO9OW4XyA3hR7pWz_kEysg5Zjqk-jgW9lL6hmdbDXKvXcKWe041JRmkGy3CBgDDRJKulx66EpQ1Oaf7nRbGCjqSOUKMq9NUg7IP3O4o5qbUmdEw7vGciCK-2V3pjhtX5gqq9m3DVyOXOHdcccq_yMJXfIDvrkvprDLV8SuiZJM',
                'ChallengeName' => 'SOFTWARE_TOKEN_MFA',
                'ChallengeParameters' => [
                    'USER_ID_FOR_SRP' => self::SOFTWARE_TOKEN_MFA,
                ],
                'AccessToken' => '',
                'ExpiresIn' => null,
                'IdToken' => '',
                'NewDeviceMetadata' => [],
                'RefreshToken' => '',
                'TokenType' => '',
            ],
        ];
    }

    public function adminCreateUser(AdminCreateUserPayload $payload): AdminCreateUserResult
    {
        return AdminCreateUserResult::createForMock([
            'Attributes' => [
                [
                    'Name' => 'sub',
                    'Value' => (string) Str::uuid(),
                ],
            ],
            'Enabled' => true,
            'MFAOptions' => [],
            'UserCreateDate' => Carbon::now(),
            'UserLastModifiedDate' => Carbon::now(),
            'UserStatus' => 'FORCE_CHANGE_PASSWORD',
            'Username' => 'mock-user',
        ]);
    }

    public function adminDeleteUser(AdminDeleteUserPayload $payload): AdminDeleteUserResult
    {
        return new AdminDeleteUserResult();
    }

    public function adminGetUser(AdminGetUserPayload $payload): AdminGetUserResult
    {
        return AdminGetUserResult::createForMock([
            'Enabled' => true,
            'MFAOptions' => [],
            'PreferredMfaSetting' => null,
            'UserAttributes' => [
                [
                    'Name' => 'sub',
                    'Value' => (string) Str::uuid(),
                ],
                [
                    'Name' => 'email',
                    'Value' => $payload->username . '@example.com',
                ],
            ],
            'UserCreateDate' => Carbon::now(),
            'UserLastModifiedDate' => Carbon::now(),
            'UserMFASettingList' => [],
            'UserStatus' => 'FORCE_CHANGE_PASSWORD',
            'Username' => $payload->username,
        ]);
    }

    public function adminInitiateAuth(AdminInitiateAuthPayload $payload): AdminInitiateAuthResult
    {
        return AdminInitiateAuthResult::createForLocal($this->userList[$payload->username] ?? $this->userList[self::SMS_MFA]);
    }

    public function adminRespondToAuthChallenge(AdminRespondToAuthChallengePayload $payload): AdminRespondToAuthChallengeResult
    {
        return AdminRespondToAuthChallengeResult::createForLocal([
            'Session' => '',
            'ChallengeName' => '',
            'ChallengeParameters' => [],
            'AccessToken' => 'eyJraWQiOiJFNjRWR094Y2dNUTQ5TUhpUGZVSVRLN0d1XC9Sb3RGaTlyVTYwUXFYYitYYz0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJhNTM4Y2Y3OS00OWUwLTQ2NjAtODFhYi1jNGRkMTY1NTIwOWYiLCJpc3MiOiJodHRwczpcL1wvY29nbml0by1pZHAuYXAtbm9ydGhlYXN0LTEuYW1hem9uYXdzLmNvbVwvYXAtbm9ydGhlYXN0LTFfbTBFRFpwUHZhIiwiY2xpZW50X2lkIjoiNW82dmxzcGFubWJwbzQwbzhsbTFzM2oxOCIsIm9yaWdpbl9qdGkiOiJhZmI2MDE3MC0zMjBlLTRhZTctOTEyMS0wMzllZTJhYTFiZDQiLCJldmVudF9pZCI6IjQ1NmQwNWQzLWE0OTAtNGViMi05NWI1LTE2MzgwNDQ2Zjc3YyIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYXdzLmNvZ25pdG8uc2lnbmluLnVzZXIuYWRtaW4iLCJhdXRoX3RpbWUiOjE2Nzg5NzIzMTYsImV4cCI6MTY3ODk3NTkxNiwiaWF0IjoxNjc4OTcyMzE2LCJqdGkiOiIyZmQzMWVkOC0zNTFmLTRkYzgtYjU0ZS02ZDUwZmEzZTg3ZmQiLCJ1c2VybmFtZSI6Ijc2MzczYTBhLWU4OTQtNGI2YS1hMjQ3LTM2YmRjYWJjZmI4MiJ9.n17RGLmcqsyASQodg56aRbgCUX1ZIgCscTMuXih6A1DuN0LEDe17aCLIGjdlnvL9R-k_XvaVvFQh7whCRmbLNq4rZUBzoE2I6RFCMhAkCOrVr1d3wvZoz6v5bZMuFsTaDaASuJu333iAuvsbXu7XjLlSy-xrGRHaWVK6B8AWj45JPCngyvoxgnssFcQUZ3nHCljkyIDL1bdE1sV_CPsKxcOE36GJFjX14-P286UHCVM1ppT9IVMKxYNy1hA6jlOf2-oSP7YzivK1-pbBmAloUwbRGEy7Axued_etEfQy08qzD9seINALS0DGFCW9rb6_bdAUb7C3CXnKMrRg91hueQ',
            'ExpiresIn' => 3600,
            'TokenType' => 'Bearer',
            'RefreshToken' => 'eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.EIyD-3dd1M51L_oPcK_yQOTMa35YAAjHBSDAX3UVcp4HAW_tiXj1uvE_DdcFVc2v5HJiBop9Sts0zLYTtFuMQS9Mci5hDEd7w0Nc0w_U4S_FxfvjCoSdOZTc793VzSwiEL1CTDH2qTWWMZGSevWdHDF2cyFPBUZOtVaQJJuLvsLFX3HosDvlL9I2iXg-hyKtyxe4nTU1BwNxxveCe32JkvQ423aRsxhp-ndWv3Ei7tBmMzbkPes4QuSTzilHgqrAJ6PRVR9TVKol1o5hDAxg3HCCpTCjta1z-ZQSEh46-YG3Ha05zPX3nEWkhSX8fxAM8JAem0lJ0sWrpDrOtuYhRA.gPxR1BkhYNRg5YhP.XIDCQwInzZi1ZKyC0Vm8wOy69sCIqoMhafAVDM7LW3eCGSLxP6dVp8Mc1smkK7XRQMr01uDg9LHF2RyRrBiZpDear-aM7493-aoLZvht-dHjo3Fer8YCbmy_cbDq3SAP9JZiSBOlsEhK38zPwXllRt1gk6D4RTbokiaFAtdQrOzWqNZBgeUK_FqNS7WUrVIvBHxtDpPXpfWdPbDIsEtaHMP3uOnV3ggjgsMz0NT7I_ufeUhizkIK1DnsIHUEPgf7oLSqp5GmlM_iSIGpLe8mChU1oIhPTKEegOCTb8ofPCIZYdJIajUIsbE_zAPaN-Acrz8h6Qc0T-9GHrGU_KKb71yvf1RIoCKHXbIlbAoiLpBOL9It5evkN52caNKjKyTfhwEy6xS2kgDwDZICEqE5RB2G2s-t79QxBggWjZ2vL3BvNbiogLOK18G0Ho-kqbPeZ5oXj1JAv49ulnrXBYWQc18Fl7MA-UGgxL0IBfJhnlTYQNbu7D4SlKUm_6_DK5nWCy6Mnhn5PSxMeeG0XClZ-yYrDYuNLZVTsp6CcVscstNs4q2sY4zS0stAs_3has6ADXBPRxZclvYP0mKzV9w4k967WO7BuKk0DwOzXukKuUVBsIErBMtAeXzIshRhcqushMObkzmHNvLLa4bn3NiDi50aIzoPGcKMgUXmdwVG1J4lrSdcIDqj6Y284mXBlqyn4Ow64boFnNaTJYJ8p6zILrX71K079az8xkEYFLpbcZkewHSjhuBJ99rsRCoiwYkPhX689N0oyhtMXOCSSumOrESIJT7Dyk4OlTxfrnRrSWfk2Y7Up-E89IbCzffAZfdajXBwqOqw1h5Kf6Y1_3ImbpiMuZMCpOGo_X99daUD58Jppt-RguE32TAPzBi846DzM7XjwjsPKpny5Zd6n4x_67T_kb1wEsKn4VcOt7_R5p8HTuG1P-pA9gMfbSAgHom5agypF4JQcrkXl1uV1rxCcwiMHAl-7gzryEhZG_D_NFX7R9rqNnOsQbRs9HsKh2emrQOI30HJmoij5-ez38fY6DDt_JZWxoe6Q1Dm1fEEx-iwdCa-HmCAIE8Gg1n4EdFkqsW1YGyrvOIcQgW-OoEiWVl5WuD3D9md-7tjxvJmd3e-XEbXGum4zIrn2vHPZaPhdU3ZiCUQUf6jj0B1fTgokhmYwZcLcBIK6LFueHTg84-ItPfDhfZGuWFDkmKkDpSRuEu3uJi06hvGK-afEMQ0PX3nV8d4orqq7uC1EgsDiXEayFhzW0Qwep9TMOUOlVRYWnoxSWU7dx2ESNiJgqpUaquCXMWBtB8yNLE0fxX9ARQ-ANk0TrxMWlFTf9nP5lLJyJZgMPiP0HE.-wPmujzCTaIewtyH1ix1MQ',
            'IdToken' => 'eyJraWQiOiJpTHdnWXN1RU9BV2JrcTZaXC9RTEpJRThud1ZYTXZub0tjU0ZWN3l5SjdCQT0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJhNTM4Y2Y3OS00OWUwLTQ2NjAtODFhYi1jNGRkMTY1NTIwOWYiLCJpc3MiOiJodHRwczpcL1wvY29nbml0by1pZHAuYXAtbm9ydGhlYXN0LTEuYW1hem9uYXdzLmNvbVwvYXAtbm9ydGhlYXN0LTFfbTBFRFpwUHZhIiwicGhvbmVfbnVtYmVyX3ZlcmlmaWVkIjp0cnVlLCJjb2duaXRvOnVzZXJuYW1lIjoiNzYzNzNhMGEtZTg5NC00YjZhLWEyNDctMzZiZGNhYmNmYjgyIiwib3JpZ2luX2p0aSI6ImFmYjYwMTcwLTMyMGUtNGFlNy05MTIxLTAzOWVlMmFhMWJkNCIsImF1ZCI6IjVvNnZsc3Bhbm1icG80MG84bG0xczNqMTgiLCJldmVudF9pZCI6IjQ1NmQwNWQzLWE0OTAtNGViMi05NWI1LTE2MzgwNDQ2Zjc3YyIsInRva2VuX3VzZSI6ImlkIiwiYXV0aF90aW1lIjoxNjc4OTcyMzE2LCJuYW1lIjoi44Ki44OJ44Of44OL44GV44KTIiwicGhvbmVfbnVtYmVyIjoiKzgxMDcwNDQyNzYzMDgiLCJleHAiOjE2Nzg5NzU5MTYsImlhdCI6MTY3ODk3MjMxNiwianRpIjoiODAxZWM0YjAtMDAzZi00YmYzLTljNWItMDM3ZWNmMDcxMDY2In0.SeNc10fWZUKfsUgxFz2FYJxrAa80Za5VxVqKU_JCRB5du1JDy2w_-Xo7CP4wbh02CI2r5mUYSdt9NMVPH3C1PsSkP0uqIV9l2GTyEWJoOZ1LHYdoqhxQT0SBDbrpbZJChtwfXMdKsYgYiPOszG9Wb43TYSZYPhfXtlTaGkstZGM7XdSfMuig09b7lOeL8UVQkyxGqrDeJh1r_2JugMs5n9CT-Rv6tyalRXPU7luhMv3rUwyIjnWt-2Bo4vZhng9UzafP-CEPt588TpCDrh2wuqUgQgZQwj4U81_apaEQgzFpee5j1fMpXePlYeWKrwuBxNDw7vhSwuGR0EPF1Iy4yw',
            'NewDeviceMetadata' => [],
        ]);
    }

    public function adminSetUserPassword(AdminSetUserPasswordPayload $payload): AdminSetUserPasswordResult
    {
        return new AdminSetUserPasswordResult();
    }

    public function adminUserGlobalSignOut(AdminUserGlobalSignOutPayload $payload): AdminUserGlobalSignOutResult
    {
        return new AdminUserGlobalSignOutResult();
    }

    public function listUsers(ListUsersPayload $payload): ListUsersResult
    {
        return ListUsersResult::createForLocal([]);
    }
}
