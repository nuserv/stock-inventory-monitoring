<?php

namespace Tests\Feature;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Swift_Message;
use Tests\TestCase;

class MailDisableTest extends TestCase
{
    public function testMailIsEnabledWhenTheDisableFlagIsFalse()
    {
        config(['email.disabled' => false]);
        $transport = Mail::getSwiftMailer()->getTransport();
        $messageCount = count($transport->messages());

        $result = Event::until(new MessageSending(new Swift_Message()));
        Mail::raw('Enabled email test.', function ($message) {
            $message->to('enabled@example.com');
        });

        $this->assertNull($result);
        $this->assertCount($messageCount + 1, $transport->messages());
    }

    public function testMailIsBlockedWhenTheDisableFlagIsTrue()
    {
        config(['email.disabled' => true]);
        $transport = Mail::getSwiftMailer()->getTransport();
        $messageCount = count($transport->messages());

        $result = Event::until(new MessageSending(new Swift_Message()));
        Mail::raw('Disabled email test.', function ($message) {
            $message->to('disabled@example.com');
        });

        $this->assertFalse($result);
        $this->assertCount($messageCount, $transport->messages());
    }
}
