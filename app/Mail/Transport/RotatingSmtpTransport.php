<?php

namespace App\Mail\Transport;

use App\Mail\MailPoolAccountRepository;
use Psr\Log\LoggerInterface;
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;
use Swift_SmtpTransport;
use Swift_Transport;
use Swift_TransportException;

class RotatingSmtpTransport implements Swift_Transport
{
    private $accounts;
    private $config;
    private $plugins = [];
    private $repository;
    private $started = false;
    private $transportFactory;
    private $logger;

    public function __construct(
        MailPoolAccountRepository $repository,
        array $config = null,
        callable $transportFactory = null,
        LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->config = $config ?: config('mail');
        $this->accounts = $this->completeAccounts(
            $this->config['pool']['accounts'] ?? []
        );
        $this->transportFactory = $transportFactory;
        $this->logger = $logger;
    }

    public function isStarted()
    {
        return $this->started;
    }

    public function start()
    {
        if (empty($this->accounts)) {
            throw new Swift_TransportException(
                'MAIL_POOL_ENABLED is true, but no complete SMTP pool accounts are configured.'
            );
        }

        $this->started = true;
    }

    public function stop()
    {
        $this->started = false;
    }

    public function ping()
    {
        return !empty($this->accounts);
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        if (!$this->isStarted()) {
            $this->start();
        }

        $failedRecipients = (array) $failedRecipients;
        $triedSlots = [];
        $lastRateLimit = null;

        while (count($triedSlots) < count($this->accounts)) {
            $slot = $this->repository->claim(array_keys($this->accounts), $triedSlots);

            if ($slot === null) {
                break;
            }

            $triedSlots[] = $slot;
            $account = $this->accounts[$slot];
            $message->setFrom(
                [$account['from_address'] => $this->fromName($message)]
            );

            $transport = $this->makeTransport($account);
            $attemptFailures = [];

            try {
                $sent = $transport->send($message, $attemptFailures);
                $this->repository->markSuccessful($slot);
                $failedRecipients = array_merge($failedRecipients, $attemptFailures);

                return $sent;
            } catch (Swift_TransportException $exception) {
                if (!$this->isRateLimitResponse($exception)) {
                    $failedRecipients = array_merge($failedRecipients, $attemptFailures);
                    throw $exception;
                }

                $lastRateLimit = $exception;
                $this->repository->markRateLimited(
                    $slot,
                    (int) ($this->config['pool']['cooldown_seconds'] ?? 300)
                );

                if ($this->logger) {
                    $this->logger->warning('SMTP pool account temporarily rate limited.', [
                        'slot' => $slot,
                        'username' => $account['username'],
                        'smtp_code' => 451,
                    ]);
                }
            } finally {
                try {
                    $transport->stop();
                } catch (\Exception $ignored) {
                    // Preserve the send result or original transport exception.
                }
            }
        }

        if ($lastRateLimit) {
            throw $lastRateLimit;
        }

        $wait = $this->repository->secondsUntilAvailable(array_keys($this->accounts));
        throw new Swift_TransportException(
            $wait
                ? "All SMTP pool accounts are cooling down; retry in {$wait} seconds."
                : 'No SMTP pool account is currently available.',
            451
        );
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->plugins[] = $plugin;
    }

    private function completeAccounts(array $accounts)
    {
        return array_filter($accounts, function ($account) {
            return !empty($account['username'])
                && !empty($account['password'])
                && !empty($account['from_address']);
        });
    }

    private function makeTransport(array $account)
    {
        if ($this->transportFactory) {
            return call_user_func($this->transportFactory, $account, $this->config);
        }

        $transport = new Swift_SmtpTransport(
            $this->config['host'],
            $this->config['port']
        );

        if (!empty($this->config['encryption'])) {
            $transport->setEncryption($this->config['encryption']);
        }

        $transport->setUsername($account['username']);
        $transport->setPassword($account['password']);

        if (isset($this->config['stream'])) {
            $transport->setStreamOptions($this->config['stream']);
        }

        if (isset($this->config['source_ip'])) {
            $transport->setSourceIp($this->config['source_ip']);
        }

        if (isset($this->config['local_domain'])) {
            $transport->setLocalDomain($this->config['local_domain']);
        }

        foreach ($this->plugins as $plugin) {
            $transport->registerPlugin($plugin);
        }

        return $transport;
    }

    private function isRateLimitResponse(Swift_TransportException $exception)
    {
        return (int) $exception->getCode() === 451
            || preg_match('/\b451\b|ratelimit/i', $exception->getMessage()) === 1;
    }

    private function fromName(Swift_Mime_SimpleMessage $message)
    {
        $from = (array) $message->getFrom();
        $name = reset($from);

        return is_string($name) && $name !== ''
            ? $name
            : ($this->config['from']['name'] ?? null);
    }
}
