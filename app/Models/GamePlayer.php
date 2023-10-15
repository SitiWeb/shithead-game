<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlayer extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'game_id', 'position'];

    /**
     * Get the user associated with the game player.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game associated with the game player.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the cards held by the game player.
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Deal cards to the game player.
     *
     * @param \Illuminate\Support\Collection $cards
     */
    

    /**
     * Skip the game player's turn.
     */
    public function skipTurn()
    {
        // Get the current game from the player
        $game = $this->game;

        // Determine the player order in the game
        $players = $game->players()->orderBy('position')->get();

        // Find the position of the current player in the player order
        $playerPosition = $players->search(function ($player) {
            return $player->id === $this->id;
        });

        if ($playerPosition !== false) {
            // Calculate the next player's position in a circular manner
            $nextPlayerPosition = ($playerPosition + 1) % count($players);

            // Get the next player based on their position
            $nextPlayer = $players[$nextPlayerPosition];

            // Update the game state to make the next player the current player
            $game->update(['current_player_id' => $nextPlayer->id]);
        }
    }

    public function hasCard($cards_data, $type){
        
        $cards = $this->cards()
    ->whereIn('id', $cards_data)
    ->where('game_player_id', $this->id)
    ->where('card_type', $type)
    ->get();
        if (count($cards_data) == count($cards)){
            return $cards;
        }
        return False;
    }

    public function takePile(){
        $game = $this->game;
    
        $cards = $game->cards()->where('card_type', 'pile')->get();

        // Check if there are any cards to update
        if ($cards->isNotEmpty()) {
            $cards->each(function ($card) {
                $card->update([
                    'card_type' => 'hand',
                    'game_player_id' => $this->id,
                    'played_at' => null,
                ]);
            });
       

            return true;
        } else {
            return false; // Handle the case where there are no cards to update.
        }

        
    }

   /**
     * Draw cards into the game player's hand.
     *
     * @param int $numCards Number of cards to draw.
     */
    public function drawCards($numCards)
    {
        // Get the current game associated with the player
        $game = $this->game;
       
        // Check if there are enough cards in the deck to draw
        $deck = $game->cards()->where('card_type', 'deck')->get();
        
        if ($deck->count() < $numCards) {
            // Handle the case where there are not enough cards in the deck
            // You can reshuffle the discard pile back into the deck or end the game
            // Here, we'll assume that the game ends when the deck runs out of cards
            return;
        }
  
        // Draw the specified number of cards from the deck
        $cardsToDraw = $deck->take($numCards);
       
        // Update the card locations to 'hand' for the drawn cards
        $cardsToDraw->each(function ($card) {
            $card->update(['card_type' => 'hand', 'game_player_id' => $this->id]);
        });
    }

 

}
