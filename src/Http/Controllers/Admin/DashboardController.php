<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use Analytics;
use Spatie\Analytics\Period;

class DashboardController extends Controller
{
    public function index()
    {
        $modules = collect(config('cms-toolkit.dashboard.modules'));

        return view('cms-toolkit::layouts.dashboard', [
            'myActivityData' => [
                [
                    'id' => 10,
                    'type' => 'Projects',
                    'date' => '2018/01/09 00:00:00',
                    'author' => 'Antoine',
                    'featured' => true,
                    'published' => true,
                    'name' => 'Barnes Foundation website',
                    'edit' => '/templates/form',
                    'activity' => 'Unpublished',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=10',
                    'permalink' => 'https://pentagram.com',
                ],
            ],
            'allActivityData' => [
                [
                    'id' => 1,
                    'type' => 'Projects',
                    'date' => '2017/11/09 00:00:00',
                    'author' => 'George',
                    'featured' => true,
                    'published' => true,
                    'name' => 'The New School Website',
                    'edit' => '/templates/form',
                    'activity' => 'Unpublished',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=1',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 2,
                    'type' => 'Projects',
                    'date' => '2017/11/08 00:00:00',
                    'author' => 'George',
                    'featured' => true,
                    'published' => true,
                    'name' => 'THG Paris website',
                    'edit' => '/templates/form',
                    'activity' => 'Updated',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=2',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 3,
                    'type' => 'News',
                    'date' => '2017/11/05 00:00:00',
                    'author' => 'George',
                    'featured' => false,
                    'published' => false,
                    'name' => 'Pentagram website',
                    'edit' => '/templates/form',
                    'activity' => 'Created',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=3',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 4,
                    'type' => 'Projects',
                    'date' => '2017/11/02 00:00:00',
                    'author' => 'George',
                    'featured' => false,
                    'published' => false,
                    'name' => 'Mai 36 Galerie website',
                    'edit' => '/templates/form',
                    'activity' => 'Updated',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=4',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 5,
                    'type' => 'News',
                    'date' => '2017/11/01 00:00:00',
                    'author' => 'George',
                    'featured' => false,
                    'published' => false,
                    'name' => 'Mai 36 Galerie website',
                    'edit' => '/templates/form',
                    'activity' => 'Published',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=5',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 6,
                    'type' => 'Partners',
                    'date' => '2017/10/09 00:00:00',
                    'author' => 'Quentin',
                    'featured' => false,
                    'published' => false,
                    'name' => 'Roto website',
                    'edit' => '/templates/form',
                    'activity' => 'Updated',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=6',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 7,
                    'type' => 'Partner',
                    'date' => '2017/09/09 00:00:00',
                    'author' => 'Partners',
                    'featured' => false,
                    'published' => true,
                    'name' => 'THG Paris website',
                    'edit' => '/templates/form',
                    'activity' => 'Published',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=7',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 8,
                    'type' => 'News',
                    'date' => '2017/04/09 00:00:00',
                    'author' => 'Martin',
                    'featured' => false,
                    'published' => true,
                    'name' => 'La Parqueterie Nouvelle strategie',
                    'edit' => '/templates/form',
                    'activity' => 'Published',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=8',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 9,
                    'type' => 'News',
                    'date' => '2017/04/09 00:00:00',
                    'author' => 'Martin',
                    'featured' => false,
                    'published' => true,
                    'name' => 'La Parqueterie Nouvelle strategie',
                    'edit' => '/templates/form',
                    'activity' => 'Published',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=8',
                    'permalink' => 'https://pentagram.com',
                ],
                [
                    'id' => 10,
                    'type' => 'News',
                    'date' => '2017/04/09 00:00:00',
                    'author' => 'Martin',
                    'featured' => false,
                    'published' => true,
                    'name' => 'La Parqueterie Nouvelle strategie',
                    'edit' => '/templates/form',
                    'activity' => 'Published',
                    'thumbnail' => 'https://source.unsplash.com/random/80x80?sig=8',
                    'permalink' => 'https://pentagram.com',
                ],
            ],
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
            'facts' => $this->getFacts(),
        ]);
    }

    private function getFacts()
    {
        $response = Analytics::performQuery(
            Period::days(60),
            'ga:users,ga:pageviews,ga:bouncerate,ga:pageviewsPerSession',
            ['dimensions' => 'ga:date']
        );

        $statsByDate = collect($response['rows'] ?? [])->map(function (array $dateRow) {
            return [
                'date' => $dateRow[0],
                'users' => (int) $dateRow[1],
                'pageViews' => (int) $dateRow[2],
                'bounceRate' => $dateRow[3],
                'pageviewsPerSession' => $dateRow[4],
            ];
        })->reverse()->values();

        return collect([
            'today',
            'yesterday',
            'week',
            'month'
        ])->mapWithKeys(function ($period) use ($statsByDate) {
            $stats = $this->getPeriodStats($period, $statsByDate);
            return [$period => [
                [
                    'label' => 'Users',
                    'figure' => $this->formatStat($stats['stats']['users']),
                    'insight' => round($stats['stats']['bounceRate']) . '% Bounce rate',
                    'trend' => $stats['moreUsers'] ? 'up' : 'down',
                    'url' => 'https://analytics.google.com/analytics/web',
                ],
                [
                    'label' => 'Pageviews',
                    'figure' => $this->formatStat($stats['stats']['pageViews']),
                    'insight' => round($stats['stats']['pageviewsPerSession'], 1) . ' Pages / Session',
                    'trend' => $stats['morePageViews'] ? 'up' : 'down',
                    'url' => 'https://analytics.google.com/analytics/web',
                ],
            ]];
        });
    }

    private function getPeriodStats($period, $statsByDate)
    {
        if ($period === 'today') {
            return [
                'stats' => $stats = $statsByDate->first(),
                'moreUsers' => $stats['users'] > $statsByDate->get(1)['users'],
                'morePageViews' => $stats['pageViews'] > $statsByDate->get(1)['pageViews'],
            ];
        } elseif ($period === 'yesterday') {
            return [
                'stats' => $stats = $statsByDate->get(1),
                'moreUsers' => $stats['users'] > $statsByDate->get(2)['users'],
                'morePageViews' => $stats['pageViews'] > $statsByDate->get(2)['pageViews'],
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
            ];
        }
    }

    private function formatStat($count)
    {
        if ($count >= 1000) {
            return round($count / 1000, 1) . "k";
        }

        return $count;
    }

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
                'singular' => $module['label_singular'] ?? str_singular($module['name']),
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
        });
    }

    private function getRepository($module)
    {
        return app(config('cms-toolkit.namespace') . "\Repositories\\" . ucfirst(str_singular($module)) . "Repository");
    }
}
