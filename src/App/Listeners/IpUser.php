<?php

namespace Apachish\Auth\App\Listeners;



use Apachish\Auth\App\Events\UserEvent;
use Apachish\Auth\Models\Device;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class IpUser implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductUpdated $event
     * @return void
     */
    public function handle(UserEvent $event)
    {
            $device = Device::find($event->device_id);
            $device->ips()->create([
                'ip' => $event->ip
            ]);
    }
}
