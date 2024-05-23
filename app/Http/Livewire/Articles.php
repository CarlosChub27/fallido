<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class Articles extends Component
{
    // Estas son las variables que se van a acceder desde la vista del componente
    // Para que estas sean accedidas desde la vista deben ser publicas
    // public $h1 = 'Listado de articulos';
    // public $articles;

    public $search ='';

    public function render()
    {
        return view('livewire.articles', [
            'articles' => Article::where('title', 'like', "%{$this->search}%")->latest()->get()
        ]);
    }
}
