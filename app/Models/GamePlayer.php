<?php

namespace App\Models;
use DB;
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
        $cards = $this->cards()->where('card_type', $type)->whereIn('id' , $cards_data)
        ->get();
  
        if (count($cards_data) == count($cards)){
            return $cards;
        }
        return False;
    }

    public function takePile(){
        if($this->game){
            $cards = $this->game->cards()->where('card_type','pile')->get();
        }
        else{
            $cards = $this->game()
            ->whereHas('cards', function ($query) {
                $query->where('card_type', 'pile');
            })
            ->with('cards')
            ->get();
        }
        
       
        

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



    public function sortCards(GamePlayer $player)
    {
 
        $cardRanks = $player->cards()->whereIn('card_type',['hand', 'open'])->get()->toArray();
        $cards = [];
        foreach($cardRanks as $index => $cardRank){
            $cards[$index] = $cardRank;
            $cards[$index]['card_rank'] = $this->convert_rank($cardRank['card_rank'] );
            
        }
  
        $sortedCards = $this->customSortCards($cards);

        $totalCards = count($sortedCards);

        // Define the number of cards to set as "hand" and "open."
        $cardsInHand = 3;
        $cardsInOpen = 3;

        // Loop through the array and update the card_type.
        for ($i = 0; $i < $totalCards; $i++) {
            if ($i < $cardsInHand) {
                $sortedCards[$i]['card_type'] = 'open';
            } elseif ($i >= $totalCards - $cardsInOpen) {
                $sortedCards[$i]['card_type'] = 'hand';
            }
        }

        foreach ($sortedCards as $cardData) {
            // Find the card by its ID or any unique identifier (e.g., $cardData['id']).
            $card = Card::find($cardData['id']);
        
            if ($card) {
                // Update the card_type field.
                $card->card_type = $cardData['card_type'];
                
                // Save the changes to the database.
                $card->save();
            }
        }

    }

    public function botPlayCard(){
        $cardTypes = $this->cards()->distinct()->pluck('card_type')->toArray();
        if (in_array('hand', $cardTypes)){
            $type = 'hand';
        }
        elseif (in_array('open', $cardTypes)){
            $type = 'open';
        }
        else{
            $type = 'closed';
        }
        
        
        $cardRanks = $this->cards()->where('card_type',$type )->get()->toArray();
       
        $cards = [];
        foreach($cardRanks as $index => $cardRank){
            $cards[$index] = (array) $cardRank;
            $cards[$index]['card_rank'] = $this->convert_rank($cardRank['card_rank'] );
            
        }
  
        $sortedCards = $this->customSortCards($cards);

        $lowestRankCards = $this->lowestRankCards($sortedCards);
        $canPlay = false;
        $playPosition = false;
        $play_cards= [];
        $previous_card = false;
        
        foreach($lowestRankCards as $card){
            
            if (!$previous_card){
                $previous_card = $card;
            }
            $card_object = (object) $card;
            $card_postition = $card['position'];
           
            if ($canPlay && $previous_card->position != $card_postition){
                break;
            }

            
            $result = $this->game->isValidCard([$card_object]);
          
            if ($result['status'] == 'success'){
                
                $play_cards[] = $card['id'];
                $canPlay = true;
            }
            
       
            $previous_card = $card_object;
        }

        if ($play_cards){
            
            $result =  ($this->game->playCard($this->id, $play_cards, $type));
            return $result;
            
        }
        else{
            if ($this->takePile()){
                return ['status' => 'success', 'action'=> 'took_pile'];
            }
            return ['status' => 'success', 'message'=> 'Failed to take pile'];
            
            
        }
        
       
        return $play_cards;

        
        
    }

    public function lowestRankCards($cards){
        
        
        $exceptions = [3, 2, 10, 14, 15];
        $lowestRank = false;
        $position = 1;
        $newCards = [];
        
        
        $cards = array_reverse($cards);
  
        // Determine the lowest rank (excluding exceptions).
        foreach ($cards as $cardData) {
            $cardRank = intval($cardData['card_rank']);

            if (!in_array($cardRank, $exceptions)) {
                if (!$lowestRank){
                    $lowestRank = $cardRank;
                }
                elseif($lowestRank != $cardRank){
                    $position++;
                }
                $cardData['position'] = $position;
            }
            else{
                if (!$lowestRank){
                    $lowestRank = $cardRank;
                }
                else{
                    $position++;
                }

                $cardData['position'] = $position;
            }
            $newCards[] = $cardData;
            
        }
        return $newCards;
    }
   

    private function customSortCards($cards)
    {
        
        usort($cards, function ($a, $b) {
            $rankOrder = [
                "10" => 1, // 10s
                "3" => 2,  // 3s
                "11" => 3, // Jack
                "12" => 4, // Queen
                "13" => 5, // King
                "14" => 6, // Ace
                "15" => 7, // Joker
                "2" => 8,  // 2s
            ];

            $rankA = isset($rankOrder[$a['card_rank']]) ? $rankOrder[$a['card_rank']] : 9;
            $rankB = isset($rankOrder[$b['card_rank']]) ? $rankOrder[$b['card_rank']] : 9;

            if ($rankA === $rankB) {
                return $a['card_rank'] < $b['card_rank'] ? 1 : -1;
            }

            return $rankA - $rankB;
        });


        return $cards;
    }

    private function convert_rank($current_rank){
        switch($current_rank){
            case 'Ace':
                $rank = 14;
                break;
            case 'King':
                $rank = 13;
                break;
            case 'Queen':
                $rank = 12;
                break;
            case 'Jack':
                $rank = 11;
                break;
            default:
                $rank = $current_rank;
                break;
        }
        return $rank;
    }

 

}
