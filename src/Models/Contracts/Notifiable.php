<?php

namespace OwowAgency\LaravelNotifications\Models\Contracts;

/**
 * The interface of Illuminate\Notifications\Notifiable trait. Unfortunately
 * we can't typehint the return because the trait doesn't do that.
 */
interface Notifiable
{
    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications();

    /**
     * Get the entity's read notifications.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function readNotifications();

    /**
     * Get the entity's unread notifications.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function unreadNotifications();

    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance);

    /**
     * Send the given notification immediately.
     *
     * @param  mixed  $instance
     * @param  array|null  $channels
     * @return void
     */
    public function notifyNow($instance, array $channels = null);

    /**
     * Get the notification routing information for the given driver.
     *
     * @param  string  $driver
     * @param  \Illuminate\Notifications\Notification|null  $notification
     * @return mixed
     */
    public function routeNotificationFor($driver, $notification = null);
}
