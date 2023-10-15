<div>
    @foreach ($game->players()->get() as $player)
        @if ($player->user_id == Auth::user()->id)
            @continue
        @endif
        <div>
            <div class="user-name d-flex gap-3 justify-content-center">
                <div>
                {{ $player->user->name }}
                </div>
                    <div>
                    <div class="user-checks" id="check-{{$player->id}}" style="display:block">
                 
                    <i class="fa-solid fa-check"></i>
                    </div>
                    </div>
            </div>
            @php

            @endphp
            <div class="open-cards">
                <div id="app">
                    <card-list player="{{ $player->id }}" type="open" game="{{ $game->id }}"
                        :should-display-card="true"></card-list>
                </div>
            </div>
            <player-stats player="{{$player->id}}" game="{{$game->id}}"></player-stats>

            {{-- <div class="hand-cards card-holder">
        <div id="app">
            <card-list player="{{$player->id}}" type="hand" game="{{$game->id}}" :should-display-card="false"></card-list>
        </div>
    </div> --}}
        </div>
    @endforeach
</div>
