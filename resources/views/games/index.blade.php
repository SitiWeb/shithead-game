@extends('layouts.app') // Use your own layout or extend a master layout

@section('content')
    <h1>Available Games</h1>

    <!-- New Game Form -->
    <form method="POST" action="{{ route('games.create') }}">
        @csrf <!-- CSRF protection token -->

        <div class="form-group">
            <label for="num_players">Number of Players:</label>
            <select id="num_players" name="num_players" class="form-control">
                <option value="2">2 Players</option>
                <option value="3">3 Players</option>
                <option value="4">4 Players</option>
                <!-- Add more options as needed -->
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Start New Game</button>
    </form>

    <ul>
        @foreach ($games as $game)
            <li>
                <a href="{{ route('games.join', ['game' => $game->id]) }}">
                    Game {{ $game->id }}
                </a>
            </li>
        @endforeach
    </ul>
@endsection
