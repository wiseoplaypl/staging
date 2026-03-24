<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */

namespace Tiralineas\HubspotApi;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Client
{
    const CLIENT_UA = 'Tiralineas HubSpot API/2.0.0';
    const API_BASE_URL = 'https://prestasyncro-functions.azurewebsites.net/api/';
    const HS_BASE_URL = 'https://api.hubapi.com';
    const TLAPI_BASE_URL = 'https://prestasyncro-functions.azurewebsites.net';
    /**
     * The http client
     *
     * @var GuzzleHttpClient
     */
    protected $httpClient = null;
    protected $httpHsClient = null;
    protected $logger;
    protected $ps_version = _PS_VERSION_;

    /**
     * Constructor
     * For debugging set $config['debug']to true or set to a PHP stream
     * returned by fopen() to enable debug output with the handler used to send a request.
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->ps_version = _PS_VERSION_;

        $headers['User-Agent'] = self::CLIENT_UA;

        if (!empty($config['headers'])) {
            foreach ($config['headers'] as $k => $h) {
                $headers[$k] = $h;
            }
        }

        $this->httpClient = new GuzzleHttpClient(
            [
                'base_url' => self::API_BASE_URL,
                'defaults' => $config,
                'headers' => $headers,
            ]
        );
        $this->httpHsClient = new GuzzleHttpClient(
            [
                'base_url' => self::HS_BASE_URL,
                'defaults' => $config,
                'headers' => $headers,
            ]
        );
        // create a log channel
        if (!version_compare('1.7.0', $this->ps_version, '>=')) {
            $this->logger = new Logger('Tiralineas_HubspotApi_Client');
        }
    }

    /**
     * Set filename to log this class
     *
     * @param mixed $filename
     * @param int $level
     *
     * @return void
     */
    public function setLogFile($filename, $level = Logger::DEBUG)
    {
        if (!version_compare('1.7.0', $this->ps_version, '>=')) {
            $this->logger->pushHandler(new StreamHandler($filename, $level));
        }
    }

    /**
     * Create a new acces token from code
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     * @param string $code
     *
     * @return void
     */
    public function sendCreateTokenRequest($client_id, $client_secret, $redirect_uri, $code)
    {
        return $this->sendTokenRequest('authorization_code', $client_id, $client_secret, $redirect_uri, $code, null);
    }

    /**
     * REfresh acces token from code
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     * @param string $refresh_token
     *
     * @return void
     */
    public function sendRefreshTokenRequest($client_id, $client_secret, $redirect_uri, $refresh_token)
    {
        return $this->sendTokenRequest('refresh_token', $client_id, $client_secret, $redirect_uri, null, $refresh_token);
    }

    /**
     * @param string $grant_type
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     * @param string $code
     * @param string $refresh_token
     *
     * @return mixed
     */
    public function sendTokenRequest($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token)
    {
        try {
            $params = [
                'grant_type' => $grant_type,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
            ];
            switch ($grant_type) {
                case 'authorization_code':
                    $params['code'] = $code;
                    break;
                case 'refresh_token':
                    $params['refresh_token'] = $refresh_token;
                    break;
            }
            $client = new GuzzleHttpClient(
                [
                    'base_url' => self::TLAPI_BASE_URL,
                    'defaults' => [
                        'headers' => [
                            'User-Agent' => self::CLIENT_UA,
                        ],
                    ],
                ]
            );

            if (version_compare($this->ps_version, '8.0.0', '>=')) {
                $return = $client->request(
                    'POST',
                    self::TLAPI_BASE_URL . '/api/token',
                    [
                        'form_params' => $params,
                    ]
                );
            } else {
                return $client->post(
                    '/api/token',
                    [
                        'body' => $params,
                    ]
                );
            }

            return $return;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error: ' . $e->getMessage());
            }
        }
    }

    public function sendGetTokenMetadataRequest($token)
    {
        try {
            return $this->sendHsRequest('GET', '/oauth/v1/access-tokens/' . $token);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error getting token metadata:' . $e->getMessage());
            }
        }
    }

    public function sendGetRefreshTokenMetadataRequest($refresh_token)
    {
        try {
            return $this->sendHsRequest('GET', '/oauth/v1/refresh-tokens/' . $refresh_token);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error getting token metadata:' . $e->getMessage());
            }
        }
    }

    public function sendGetDealsPipelinesRequest()
    {
        try {
            return $this->sendHsRequest('GET', '/crm-pipelines/v1/pipelines/deals');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error getting deals pipelines:' . $e->getMessage());
            }
        }
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $admin_url
     *
     * @return true
     */
    public function sendCreateStoreRequest($id, $label, $admin_url)
    {
        try {
            $response = $this->sendPsRequest(
                'PUT',
                'extensions/ecomm/v2/stores',
                [
                    'json' => [
                        'id' => $id,
                        'label' => $label,
                        'adminUri' => str_replace('_', '', $admin_url),
                    ],
                ]
            );

            $json = (array) json_decode($response->getBody());

            return $json;
            // return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating Store: ' . $e->getMessage());
            }
        }
    }

    public function getStoresRequest()
    {
        try {
            $response = $this->sendPsRequest(
                'GET',
                'extensions/ecomm/v2/stores'
            );
            $json = (array) json_decode($response->getBody());

            return $json;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating Store: ' . $e->getMessage());
            }
        }

        return null;
    }

    public function sendCreatePropertyRequest($object_type, $property)
    {
        try {
            $this->sendHsRequest(
                'POST',
                '/crm/v3/properties/' . $object_type,
                [
                    'json' => $property,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 409) {
                // Probably means that already exists
                return $this->sendUpdatePropertyRequest($object_type, $property);
            }
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating property: ' . $e->getMessage());
            }
        }
    }

    public function sendUpdatePropertyRequest($object_type, $property)
    {
        $name = $property['name'];
        unset($property['name']);
        try {
            $this->sendHsRequest(
                'PATCH',
                '/crm/v3/properties/' . $object_type . '/' . $name,
                [
                    'json' => $property,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Updating property: ' . $e->getMessage());
            }
        }
    }

    public function sendCreatePropertyGroupRequest($object_type, $group)
    {
        try {
            $this->sendHsRequest(
                'POST',
                '/crm/v3/properties/' . $object_type . '/groups/',
                [
                    'json' => $group,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 409) {
                // Probably means that already exists
                return true;
            }
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating property: ' . $e->getMessage());
            }
        }
    }

    public function sendCreateListRequest($list)
    {
        try {
            $this->sendHsRequest(
                'POST',
                '/contacts/v1/lists',
                [
                    'json' => $list,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 409) {
                // Probably means that already exists
                return true;
            }
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating list: ' . $e->getMessage());
            }
        }
    }

    public function sendCreateWorkflowRequest($workflow)
    {
        try {
            $this->sendHsRequest(
                'POST',
                '/automation/v3/workflows',
                [
                    'json' => $workflow,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 409) {
                // Probably means that already exists
                return true;
            }
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Creating workflow: ' . $e->getMessage());
            }
        }
    }

    /**
     * @param $email
     *
     * @return int
     */
    private function verifyContactExists($email)
    {
        try {
            $response = $this->sendHsRequest(
                'POST',
                '/crm/v3/objects/contacts/search',
                [
                    'json' => [
                        'filterGroups' => [
                            [
                                'filters' => [
                                    [
                                        'propertyName' => 'email',
                                        'operator' => 'EQ',
                                        'value' => $email,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

            $json = json_decode($response->getBody());

            return isset($json->results[0]) ? (int) $json->results[0]->id : 0;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error searchingcontact by email: ' . $e->getMessage());
            }
        }

        return 0;
    }

    public function getApiPropertyNames($objectType)
    {
        try {
            return array_map(
                function ($item) {
                    return $item->name;
                },
                json_decode($this->getApiProperties($objectType))
            );
        } catch (\Exception $ex) {
            return [];
        }
    }

    public function getApiProperties($objectType)
    {
        return $this->sendHsRequest('GET', '/properties/v2/' . \Tools::strtolower($objectType) . 's/properties')->getBody();
    }

    /**
     * @param $props array
     *
     * @return int|null
     */
    public function upsertMessages($storeId, $objectType, $messages)
    {
        try {
            $module = \Module::getInstanceByName('pshubspot');
            $messages = self::removeNulls($messages);
            $payload = [
                'storeId' => $storeId,
                'objectType' => $objectType,
                'PS_version' => $this->ps_version,
                'PS_Hubspot_version' => $module->version,
                'messages' => $messages,
            ];
            if (!empty($messages)) {
                $this->sendPsRequest(
                    'PUT',
                    'extensions/ecomm/v2/sync/messages',
                    [
                        'json' => $payload,
                    ]
                );
            }

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync message: ' . $e->getMessage());
            }
        } catch (\Exception $ex) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync message: ' . $ex->getMessage());
            }
        }
    }

    /**
     * @param $props array
     *
     * @return int|null
     */
    public function migrationMessages($storeId, $objectType, $messages)
    {
        try {
            $messages = self::removeNulls($messages);
            $payload = [
                'storeId' => $storeId,
                'objectType' => $objectType,
                'messages' => $messages,
            ];

            $this->sendPsRequest(
                'PUT',
                'migrate',
                [
                    'json' => $payload,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync message: ' . $e->getMessage());
            }
        }
    }

    /**
     * @param $props array
     *
     * @return int|null
     */
    public function migrateProperties($storeId)
    {
        try {
            $payload = [
                'storeId' => $storeId,
            ];

            $this->sendPsRequest(
                'PUT',
                'migrate/properties',
                [
                    'json' => $payload,
                ]
            );

            return true;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync message: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check sync status
     *
     * @param mixed $storeId
     * @param mixed $objectType
     * @param mixed $externalObjectId
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     * @throws RequestException
     */
    public function checkSyncStatus($storeId, $objectType, $externalObjectId)
    {
        try {
            $res = $this->sendHsRequest('GET', '/extensions/ecomm/v2/sync/status/' . $storeId . '/' . $objectType . '/' . $externalObjectId);

            return json_decode($res->getBody());
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 400) {
                if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                    $this->logger->info('Checking status probably not ready yet: ' . $e->getMessage());
                }
            }
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Checking status: ' . $e->getMessage());
            }
        }
    }

    /**
     * @param $props array
     *
     * @return int|null
     */
    public function upsertContact($props)
    {
        try {
            $res = $this->sendHsRequest(
                'POST',
                '/contacts/v1/contact/createOrUpdate/email/' . $props['email'],
                [
                    'json' => ['properties' => $this->explodeAsPropertyArray($props)],
                ]
            );
            $json = json_decode($res->getBody());

            return $json->vid;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync contact: ' . $e->getMessage());
            }
        }
    }

    /**
     * Build prperty array to send as body in upsert request
     *
     * @return void
     */
    private function explodeAsPropertyArray($props)
    {
        $data = [];
        foreach ($props as $property => $value) {
            $data[] = ['property' => $property, 'value' => $value];
        }

        return $data;
    }

    public function getProductApiPropertyNames()
    {
        return array_map(
            function ($item) {
                return $item->name;
            },
            json_decode($this->getProductApiProperties())
        );
    }

    public function getProductApiProperties()
    {
        return $this->sendHsRequest('GET', '/properties/v2/products/properties')->getBody();
    }

    /**
     * @param $email
     *
     * @return int
     */
    private function verifyProductExists($sku)
    {
        try {
            $response = $this->sendHsRequest(
                'POST',
                '/crm/v3/objects/products/search',
                [
                    'json' => [
                        'filterGroups' => [
                            [
                                'filters' => [
                                    [
                                        'propertyName' => 'sku',
                                        'operator' => 'EQ',
                                        'value' => $sku,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );

            return $response->getBody()->results[0]->id ?: 0;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error searching product by sku: ' . $e->getMessage());
            }
        }

        return 0;
    }

    /**
     * @param $props array
     *
     * @return int|null
     */
    public function upsertProduct($props)
    {
        try {
            $product_id = $this->verifyContactExists($props['hs_sku']);
            if ($product_id > 0) {
                $res = $this->sendHsRequest(
                    'PATCH',
                    '/crm/v3/objects/products/{$product_id}',
                    [
                        'json' => ['properties' => $props],
                    ]
                );

                return $product_id;
            } else {
                $res = $this->sendHsRequest(
                    'POST',
                    '/crm/v3/objects/products/',
                    [
                        'json' => ['properties' => $props],
                    ]
                );
                $json = json_decode($res->getBody());

                return $json->vid;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if (!version_compare('1.7.0', $this->ps_version, '>=')) {
                $this->logger->warning('Error Sync product: ' . $e->getMessage());
            }
        }
    }

    public static function removeNulls($data)
    {
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                unset($data[$key]);
            } else {
                if (is_array($value)) {
                    $data[$key] = self::removeNulls($value);
                }
            }
        }

        return $data;
    }

    public function sendHsRequest($type, $uri, $data = [])
    {
        if (version_compare($this->ps_version, '8.0.0', '>=')) {
            return $this->httpHsClient->request(
                $type,
                self::HS_BASE_URL . $uri,
                $data
            );
        } else {
            switch ($type) {
                case 'GET':
                    return $this->httpHsClient->get($uri);
                case 'POST':
                    return $this->httpHsClient->post(
                        $uri,
                        $data
                    );
                case 'PATCH':
                    return $this->httpHsClient->patch(
                        $uri,
                        $data
                    );
                default:
                    return '';
            }
        }
    }

    public function sendPsRequest($type, $uri, $data = [])
    {
        if (version_compare($this->ps_version, '8.0.0', '>=')) {
            return $this->httpClient->request(
                $type,
                self::API_BASE_URL . $uri,
                $data
            );
        } else {
            switch ($type) {
                case 'GET':
                    return $this->httpClient->get($uri);
                case 'POST':
                    return $this->httpClient->post(
                        $uri,
                        $data
                    );
                case 'PUT':
                    return $this->httpClient->post(
                        $uri,
                        $data
                    );
                case 'PATCH':
                    return $this->httpClient->patch(
                        $uri,
                        $data
                    );
                default:
                    return '';
            }
        }
    }
}
