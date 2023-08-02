<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Repositories\ModuleRepository;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface as Logger;
use Spatie\Activitylog\Models\Activity;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Exceptions\InvalidConfiguration;
use Spatie\Analytics\Period;

class DashboardController extends Controller
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var AuthFactory
     */
    protected $authFactory;

    public function __construct(
        Application $app,
        Config $config,
        Logger $logger,
        ViewFactory $viewFactory,
        AuthFactory $authFactory
    ) {
        parent::__construct();

        $this->app = $app;
        $this->config = $config;
        $this->logger = $logger;
        $this->viewFactory = $viewFactory;
        $this->authFactory = $authFactory;
    }

    /**
     * Displays the Twill dashboard.
     */
    public function index(): View|JsonResponse
    {
        if (request()?->expectsJson()) {
            if (request()?->input('mine')) {
                return new JsonResponse($this->getLoggedInUserActivities());
            }

            return new JsonResponse($this->getAllActivities());
        }
        $modules = Collection::make($this->config->get('twill.dashboard.modules'));

        return $this->viewFactory->make('twill::layouts.dashboard', [
            'allActivityData' => $this->getAllActivities(),
            'myActivityData' => $this->getLoggedInUserActivities(),
            'ajaxBaseUrl' => request()?->url(),
            'tableColumns' => [
                [
                    'name' => 'thumbnail',
                    'label' => 'Thumbnail',
                    'visible' => true,
                    'optional' => false,
                    'sortable' => false,
                ],
                [
                    'name' => 'published',
                    'label' => 'Published',
                    'visible' => true,
                    'optional' => false,
                    'sortable' => false,
                ],
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'visible' => true,
                    'optional' => false,
                    'sortable' => true,
                ],
            ],
            'shortcuts' => $this->getShortcuts($modules),
            'facts' => $this->config->get('twill.dashboard.analytics.enabled', false) ? $this->getFacts() : null,
            'drafts' => $this->getDrafts($modules),
        ]);
    }

    public function search(Request $request): Collection
    {
        $modules = Collection::make($this->config->get('twill.dashboard.modules'));

        return $modules->filter(function ($module) {
            return $module['search'] ?? false;
        })->map(function ($module) use ($request) {
            $repository = $this->getRepository($module['name'], $module['repository'] ?? null);

            $found = $repository->cmsSearch($request->get('search'), $module['search_fields'] ?? ['title'])->take(10);

            return $found->map(function ($item) use ($module) {
                try {
                    $author = $item->revisions()->latest()->first()->user->name ?? 'Admin';
                } catch (\Exception) {
                    $author = 'Admin';
                }

                $date = null;
                if ($item->updated_at) {
                    $date = $item->updated_at->toIso8601String();
                } elseif ($item->created_at) {
                    $date = $item->created_at->toIso8601String();
                }

                return [
                    'id' => $item->id,
                    'href' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'edit', $item->id),
                    'thumbnail' => method_exists($item, 'defaultCmsImage') ? $item->defaultCmsImage(['w' => 100, 'h' => 100]) : null,
                    'published' => $item->published,
                    'activity' => twillTrans('twill::lang.dashboard.search.last-edit'),
                    'date' => $date,
                    'title' => $item->titleInDashboard ?? $item->title,
                    'author' => $author,
                    'type' => ucfirst($module['label_singular'] ?? Str::singular($module['name'])),
                ];
            });
        })->collapse()->values();
    }

    private function getEnabledActivities(): array
    {
        $modules = $this->config->get('twill.dashboard.modules');
        $listActivities = [];

        foreach ($modules as $moduleClass => $moduleConfiguration) {
            $moduleClassToCheck = Relation::getMorphedModel($moduleClass) ?? $moduleClass;
            if (! empty($moduleConfiguration['activity'])) {
                if (! class_exists($moduleClassToCheck)) {
                    //  Try to load it from the morph map.
                    throw new \Exception(
                        "Class $moduleClassToCheck specified in twill.dashboard configuration does not exists."
                    );
                }
                $listActivities[] = $moduleClass;
            }
        }

        if (config('twill.dashboard.auth_activity_log.login', false)) {
            $listActivities[] = config('twill.dashboard.auth_activity_causer', 'users');
        }

        if (config('twill.dashboard.auth_activity_log.logout', false)) {
            $listActivities[] = config('twill.dashboard.auth_activity_causer', 'users');
        }

        return $listActivities;
    }

    private function getAllActivities(): LengthAwarePaginator
    {
        $activity = Activity::whereIn('subject_type', $this->getEnabledActivities())
            ->latest()
            ->paginate(perPage: 20, pageName: 'all');

        $list = $activity->map(function ($activity) {
            return $this->formatActivity($activity);
        })
            ->filter()
            ->values();

        return new LengthAwarePaginator(
            $list,
            $activity->total(),
            $activity->perPage(),
            $activity->currentPage(),
            ['path' => request()->path(), 'pageName' => 'all']
        );
    }

    private function getLoggedInUserActivities(): LengthAwarePaginator
    {
        $activity = Activity::whereIn('subject_type', $this->getEnabledActivities())
            ->where('causer_id', $this->authFactory->guard('twill_users')->user()->id)
            ->latest()
            ->paginate(perPage: 20, pageName: 'mine');

        $list = $activity->map(function ($activity) {
            return $this->formatActivity($activity);
        })
            ->filter()
            ->values();

        return new LengthAwarePaginator(
            $list,
            $activity->total(),
            $activity->perPage(),
            $activity->currentPage(),
            ['path' => request()->path(), 'pageName' => 'mine']
        );
    }

    private function formatActivity(Activity $activity): ?array
    {
        if ($activity->subject_type === config('twill.auth_activity_causer', 'users')) {
            return $this->formatAuthActivity($activity);
        }

        $dashboardModule = $this->config->get('twill.dashboard.modules.' . $activity->subject_type);

        if (! $dashboardModule || ! $dashboardModule['activity'] ?? false) {
            return null;
        }

        if (is_null($activity->subject)) {
            return null;
        }

        $parentRelationship = $dashboardModule['parentRelationship'] ?? null;
        $parent = $activity->subject->$parentRelationship;

        // @todo: Improve readability of what is happening here.
        return [
            'id' => $activity->id,
            'type' => ucfirst($dashboardModule['label_singular'] ?? Str::singular($dashboardModule['name'])),
            'date' => $activity->created_at->toIso8601String(),
            'author' => $activity->causer->name ?? twillTrans('twill::lang.dashboard.unknown-author'),
            'name' => $activity->subject->titleInDashboard ?? $activity->subject->title,
            'activity' => twillTrans('twill::lang.dashboard.activities.' . $activity->description, $activity->properties->toArray()),
        ] + (classHasTrait($activity->subject, HasMedias::class) ? [
            'thumbnail' => $activity->subject->defaultCmsImage(['w' => 100, 'h' => 100]),
        ] : []) + (! $activity->subject->trashed() ? [
            'edit' => moduleRoute(
                $dashboardModule['name'],
                $dashboardModule['routePrefix'] ?? null,
                'edit',
                array_merge($parentRelationship ? [$parent->id] : [], [$activity->subject_id])
            ),
        ] : []) + (! is_null($activity->subject->published) ? [
            'published' => $activity->description === 'published' ? true : ($activity->description === 'unpublished' ? false : $activity->subject->published),
        ] : []);
    }

    private function formatAuthActivity(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'type' => twillTrans('twill::lang.auth.auth-causer'),
            'date' => $activity->created_at->toIso8601String(),
            'author' => $activity->causer->name ?? twillTrans('twill::lang.dashboard.unknown-author'),
            'name' => ucfirst($activity->description) ?? '',
            'activity' => twillTrans('twill::lang.dashboard.activities.' . $activity->description, $activity->properties->toArray()),
        ] + (classHasTrait($activity->subject, HasMedias::class) ? [
            'thumbnail' => $activity->subject->defaultCmsImage(['w' => 100, 'h' => 100]),
        ] : []);
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    private function getFacts()
    {
        /** @var Analytics $analytics */
        $analytics = app(Analytics::class);
        try {
            $response = $analytics->performQuery(
                Period::days(60),
                'ga:users,ga:pageviews,ga:bouncerate,ga:pageviewsPerSession',
                ['dimensions' => 'ga:date']
            );
        } catch (InvalidConfiguration $exception) {
            $this->logger->error($exception);

            return [];
        }

        $statsByDate = Collection::make($response['rows'] ?? [])->map(function (array $dateRow) {
            return [
                'date' => $dateRow[0],
                'users' => (int) $dateRow[1],
                'pageViews' => (int) $dateRow[2],
                'bounceRate' => $dateRow[3],
                'pageviewsPerSession' => $dateRow[4],
            ];
        })->reverse()->values();

        $dummyData = null;
        if ($statsByDate->isEmpty()) {
            $dummyData = [
                [
                    'label' => 'Users',
                    'figure' => 0,
                    'insight' => '0% Bounce rate',
                    'trend' => twillTrans('None'),
                    'data' => [0 => 0],
                    'url' => 'https://analytics.google.com/analytics/web',
                ],
                [
                    'label' => 'Pageviews',
                    'figure' => 0,
                    'insight' => '0 Pages / Session',
                    'trend' => twillTrans('None'),
                    'data' => [0 => 0],
                    'url' => 'https://analytics.google.com/analytics/web',
                ],
            ];
        }

        return Collection::make([
            'today',
            'yesterday',
            'week',
            'month',
        ])->mapWithKeys(function ($period) use ($statsByDate, $dummyData) {
            if ($dummyData) {
                return [$period => $dummyData];
            }

            $stats = $this->getPeriodStats($period, $statsByDate);

            return [
                $period => [
                    [
                        'label' => 'Users',
                        'figure' => $this->formatStat($stats['stats']['users']),
                        'insight' => round($stats['stats']['bounceRate']) . '% Bounce rate',
                        'trend' => $stats['moreUsers'] ? 'up' : 'down',
                        'data' => $stats['usersData']->reverse()->values()->toArray(),
                        'url' => 'https://analytics.google.com/analytics/web',
                    ],
                    [
                        'label' => 'Pageviews',
                        'figure' => $this->formatStat($stats['stats']['pageViews']),
                        'insight' => round($stats['stats']['pageviewsPerSession'], 1) . ' Pages / Session',
                        'trend' => $stats['morePageViews'] ? 'up' : 'down',
                        'data' => $stats['pageViewsData']->reverse()->values()->toArray(),
                        'url' => 'https://analytics.google.com/analytics/web',
                    ],
                ],
            ];
        });
    }

    /**
     * @param string $period
     * @param \Illuminate\Support\Collection $statsByDate
     * @return array
     */
    private function getPeriodStats($period, $statsByDate)
    {
        if ($period === 'today') {
            return [
                'stats' => $stats = $statsByDate->first(),
                'moreUsers' => $stats['users'] > $statsByDate->get(1)['users'],
                'morePageViews' => $stats['pageViews'] > $statsByDate->get(1)['pageViews'],
                'usersData' => $statsByDate->take(7)->map(function ($stat) {
                    return $stat['users'];
                }),
                'pageViewsData' => $statsByDate->take(7)->map(function ($stat) {
                    return $stat['pageViews'];
                }),
            ];
        } elseif ($period === 'yesterday') {
            return [
                'stats' => $stats = $statsByDate->get(1),
                'moreUsers' => $stats['users'] > $statsByDate->get(2)['users'],
                'morePageViews' => $stats['pageViews'] > $statsByDate->get(2)['pageViews'],
                'usersData' => $statsByDate->slice(1)->take(7)->map(function ($stat) {
                    return $stat['users'];
                }),
                'pageViewsData' => $statsByDate->slice(1)->take(7)->map(function ($stat) {
                    return $stat['pageViews'];
                }),
            ];
        } elseif ($period === 'week') {
            $first7stats = $statsByDate->take(7)->all();

            $stats = [
                'users' => array_sum(array_column($first7stats, 'users')),
                'pageViews' => array_sum(array_column($first7stats, 'pageViews')),
                'bounceRate' => array_sum(array_column($first7stats, 'bounceRate')) / 7,
                'pageviewsPerSession' => array_sum(array_column($first7stats, 'pageviewsPerSession')) / 7,
            ];

            $compareStats = [
                'users' => array_sum(array_column($statsByDate->slice(7)->take(7)->all(), 'users')),
                'pageViews' => array_sum(array_column($statsByDate->slice(7)->take(7)->all(), 'pageViews')),
            ];

            return [
                'stats' => $stats,
                'moreUsers' => $stats['users'] > $compareStats['users'],
                'morePageViews' => $stats['pageViews'] > $compareStats['pageViews'],
                'usersData' => $statsByDate->slice(1)->take(29)->map(function ($stat) {
                    return $stat['users'];
                }),
                'pageViewsData' => $statsByDate->slice(1)->take(29)->map(function ($stat) {
                    return $stat['pageViews'];
                }),
            ];
        } elseif ($period === 'month') {
            $first30stats = $statsByDate->take(30)->all();

            $stats = [
                'users' => array_sum(array_column($first30stats, 'users')),
                'pageViews' => array_sum(array_column($first30stats, 'pageViews')),
                'bounceRate' => array_sum(array_column($first30stats, 'bounceRate')) / 30,
                'pageviewsPerSession' => array_sum(array_column($first30stats, 'pageviewsPerSession')) / 30,
            ];

            $compareStats = [
                'users' => array_sum(array_column($statsByDate->slice(30)->take(30)->all(), 'users')),
                'pageViews' => array_sum(array_column($statsByDate->slice(30)->take(30)->all(), 'pageViews')),
            ];

            return [
                'stats' => $stats,
                'moreUsers' => $stats['users'] > $compareStats['users'],
                'morePageViews' => $stats['pageViews'] > $compareStats['pageViews'],
                'usersData' => $statsByDate->slice(1)->take(29)->map(function ($stat) {
                    return $stat['users'];
                }),
                'pageViewsData' => $statsByDate->slice(1)->take(29)->map(function ($stat) {
                    return $stat['pageViews'];
                }),
            ];
        }

        return [];
    }

    /**
     * @param int $count
     * @return string
     */
    private function formatStat($count)
    {
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'k';
        }

        return $count;
    }

    private function getShortcuts(Collection $modules): Collection
    {
        return $modules->filter(function ($module) {
            return ($module['count'] ?? false) || ($module['create'] ?? false);
        })->map(function ($module) {
            $repository = $this->getRepository($module['name'], $module['repository'] ?? null);

            $moduleOptions = [
                'count' => $module['count'] ?? false,
                'create' => $module['create'] ?? false,
                'label' => $module['label'] ?? $module['name'],
                'singular' => $module['label_singular'] ?? Str::singular($module['name']),
            ];

            return [
                'label' => ucfirst($moduleOptions['label']),
                'singular' => ucfirst($moduleOptions['singular']),
                'number' => $moduleOptions['count'] ? $repository->getCountByStatusSlug(
                    'all',
                    $module['countScope'] ?? []
                ) : null,
                'url' => moduleRoute(
                    $module['name'],
                    $module['routePrefix'] ?? null,
                    'index'
                ),
                'createUrl' => $moduleOptions['create'] ? moduleRoute(
                    $module['name'],
                    $module['routePrefix'] ?? null,
                    'index',
                    ['openCreate' => true]
                ) : null,
            ];
        })->values();
    }

    private function getDrafts(Collection $modules): Collection
    {
        return $modules->filter(function ($module) {
            return $module['draft'] ?? false;
        })->map(function ($module) {
            $repository = $this->getRepository($module['name'], $module['repository'] ?? null);

            $query = $repository->draft()->limit(3)->latest();

            if ($repository->hasBehavior('revisions')) {
                $query->mine();
            }

            return $query->get()->map(function ($draft) use ($module) {
                return [
                    'type' => ucfirst($module['label_singular'] ?? Str::singular($module['name'])),
                    'name' => $draft->titleInDashboard ?? $draft->title,
                    'url' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'edit', [$draft->id]),
                ];
            });
        })->collapse()->values();
    }

    private function getRepository(string $module, string $forModule = null): ModuleRepository
    {
        return $this->app->make($forModule ?? $this->config->get('twill.namespace') . "\Repositories\\" . ucfirst(Str::singular($module)) . 'Repository');
    }
}
