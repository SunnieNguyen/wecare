<?php

namespace WappoVendor\Illuminate\Support\Testing\Fakes;

use WappoVendor\Illuminate\Mail\Mailable;
use WappoVendor\Illuminate\Mail\PendingMail;
class PendingMailFake extends \WappoVendor\Illuminate\Mail\PendingMail
{
    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Support\Testing\Fakes\MailFake  $mailer
     * @return void
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * Send a new mailable message instance.
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function send(\WappoVendor\Illuminate\Mail\Mailable $mailable)
    {
        return $this->sendNow($mailable);
    }
    /**
     * Send a mailable message immediately.
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function sendNow(\WappoVendor\Illuminate\Mail\Mailable $mailable)
    {
        $this->mailer->send($this->fill($mailable));
    }
    /**
     * Push the given mailable onto the queue.
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function queue(\WappoVendor\Illuminate\Mail\Mailable $mailable)
    {
        return $this->mailer->queue($this->fill($mailable));
    }
}
