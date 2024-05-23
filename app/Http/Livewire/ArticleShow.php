<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class ArticleShow extends Component
{
    //Variable tipada porque tiene el tipo de datos que es Article, y a esto tambien en conjunto se le llama routeModelbinding
    public Article $article;

    // Quitamos el metodo mount ya que vamos a tipar la variable articulo, es decir asignar que tipo de dato va a almacenar
    // public function mount(Article $article)
    // {
    //     $this->article=$article;
    //     // $this->article=Article::findOrFail($article);
    //     // $this->article = Article::findOrFail(request()->route('article'));
    // }

    public function render()
    {
        
        return view('livewire.article-show');
    }
}
