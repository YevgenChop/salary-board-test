<?php
declare(strict_types=1);
namespace App\Services\GithubUser;

/**
 * Interface GitHubUserServiceInterface
 * @package App\Services\GithubUser
 */
interface GitHubUserServiceInterface
{
    function listUsers() : array;
    function searchUsers(string $q) : array;
    function transformResult(array $users, $response): array;
    function makePromises(array $usersRes): array;
}
