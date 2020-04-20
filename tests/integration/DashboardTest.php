<?php

namespace A17\Twill\Tests\Integration;

class DashboardTest extends ModulesTestBase
{
    public function setup(): void
    {
        parent::setUp();

        // TODO -- disabled to fix error in Travis
        //

        $this->skipOnTravis();

        $this->loadConfig('{$stubs}/modules/dashboard/twill.php'); /// commenting this line it start working
    }

    /**
     * This test produce an unknown failure which happens only on Travis.
     *
     * Please check: https://travis-ci.org/antonioribeiro/twill/jobs/604677960
     *
     * moved to ModulesAuthorsTest
     */
    public function testCanDisplayDashboard()
    {
        $this->request('/twill')->assertStatus(200);

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->request('/twill/personnel/authors')->assertStatus(200);

        $this->assertSee('Name');
        $this->assertSee('Languages');
        $this->assertSee('Mine');
        $this->assertSee('Add new');

        $this->request('/twill/categories')->assertStatus(200);
    }

    /**
     * This test produce an unknown failure which happens only on Travis.
     *
     * Please check: https://travis-ci.org/antonioribeiro/twill/jobs/604677960
     */
    public function testCanSearchString()
    {
        $this->createAuthor(3);

        $this->ajax("/twill/search?search={$this->name_en}")->assertStatus(200);

        $this->assertJson($this->content());

        $result = json_decode($this->content(), true);

        $this->assertGreaterThan(0, count($result));

        $this->assertEquals(
            $this->now->format('Y-m-d\TH:i:s+00:00'),
            $result[0]['date']
        );
    }

    /**
     * Trying to debug those tests
     */

    //    public function testCanDisplayDashboard()
    //    {
    //        require $this->makeFileName('{$base}/routes/admin.php');
    //
    //        dump(
    //            file_get_contents($this->makeFileName('{$base}/routes/admin.php'))
    //        );
    //
    //        try {
    //            dump(route('admin.personnel.authors.index'));
    //        } catch (\Exception $e) {
    //            dump('route admin.personnel.authors.index NOT FOUND');
    //        }
    //
    //        dump($this->getUriWithNames());
    //
    //        $this->request('/twill')->assertStatus(200);
    //
    //        $this->assertSee('Personnel');
    //        $this->assertSee('Categories');
    //
    //        $this->request('/twill/personnel/authors')->assertStatus(200);
    //
    //        $this->assertSee('Name');
    //        $this->assertSee('Languages');
    //        $this->assertSee('Mine');
    //        $this->assertSee('Add new');
    //
    //        $this->request('/twill/categories')->assertStatus(200);
    //    }
}
