<?php

namespace Hasnularief\Iqueue;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IqueueEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $location, $counter, $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($location, $counter, $data)
    {
        $this->location = $location;
        $this->counter  = $counter;
        $this->data     = $data;

        $this->$broadcastQueue = config('iqueue.queue_name');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('tv-queue-'.$this->location);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'iqueue.broadcast';
    }

}
