<?php

namespace App\Http\Controllers;
use Pusher\Pusher;
use Illuminate\Support\Facades\Validator;
use App\Events\GameUpdate;
use App\Events\GameListUpdate;
use App\Models\Game;
use Auth;
use App\Models\GamePlayer;
use GMP;
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

    public function gameData(Game $game)
    {
        $cards = $game->cards()->select('id', 'card_rank', 'card_type', 'card_suit', 'game_player_id')->where('game_player_id',null)->orderBy('played_at','desc')->get();
        $players = $game->players()->with('cards')->get();
        $players_array = [];
        foreach($players as $player){
            foreach($player->cards as $card){
                $players_array[$player->id][$card->card_type][] = $card;
            } 
        }
   
        return response()->json(['message' => 'Game Data', 'game' => $game, 'cards' => $cards, 'players' => $players_array]);
    }

    public function lobbyData()
    {
        $availableGames = Game::where('status', 'created')
            ->orWhere('status', 'LIKE', 'starting%')
            ->with('players') // Load the related players
            ->get();
        return response()->json(['message' => 'Game list Data', 'lobby' => $availableGames]);
    }

    public function lobbylobbyData(Game $game){
        $game->players = $game->players()->get();
        return response()->json(['message' => 'Lobby Data', 'lobby' => $game]);
    }

    public function leaveGame(Game $game)
    {
        // Find and update records where player_id is equal to 6
        $player = $game->players->where('user_id', Auth::user()->id)->first();
        $success = false;
        if ($player) {
            $player->user_id = null;
            $player->save();
            $success = true;
            
        }

        
        if ($success){
            if ($game->players->whereNotNull('user_id')->count() == 0){
                $game->status = 'aborted';
                $game->save();

            }
            event(new GameListUpdate());
            return response()->json(['status' => 'success', 'message' => 'Succesfully left lobby']);
        }
        if ($game->players->whereNotNull('user_id')->count() == 0){
            $game->status = 'aborted';
            $game->save();
            event(new GameListUpdate());
            return response()->json(['status' => 'error', 'message' => 'Deleted room']);
        }
        event(new GameListUpdate());
        return response()->json(['status' => 'error', 'message' => 'Failed to leave lobby']);
   
       
    }


    /**
     * Create a new game instance and initialize players.
     */
    public function createGame(Request $request)
    {
        // Validate and sanitize the input data from the request (e.g., number of players)
         // Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', // You can adjust the validation rules as needed
            'num_players' => 'required|integer|min:2|max:4', // Example rules for the number of players
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            event(new GameListUpdate());
            return response()->json(['status' => 'error', 'message' => implode(", ",$validator->errors()->all())]);
        }
        // Create a new game instance
        $game = new Game();
        $game->status = 'created'; // Set the initial game status (you can use your own status definitions)
        $game->name = $request->input('name');
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
        event(new GameListUpdate());
        return response()->json(['status'=> 'success','message' => 'Game created successfully', 'game_id' => $game->id]);
    }

    public function joinGame(Game $game)
    {
      
        $players = $game->players; // This will retrieve the players related to the game.

        // Now you can work with the $players collection.
        $can_join = false;
        $user_id = Auth::user()->id;
        foreach ($players as $player){
            if ($player->user_id == $user_id){
                event(new GameListUpdate());
                return response()->json(['status'=> 'error','message' => 'You are already in this lobby', 'game_id' => $game->id]);
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
            event(new GameListUpdate());
            return response()->json(['status'=> 'success','message' => 'You joined this lobby', 'game_id' => $game->id]);
        }
        event(new GameListUpdate());
        return response()->json(['status'=> 'error','message' => 'Failed to join lobby', 'game_id' => $game->id]);
    }

    public function showLobby(Game $game){
       
        return view('games.lobby', ['game' => $game]);
    }

    public function botGame(Game $game){
        $players = $game->players; // This will retrieve the players related to the game.

        // Now you can work with the $players collection.
        $can_join = false;
        
        foreach ($players as $player){
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
            $spot->user_id = 3;
            $spot->save();
            event(new GameListUpdate());
            return response()->json(['status'=> 'success','message' => 'Bot successfully joined', 'game_id' => $game->id]);
        }
        return response()->json(['status'=> 'error','message' => 'Bot cant join', 'game_id' => $game->id]);
  
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
            event(new GameListUpdate());
            return  redirect()->route('games.show', ['game' => $game ]);
        }
        event(new GameListUpdate());
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
                        $result = $this->playCard($request, $game, $request->input('player'), $request->input('card.hand'), 'hand');
                    }elseif($request->has('card.closed') && $request->has('player')){
                        $result = $this->playCard($request, $game, $request->input('player'), $request->input('card.closed'), 'closed');
                    }
                    elseif($request->has('card.open') && $request->has('player')){
                        $result = $this->playCard($request, $game, $request->input('player'), $request->input('card.open'), 'open');
                    }
                    else{
                        return response()->json(['status'=> 'error','message' => 'Unknown or Missing action', 'game_id' => $game->id]);
                    }
                    return response()->json($result);
                   
                case 'draw_pile':
                    $result = $this->DrawPile($request, $game);
                    if($result['status'] ==  'success'){
                        if ($result['game']->current_turn){
                            $playerIdToFind = 3;
                            $foundPlayer = $result['game']->players->first(function ($player) use ($playerIdToFind) {
                                return $player->user_id === $playerIdToFind;
                            });
                            event(new GameUpdate($game));
                            if ($foundPlayer){
                                sleep(2);
                                $result = $foundPlayer->botPlayCard();
                                if ($result['status'] == 'success'){
                                    $result['game']->current_turn = $result['game']->nextPlayer();
                                    $result['game']->save();
                                    event(new GameUpdate($result['game']));
                                    $result['message'] = 'Bot played turn';
                                    return response()->json($result);
                                }
                                
                            }
                         
                            return response()->json($result);
                        }
                      
                    }
                    return response()->json($result);
                case 'send_update':
                    $current_player = $game->current_turn;
                    foreach($game->players as $player){
                        if ( $current_player == $player-> id && $player->user_id == 3){
                            $result = $player->botPlayCard();
                            if (isset($result['status']) && $result['status'] == 'success'){           
                                $game->current_turn = $game->nextPlayer();
                                $game->save();
                                event(new GameUpdate($game));
                                $result['message'] = 'Bot played turn';
                            }
                        }
                    }
                    event(new GameUpdate($game));
                    return response()->json(['status'=>'success','message' => 'Sent an update to all players', 'game_id' => $game->id]);
                default:
                    return response()->json(['message' => 'Unknown or Missing action', 'game_id' => $game->id]);
            }
        }
    }


    public function DrawPile($request, $game){
        if ($request->has(['player'])) {
            $player = GamePlayer::where('id',$request->input('player'))->first();
            if ($player){
                
                if($player->takePile()){
                    $game = $player->game()->first();
                    $game->current_turn = $game->nextPlayer();
                    $game->save();
                    return ['status' => 'success', 'message' => 'Succesfully drew pile', 'game' => $game];
                }
                return ['status' => 'error', 'message' => 'There is no pile to take'];
            }
            return ['status' => 'error', 'message' => 'Player not found'];
        }
    }

    public function ready(Request $request, Game $game){
    
        
        
        if ($request->has(['player'])) {
            if ($game->players()->where('id', $request->input('player'))->update(['is_ready'=>true])){
                $is_ready = true;
                foreach($game->players()->get() as $player){
                    if ($player->user_id === 3){
                        $player->sortCards($player);
                        $player->is_ready = true;
                        $player->save();
                    }
                    if (!$player->is_ready){
                 
                        $is_ready = false;
                    }
                }
                if ($is_ready){
                    $game->status = 'in_progress';
                    $game->save();
                    return ['status' => 'success', 'message' => 'Everyone is ready, starting game'];
                }
                return ['status' => 'success', 'message' => 'Set status to is ready'];
            }
            return ['status' => 'error', 'message' => 'Failed to set status to ready'];
        }
    }

    public function switch(Request $request, Game $game){

         // Check if the request has both keys "card[open]" and "card[hand]"
        if ($game->status != 'starting'){
            return response()->json(['status' => 'error', 'message' => 'Game has already started or still in lobby', 'game_id' => $game->id]);
        }
        
        if ($request->has(['card.open', 'card.hand'])) {
            // Check if the values of both keys are numeric
            if (($request->input('card.open')) && ($request->input('card.hand'))) {
                if( $game->switchCards($request->input('card.open'), $request->input('card.hand'), $request->input('player'))){
                    event(new GameUpdate($game));
                    return response()->json(['status' => 'success', 'message' => 'Card has been switched', 'game_id' => $game->id]);
                }
                return response()->json(['status' => 'error', 'message' => 'Something went wrong', 'game_id' => $game->id]);

            } else {
                return response()->json(['status' => 'error', 'message' => 'Values must be numeric', 'game_id' => $game->id]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'One or both values are missing', 'game_id' => $game->id]);
        }
    }

    /**
     * Play a card from a player's hand.
     */
    public function playCard(Request $request, Game $game, $playerId, $cardId, $type = 'hand')
    {
        $types = ['hand', 'closed', 'open'];
        if (!in_array($type, $types)){
            return ['status' => 'Error', 'message' => 'Type doesnt exist'];
        }
        $result = $game->playCard($game->current_turn , $cardId, $type);
        $do_turn = true;
        $result = ['status' => 'error','message' => 'unknown error'];
        while ($do_turn){
            
            
            event(new GameUpdate($game));
            if ($result['status'] == 'success'){
                $game = Game::find($game->id);
                $playerIdToFind = $game->current_turn;
                foreach($game->players as $player){
                    if ($player->id == $playerIdToFind && $player->user_id == 3){
                        $foundPlayer = $player;
                    }
                }

                if ($foundPlayer){
                    // next turn is a bot
                    sleep(2);
                    $result = $foundPlayer->botPlayCard();
                    if (isset($result['status']) && $result['status'] == 'success'){           
                        $game->current_turn = $game->nextPlayer();
                        $game->save();
                        event(new GameUpdate($game));
                        $result['message'] = 'Bot played turn';
                    }
                }
                else{
                    break;
                }
            }else{
                
                break;
            }

        }  
        return $result;
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
