<?php

namespace App\Auth\Helpers;

use App\App\ApiRequest;
use App\Auth\Models\User;
use App\Base\Models\File;
use App\System\Exceptions\BusinessException;
use Firebase\JWT\JWT;
use Illuminate\Support\Arr;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class DocuSignService
{
    public static function getToken()
    {
        $iat = \DateTimeImmutable::createFromFormat('U', time());
        $exp = \DateTimeImmutable::createFromFormat('U', time() + 6000);


        $privateKey = file_get_contents(database_path('files/keys/prod/private.pem'));
        $private = InMemory::plainText($privateKey);
        $publicKey = file_get_contents(database_path('files/keys/prod/public.pem'));
        $public = InMemory::plainText($publicKey);
        $config = Configuration::forAsymmetricSigner(
            new Sha256(),
            $private,
            $public
        );

        $token = $config->builder()
            ->withClaim('uid', 1)
            ->issuedAt($iat)
            ->expiresAt($exp)
            ->issuedBy(config('docusign.integrationKey'))
            ->permittedFor(config('docusign.permittedFor'))
            ->withClaim('scope', 'signature impersonation')
            ->relatedTo(config('docusign.userId'))
            ->getToken($config->signer(), $config->signingKey());
        $strToken = (string)$token;
        $api = new ApiRequest(
            [
                'type' => 'form-data',
            ]
        );

        $oauth = $api->post(config('docusign.host') . '/oauth/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $strToken
        ]);
        $accessToken = json_decode($oauth)->access_token;

        return $accessToken;
    }

    public static function sendDoc($accessToken, $userId, $fileId)
    {
        $api = new ApiRequest(
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ]
            ]
        );

        $user = User::query()->find($userId);
        $file = File::query()->find($fileId);
        $ext = explode('.', $file->src);
        $ext = $ext[1];

        $fileContent = base64_encode(file_get_contents(storage_path('app/files/' . $file->src)));

        $data = [
            'emailSubject' => 'Пожалуйста подпишите документ:' . $file->name,
            'documents' => [
                [
                    'documentBase64' => $fileContent,
                    'name' => $file->name,
                    'fileExtension' => $ext,
                    'documentId' => $file->id,
                ]
            ],
            'recipients' => [
                'signers' => [
                    [
                        'email' => $user->email,
                        'name' => $user->fullName,
                        'recipientId' => $user->id,
                        'routingOrder' => '1',
                        'clientUserId' => $user->id,
                        'tabs' => [
                            'signHereTabs' => [
                                [
                                    'anchorString' => '/signature/',
                                    'anchorXOffset' => '0',
                                    'anchorYOffset' => '0',
                                    'anchorUnits' => 'pixels',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'status' => 'sent',
        ];

        $result = $api->post(config('docusign.apiHost') . '/v2.1/accounts/' . config('docusign.apiId') . '/envelopes', $data);


        if (!empty($result['envelopeId'])) {
            $envelopeId = $result['envelopeId'];
        } else {
            throw new BusinessException('Документ не был отправлен');
        }

        return $envelopeId;
    }

    public static function getUrl($user, $doc)
    {

        $accessToken = self::getToken();

        $api = new ApiRequest(
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ]
            ]
        );


        $result = $api->post(config('docusign.apiHost') . '/v2.1/accounts/' . config('docusign.apiId') . '/envelopes/' . $doc->envelopeId . '/views/recipient', [
            'returnUrl' => 'kraftapp:///ordersinformationzakaz?id=1&to=myorders&isSigned=true',
            'authenticationMethod' => 'none',
            'userName' => $user->fullName,
            'email' => $user->email,
            'clientUserId' => $user->id,
        ]);

        if (!Arr::get($result, 'url')) {
            throw new BusinessException('Не удалось создать ссылку для документа');
        }

        return $result['url'];
    }

    public static function getIsSigned($user, $doc)
    {

        $accessToken = self::getToken();

        $api = new ApiRequest(
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ]
            ]
        );

        $result = $api->get(config('docusign.apiHost') . '/v2.1/accounts/' . config('docusign.apiId') . '/envelopes/' . $doc->envelopeId);

        if (!$result) {
            throw new BusinessException('Не удалось узнать статус документа');
        }
        return $result['status'] === 'completed';
    }
}
