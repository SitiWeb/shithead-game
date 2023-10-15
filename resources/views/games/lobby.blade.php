@extends('layouts.app') // Use your own layout or extend a master layout

@section('content')
    <h1>Lobby</h1>
    <h2>{{$game->name}} - Num of players: {{count($game->players)}}</h2>
    <ul>
        @foreach ($game->players as $player)
            @if ($player->user_id)
            <li>{{$player->user->name}}</li>
            @endif
        @endforeach
    </ul>
    <a href="{{ route('games.start', ['game' => $game->id]) }}">
        Start game
    </a>
@endsection
