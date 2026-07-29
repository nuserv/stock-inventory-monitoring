<?php

namespace Tests\Unit;

use App\Mail\MailPoolAccountRepository;
use App\Mail\Transport\RotatingSmtpTransport;
use Mockery;
use PHPUnit\Framework\TestCase;
use Swift_Message;
use Swift_Transport;
use Swift_TransportException;

class RotatingSmtpTransportTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testItUsesTheAccountClaimedByTheSharedPool()
    {
        $repository = Mockery::mock(MailPoolAccountRepository::class);
        $repository->shouldReceive('claim')->once()->with([1, 2], [])->andReturn(2);
        $repository->shouldReceive('markSuccessful')->once()->with(2);

        $smtp = Mockery::mock(Swift_Transport::class);
        $smtp->shouldReceive('send')->once()->withArgs(function ($message) {
            return array_key_exists('two@example.com', $message->getFrom());
        })->andReturn(1);
        $smtp->shouldReceive('stop')->once();

        $transport = new RotatingSmtpTransport(
            $repository,
            $this->config(),
            function () use ($smtp) {
                return $smtp;
            }
        );

        $message = (new Swift_Message('Test'))
            ->setFrom('original@example.com', 'Stock Monitoring')
            ->setTo('recipient@example.com')
            ->setBody('Test');

        $failedRecipients = [];
        $this->assertSame(1, $transport->send($message, $failedRecipients));
        $this->assertSame(
            ['two@example.com' => 'Stock Monitoring'],
            $message->getFrom()
        );
    }

    public function testItCoolsDownA451AccountAndTriesTheNextAccount()
    {
        $repository = Mockery::mock(MailPoolAccountRepository::class);
        $repository->shouldReceive('claim')->once()->with([1, 2], [])->andReturn(1);
        $repository->shouldReceive('claim')->once()->with([1, 2], [1])->andReturn(2);
        $repository->shouldReceive('markRateLimited')->once()->with(1, 300);
        $repository->shouldReceive('markSuccessful')->once()->with(2);

        $limited = Mockery::mock(Swift_Transport::class);
        $limited->shouldReceive('send')->once()->andThrow(
            new Swift_TransportException(
                'Expected response code 250 but got code "451", with message "Ratelimit exceeded"',
                451
            )
        );
        $limited->shouldReceive('stop')->once();

        $available = Mockery::mock(Swift_Transport::class);
        $available->shouldReceive('send')->once()->andReturn(1);
        $available->shouldReceive('stop')->once();

        $transport = new RotatingSmtpTransport(
            $repository,
            $this->config(),
            function ($account) use ($limited, $available) {
                return $account['username'] === 'one@example.com'
                    ? $limited
                    : $available;
            }
        );

        $message = (new Swift_Message('Test'))
            ->setFrom('original@example.com')
            ->setTo('recipient@example.com')
            ->setBody('Test');

        $failedRecipients = [];
        $this->assertSame(1, $transport->send($message, $failedRecipients));
        $this->assertArrayHasKey('two@example.com', $message->getFrom());
    }

    public function testItDoesNotRetryAnAmbiguousTransportFailure()
    {
        $repository = Mockery::mock(MailPoolAccountRepository::class);
        $repository->shouldReceive('claim')->once()->with([1, 2], [])->andReturn(1);
        $repository->shouldNotReceive('markRateLimited');
        $repository->shouldNotReceive('markSuccessful');

        $smtp = Mockery::mock(Swift_Transport::class);
        $smtp->shouldReceive('send')->once()->andThrow(
            new Swift_TransportException('Connection unexpectedly closed.')
        );
        $smtp->shouldReceive('stop')->once();

        $transport = new RotatingSmtpTransport(
            $repository,
            $this->config(),
            function () use ($smtp) {
                return $smtp;
            }
        );

        $message = (new Swift_Message('Test'))
            ->setFrom('original@example.com')
            ->setTo('recipient@example.com')
            ->setBody('Test');

        $this->expectException(Swift_TransportException::class);
        $this->expectExceptionMessage('Connection unexpectedly closed.');

        $failedRecipients = [];
        $transport->send($message, $failedRecipients);
    }

    private function config()
    {
        return [
            'host' => 'smtp.example.com',
            'port' => 587,
            'encryption' => 'tls',
            'from' => ['name' => 'Stock Monitoring'],
            'pool' => [
                'cooldown_seconds' => 300,
                'accounts' => [
                    1 => [
                        'username' => 'one@example.com',
                        'password' => 'secret-one',
                        'from_address' => 'one@example.com',
                    ],
                    2 => [
                        'username' => 'two@example.com',
                        'password' => 'secret-two',
                        'from_address' => 'two@example.com',
                    ],
                ],
            ],
        ];
    }
}
