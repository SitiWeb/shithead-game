@extends('layouts.app') // Use your own layout or extend a master layout

@section('content')
    <div class="position-fixed top-0 right-0 p-3" style="z-index: 11; width:300px;">
        <div id="toast-container" class="toast-container">
        </div>
    </div>
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <h1>Lobby</h1>
                <table class="table table-dark">

                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>

                            <th scope="col">user ID</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($game->players as $player)
                            @if ($player->user)
                                <tr>
                                    <td>{{ $player->id }}</td>
                                    <td>{{ $player->user->name }}</td>

                                    <td>{{ $player->user_id }}</td>

                                </tr>
                            @else
                                <tr>
                                    <td>{{ $player->id }}</td>
                                    <td>-</td>

                                    <td>-</td>

                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
        <div class="row">
            <div class="col">
                <div class="d-flex gap-3">
                    <div>
                    <form method="POST" id="leaveGameForm" action="{{ route('games.leave', ['game' => $game]) }}">
                        @csrf
                        <input type="hidden" name="game" value="$game->id" />
                        <button class="btn btn-primary" type="submit">Leave game</button>
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
