@extends('layouts.app')

@section('content')
    <div class="position-fixed top-0 right-0 p-3" style="z-index: 11; width:300px;">
        <div id="toast-container" class="toast-container">
        </div>
    </div>
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <h1>Lobby</h1>
                <div id="app">
                    <game-lobby :game="{{ $game->id }}"></game-lobby>
                  </div>

            </div>

        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex gap-3">
                    <div>
                    <form method="POST" class="lobby-forms" id="leaveGameForm" action="{{ route('games.leave', ['game' => $game]) }}">
                        @csrf
                        <input type="hidden" name="game" value="$game->id" />
                        <button class="btn btn-primary" type="submit">Leave game</button>
                    </form>
                </div>
                <div class="d-flex gap-3">
                    <div>
                    <form method="POST" class="lobby-forms" id="addBot" action="{{ route('games.bot', ['game' => $game]) }}">
                        @csrf
                        <input type="hidden" name="game" value="$game->id" />
                        <button class="btn btn-primary" type="submit">Add bot</button>
                    </form>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('games.start', ['game' => $game->id]) }}">
                        Start game
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection
