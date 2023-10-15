<form method="post" id="myForm" action="{{ env('APP_URL') }}/games/{{ $game->id }}/action">


    <div class="d-flex justify-content-center">
        
        @foreach ($game->players()->get() as $player)
            
                
                @if ($player->user_id != Auth::user()->id)
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
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="player" value="{{ $player->id }}" />
                </div>
                @php
                    $cards = $player->cards()->get();
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

                <div id="app">
                    <card-list  player="{{$player->id}}" type="hand" game="{{$game->id}}" :should-display-card="true"></card-list>
                </div>
                
            </div>
        </div>
        @endforeach
    <div class="d-flex justify-content-center my-4 gap-2">
    @include('games.current_buttons')
    </div>
</form>

