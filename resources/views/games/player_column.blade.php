<div>
@foreach($game->players()->get() as $player)

    @if ($player->user_id == Auth::user()->id)
        @continue
    @endif
    <div>
    <div class="user-name">
        {{$player->user->name}}
        @if ($player->is_ready)
        <i class="fa-solid fa-check"></i>
        @endif
    </div>
    @php
    $cards = $player->cards()->get()
    @endphp
    <div class="open-cards">
        <div id="app">
            <card-list player="{{$player->id}}" type="open" game="{{$game->id}}" :should-display-card="true"></card-list>
        </div>
    </div>

    <div class="closed-cards">
        <div id="app">
            <card-list player="{{$player->id}}" type="closed" game="{{$game->id}}" :should-display-card="false"></card-list>
        </div>
    </div>

    <div class="hand-cards card-holder">
        <div id="app">
            <card-list player="{{$player->id}}" type="hand" game="{{$game->id}}" :should-display-card="false"></card-list>
        </div>
    </div>
</div>
@endforeach
</div>

