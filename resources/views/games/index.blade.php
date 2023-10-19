@extends('layouts.app')

@section('content')
    <div class="position-fixed top-0 right-0 p-3" style="z-index: 11; width:300px;">
        <div id="toast-container" class="toast-container">
        </div>
    </div>
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <a class="btn btn-danger mb-4" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                <h1>Available Games</h1>
                <div id="app">
                    <game-table :games="{{ json_encode($games) }}"></game-table>
                  </div>
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
