<?php

namespace WappoVendor\Illuminate\Contracts\Mail;

use WappoVendor\Illuminate\Contracts\Queue\Factory as Queue;
interface Mailable
{
    /**
     * Send the message using the given mailer.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send(\WappoVendor\Illuminate\Contracts\Mail\Mailer $mailer);
    /**
     * Queue the given message.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     * @return mixed
     */
    public function queue(\WappoVendor\Illuminate\Contracts\Queue\Factory $queue);
    /**
     * Deliver the queued message after the given delay.
     *
     * @param  \DateTime|int  $delay
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     * @return mixed
     */
    public function later($delay, \WappoVendor\Illuminate\Contracts\Queue\Factory $queue);
}
