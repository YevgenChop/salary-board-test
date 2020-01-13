<?php
declare(strict_types=1);
namespace App\Services\GithubUser;

use \GuzzleHttp\Client;
use \GuzzleHttp\Promise;

/**
 * Class GitHubUserService
 * @package App\Services\GithubUser
 */
class GitHubUserService implements GitHubUserServiceInterface
{
    /**
     * Client instance
     * @var Client
     */
    protected $client;

    /**
     * Client options
     *
     * @var array
     */
    protected $clientOptions = [];

    /**
     * Service base uri
     * @var string
     */
    protected $baseUri = 'https://api.github.com';

    /**
     * Github auth token
     * @var
     */
    protected $githubAuthToken;

    /**
     * Limit users because of the github api request limit
     */
    protected $usersLimit;

    /**
     * GitHubUserService constructor.
     */
    public function __construct()
    {
        $this->usersLimit = (int)env('GITHUB_USERS_LIMIT', 2);
        $this->githubAuthToken = env('GITHUB_AUTH_TOKEN');
        $this->clientOptions['base_uri'] = $this->baseUri;

        if($this->githubAuthToken) {
            $this->clientOptions['headers']['Authorization'] = 'token '.$this->githubAuthToken;
        }

        $this->client = new Client($this->clientOptions);
    }

    /**
     * List users
     *
     * @return array
     * @throws \Throwable
     */
    function listUsers() : array
    {
        $req = $this->client->request('GET', '/users');
        $users = json_decode($req->getBody()->getContents(), true);

        // Get only two users for because of api req limit
        $usersRes = array_slice($users, 0, $this->usersLimit, true);

        // Create array of asynchronous requests
        $promises = $this->makePromises($usersRes);

        // Send multiple requests concurrently
        $response =  Promise\unwrap($promises);

        return $this->transformResult($usersRes, $response);
    }

    /**
     * Search users
     *
     * @param string $q
     * @return array
     * @throws \Throwable
     */
    function searchUsers(string $q) : array
    {
        $req = $this->client->request('GET', "/search/users?q=${q}");
        $users = json_decode($req->getBody()->getContents(), true);

        // Get only two users for because of api req limit
        $usersRes = array_slice($users['items'], 0, $this->usersLimit, true);

        // Create array of asynchronous requests
        $promises = $this->makePromises($usersRes);

        // Send multiple requests concurrently
        $response =  Promise\unwrap($promises);

        return $this->transformResult($usersRes, $response);
    }

    /**
     * Transform response and merge with user
     *
     * @param array $users
     * @param $response
     * @return array
     */
    function transformResult(array $users, $response): array {
        foreach (array_chunk($response, 2) as $key => $item) {
            $users[$key]['followers'] = json_decode($item[0]->getBody()->getContents(), true);
            $users[$key]['repos'] = json_decode($item[1]->getBody()->getContents(), true);
        }

        return $users;
    }

    /**
     * Make array of promises
     * @param $usersRes
     * @return array
     */
    function makePromises(array $usersRes): array {
        $promises = [];
        foreach ($usersRes as $user) {
            $promises["${user['id']}_followers"] = $this->client->getAsync($user['followers_url']);
            $promises["${user['id']}_repos"] = $this->client->getAsync($user['repos_url']);
        }

        return $promises;
    }
}
