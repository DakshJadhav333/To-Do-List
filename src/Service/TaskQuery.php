<?php

namespace App\Service;

use App\Repository\TaskRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TaskQuery
{
    public function __construct(
        private TaskRepository $repo,
        private CacheInterface $cache
    ) {}

    public function getPageData(int $page, int $limit, ?string $search): array
    {
        $cacheKey = 'tasks_page_'.$page.'_search_'.($search ?? 'all');

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page, $limit, $search) {
            $item->expiresAfter(60);

            $tasks = $this->repo->findPaginatedTasks($page, $limit, $search);
            $total = $this->repo->countTasks($search);
            $completed = $this->repo->countCompleted();

            return compact('tasks', 'total', 'completed');
        });
    }
}
