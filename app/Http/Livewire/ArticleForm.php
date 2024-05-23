<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArticleForm extends Component
{
    use WithFileUploads;

    use WithFileUploads;
 
    public $photo;
 
    public function save()
    {
        // $this->validate([
        //     'photo' => 'image|max:1024', // 1MB Max
        // ]);
 
        dd($this->photo);
        $this->photo->store('photos');
    }

    public function render()
    {
        return view('livewire.article-form');
    }
}
