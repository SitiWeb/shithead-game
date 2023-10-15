<form method="post" id="myForm" action="{{ env('APP_URL') }}/games/{{ $game->id }}/action">


    <div class="d-flex justify-content-center">
        
        @foreach ($game->players()->get() as $player)
            
                
                @if ($player->user_id != Auth::user()->id)
                    @continue
                @endif
                
                <div>
                <div class="user-name">
                    {{ $player->user->name }}
                    @if ($player->is_ready)
                    <i class="fa-solid fa-check"></i>
                    @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="player" value="{{ $player->id }}" />
                </div>
                @php
                    $cards = $player->cards()->get();
                @endphp
                <div class="open-cards d-flex gap-1 ">
                    @foreach ($cards as $card)
                        @if ($card->card_type != 'open')
                            @continue
                        @endif
                        <input type="checkbox" style="" name="card[open]" value="{{ $card->id }}"
                            id="card-{{ $card->id }}" />
                        <label for="card-{{ $card->id }}">
                            @include('games.card')
                        </label>
                    @endforeach
                </div>

                <div class="closed-cards d-flex gap-1">
                    @foreach ($cards as $card)
                        @if ($card->card_type != 'closed')
                            @continue
                        @endif
                        <input type="checkbox" style="" name="card[closed]" value="{{ $card->id }}"
                            id="card-{{ $card->id }}" />
                        <label for="card-{{ $card->id }}">
                            @include('games.card_closed')
                        </label>
                    @endforeach
                </div>

                <div class="hand-cards d-flex gap-1 card-holder">
                    @foreach ($cards as $card)
                        @if ($card->card_type != 'hand')
                            @continue
                        @endif
                        @livewire('player-cards', ['card' => $card])
                        {{-- <input type="checkbox" style="" name="card[hand]" value="{{ $card->id }}"
                            id="card-{{ $card->id }}" />
                        <label for="card-{{ $card->id }}">
                            @include('games.card')
                        </label> --}}
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    <div class="d-flex justify-content-center my-4 gap-2">
    @include('games.current_buttons')
    </div>
</form>

<div>
    <form wire:submit.prevent="addElement">
        <input type="text" wire:model="newElement" placeholder="New Element">
        <button type="submit">Add</button>
    </form>

    <ul>
        @foreach($cards as $card)
            <li>{{ $card }}</li>
        @endforeach
    </ul>
</div>