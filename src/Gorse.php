<?php

namespace Gorse;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

final class Gorse
{
    private string $endpoint;
    private string $apiKey;

    /**
     * Constructs a new Gorse instance.
     *
     * @param string $endpoint The base URL endpoint for the Gorse API.
     * @param string $apiKey The API key used for authentication.
     */
    public function __construct(string $endpoint, string $apiKey)
    {
        $this->endpoint = $endpoint;
        $this->apiKey   = $apiKey;
    }

    /**
     * Inserts a new user into the Gorse system.
     *
     * @param User $user The user object to be inserted.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function insertUser(User $user): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/user/', $user));
    }

    /**
     * Retrieves a user from the Gorse system.
     *
     * @param string $user_id The ID of the user to retrieve.
     * @return User The retrieved user object.
     * @throws GuzzleException
     */
    public function getUser(string $user_id): User
    {
        return User::fromJSON($this->request('GET', "/api/user/$user_id", null));
    }

    /**
     * Deletes a user from the Gorse system.
     *
     * @param string $user_id The ID of the user to delete.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function deleteUser(string $user_id): RowAffected
    {
        return RowAffected::fromJSON($this->request('DELETE', "/api/user/$user_id", null));
    }

    /**
     * Inserts a new item into the Gorse system.
     *
     * @param Item $item The item object to be inserted.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function insertItem(Item $item): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/item/', $item));
    }

    /**
     * Batch inserts multiple items into the Gorse system.
     *
     * @param Item[] $items An array of item objects to be inserted.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function batchInsertItem(array $items): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/items', $items));
    }

    /**
     * Retrieves an item from the Gorse system.
     *
     * @param string $item_id The ID of the item to retrieve.
     * @return Item The retrieved item object.
     * @throws GuzzleException
     */
    public function getItem(string $item_id): Item
    {
        return Item::fromJSON($this->request('GET', "/api/item/$item_id", null));
    }

    /**
     * Retrieves neighbors of an item from the Gorse system.
     *
     * @param string $item_id The ID of the item.
     * @return array An array containing the neighbors of the item.
     * @throws GuzzleException
     */
    public function getItemNeighbors(string $item_id): array
    {
        return $this->request('GET', "/api/item/$item_id/neighbors", null);
    }

    /**
     * Retrieves neighbors of an item in a specified category from the Gorse system.
     *
     * @param string $item_id The ID of the item.
     * @param string $category The category of the item.
     * @return array An array containing the neighbors of the item in the specified category.
     * @throws GuzzleException
     */
    public function getItemNeighborsInCategory(string $item_id, string $category): array
    {
        return $this->request('GET', "/api/item/$item_id/neighbors/$category", null);
    }

    /**
     * Deletes an item from the Gorse system.
     *
     * @param string $item_id The ID of the item to delete.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function deleteItem(string $item_id): RowAffected
    {
        return RowAffected::fromJSON($this->request('DELETE', "/api/item/$item_id", null));
    }

    /**
     * Inserts feedback into the Gorse system.
     *
     * @param array $feedback An array containing the feedback data.
     * @return RowAffected The result of the operation, indicating the number of rows affected.
     * @throws GuzzleException
     */
    public function insertFeedback(array $feedback): RowAffected
    {
        return RowAffected::fromJSON($this->request('POST', '/api/feedback/', $feedback));
    }

    /**
     * Retrieves the latest items from the Gorse system.
     *
     * @return array An array containing the latest items.
     * @throws GuzzleException
     */
    public function getLatestItems(): array
    {
        return $this->request('GET', '/api/latest/', null);
    }

    /**
     * Retrieves the latest items in a specified category from the Gorse system.
     *
     * @param string $category The category of the items.
     * @return array An array containing the latest items in the specified category.
     * @throws GuzzleException
     */
    public function getLatestCategoryItems(string $category): array
    {
        return $this->request('GET', "/api/latest/$category", null);
    }

    /**
     * Retrieves popular items from the Gorse system.
     *
     * @return array An array containing the popular items.
     * @throws GuzzleException
     */
    public function getPopularItems(): array
    {
        return $this->request('GET', '/api/popular', null);
    }

    /**
     * Retrieves popular items in a specified category from the Gorse system.
     *
     * @param string $category The category of the items.
     * @return array An array containing the popular items in the specified category.
     * @throws GuzzleException
     */
    public function getPopularItemsInCategory(string $category): array
    {
        return $this->request('GET', "/api/popular/$category", null);
    }

    /**
     * Retrieves recommendations for a user from the Gorse system.
     *
     * @param string $user_id The ID of the user.
     * @param string|null $writeBackType The type of write-back mechanism to use (optional). Possible value: "read".
     * @param string|null $writeBackDelay The delay for the write-back mechanism (optional). Possible value: "10m" for one minute.
     * @param int|null $n The number of recommendations to retrieve (optional).
     * @return array An array containing the recommendations for the user.
     * @throws GuzzleException
     */
    public function getRecommend(string $user_id, ?string $writeBackType = null, ?string $writeBackDelay = null, ?int $n = null): array
    {
        $queryParameters = [];

        // Add write-back-type parameter if provided
        if ($writeBackType !== null) {
            $queryParameters['write-back-type'] = $writeBackType;
        }

        // Add write-back-delay parameter if provided
        if ($writeBackDelay !== null) {
            $queryParameters['write-back-delay'] = $writeBackDelay;
        }

        // Add n (number of items) parameter if provided
        if ($n !== null) {
            $queryParameters['n'] = $n;
        }

        return $this->request('GET', "/api/recommend/$user_id", null, $queryParameters);
    }

    /**
     * Retrieves recommendations for a user in a specified category from the Gorse system.
     *
     * @param string $user_id The ID of the user.
     * @param string|null $writeBackType The type of write-back mechanism to use (optional). Possible value: "read".
     * @param string|null $writeBackDelay The delay for the write-back mechanism (optional). Possible value: "10m" for one minute.
     * @param int|null $n The number of recommendations to retrieve (optional).
     * @param string $category The category of the items.
     * @return array An array containing the recommendations for the user in the specified category.
     * @throws GuzzleException
     */
    public function getRecommendInCategory(string $user_id, string $category, ?string $writeBackType = null, ?string $writeBackDelay = null, ?int $n = null): array
    {
        $queryParameters = [];

        // Add write-back-type parameter if provided
        if ($writeBackType !== null) {
            $queryParameters['write-back-type'] = $writeBackType;
        }

        // Add write-back-delay parameter if provided
        if ($writeBackDelay !== null) {
            $queryParameters['write-back-delay'] = $writeBackDelay;
        }

        // Add n (number of items) parameter if provided
        if ($n !== null) {
            $queryParameters['n'] = $n;
        }

        return $this->request('GET', "/api/recommend/$user_id/$category", null, $queryParameters);
    }

    /**
     * Makes a request to the Gorse API.
     *
     * @param string $method The HTTP method (GET, POST, DELETE, etc.).
     * @param string $uri The URI of the request.
     * @param mixed $body The body of the request.
     * @param mixed $queryParameters The query parameters of the request.
     * @return mixed The response from the API.
     * @throws GuzzleException
     */
    private function request(string $method, string $uri, $body, $queryParameters = null)
    {
        $client  = new Client(['base_uri' => $this->endpoint]);
        $options = [RequestOptions::HEADERS => ['X-API-Key' => $this->apiKey]];

        // Add JSON body if provided
        if ($body != null) {
            $options[RequestOptions::JSON] = $body;
        }

        // Prepare query parameters
        $query = '';
        if ($queryParameters !== null) {
            $query = '?' . http_build_query($queryParameters);
        }

        // Make the request
        $response = $client->request($method, $uri . $query, $options);

        // Decode and return the response body
        return json_decode($response->getBody());
    }
}
