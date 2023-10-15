<?php

namespace App\Livewire;

use Livewire\Component;

class PlayerCards extends Component
{
    public $card;
    public $newElement;
    public function render()
    {
        return view('livewire.player-cards');
    }
    

    public function addElement()
    {
        if ($this->newElement) {
            $this->cards[] = $this->newElement;
            $this->newElement = ''; // Reset the input field
        }
    }
}
