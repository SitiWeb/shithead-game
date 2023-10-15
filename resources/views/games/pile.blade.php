@if ($game->getTopCard())
@php
$card = $game->getTopCard();   
@endphp
<div class="d-flex justify-content-center align-items-center h-100 pile">
<stack :card="$card" :game="{{$game->id}}"></stack>
</div>
@endif