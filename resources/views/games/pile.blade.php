@if ($game->getTopCard())
@php
$card = $game->getTopCard();   
@endphp
<div class="d-flex justify-content-center align-items-center h-100 pile">
@include('games.card')
</div>
@endif