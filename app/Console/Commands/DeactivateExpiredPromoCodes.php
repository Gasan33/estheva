<?php

namespace App\Console\Commands;

use App\Models\PromoCodes;
use Illuminate\Console\Command;

class DeactivateExpiredPromoCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-expired-promo-codes';
    // protected $signature = 'promo:deactivate-expired';
    // protected $description = 'Deactivate expired promo codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired promo codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        PromoCodes::where('expiration_date', '<', now())->update(['status' => 'expired']);
        $this->info('Expired promo codes deactivated successfully.');
    }
}
