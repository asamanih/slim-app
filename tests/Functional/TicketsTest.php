<?php
namespace Tests\Functional;

class TicketsTest extends BaseTestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->container['db']->beginTransaction();
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    $this->container['db']->rollback();
  }

  public function testIndex()
  {
    $response = $this->runApp('GET', '/tickets');
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertStringContainsString('チケット一覧', (string)$response->getBody());
  }

  public function testStore()
  {
    $response = $this->runApp('POST', '/tickets', ['subject' => 'functional_test_ticket']);

    $id = $this->container['db']->lastInsertId();
    $stmt = $this->container['db']->query('SELECT * FROM tickets WHERE id = ' . $id);
    $ticket = $stmt->fetch();

    $this->assertEquals(302, $response->getStatusCode());
    $this->assertEquals('/tickets', (string)$response->getHeaderLine('Location'));
    $this->assertEquals('functional_test_ticket', $ticket['subject']);
  }
}
