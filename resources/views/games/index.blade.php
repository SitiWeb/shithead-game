@extends('layouts.app')

@section('content')
    <div class="position-fixed top-0 right-0 p-3" style="z-index: 11; width:300px;">
        <div id="toast-container" class="toast-container">
        </div>
    </div>
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <h1>Available Games</h1>
                <table class="table table-dark">

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Made by</th>
                            <th scope="col">Num of players</th>
                            <th scope="col">Join</th>
                            <th scope="col">Lobby</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($games as $game)
                            <tr>
                                <th scope="row">{{ $game->id }}</th>
                                <td>{{ $game->name }}</td>
                                <td>{{ $game->created_by }}</td>
                                <td>{{ $game->players()->count() }}</td>
                                <td>
                                    <form method="POST" id="joinGameForm"
                                        action="{{ route('games.join', ['game' => $game]) }}">
                                        @csrf
                                        <input type="hidden" name="game" value="$game->id" />
                                        <button class="btn btn-primary" type="submit">Join game</button>
                                    </form>
                                </td>
                                <td>
                                    <a class="btn btn-secondary" href="{{route('games.lobby',['game'=> $game])}}">Lobby</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <form class="bg-dark p-3 rounded" id="createLobbyForm"  method="POST" action="{{ route('games.create') }}">
            @csrf <!-- CSRF protection token -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label text-white">Lobby name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter text">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="num_players" class="form-label text-white">Select num of players</label>
              
                    <select class="form-select"  id="num_players" name="num_players" class="form-control">
                        <option value="2">2 Players</option>
                        <option value="3">3 Players</option>
                        <option value="4">4 Players</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="invisible">Submit</label>
                    <button type="submit" class="btn btn-primary btn-block">Create lobby</button>
                </div>
            </div>
        </form>
    </div>

    
@endsection
