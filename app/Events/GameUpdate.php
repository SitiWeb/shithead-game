<?php

namespace App\Events;
use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
     /**
     * The game instance.
     *
     * @var \App\Game
     */
    public $game;

    /**
     * The cards related to the game.
     *
     * @var array
     */
    public $cards;

    /**
     * The cards related to the game.
     *
     * @var array
     */
    public $players;

    /**
     * Create a new event instance.
     *
     * @param \App\Game $game
     * @param array $cards
     */
    public function __construct($game)
    {
        $cards = $game->cards()->select('id', 'card_rank', 'card_type', 'card_suit', 'game_player_id')->where('game_player_id',null)->orderBy('played_at','desc')->get();
        $this->game = $game;
        $this->cards = $cards;
        $players = $game->players()->with('cards')->get();
        foreach($players as $player){
            foreach($player->cards as $card){
                $this->players[$player->id][$card->card_type][] = $card;
            }
            
        }
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
   
        return new Channel('game.'.$this->game->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'game' => $this->game,
            'players' => $this->players,
            'cards' => $this->cards
        ];
    }
}
