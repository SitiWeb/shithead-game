

<div class="card">
    <div class="card-body p-1">
        <ul>
        @foreach($game->toArray() as $item => $value)
        <li>
        {{$item}}: {{$value}}
        </li>
        @endforeach
    </ul>
    <ul>
        @php
        $player = $game->players()->where('user_id', Auth::user()->id)->first();
        @endphp
        
        <li>Player id: {{$player->id}}</li>
        <li>is_ready: {{$player->is_ready}}</li>
        <li>User id: {{$player->user_id}}</li>
        <li>Turn: @if ($game->current_turn == $player->id)
            Yes 
            @else
            no 
            @endif 
        </li>
        
        
    </ul>
    </div>
</div>