<?php

namespace common\components;

use RuntimeException;

class MomentusClient
{
    private $baseUrl;
    private $apiToken;
    private $subscriptionKey;
    private $orgCode;
    private $timeout;

    public function __construct(array $config = [])
    {
        $defaultConfig = [
            'baseUrl' => 'https://api-sandbox.gomomentus.com/enterprise/connect/api',
            'apiToken' => '',
            'subscriptionKey' => '',
            'orgCode' => '10',
            'timeout' => 20,
        ];

        if (class_exists('\Yii', false) && \Yii::$app !== null) {
            $paramsConfig = (array) \Yii::$app->params;
            if (isset($paramsConfig['momentus']) && is_array($paramsConfig['momentus'])) {
                $defaultConfig = array_merge($defaultConfig, $paramsConfig['momentus']);
            }
        }

        $config = array_merge($defaultConfig, $config);

        $this->baseUrl = rtrim((string) $config['baseUrl'], '/');
        $this->apiToken = (string) $config['apiToken'];
        $this->subscriptionKey = (string) $config['subscriptionKey'];
        $this->orgCode = (string) $config['orgCode'];
        $this->timeout = (int) $config['timeout'];
    }

    public function searchSpaces($searchString, $page = null, $pageSize = null, $order = null, $orgCode = null)
    {
        $orgCode = $orgCode !== null && $orgCode !== '' ? (string) $orgCode : $this->orgCode;
        $q = trim((string) $searchString);
        $bookable = "(Bookable eq 'Y')";
        if ($q === '') {
            $search = $bookable;
        } else {
            $text = $this->buildSearchQuery($q, 'SpaceDescription', 'Code');
            $search = '(' . $text . ') and ' . $bookable;
        }
        $params = [
            'search' => $search,
        ];

        if ($page !== null && $page !== '') {
            $params['page'] = (int) $page;
        }
        if ($pageSize !== null && $pageSize !== '') {
            $params['pageSize'] = (int) $pageSize;
        }
        if ($order !== null && $order !== '') {
            $params['order'] = (string) $order;
        }

        return $this->request('GET', '/Spaces/' . $this->normalizeOrgCode($orgCode), $params);
    }

    public function searchResources($searchString, $page = null, $pageSize = null, $order = null, $orgCode = null)
    {
        $orgCode = $orgCode !== null && $orgCode !== '' ? (string) $orgCode : $this->orgCode;

        $filter = $this->buildSearchQuery($searchString, 'ResourceTypeDescription', 'ResourceCodeDescription');

        // The Resources endpoint requires the "search" param to be present.
        // Use the user's search string if provided, otherwise "All" for a full list.
        // Class filtering is applied PHP-side in formatResources().
        $params = [
            'search' => $filter !== '' ? $filter : 'All',
        ];

        return $this->request('GET', '/Resources/' . $this->normalizeOrgCode($orgCode), $params);
    }

    public function searchNotes($odataSearch = '', $page = null, $pageSize = null, $order = null)
    {
        $params = [];

        if ($odataSearch !== null && trim((string) $odataSearch) !== '') {
            $params['search'] = $this->buildSearchNoteOdata($odataSearch);
        }

        if ($page !== null && $page !== '') {
            $params['page'] = (int) $page;
        }
        if ($pageSize !== null && $pageSize !== '') {
            $params['pageSize'] = (int) $pageSize;
        }
        if ($order !== null && $order !== '') {
            $params['order'] = (string) $order;
        }

        return $this->request(
            'GET',
            '/Notes/' . $this->normalizeOrgCode($this->orgCode),
            $params
        );
    }

    public function getNote($type, $code, $sequenceNumber, $orgCode = null)
    {
        return $this->request(
            'GET',
            '/Notes/' . $this->normalizeOrgCode($orgCode) . '/'
            . rawurlencode((string) $type) . '/'
            . rawurlencode((string) $code) . '/'
            . rawurlencode((string) $sequenceNumber)
        );
    }

    public function createNote(array $payload)
    {
        return $this->request('POST', '/Notes', [], $payload);
    }

    public function updateNote($type, $code, $sequenceNumber, array $payload, $orgCode = null)
    {
        return $this->request(
            'PUT',
            '/Notes/' . $this->normalizeOrgCode($orgCode) . '/'
            . rawurlencode((string) $type) . '/'
            . rawurlencode((string) $code) . '/'
            . rawurlencode((string) $sequenceNumber),
            [],
            $payload
        );
    }

    public function deleteNote($type, $code, $sequenceNumber, $orgCode = null)
    {
        $this->request(
            'DELETE',
            '/Notes/' . $this->normalizeOrgCode($orgCode) . '/'
            . rawurlencode((string) $type) . '/'
            . rawurlencode((string) $code) . '/'
            . rawurlencode((string) $sequenceNumber)
        );
    }

    // ---------------------------------------------------------------
    // Service Orders
    // ---------------------------------------------------------------

    /**
     * List service orders using an OData query string.
     * Endpoint: GET /odata/ServiceOrders
     */
    public function listServiceOrders($odataQuery = '')
    {
        $params = [];
        if ($odataQuery !== null && trim((string) $odataQuery) !== '') {
            $params['ODataQuery'] = $odataQuery;
        }
        return $this->request('GET', '/odata/ServiceOrders', $params);
    }

    /**
     * Get a single service order.
     * Endpoint: GET /ServiceOrders/{OrgCode}/{OrderNumber}
     */
    public function getServiceOrder($orgCode, $orderNumber)
    {
        return $this->request(
            'GET',
            '/ServiceOrders/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $orderNumber)
        );
    }

    /**
     * Create a service order.
     * Endpoint: POST /ServiceOrders
     */
    public function addServiceOrder(array $payload)
    {
        return $this->request('POST', '/ServiceOrders', [], $payload);
    }

    /**
     * Update a service order.
     * Endpoint: PUT /ServiceOrders/{OrgCode}/{OrderNumber}
     */
    public function updateServiceOrder($orgCode, $orderNumber, array $payload)
    {
        return $this->request(
            'PUT',
            '/ServiceOrders/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $orderNumber),
            [],
            $payload
        );
    }

    // ---------------------------------------------------------------
    // Service Order Items
    // ---------------------------------------------------------------

    /**
     * List service order items using an OData query string.
     * Endpoint: GET /odata/ServiceOrderItems
     */
    public function listServiceOrderItems($odataQuery = '')
    {
        $params = [];
        if ($odataQuery !== null && trim((string) $odataQuery) !== '') {
            $params['ODataQuery'] = $odataQuery;
        }
        return $this->request('GET', '/odata/ServiceOrderItems', $params);
    }

    /**
     * Get a single service order item.
     * Endpoint: GET /ServiceOrderItems/{OrgCode}/{OrderNumber}/{OrderLineNumber}
     */
    public function getServiceOrderItem($orgCode, $orderNumber, $orderLineNumber)
    {
        return $this->request(
            'GET',
            '/ServiceOrderItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $orderNumber) . '/'
            . rawurlencode((string) $orderLineNumber)
        );
    }

    /**
     * Add an item to an existing service order.
     * Endpoint: POST /ServiceOrderItems
     *
     * Key fields: OrganizationCode, OrderNumber, Event, Function,
     * ResourceCode, Units, PriceListDetailSeqNbr, PriceList, Description, etc.
     */
    public function addServiceOrderItem(array $payload)
    {
        return $this->request('POST', '/ServiceOrderItems', [], $payload);
    }

    /**
     * Edit a service order item.
     * Endpoint: PUT /ServiceOrderItems/{OrgCode}/{OrderNumber}/{OrderLineNumber}
     */
    public function updateServiceOrderItem($orgCode, $orderNumber, $orderLineNumber, array $payload)
    {
        return $this->request(
            'PUT',
            '/ServiceOrderItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $orderNumber) . '/'
            . rawurlencode((string) $orderLineNumber),
            [],
            $payload
        );
    }

    /**
     * Delete a service order item.
     * Endpoint: DELETE /ServiceOrderItems/{OrgCode}/{OrderNumber}/{OrderLineNumber}
     */
    public function deleteServiceOrderItem($orgCode, $orderNumber, $orderLineNumber)
    {
        $this->request(
            'DELETE',
            '/ServiceOrderItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $orderNumber) . '/'
            . rawurlencode((string) $orderLineNumber)
        );
    }

    /**
     * Add a new item to an existing package from the price list.
     * Endpoint: POST /ServiceOrderItems/SaveNewItemToExistingPackage
     *
     * Required: OrganizationCode, OrderNumber, PackageHeaderOrderLineNumber,
     * PriceListDetailSeqNbr, StartDate, EndDate, StartTime, EndTime, Units.
     */
    public function saveNewItemToExistingPackage(array $payload)
    {
        return $this->request('POST', '/ServiceOrderItems/SaveNewItemToExistingPackage', [], $payload);
    }

    // ---------------------------------------------------------------
    // Price List Items
    // ---------------------------------------------------------------

    /**
     * List functions via OData.
     * Endpoint: GET /odata/Functions
     *
     * Typical filter: $filter=OrganizationCode eq '10'
     * Event-specific: $filter=OrganizationCode eq '10' and EventID eq 9427
     */
    public function listFunctions($odataQuery = '', $orgCode = null)
    {
        $orgCode = ($orgCode !== null && $orgCode !== '') ? (string) $orgCode : $this->orgCode;
        if ($odataQuery === '' || $odataQuery === null) {
            $odataQuery = '$filter=OrganizationCode eq \'' . addslashes($orgCode) . '\'';
        }
        $params = ['ODataQuery' => $odataQuery];
        return $this->request('GET', '/odata/Functions', $params);
    }

    /**
     * List all price lists via OData.
     * Endpoint: GET /odata/PriceList
     *
     * Typical filter: $filter=OrganizationCode eq '10'
     */
    public function listPriceLists($odataQuery = '', $orgCode = null)
    {
        $orgCode = ($orgCode !== null && $orgCode !== '') ? (string) $orgCode : $this->orgCode;
        if ($odataQuery === '' || $odataQuery === null) {
            $odataQuery = '$filter=OrganizationCode eq \'' . addslashes($orgCode) . '\'';
        }
        $params = ['ODataQuery' => $odataQuery];
        return $this->request('GET', '/odata/PriceList', $params);
    }

    /**
     * Search price list items using a keyword.
     * Endpoint: GET /PriceListItems/{OrgCode}?search=...
     *
     * NOTE: The search parameter is REQUIRED by the API; calling without
     * it returns HTTP 404. Always pass a non-empty search string.
     */
    public function searchPriceListItems($searchString = '', $orgCode = null)
    {
        $orgCode = ($orgCode !== null && $orgCode !== '') ? (string) $orgCode : $this->orgCode;
        $params = [];
        if ($searchString !== null && trim((string) $searchString) !== '') {
            $params['search'] = $searchString;
        }
        return $this->request('GET', '/PriceListItems/' . $this->normalizeOrgCode($orgCode), $params);
    }

    /**
     * List all price list items for a specific price list code.
     * Endpoint: GET /PriceListItems/{OrgCode}/{PriceList}
     *
     * Falls back to OData if the REST endpoint returns 404.
     */
    public function listPriceListItemsByCode($priceListCode, $orgCode = null)
    {
        $orgCode = ($orgCode !== null && $orgCode !== '') ? (string) $orgCode : $this->orgCode;

        // Approach 1: REST endpoint /PriceListItems/{OrgCode}/{PriceList}
        try {
            $result = $this->request(
                'GET',
                '/PriceListItems/' . $this->normalizeOrgCode($orgCode) . '/'
                . rawurlencode((string) $priceListCode)
            );
            return $result;
        } catch (RuntimeException $e) {
            // If 404, fall through to OData approach.
            if (strpos($e->getMessage(), '404') === false) {
                throw $e;
            }
        }

        // Approach 2: OData endpoint
        $odataQuery = '$filter=PriceList eq \'' . addslashes($priceListCode)
            . '\' and OrganizationCode eq \'' . addslashes($orgCode) . '\'';
        return $this->request('GET', '/odata/PriceListItems', ['ODataQuery' => $odataQuery]);
    }

    /**
     * Get a single price list item.
     * Endpoint: GET /PriceListItems/{OrgCode}/{PriceList}/{Sequence}
     */
    public function getPriceListItem($orgCode, $priceList, $sequence)
    {
        return $this->request(
            'GET',
            '/PriceListItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $priceList) . '/'
            . rawurlencode((string) $sequence)
        );
    }

    /**
     * Add a price list item.
     * Endpoint: POST /PriceListItems
     */
    public function addPriceListItem(array $payload)
    {
        return $this->request('POST', '/PriceListItems', [], $payload);
    }

    /**
     * Edit a price list item.
     * Endpoint: PUT /PriceListItems/{OrgCode}/{PriceList}/{Sequence}
     */
    public function updatePriceListItem($orgCode, $priceList, $sequence, array $payload)
    {
        return $this->request(
            'PUT',
            '/PriceListItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $priceList) . '/'
            . rawurlencode((string) $sequence),
            [],
            $payload
        );
    }

    /**
     * Delete a price list item.
     * Endpoint: DELETE /PriceListItems/{OrgCode}/{PriceList}/{Sequence}
     */
    public function deletePriceListItem($orgCode, $priceList, $sequence)
    {
        $this->request(
            'DELETE',
            '/PriceListItems/' . rawurlencode((string) $orgCode) . '/'
            . rawurlencode((string) $priceList) . '/'
            . rawurlencode((string) $sequence)
        );
    }

    // ---------------------------------------------------------------
    // Events — read (OData)
    // ---------------------------------------------------------------

    /**
     * Load one Event row from OData /odata/Events for floorplan field sync.
     * Tries common key variants (Event vs EventID) used across Ungerboeck/Momentus builds.
     *
     * @return array<string,mixed>|null
     */
    public function getEventRowForFloorplan($eventId, $orgCode = null)
    {
        $orgCode = ($orgCode !== null && $orgCode !== '') ? (string) $orgCode : $this->orgCode;
        $eid = (int) $eventId;
        if ($eid <= 0) {
            return null;
        }
        $escapedOrg = str_replace("'", "''", $orgCode);
        $odataQuery = '$filter=Organization eq \'' . $escapedOrg . '\' and EventID eq ' . $eid;
        $result = $this->request('GET', '/odata/Events', ['ODataQuery' => $odataQuery]);
        $items = $this->extractODataItems($result);
        if (isset($items[0]) && is_array($items[0])) {
            return $items[0];
        }
        return null;
    }

    // ---------------------------------------------------------------
    // Organizations
    // ---------------------------------------------------------------

    /**
     * GET /Organizations/{OrgCode}
     * Returns the organisation name for a given org code, or null if not found.
     *
     * @param string $orgCode
     * @return string|null
     */
    public function getOrganizationName($orgCode)
    {
        $orgCode = trim((string) $orgCode);
        if ($orgCode === '') {
            return null;
        }
        try {
            $result = $this->request('GET', '/Organizations/' . rawurlencode($orgCode));
            if (is_array($result)) {
                foreach (['OrganizationName', 'Description', 'Name', 'LongDescription'] as $key) {
                    if (!empty($result[$key])) {
                        return (string) $result[$key];
                    }
                }
            }
        } catch (\Exception $e) {
            // best-effort — don't break the page
        }
        return null;
    }

    // ---------------------------------------------------------------
    // Event Space Diagrams
    // ---------------------------------------------------------------

    /**
     * GET /EventSpaceDiagrams/{ID}
     * Retrieve a single Event Space Diagram entry by its ID.
     *
     * @param int $id  The EventSpaceDiagram ID
     * @return array   The diagram model as an associative array
     */
    public function getEventSpaceDiagram($id)
    {
        return $this->request('GET', '/EventSpaceDiagrams/' . (int) $id);
    }

    /**
     * Update the EventdrawDiagramUrl field on a Momentus EventSpaceDiagram.
     *
     * Performs a GET to fetch current values, sets EventdrawDiagramUrl to the
     * given URL, then PUTs the full model back (Momentus requires the full body).
     *
     * @param int    $id      The EventSpaceDiagram ID
     * @param string $url     The EventDraw floor plan URL to store
     * @param string $orgCode Organisation code (optional)
     * @return array          The API response
     */
    public function updateEventSpaceDiagramUrl($id, $url, $orgCode = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            throw new RuntimeException('Invalid EventSpaceDiagram ID: ' . $id);
        }

        try {
            $current = $this->getEventSpaceDiagram($id);
        } catch (\Exception $e) {
            throw new RuntimeException(
                'Failed to GET EventSpaceDiagram ' . $id . ': ' . $e->getMessage()
            );
        }

        // Ensure the ID in the body matches the URL path parameter (API requirement)
        $current['ID'] = $id;
        $current['EventdrawDiagramUrl'] = (string) $url;

        // Override org code if provided
        if ($orgCode !== null && $orgCode !== '') {
            $current['OrganizationCode'] = (string) $orgCode;
        }

        try {
            return $this->request('PUT', '/EventSpaceDiagrams/' . $id, [], $current);
        } catch (\Exception $e) {
            throw new RuntimeException(
                'Failed to PUT EventSpaceDiagram ' . $id . ': ' . $e->getMessage()
            );
        }
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Extract items from an OData response (handles {value:[...]} wrapper).
     */
    private function extractODataItems(array $payload)
    {
        if (isset($payload['value']) && is_array($payload['value'])) {
            return $payload['value'];
        }
        $keys = array_keys($payload);
        if ($keys === range(0, count($payload) - 1) || $payload === []) {
            return $payload;
        }
        foreach (['Results', 'results', 'Items', 'items'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }
        return [];
    }

    public function getDefaultOrgCode()
    {
        return $this->orgCode;
    }

    private function request($method, $endpoint, array $queryParams = [], array $payload = null)
    {
        if ($this->apiToken === '' || $this->subscriptionKey === '') {
            throw new RuntimeException('Momentus credentials are not configured.');
        }

        $url = $this->baseUrl . '/' . ltrim((string) $endpoint, '/');
        if ($queryParams !== []) {
            $url .= '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
        }

        $headers = [
            'accept: application/json',
            'apitoken: ' . $this->apiToken,
            'ocp-apim-subscription-key: ' . $this->subscriptionKey,
        ];
        
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => strtoupper((string) $method),
        ];

        if ($payload !== null) {
            $headers[] = 'content-type: application/json';
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Momentus cURL error: ' . $error);
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Momentus API request failed with HTTP ' . $statusCode . ': ' . $response);
        }

        if ($response === '' || $response === null) {
            return [];
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeOrgCode($orgCode = null)
    {
        $value = $orgCode !== null && $orgCode !== '' ? (string) $orgCode : $this->orgCode;
        return rawurlencode($value);
    }

    private function buildSearchQuery($searchString, $fieldName, $fieldName2 = null)
    {
        $safeSearch = str_replace("'", "''", trim((string) $searchString));
        if ($fieldName2) {
            return "substringof('" . $safeSearch . "', " . $fieldName . ") or substringof('" . $safeSearch . "', " . $fieldName2 . ")";
        }
        return "substringof('" . $safeSearch . "', " . $fieldName . ")";
    }

    private function buildSearchNoteOdata($searchString)
    {
        $search = "substringof('" . $searchString . "', Code) or substringof('" . $searchString . "', Title) or substringof('" . $searchString . "', Class)  or substringof('" . $searchString . "', Text)";
        if (is_numeric($searchString)) {
            $search .= "or SequenceNumber eq " . $searchString;
        }
        return $search;
    }
}
