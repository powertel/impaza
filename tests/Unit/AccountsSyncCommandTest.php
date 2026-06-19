<?php

namespace Tests\Unit;

use App\Console\Commands\AccountsSync;
use PHPUnit\Framework\TestCase;

class AccountsSyncCommandTest extends TestCase
{
    public function testExtractLastPageReadsPaginationMetadata(): void
    {
        $command = new AccountsSync();
        $method = new \ReflectionMethod($command, 'extractLastPage');
        $method->setAccessible(true);

        $this->assertSame(4, $method->invoke($command, [
            'pagination' => [
                'current_page' => 1,
                'last_page' => 4,
            ],
        ]));
    }

    public function testExtractLastPageFallsBackToOneWhenMissing(): void
    {
        $command = new AccountsSync();
        $method = new \ReflectionMethod($command, 'extractLastPage');
        $method->setAccessible(true);

        $this->assertSame(1, $method->invoke($command, [
            'status' => true,
            'data' => [],
        ]));
    }

    public function testUrlWithPageAppendsPageQuery(): void
    {
        $command = new AccountsSync();
        $method = new \ReflectionMethod($command, 'urlWithPage');
        $method->setAccessible(true);

        $this->assertSame(
            'http://192.168.15.246:8080/api/v1/accounts?page=3',
            $method->invoke($command, 'http://192.168.15.246:8080/api/v1/accounts', 3)
        );
    }

    public function testUrlWithPageReplacesExistingPageQuery(): void
    {
        $command = new AccountsSync();
        $method = new \ReflectionMethod($command, 'urlWithPage');
        $method->setAccessible(true);

        $this->assertSame(
            'http://192.168.15.246:8080/api/v1/accounts?status=active&page=2',
            $method->invoke($command, 'http://192.168.15.246:8080/api/v1/accounts?status=active&page=1', 2)
        );
    }
}
