<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Models\Behaviors\HasMedias;
use Analytics;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Psr\Log\LoggerInterface as Logger;
use Spatie\Activitylog\Models\Activity;
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
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $modules = Collection::make($this->config->get('twill.dashboard.modules'));

        return $this->viewFactory->make('twill::layouts.dashboard', [
            'allActivityData' => $this->getAllActivities(),
            'myActivityData' => $this->getLoggedInUserActivities(),
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

    /**
     * @param Request $request
     * @return Collection
     */
    public function search(Request $request)
    {
        $modules = Collection::make($this->config->get('twill.dashboard.modules'));

        return $modules->filter(function ($module) {
            return ($module['search'] ?? false);
        })->map(function ($module) use ($request) {
            $repository = $this->getRepository($module['name']);

            $found = $repository->cmsSearch($request->get('search'), $module['search_fields'] ?? ['title'])->take(10);

            return $found->map(function ($item) use ($module) {
                try {
                    $author = $item->revisions()->latest()->first()->user->name ?? 'Admin';
                } catch (\Exception $e) {
                    $author = 'Admin';
                }

                return [
                    'id' => $item->id,
                    'href' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'edit', $item->id),
                    'thumbnail' => method_exists($item, 'defaultCmsImage') ? $item->defaultCmsImage(['w' => 100, 'h' => 100]) : null,
                    'published' => $item->published,
                    'activity' => 'Last edited',
                    'date' => $item->updated_at->toIso8601String(),
                    'title' => $item->titleInDashboard ?? $item->title,
                    'author' => $author,
                    'type' => ucfirst($module['label_singular'] ?? Str::singular($module['name'])),
                ];
            });
        })->collapse()->values();
    }

    /**
     * @return array
     */
    private function getAllActivities()
    {
        return Activity::take(20)->latest()->get()->map(function ($activity) {
            return $this->formatActivity($activity);
        })->filter()->values();
    }

    /**
     * @return array
     */
    private function getLoggedInUserActivities()
    {
        return Activity::where('causer_id', $this->authFactory->guard('twill_users')->user()->id)->take(20)->latest()->get()->map(function ($activity) {
            return $this->formatActivity($activity);
        })->filter()->values();
    }

    /**
     * @param \Spatie\Activitylog\Models\Activity $activity
     * @return array|null
     */
    private function formatActivity($activity)
    {
        $dashboardModule = $this->config->get('twill.dashboard.modules.' . $activity->subject_type);

        if (!$dashboardModule || !$dashboardModule['activity'] ?? false) {
            return null;
        }

        return [
            'id' => $activity->id,
            'type' => ucfirst($dashboardModule['label_singular'] ?? Str::singular($dashboardModule['name'])),
            'date' => $activity->created_at->toIso8601String(),
            'author' => $activity->causer->name ?? 'Unknown',
            'name' => $activity->subject->titleInDashboard ?? $activity->subject->title,
            'activity' => ucfirst($activity->description),
        ] + (classHasTrait($activity->subject, HasMedias::class) ? [
            'thumbnail' => $activity->subject->defaultCmsImage(['w' => 100, 'h' => 100]),
        ] : []) + (!$activity->subject->trashed() ? [
            'edit' => moduleRoute($dashboardModule['name'], $dashboardModule['routePrefix'] ?? null, 'edit', $activity->subject_id),
        ] : []) + (!is_null($activity->subject->published) ? [
            'published' => $activity->description === 'published' ? true : ($activity->description === 'unpublished' ? false : $activity->subject->published),
        ] : []);
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    private function getFacts()
    {
        try {
            $response = Analytics::performQuery(
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

        return Collection::make([
            'today',
            'yesterday',
            'week',
            'month',
        ])->mapWithKeys(function ($period) use ($statsByDate) {
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
     * @param \Illuminate\Database\Query\Builder $statsByDate
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
    }

    /**
     * @param int $count
     * @return string
     */
    private function formatStat($count)
    {
        if ($count >= 1000) {
            return round($count / 1000, 1) . "k";
        }

        return $count;
    }

    /**
     * @param array $modules
     * @return array
     */
    private function getShortcuts($modules)
    {
        return $modules->filter(function ($module) {
            return ($module['count'] ?? false) || ($module['create'] ?? false);
        })->map(function ($module) {
            $repository = $this->getRepository($module['name']);

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
                    'all', $module['countScope'] ?? []
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
                ) : null
            ];
        })->values();
    }

    /**
     * @param array $modules
     * @return array
     */
    private function getDrafts($modules)
    {
        return $modules->filter(function ($module) {
            return ($module['draft'] ?? false);
        })->map(function ($module) {
            $repository = $this->getRepository($module['name']);

            $drafts = $repository->draft()->mine()->limit(3)->latest()->get();

            return $drafts->map(function ($draft) use ($module) {
                return [
                    'type' => ucfirst($module['label_singular'] ?? Str::singular($module['name'])),
                    'name' => $draft->titleInDashboard ?? $draft->title,
                    'url' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'edit', $draft->id),
                ];
            });
        })->collapse()->values();
    }

    /**
     * @param string $module
     * @return \A17\Twill\Repositories\ModuleRepository
     */
    private function getRepository($module)
    {
        return $this->app->get($this->config->get('twill.namespace') . "\Repositories\\" . ucfirst(Str::singular($module)) . "Repository");
    }
}
