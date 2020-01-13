<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\GithubUser\GitHubUserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Throwable;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends BaseController
{
    /**
     * List users
     *
     * @param Request $request
     * @param GitHubUserServiceInterface $gitHubUserService
     * @return JsonResponse
     */
    public function list(Request $request, GitHubUserServiceInterface $gitHubUserService): JsonResponse
    {
        try {
            $search = $request->input('q');
            $users = $search !== null ? $gitHubUserService->searchUsers($search): $gitHubUserService->listUsers();

            return response()->json($users);
        } catch (Throwable $e) {
            return response()->json($e->getMessage());
        }
    }
}
