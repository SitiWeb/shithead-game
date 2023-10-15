<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Game; // 
class UpdateGameStatus extends Command
{
   

    
    protected $signature = 'games:update-status';
    protected $description = 'Update game statuses';

    public function handle()
    {
        $oneHourAgo = Carbon::now()->subHour();

        // Update games created more than an hour ago to "aborted"
        Game::where('created_at', '<', $oneHourAgo)
            ->where('status', 'created') // You can adjust this condition as needed
            ->update(['status' => 'aborted']);

        $this->info('Game statuses updated successfully.');
    }
}
