<?php

namespace A17\Twill\Tests\Integration\Repositories;

use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Models\Partner;
use App\Models\Project;
use App\Repositories\ProjectRepository;

/**
 * Tests the belongToMany with pivots.
 *
 * In this scope the project is the main model, and the partner is the belongsToMany target.
 *
 * You can install the code for this test by running `php artisan twill:install portfolio`.
 */
class RepositoryRepeatersTest extends ModulesTestBase
{
    public ?string $example = 'portfolio';

    protected Partner $partner;
    protected Project $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->partner = Partner::create([
            'title' => 'partner 1',
            'active' => true,
        ]);
        $this->project = Project::create([
            'title' => 'Project 1',
            'active' => true,
        ]);
    }

    public function testCanAttachPartnerToProject(): void
    {
        $fields = [
            'repeaters' => [
                'project_partners' => [
                    [
                        // @todo This should not be required to be here.
                        // In prepareFieldsBeforeSaveHandleTranslations there is null-ing of fields that are
                        // not in the data request, but that is not needed.
                        // At least not for repeaters.
                        'title' => ['en' => 'partner 1'],
                        'role' => ['some role'],
                        'repeater_target_id' => $this->partner->id,
                        'id' => time(),
                    ],
                ],
            ],
        ];

        app(ProjectRepository::class)->updateRepeaterWithPivot(
            $this->project,
            $fields,
            'partners',
            ['role'],
            'partner',
            'project_partners'
        );

        $partners = $this->project->partners()->withPivot('role')->get();
        $this->assertEquals(1, $partners->count());

        $this->assertEquals($this->partner->title, $partners[0]->title);
        $this->assertEquals('["some role"]', $partners[0]->pivot->role);
    }

    public function testCanCreatePartnerToProject(): void
    {
        $fields = [
            'repeaters' => [
                'project_partners' => [
                    [
                        'role' => ['The partner role'],
                        'repeater_target_id' => null,
                        'title' => ['en' => 'Partner name'],
                        'id' => time(),
                    ],
                ],
            ],
        ];

        app(ProjectRepository::class)->updateRepeaterWithPivot(
            $this->project,
            $fields,
            'partners',
            ['role'],
            'partner',
            'project_partners'
        );

        $partners = $this->project->partners()->withPivot('role')->get();
        $this->assertEquals(1, $partners->count());

        $this->assertEquals('Partner name', $partners[0]->title);
        $this->assertEquals('["The partner role"]', $partners[0]->pivot->role);
    }

    public function testCanReferenceTheSamePartnerTwice(): void
    {
        $fields = [
            'repeaters' => [
                'project_partners' => [
                    [
                        'role' => ['Partner 1 role 1'],
                        'repeater_target_id' => $this->partner->id,
                        'id' => time(),
                    ],
                    [
                        'role' => ['Partner 1 role 2'],
                        'repeater_target_id' => $this->partner->id,
                        'id' => time() + 1,
                    ],
                ],
            ],
        ];

        app(ProjectRepository::class)->updateRepeaterWithPivot(
            $this->project,
            $fields,
            'partners',
            ['role'],
            'partner',
            'project_partners'
        );

        $partners = $this->project->partners()->withPivot('role')->get();
        $this->assertEquals(2, $partners->count());

        // Here we also check the id's to make sure these are not duplicated.
        $this->assertEquals($this->partner->id, $partners[0]->id);
        $this->assertEquals('["Partner 1 role 1"]', $partners[0]->pivot->role);

        $this->assertEquals($this->partner->id, $partners[1]->id);
        $this->assertEquals('["Partner 1 role 2"]', $partners[1]->pivot->role);
    }

    public function testCanCreateAndReferenceTAtTheSameTime(): void
    {
        $fields = [
            'repeaters' => [
                'project_partners' => [
                    [
                        'role' => ['Existing partner role'],
                        'repeater_target_id' => $this->partner->id,
                        'id' => time(),
                    ],
                    [
                        'role' => ['New Partner role'],
                        'repeater_target_id' => null,
                        'id' => time() + 1,
                        'title' => ['en' => 'New partner'],
                    ],
                ],
            ],
        ];

        app(ProjectRepository::class)->updateRepeaterWithPivot(
            $this->project,
            $fields,
            'partners',
            ['role'],
            'partner',
            'project_partners'
        );

        $partners = $this->project->partners()->withPivot('role')->get();
        $this->assertEquals(2, $partners->count());

        $this->assertEquals($this->partner->id, $partners[0]->id);
        $this->assertEquals('["Existing partner role"]', $partners[0]->pivot->role);

        $this->assertNotEquals($partners[0]->id, $partners[1]->id);

        $this->assertEquals('New partner', $partners[1]->title);
        $this->assertEquals('["New Partner role"]', $partners[1]->pivot->role);
    }

    public function testCanOverwriteFieldsInPartner(): void
    {
        $this->assertEquals('partner 1', $this->partner->title);

        $fields = [
            'repeaters' => [
                'project_partners' => [
                    [
                        'role' => ['some role'],
                        'title' => ['en' => 'new title'],
                        'repeater_target_id' => $this->partner->id,
                        'id' => time(),
                    ],
                ],
            ],
        ];

        app(ProjectRepository::class)->updateRepeaterWithPivot(
            $this->project,
            $fields,
            'partners',
            ['role'],
            'partner',
            'project_partners'
        );

        $partners = $this->project->partners()->withPivot('role')->get();

        // Check that the new title is there.
        $this->assertEquals($this->partner->id, $partners[0]->id);
        $this->assertEquals('new title', $partners[0]->title);
        $this->assertEquals('["some role"]', $partners[0]->pivot->role);
    }

    public function testGetBrowserDataForRepeater(): void
    {
        $this->getJson(route('twill.partners.browser', ['forRepeater' => 'true']))
            ->assertJsonPath(
                'data.0.repeaterFields',
                [
                    'published' => 0,
                    'title' => [
                        'en' => 'partner 1',
                    ],
                    'description' => [
                        'en' => null,
                    ],
                ]
            );
    }
}
