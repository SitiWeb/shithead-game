<!-- resources/views/livewire/my-component.blade.php -->
<div>
<input wire:model="game" type="checkbox" style="" name="card[hand]" value="{{ $card->id }}"
                            id="card-{{ $card->id }}" />
<label for="card-{{ $card->id }}">
    @include('games.card')
</label>
</div>