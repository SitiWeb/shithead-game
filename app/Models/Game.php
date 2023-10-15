<?php

namespace App\Models;

use App\Events\GameUpdate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'created_by',
    ];

    public function players()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function startGame()
    {
        // Create and shuffle a deck of cards
        if ($this->status != 'created'){
            return false;
        }
        $deck = $this->createShuffledDeck();
        
        // Deal cards to players
        $players = $this->players()->get();
      
        foreach ($players as $player) {
            $closed = $deck->splice(0, 3); // Deal 3 cards to each player
            foreach($closed as $card){
                $card['card_type'] = 'closed';
                $card['game_player_id'] = $player->id;
                $this->cards()->create($card);
            }
            $open = $deck->splice(0, 3); // Deal 3 cards to each player
            foreach($open as $card){
                $card['card_type'] = 'open';
                $card['game_player_id'] = $player->id;
                $this->cards()->create($card);
            }
            $hand = $deck->splice(0, 3); // Deal 3 cards to each player
            foreach($hand as $card){
                $card['card_type'] = 'hand';
                $card['game_player_id'] = $player->id;
                $this->cards()->create($card);
            }
            
        }
       
        foreach($deck as $card){
            $this->cards()->create($card);
        }
        
        // Set the game status to "in progress"
        $this->update(['status' => 'starting']);
        return true;
    }





    /**
     * End the game and determine the winner.
     */
    public function endGame()
    {
        // Determine the winner based on the game's rules
        $winner = $this->determineWinner();

        // Update the game status to "completed"
        $this->update(['status' => 'completed']);

        // Perform any other cleanup tasks, if needed

        return $winner;
    }

    /**
     * Create and shuffle a deck of cards.
     */
    private function createShuffledDeck()
    {
        $suits = ['Hearts', 'Diamonds', 'Clubs', 'Spades'];
        $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King', 'Ace'];

        $deck = [];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $deck[] = [
                    'card_suit' => $suit,
                    'card_rank' => $rank,
                ];
            }
        }

        shuffle($deck);

        return collect($deck);
    }

    /**
     * Determine the winner of the game based on the game rules.
     */
    private function determineWinner()
    {
        // Determine the winner based on the game's rules
        // For example, check which player has no cards left
        $players = $this->players()->get();

        foreach ($players as $player) {
            if ($player->cards()->count() === 0) {
                return $player;
            }
        }

        return null; // No winner (draw or game still in progress)
    }

    /**
     * Handle the distribution of cards to players.
     *
     * @param Collection $deck A collection of cards to be distributed.
     */
    public function dealCardsToPlayers(Collection $deck)
    {
        // Get the list of players in the game
        $players = $this->players()->get();

        // Calculate the number of cards each player should receive
        $cardsPerPlayer = floor(count($deck) / count($players));

        // Distribute cards to each player
        foreach ($players as $player) {
            $hand = $deck->splice(0, $cardsPerPlayer);
            $player->dealCards($hand);
        }

        // If there are remaining cards in the deck, distribute them equally among players
        while (!$deck->isEmpty()) {
            foreach ($players as $player) {
                $card = $deck->shift();
                $player->dealCards(collect([$card]));
            }
        }
    }

    /**
     * Handle a player playing a card.
     *
     * @param GamePlayer $player The player who is playing the card.
     * @param array $playedCardData The card data representing the played card.
     * @return bool True if the card was played successfully, false otherwise.
     */
    public function playCard( $playerId, $played_card, $type)
    {
        event(new GameUpdate($this));
        return;
        $player = $this->players()->where('id' , $playerId)->first();
        $card = $this->cards()->where('id' , $played_card)->first();
        // Validate if the move is valid (e.g., based on rank and suit matching)
        if (!$this->validateMove($player, $card, $type)) {
            dd('Failed validation');
            return false; // The move is not valid
        }
        
        // Move the played card from the player's hand to the discard pile
        $this->moveCardToDiscard($player, $card);

        

        // Update the game state as needed (e.g., change the current player's turn)
        $this->fill_stack($player);

        if ($this->maybeDiscardPile()){
            $this->pileDiscard();
        }
        else{
            $this->current_turn = $this->nextPlayer();
            $this->save();
            
        }

        if($player->cards()->count() === 0){
            $this->endGame();
        }
        
        
        // You can add more logic based on your game's rules

        return true; // The card was played successfully
    }

    public function nextPlayer(){
        $current_turn = $this->current_turn;
        // Een array met 4 namen
        
        $players = $this->players()->orderBy('position')->get();
        $ids = $players->pluck('id')->toArray();
        // Zoek de index van de huidige naam in de array
        $index = array_search($current_turn , $ids);

        if ($index !== false) {
            // Verkrijg de volgende naam
            $volgendeIndex = ($index + 1) % count($ids);
            
            return $ids[$volgendeIndex];
        }

    }

    private function maybeDiscardPile() {
        $top_4 = $this->cards()->where(['card_type'=>'pile'])->orderBy('played_at','desc')->limit(4)->get();
        if (count($top_4) < 4){
            return false;
        }
        $rank = '';
        $rank_logic = true;
        $suit = '';
        $suit_logic = true;
        foreach($top_4 as $card){
            $current_rank = $this->convert_rank($card->card_rank);
            $current_suit = $card->card_suit;
            if (empty($rank)){
                $rank = $current_rank;
            }
            if ($rank != $current_rank && $rank != 3){
                $rank_logic = false;
            }

            if (empty($suit)){
                $suit = $current_suit;
            }
            if ($suit != $current_suit && $rank != 3){
                $suit_logic = false;
            }   
        }
        if ($suit_logic || $rank_logic){
      
            return True;
        }
        return False;
        
    }

    private function pileDiscard(){
        $cards = $this->cards()->where(['card_type'=>'pile']);
        if($cards->update(['card_type'=>'graveyard', 'played_at'=> null])){
            return true;
        }
        return false;
    }

    private function fill_stack($player){
        $count_cards = $player->cards()->where('card_type','hand')->count();
        if ($count_cards < 3){
            $player->drawCards(3 - $count_cards);
        }
    }

    /**
     * Move a card from the player's hand to the discard pile.
     *
     * @param GamePlayer $player The player who is playing the card.
     * @param array $cardData The card data representing the played card.
     */
    private function moveCardToDiscard(GamePlayer $player, $card)
    {
        

        if ($card) {
            $card->update(['card_type' => 'pile', 'game_player_id' => null, 'played_at' => date('Y-m-d H:i:s')]);
        }
        
    }

  /**
     * Check for special card effects and trigger them.
     *
     * @param array $playedCardData The card data representing the played card.
     */
    private function checkAndTriggerSpecialEffects($card)
    {
        $cardRank = $card->card_rank;

        switch ($cardRank) {
            // case '2':
            //     $this->handleTwoSpecialEffect();
            //     break;

            case '10':
                $this->handleTenSpecialEffect();
                break;

            // case '7':
            //     $this->handleSevenSpecialEffect();
            //     break;

            // Add cases for other special cards if needed

            default:
                // No special effect for this card
                break;
        }
    }



    /**
     * Handle the special effect of a played "2" card.
     */
    private function handleTwoSpecialEffect()
    {
        // Get the next player in the turn order
        $nextPlayer = $this->getNextPlayer();

        // Implement the logic for the "2" card's special effect
        // For example, skip the next player's turn
        $nextPlayer->skipTurn();
    }

    /**
     * Handle the special effect of a played "10" card.
     */
    private function handleTenSpecialEffect()
    {
        // Implement the logic for the "10" card's special effect
        // For example, reverse the direction of play or clear the discard pile
        // In this example, we reverse the direction of play by changing a game state variable

        if ($this->isClockwisePlay()) {
            $this->update(['play_direction' => 'counterclockwise']);
        } else {
            $this->update(['play_direction' => 'clockwise']);
        }
    }


    public function validateMove( $player, $card, $type)
    {
        
        // Check if it's the player's turn
        if (!$this->isPlayerTurn($player)) {
            dd('Not players turn');
            return false;
        }
        
        // Check if the player has the card they are trying to play
        if (!$player->hasCard($card, $type)) {
            dd('Player doesnt have card');
            return false;
        }

        // Check if the played card matches the rank or suit of the top discard card
        if (!$this->isValidCard($card)) {
            if ($type == 'closed'){
                $card->card_type = 'hand';
                $card->save();
                return false;
            }
            dd('Is not valid');
            return false;
        }

        // If all checks pass, the move is valid
        return true;
    }

    /**
     * Check if it's the player's turn to make a move.
     *
     * @param GamePlayer $player
     * @return bool
     */
    private function isPlayerTurn($player)
    {
        // Get the list of players in the game and their positions
        //$players = $this->players()->orderBy('position')->get();
        if ($this->current_turn === 0){
            $beginner = $this->decideBeginner();
            if ($beginner){
                $this->current_turn = $beginner;
                $this->save();
            }
        }
        
        if ($this->current_turn === $player->id){
            return True;
        }
        return False;
    }

    private function decideBeginner(){
        $players = $this->players()->get();
        $lowest_card = 14;
        $players_with_lowest = [];
        
        foreach($players as $player){
            
            foreach($player->cards()->get() as $card){
         
                if ($card->card_type == 'hand'){
                    $rank = $this->convert_rank($card->card_rank);
                    if ($rank > $lowest_card){
                        continue;
                    }
                    if ($rank < $lowest_card){
                        $players_with_lowest = [$player->id]; 
                    }
                    if ($rank == $lowest_card){
                        $players_with_lowest[] = $player->id;
                    }
                }
            }
        }
        $values = array_count_values($players_with_lowest);
        arsort($values);
        $most_lowest = array_slice(array_keys($values), 0, 1, true);

        return $most_lowest[0];
    }

    private function convert_rank($current_rank){
        switch($current_rank){
            case 'Ace':
                $rank = 14;
                break;
            case 'Kings':
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

    /**
     * Get the top card on the discard pile.
     *
     * @return array|null The card data of the top discard card, or null if the pile is empty.
     */
    private function getTopDiscardCard()
    {
        // Retrieve the game's discard pile, assuming it's a collection of cards
        $discardPile = $this->cards()->where('location', 'discard')->get();

        // Check if the discard pile is empty
        if ($discardPile->isEmpty()) {
            return null; // No top discard card (pile is empty)
        }

        // The top discard card is the last card in the pile
        $topDiscardCard = $discardPile->last();

        // Return the card data (suit and rank) as an array
        return [
            'card_suit' => $topDiscardCard->suit,
            'card_rank' => $topDiscardCard->rank,
        ];
    }

    public function getTopCard(){
        return $this->cards()->where(['card_type'=>'pile'])->orderBy('played_at','desc')->first();
    }

    /**
     * Check if a played card matches the rank or suit of the top discard card.
     *
     * @param array $playedCardData The card data representing the played card.
     * @param array|null $topDiscardCardData The card data representing the top discard card, or null if the pile is empty.
     * @return bool True if the move is valid, false otherwise.
     */
    private function isValidCard($card)
    {
        $topCard = $this->getTopCard();
        // If the discard pile is empty, any card can be played
        if ($topCard === null) {
            return true;
        }

        // Extract the rank and suit of the played card and the top discard card
        $playedCardRank = $this->convert_rank($card->card_rank);
       
        $topDiscardCardRank =  $this->convert_rank($topCard->card_rank);
       
        if ($playedCardRank == 2){
            return true;
        } 

        if ($playedCardRank == 3){
            return true;
        }
 
        if ($playedCardRank == 10){
            return true;
        }

        // Check if the played card's rank matches the top discard card's rank
        // or if the played card's suit matches the top discard card's suit
        if ($topDiscardCardRank!= 7 && $playedCardRank >= $topDiscardCardRank) {
            return true; // The move is valid
        }
        if ($topDiscardCardRank == 7 && $playedCardRank <= $topDiscardCardRank) {
            return true; // The move is valid
        }
       
        return false; // The move is not valid
    }

    /**
     * Retrieve and return the current game state.
     *
     * @return array The game state data.
     */
    public function getGameState()
    {
        // Get the top card on the discard pile
        $topDiscardCard = $this->getTopDiscardCard();

        // Get the status of the game (e.g., in progress, completed)
        $gameStatus = $this->status;

        // Get the cards in each player's hand
        $players = $this->players()->get();
        $playerHands = [];

        foreach ($players as $player) {
            $hand = $player->cards()->where('location', 'hand')->get();
            $playerHands[$player->id] = $hand;
        }

        // Assemble the game state data
        $gameState = [
            'top_discard_card' => $topDiscardCard,
            'game_status' => $gameStatus,
            'player_hands' => $playerHands,
        ];

        return $gameState;
    }

    /**
     * Retrieve and return a history of game actions and moves made by players.
     *
     * @return Collection The collection of game history entries.
     */
    public function getGameHistory()
    {
        // Assuming you have a 'game_history' table to store game actions
        $gameHistory = GameHistory::where('game_id', $this->id)->orderBy('created_at', 'asc')->get();

        return $gameHistory;
    }

    public function switchCards($card_1, $card_2, $player){
        $data_1 = $this->cards()->where(['id' => $card_1, 'game_player_id' => $player, 'card_type' => 'open'])->first();
        if(!$data_1){
            return false;
        }
        $data_2 = $this->cards()->where(['id' => $card_2, 'game_player_id' => $player, 'card_type' => 'hand'])->first();
        if(!$data_2){
            return false;
        }

        $data_1->card_type = 'hand';
        $data_2->card_type = 'open';
        $data_1->save();
        $data_2->save();
        return true;
        
    }
}
