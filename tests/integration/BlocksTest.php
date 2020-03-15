<?php

namespace A17\Twill\Tests\Integration;

class BlocksTest extends ModulesTestBase
{
    /**
     * Setup tests.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCanShowEditForm()
    {
        $this->createAuthor();
        $this->editAuthor();

        $this->request(
            "/twill/personnel/authors/{$this->author->id}/edit"
        )->assertStatus(200);

        // Check if it can see a rendered block
        $this->assertSee(
            '<script*type="text/x-template"*id="a17-block-quote">'
        );
    }
}
