<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TaskRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TaskQuery
{
    public function __construct(
        private TaskRepository $repo,
        private CacheInterface $cache
    ) {}

    public function getPageData(User $user, int $page, int $limit, ?string $search): array
    {
        // ✅ include user in cache key so user A and user B don't share cache
        $cacheKey = sprintf(
            'tasks_user_%d_page_%d_limit_%d_search_%s',
            $user->getId(),
            $page,
            $limit,
            $search ?? 'all'
        );

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($user, $page, $limit, $search) {
            $item->expiresAfter(5);

            // ✅ pass user to repository methods
            $tasks     = $this->repo->findPaginatedTasks($user, $page, $limit, $search);
            $total     = $this->repo->countTasks($user, $search);
            $completed = $this->repo->countCompleted($user);

            return compact('tasks', 'total', 'completed');
        });
    }
}