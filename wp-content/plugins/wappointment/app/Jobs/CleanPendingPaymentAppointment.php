<?php

namespace Wappointment\Jobs;

use Wappointment\ClassConnect\Carbon;
use Wappointment\Services\AppointmentNew;
use Wappointment\Services\Settings;
use Wappointment\Jobs\JobInterface;
use Wappointment\Services\Queue;
use Wappointment\Models\Job;
use Wappointment\Models\Order;
use Wappointment\Models\Appointment;
use Wappointment\Services\Payment;
use Wappointment\Services\Ticket;
class CleanPendingPaymentAppointment implements \Wappointment\Jobs\JobInterface
{
    public function handle()
    {
        $data = !\Wappointment\Services\Payment::isWooActive() ? $this->appointmentsToProcess() : \WappointmentAddonWoocommerce\Jobs\CleanPendingPaymentAppointment::getAppointmentsToReview();
        foreach ($data['orders'] as $orderData) {
            static::cancelReservations($orderData, static::getAppointment($data, $orderData));
        }
        static::registerJob(true);
    }
    public static function cancelReservations($orderData, $appointment = null)
    {
        $look_for_appointment = \is_null($appointment);
        if (!empty($orderData['reservations']) && !isset($orderData['already_cancelled'])) {
            foreach ($orderData['reservations'] as $reservation) {
                if (!empty($reservation['appointment_id'])) {
                    if ($look_for_appointment || $appointment->id !== (int) $reservation['appointment_id']) {
                        $appointment = \Wappointment\Models\Appointment::find((int) $reservation['appointment_id']);
                    }
                    if (!empty($appointment) && (int) $reservation['appointment_id'] === (int) $appointment->id) {
                        $ticket = apply_filters('wappointment_appointment_get_ticket', $appointment, $orderData['client_id']);
                        \Wappointment\Services\Ticket::cancelTrigger($ticket, !empty($reservation['slots']) ? $reservation['slots'] : false);
                    }
                }
            }
            static::flagCancelled($orderData);
        }
    }
    /**
     * allow to load the appointment of the order without refetching
     *
     * @param array $data
     * @param array $orderData
     * @return void
     */
    protected static function getAppointment($data, $orderData)
    {
        if (!empty($orderData['reservations'])) {
            foreach ($orderData['reservations'] as $reservation) {
                if (!empty($reservation['appointment_id'])) {
                    foreach ($data['appointments'] as $appointment) {
                        if ((int) $reservation['appointment_id'] === (int) $appointment->id) {
                            return $appointment;
                        }
                    }
                }
            }
        }
        return null;
    }
    protected static function flagCancelled($orderData)
    {
        //woo marker
        do_action('wappointment_woo_cancelled_order', $orderData);
        //standalone marker
        if (isset($orderData['orderObj']) && !\is_null($orderData['orderObj'])) {
            $orderData['orderObj']->setAutoCancelled();
        }
    }
    public function appointmentsToProcess()
    {
        // 1 - get orders that are pending for more than  the last X minutes
        $orders = \Wappointment\Models\Order::pending()->where('updated_at', '<', \Wappointment\ClassConnect\Carbon::now()->subSeconds(static::getDelayInSeconds())->format('Y-m-d H:i'))->get();
        $appointments = [];
        $ordersData = [];
        // 3 - delete the connected appointments
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $ordersData[] = ['client_id' => $order->client_id, 'reservations' => $order->options['reservations'], 'orderObj' => $order];
                foreach ($order->prices as $charge) {
                    $appointments[] = $charge->appointment;
                }
                //
            }
        }
        return ['appointments' => $appointments, 'orders' => $ordersData];
    }
    public static function registerJob($skipCheck = false)
    {
        if ($skipCheck || self::lastCheckExpired() && !self::hasJobInQueue('pending_clean')) {
            \Wappointment\Models\Job::where('queue', 'pending_clean')->delete();
            \Wappointment\Services\Queue::push('Wappointment\\Jobs\\CleanPendingPaymentAppointment', [], 'pending_clean', static::next());
        }
    }
    public static function lastCheckExpired()
    {
        return \Wappointment\Services\Settings::get('clean_last_check') === false || \Wappointment\Services\Settings::get('clean_last_check') < \time() - 5 * 60;
    }
    public static function hasJobInQueue($queue_key)
    {
        \Wappointment\Services\Settings::save('clean_last_check', \time());
        return \Wappointment\Models\Job::where('queue', $queue_key)->first();
    }
    public static function next()
    {
        return \time() + static::getDelayInSeconds();
    }
    public static function getDelayInSeconds()
    {
        return \Wappointment\Services\Settings::get('clean_pending_every') * 60;
    }
}
