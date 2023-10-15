@extends('layouts.app')

@section('content')

<div class="container-fluid min-vh-100 d-flex flex-column justify-content-between" style="background-color:green;">
    <div class="row" style="flex-grow:4;">
        <div class="col-3">
            @include('games.player_column')
        </div>
        <div class="col-6">
            @include('games.pile')
        </div>
        <div class="col-3">
            @include('games.debug')
        </div>
    </div>
    <div class="row" style="flex-grow:1;">
        <div class="col">
            @include('games.current_player_column')
        </div>
    </div>
</div>
@endsection
