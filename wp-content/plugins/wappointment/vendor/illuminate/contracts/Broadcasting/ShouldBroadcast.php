<?php

namespace WappoVendor\Illuminate\Contracts\Broadcasting;

use WappoVendor\Illuminate\Broadcasting\Channel;
interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]
     */
    public function broadcastOn();
}
