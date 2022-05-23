<?php

declare(strict_types=1);

namespace Snicco\Enterprise\Bundle\Auth\Tests\acceptance\Fail2Ban;

use Codeception\Test\Unit;
use Snicco\Enterprise\Bundle\Auth\Tests\fixtures\TestSysLogger;
use Snicco\Enterprise\Bundle\Auth\Tests\unit\Fail2Ban\TestBanworthy;
use Snicco\Enterprise\Bundle\Auth\Fail2Ban\Application\Fail2BanCommandHandler;

use Snicco\Enterprise\Bundle\Auth\Fail2Ban\Application\ReportEvent\ReportBanworthyEvent;

use const LOG_PID;
use const LOG_ERR;
use const LOG_AUTH;
use const LOG_WARNING;

final class ReportSuspiciousEventTest extends Unit
{
    /**
     * @test
     */
    public function that_a_suspicious_event_can_be_reported() :void
    {
        $fail2ban = new Fail2BanCommandHandler(
            $test_logger = new TestSysLogger(),
            [
                'daemon' => 'snicco',
                'flags' => LOG_PID,
                'facility' => LOG_AUTH,
            ]
        );
    
        $fail2ban(new ReportBanworthyEvent('foo', LOG_WARNING, '127.0.0.1'));
    
        $test_logger->assertLogOpened();
        $test_logger->assertLogOpenedWithFlags(LOG_PID);
        $test_logger->assertLogOpenedForFacility(LOG_AUTH);
        $test_logger->assertLogEntry(LOG_WARNING, 'foo from 127.0.0.1');
        
        $test_logger->reset();
    
        $fail2ban(new ReportBanworthyEvent('bar', LOG_ERR, '127.1.1.1'));
    
        $test_logger->assertLogOpened();
        $test_logger->assertLogOpenedWithFlags(LOG_PID);
        $test_logger->assertLogOpenedForFacility(LOG_AUTH);
        $test_logger->assertLogEntry(LOG_ERR, 'bar from 127.1.1.1');
    }
}