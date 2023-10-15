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
    <div class="open-cards d-flex gap-1 ">
        @foreach($cards as $card)
            @if ($card->card_type != 'open')
            @continue
            @endif
            @include('games.card')
        @endforeach
    </div>

    <div class="closed-cards d-flex gap-1">
        @foreach($cards as $card)
            @if ($card->card_type != 'closed')
            @continue
            @endif
            @include('games.card_closed')
        @endforeach
    </div>

    <div class="hand-cards d-flex gap-1 card-holder">
        @foreach($cards as $card)
            @if ($card->card_type != 'hand')
            @continue
            @endif
            @include('games.card_closed')
        @endforeach
    </div>
</div>
@endforeach
</div>

<script>
function handCards(){
    console.log('run');
    var cardsholders = document.querySelectorAll('.card-holder');
    cardsholders.forEach(elements => {
        console.log(cards)
        var cards = elements.querySelectorAll('.card');
        var cardCount = cards.length;
        var diff = 20 / (cardCount - 1);
        var start = -10;
        cards.forEach(element => {
            element.style.transform = 'skew('+ -start +'deg, '+ start +'deg)';
            start = start + diff;
        
        });
    })
    
}
document.addEventListener('DOMContentLoaded', handCards);


</script>