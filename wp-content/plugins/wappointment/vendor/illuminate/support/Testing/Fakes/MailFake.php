<?php

namespace WappoVendor\Illuminate\Support\Testing\Fakes;

use WappoVendor\Illuminate\Contracts\Mail\Mailer;
use WappoVendor\Illuminate\Contracts\Mail\Mailable;
use WappoVendor\PHPUnit\Framework\Assert as PHPUnit;
use WappoVendor\Illuminate\Contracts\Queue\ShouldQueue;
class MailFake implements \WappoVendor\Illuminate\Contracts\Mail\Mailer
{
    /**
     * All of the mailables that have been sent.
     *
     * @var array
     */
    protected $mailables = [];
    /**
     * All of the mailables that have been queued.
     *
     * @var array
     */
    protected $queuedMailables = [];
    /**
     * Assert if a mailable was sent based on a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertSent($mailable, $callback = null)
    {
        if (\is_numeric($callback)) {
            return $this->assertSentTimes($mailable, $callback);
        }
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue($this->sent($mailable, $callback)->count() > 0, "The expected [{$mailable}] mailable was not sent.");
    }
    /**
     * Assert if a mailable was sent a number of times.
     *
     * @param  string  $mailable
     * @param  int  $times
     * @return void
     */
    protected function assertSentTimes($mailable, $times = 1)
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue(($count = $this->sent($mailable)->count()) === $times, "The expected [{$mailable}] mailable was sent {$count} times instead of {$times} times.");
    }
    /**
     * Determine if a mailable was not sent based on a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotSent($mailable, $callback = null)
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue($this->sent($mailable, $callback)->count() === 0, "The unexpected [{$mailable}] mailable was sent.");
    }
    /**
     * Assert that no mailables were sent.
     *
     * @return void
     */
    public function assertNothingSent()
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertEmpty($this->mailables, 'Mailables were sent unexpectedly.');
    }
    /**
     * Assert if a mailable was queued based on a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertQueued($mailable, $callback = null)
    {
        if (\is_numeric($callback)) {
            return $this->assertQueuedTimes($mailable, $callback);
        }
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue($this->queued($mailable, $callback)->count() > 0, "The expected [{$mailable}] mailable was not queued.");
    }
    /**
     * Assert if a mailable was queued a number of times.
     *
     * @param  string  $mailable
     * @param  int  $times
     * @return void
     */
    protected function assertQueuedTimes($mailable, $times = 1)
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue(($count = $this->queued($mailable)->count()) === $times, "The expected [{$mailable}] mailable was queued {$count} times instead of {$times} times.");
    }
    /**
     * Determine if a mailable was not queued based on a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotQueued($mailable, $callback = null)
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertTrue($this->queued($mailable, $callback)->count() === 0, "The unexpected [{$mailable}] mailable was queued.");
    }
    /**
     * Assert that no mailables were queued.
     *
     * @return void
     */
    public function assertNothingQueued()
    {
        \WappoVendor\PHPUnit\Framework\Assert::assertEmpty($this->queuedMailables, 'Mailables were queued unexpectedly.');
    }
    /**
     * Get all of the mailables matching a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function sent($mailable, $callback = null)
    {
        if (!$this->hasSent($mailable)) {
            return \WappointmentLv::collect();
        }
        $callback = $callback ?: function () {
            return true;
        };
        return $this->mailablesOf($mailable)->filter(function ($mailable) use($callback) {
            return $callback($mailable);
        });
    }
    /**
     * Determine if the given mailable has been sent.
     *
     * @param  string  $mailable
     * @return bool
     */
    public function hasSent($mailable)
    {
        return $this->mailablesOf($mailable)->count() > 0;
    }
    /**
     * Get all of the queued mailables matching a truth-test callback.
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function queued($mailable, $callback = null)
    {
        if (!$this->hasQueued($mailable)) {
            return \WappointmentLv::collect();
        }
        $callback = $callback ?: function () {
            return true;
        };
        return $this->queuedMailablesOf($mailable)->filter(function ($mailable) use($callback) {
            return $callback($mailable);
        });
    }
    /**
     * Determine if the given mailable has been queued.
     *
     * @param  string  $mailable
     * @return bool
     */
    public function hasQueued($mailable)
    {
        return $this->queuedMailablesOf($mailable)->count() > 0;
    }
    /**
     * Get all of the mailed mailables for a given type.
     *
     * @param  string  $type
     * @return \Illuminate\Support\Collection
     */
    protected function mailablesOf($type)
    {
        return \WappointmentLv::collect($this->mailables)->filter(function ($mailable) use($type) {
            return $mailable instanceof $type;
        });
    }
    /**
     * Get all of the mailed mailables for a given type.
     *
     * @param  string  $type
     * @return \Illuminate\Support\Collection
     */
    protected function queuedMailablesOf($type)
    {
        return \WappointmentLv::collect($this->queuedMailables)->filter(function ($mailable) use($type) {
            return $mailable instanceof $type;
        });
    }
    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function to($users)
    {
        return (new \WappoVendor\Illuminate\Support\Testing\Fakes\PendingMailFake($this))->to($users);
    }
    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function bcc($users)
    {
        return (new \WappoVendor\Illuminate\Support\Testing\Fakes\PendingMailFake($this))->bcc($users);
    }
    /**
     * Send a new message when only a raw text part.
     *
     * @param  string  $text
     * @param  \Closure|string  $callback
     * @return int
     */
    public function raw($text, $callback)
    {
        //
    }
    /**
     * Send a new message using a view.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @return void
     */
    public function send($view, array $data = [], $callback = null)
    {
        if (!$view instanceof \WappoVendor\Illuminate\Contracts\Mail\Mailable) {
            return;
        }
        if ($view instanceof \WappoVendor\Illuminate\Contracts\Queue\ShouldQueue) {
            return $this->queue($view, $data, $callback);
        }
        $this->mailables[] = $view;
    }
    /**
     * Queue a new e-mail message for sending.
     *
     * @param  string|array  $view
     * @param  string|null  $queue
     * @return mixed
     */
    public function queue($view, $queue = null)
    {
        if (!$view instanceof \WappoVendor\Illuminate\Contracts\Mail\Mailable) {
            return;
        }
        $this->queuedMailables[] = $view;
    }
    /**
     * Get the array of failed recipients.
     *
     * @return array
     */
    public function failures()
    {
        //
    }
}
