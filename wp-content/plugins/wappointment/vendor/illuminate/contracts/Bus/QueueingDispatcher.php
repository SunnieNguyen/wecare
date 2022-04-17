<?php

namespace WappoVendor\Illuminate\Contracts\Bus;

interface QueueingDispatcher extends \WappoVendor\Illuminate\Contracts\Bus\Dispatcher
{
    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatchToQueue($command);
}
