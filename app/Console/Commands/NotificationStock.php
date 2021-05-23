<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class NotificationStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ramz:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification for stock on RAMZ';

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
        $cnt_item = 0;
        $message = '<b>[List of Expired Stock]</b>' . chr(10);
        $message .= chr(10);

        $item5months = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addMonths(5)->toDateString())->get();
        if ($item5months->count() > 0) {

            $message .= '5 Months from now' . chr(10);
            $cnt_item += $item5months->count();

            foreach ($item5months as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs)" . chr(10);
            }
            $message .= chr(10);
        }

        $item3months = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addMonths(3)->toDateString())->get();
        if ($item3months->count() > 0) {

            $message .= '3 Months from now. ' . chr(10);
            $cnt_item += $item3months->count();

            foreach ($item3months as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs) " . chr(10);
            }
            $message .= chr(10);
        }

        $item1months = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addMonths(1)->toDateString())->get();
        if ($item1months->count() > 0) {

            $message .= '1 Month from now. ' . chr(10);
            $cnt_item += $item1months->count();

            foreach ($item1months as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs) " . chr(10);
            }
            $message .= chr(10);
        }

        $item2weeks = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addWeeks(2)->toDateString())->get();
        if ($item2weeks->count() > 0) {

            $message .= '2 Weeks from now. ' . chr(10);
            $cnt_item += $item2weeks->count();

            foreach ($item2weeks as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs) " . chr(10);
            }
            $message .= chr(10);
        }

        $item1weeks = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addWeeks(1)->toDateString())->get();
        if ($item1weeks->count() > 0) {

            $message .= '1 Weeks from now. ' . chr(10);
            $cnt_item += $item1weeks->count();

            foreach ($item1weeks as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs) " . chr(10);
            }
            $message .= chr(10);
        }

        $item1Day = Stock::select(DB::raw('`barcode`, `name`, `expired_date`, SUM(CASE WHEN `position`= \'IN\' THEN ABS(stock) ELSE -1 * stock END)  AS summarize_stock'))
            ->groupBy('barcode', 'name', 'expired_date')->whereDate('expired_date', '=', Carbon::today()->addDays(1)->toDateString())->get();

        if ($item1Day->count() > 0) {

            $message .= '1 Day from now. ' . chr(10);
            $cnt_item += $item1Day->count();

            foreach ($item1Day as $key => $item) {
                $message .= "- $item->name x $item->summarize_stock (pcs) " . chr(10);
            }
            $message .= chr(10);
        }

        if ($cnt_item < 1) {
            $message .= " Yay, your stocks are fresh and new.";
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'basic ' . env('ONE_SIGNAL_KEY'),
            'charset' => 'utf-8'
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => env('ONE_SIGNAL_APP_ID'),
            'included_segments' => [
                'Subscribed Users'
            ],
            'contents' => [
                'en' => "$cnt_item item(s) expired soon !"
            ],
            'web_buttons' => [
                [
                    'id'    => 'link-button',
                    'text'  => 'Lihat',
                    'url'   => 'https://lmp-dashboard.dapurkode.com/ramz'
                ]
            ]
        ]);

        $response = Telegram::sendMessage([
            'chat_id' => -1001483271021,
            'text'    => $message,
            'parse_mode' => 'HTML'
        ]);
        $this->info("Response: $response");
    }
}
