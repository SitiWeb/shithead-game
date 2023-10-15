<?php

namespace App\Http\Controllers;
use Pusher\Pusher;
use App\Events\GameUpdate;
use App\models\Game;
use App\models\Card;
use Auth;
use App\models\GamePlayer;
//use App\models\Card; // Assuming you have a Card model

use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a list of available games (lobby page).
     */
    public function index()
    {
        // Retrieve a list of available games from your database
        $availableGames = Game::where('status', 'created')->get();

        // Pass the list of games to a view and render the lobby page
        return view('games.index', ['games' => $availableGames]);
    }
    /**
     * Create a new game instance and initialize players.
     */
    public function createGame(Request $request)
    {
        // Validate and sanitize the input data from the request (e.g., number of players)

        // Create a new game instance
        $game = new Game();
        $game->status = 'created'; // Set the initial game status (you can use your own status definitions)
        $game->name = 'tmp';
        // Associate the game with the current user
        $game->created_by = Auth::user()->id;
        // Save the game to the database
        $game->save();

        // Initialize players (you can customize this based on your game's player setup)
        $numPlayers = $request->input('num_players'); // Get the number of players from the request
        for ($i = 1; $i <= $numPlayers; $i++) {
            
            $player = new GamePlayer();
            if ($i == 1){
                $player->user_id = Auth::user()->id;
            }
            $player->game_id = $game->id;
           
            $player->position = $i; // Assign player positions based on the order they join the game
            $player->save();
        }

        // Optionally, set up additional game-specific initialization logic here

        return response()->json(['message' => 'Game created successfully', 'game_id' => $game->id]);
    }

    public function joinGame(Game $game)
    {
        $players = $game->players; // This will retrieve the players related to the game.

        // Now you can work with the $players collection.
        $can_join = false;
        $user_id = Auth::user()->id;
        foreach ($players as $player){
            if ($player->user_id == $user_id){
                return response()->json(['message' => 'You are already in this lobby', 'game_id' => $game->id]);
            }

            if ($player->user_id){
                // Slot taken
                continue;
            }

            if ($player->user_id === null){
                $can_join = true;
                $spot = $player;
            }
        }
        
        if ($can_join){
            $spot->user_id = $user_id;
            $spot->save();
            return response()->json(['message' => 'You joined this lobby', 'game_id' => $game->id]);
        }
        return response()->json(['message' => 'Failed to join lobby', 'game_id' => $game->id]);
    }

    public function showLobby(Game $game){
       
        return view('games.lobby', ['game' => $game]);
    }

    public function showBoard(Game $game){
        
        
        return view('games.board', ['game' => $game]);
    }

    /**
     * Start the game by dealing cards and determining the starting player.
     */
    public function startGame(Request $request, Game $game)
    {
        if ($game->startGame()){
            return  redirect()->route('games.show', ['game' => $game ]);
        }
        return view('games.lobby', ['game' => $game]);
        // Logic to start the game, including dealing cards and determining the starting player
    }

    public function action(Request $request, Game $game){
        if ($request->has('action')){
            switch($request->input('action')){
                case 'switch':
                    return $this->switch($request, $game);
                case 'ready':
                    return $this->ready($request, $game);
                case 'play_card':
                    if ($request->has('card.hand') && $request->has('player')){
                        return $this->playCard($request, $game, $request->input('player'), $request->input('card.hand'), 'hand');
                    }elseif($request->has('card.closed') && $request->has('player')){
                        return $this->playCard($request, $game, $request->input('player'), $request->input('card.closed'), 'closed');
                    }
                    elseif($request->has('card.open') && $request->has('player')){
                        return $this->playCard($request, $game, $request->input('player'), $request->input('card.open'), 'open');
                    }
                    return response()->json(['message' => 'Missing a parameter', 'game_id' => $game->id]);
                case 'draw_pile':
                    return $this->DrawPile($request, $game);

                case 'send_update':
                    
                    // New Pusher instance with our config data
                    $pusher = new Pusher(
                        config('broadcasting.connections.pusher.key'),
                        config('broadcasting.connections.pusher.secret'),
                        config('broadcasting.connections.pusher.app_id'),
                        config('broadcasting.connections.pusher.options')
                    );
                        
                    // Enable pusher logging - I used an anonymous class and the Monolog
                    // $pusher->set_logger(new class {
                    //     public function log($msg)
                    //     {
                    //             \Log::info($msg);
                    //     }
                    // });
                        
                    // Your data that you would like to send to Pusher
                    $data = ['text' => 'hello world from Laravel 5.3'];
                        
                    // Sending the data to channel: "test_channel" with "my_event" event
                    $pusher->trigger( 'game.4', 'GameUpdate', $data);
                        
                    return 'ok'; 
                default:
                    return response()->json(['message' => 'Unknown or Missing action', 'game_id' => $game->id]);
            }
        }
    }


    public function DrawPile($request, $game){
        if ($request->has(['player'])) {
            $player = GamePlayer::where('id',$request->input('player'))->first();
            if ($player){
                
                $player->takePile();
                $game = $player->game()->first();
                $game->current_turn = $game->nextPlayer();
                $game->save();
            }
            return  redirect()->route('games.show', ['game' => $game ]);

        }
    }

    public function ready(Request $request, Game $game){
        if ($request->has(['player'])) {
            if ($game->players()->where('id', $request->input('player'))->update(['is_ready'=>true])){
                $is_ready = true;
                foreach($game->players()->get() as $player){
                    if (!$player->is_ready){
                 
                        $is_ready = false;
                    }
                }
                if ($is_ready){
                    $game->status = 'in_progress';
                    $game->save();
                }
                return  redirect()->route('games.show', ['game' => $game ]);
            }
        }
    }

    public function switch(Request $request, Game $game){

         // Check if the request has both keys "card[open]" and "card[hand]"
        if ($game->status != 'starting'){
            return response()->json(['message' => 'Game has already started or still in lobby', 'game_id' => $game->id]);
        }
        
        if ($request->has(['card.open', 'card.hand'])) {
            // Check if the values of both keys are numeric
            if (is_numeric($request->input('card.open')) && is_numeric($request->input('card.hand'))) {
                if( $game->switchCards($request->input('card.open'), $request->input('card.hand'), $request->input('player'))){
                    return  redirect()->route('games.show', ['game' => $game ]);
                }
                return response()->json(['message' => 'Something went wrong', 'game_id' => $game->id]);

            } else {
                return response()->json(['message' => 'Values must be numeric', 'game_id' => $game->id]);
            }
        } else {
            return response()->json(['message' => 'One or both values are missing', 'game_id' => $game->id]);
        }
    }

    /**
     * Play a card from a player's hand.
     */
    public function playCard(Request $request, Game $game, $playerId, $cardId, $type = 'hand')
    {
        if ($game->playCard($playerId, $cardId, $type)){
            event(new GameUpdate($game));
            return  redirect()->route('games.show', ['game' => $game ]);
        }

        // $played_card = $game->cards()->where(['game_player_id'=> $playerId, 'id'=> $cardId])->first();
        // if (!$played_card){
        //     return response()->json(['message' => 'Card doesnt belong to player', 'game_id' => $game->id]);
        // }
        // $game->playCard($game, $played_card, $playerId);
        // Logic to handle playing a card from a player's hand
        // Validate the move and update the game state accordingly


    }

    /**
     * Draw cards into a player's hand.
     */
    public function drawCards(Request $request, $gameId, $playerId, $numCards)
    {
        // Logic to handle drawing cards into a player's hand
        // Validate the move and update the game state accordingly
    }

    /**
     * Skip the current player's turn.
     */
    public function skipTurn(Request $request, $gameId, $playerId)
    {
        // Logic to skip the current player's turn
        // Update the game state to pass the turn to the next player
    }

    /**
     * Retrieve and display the current game state.
     */
    public function getGameState(Request $request, $gameId)
    {
        // Logic to retrieve and display the current game state
        // Include player hands, top card on the discard pile, current player, etc.
    }

    /**
     * Retrieve and display the game history.
     */
    public function getGameHistory(Request $request, $gameId)
    {
        // Logic to retrieve and display the game history, including player actions and moves
    }
}
