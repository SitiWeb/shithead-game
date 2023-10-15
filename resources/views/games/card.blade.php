
<div class="card text-center card-ratio">
    <div class="card-body p-0">
        <div class="card-number">
            <span class="card-icon">
                @switch($card->card_suit)
                    @case('Hearts')
                        ♡
                        @break
                    @case('Spades')
                        ♤
                        @break
                    @case('Clubs')
                        ♧
                        @break
                    @case('Diamonds')
                        ♢
                        @break
                    @default
                        ?
                @endswitch
            </span>
                
            <span class="card-value"> 
                @switch($card->card_rank)
                @case('Queen')
                    Q
                    @break
                @case('Ace')
                    A
                    @break
                @case('Jack')
                    J
                    @break
                @case('King')
                    K
                    @break
                @default
                    {{$card->card_rank}}
            @endswitch</span>
        </div>
    </div>
</div>
