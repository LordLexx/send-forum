<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Welcome extends Component
{
    #[Layout('layouts.master')]
    public function mount()
    {
        // No need to assign collections to properties anymore
    }

    public function render()
    {
        return view('welcome');
    }
}
