<?php

namespace App\Console\Commands;

use App\Mail\BookingExpiredMail;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending';
    protected $description = 'Expire pending bookings that have passed their expiry time';

    public function handle()
    {
        $this->info('Checking for expired pending bookings...');

        // ✅ Expire bookings that have passed expires_at
        $expiredBookings = Appointment::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->whereNull('expired_at')
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            try {
                $booking->update([
                    'status' => 'expired',
                    'expired_at' => now(),
                ]);

                // ✅ Send email notification
                if ($booking->email) {
                    Mail::to($booking->email)->send(new BookingExpiredMail($booking));
                }

                $count++;
                $this->info("Expired booking #{$booking->id} for {$booking->user_name}");

            } catch (\Exception $e) {
                Log::error('Failed to expire booking: ' . $e->getMessage());
                $this->error("Failed to expire booking #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("✅ Total expired: {$count}");

        // ✅ Send reminder for expiring bookings (1 hour before)
        $expiringSoon = Appointment::where('status', 'pending')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addHour())
            ->where('expiry_reminder_sent', false)
            ->get();

        $reminderCount = 0;

        foreach ($expiringSoon as $booking) {
            try {
                if ($booking->email) {
                    Mail::to($booking->email)->send(new BookingExpiringSoonMail($booking));
                }

                $booking->update(['expiry_reminder_sent' => true]);
                $reminderCount++;

                $this->info("Sent reminder for booking #{$booking->id}");

            } catch (\Exception $e) {
                Log::error('Failed to send reminder: ' . $e->getMessage());
                $this->error("Failed to send reminder for booking #{$booking->id}");
            }
        }

        $this->info("✅ Reminders sent: {$reminderCount}");

        return Command::SUCCESS;
    }
}