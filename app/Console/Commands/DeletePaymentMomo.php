<?php

namespace App\Console\Commands;

use App\Models\PaymentMomo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;

class DeletePaymentMomo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:delete-payment-momo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete overdue momo transaction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $now = Carbon::now()->timestamp;
            $payments = PaymentMomo::where('status', PaymentMomo::STATUS['UNPAID'])->get();
            foreach ($payments as $payment) {
                if ($now - $payment->time > 600) {
                    $payment->delete();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error delete overdue momo transaction', [
                'method' => __METHOD__,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
