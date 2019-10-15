<?php

namespace A17\Twill\Tests\Integration;

class UsersTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testCanListUsers()
    {
        $crawler = $this->ajax(
            '/twill/users?sortKey=email&sortDir=asc&page=1&offset=20&columns[]=bulk&columns[]=published&columns[]=name&columns[]=email&columns[]=role_value&filter=%7B%22status%22:%22published%22%7D'
        );

        $this->assertJson($crawler->getContent());
    }
}
