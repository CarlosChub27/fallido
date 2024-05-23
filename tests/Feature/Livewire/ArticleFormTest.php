<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\ArticleForm;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleFormTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    //Crear una base de datos en memoria para que no nos llene nuestra bd como tal
    use RefreshDatabase;

    //Test para verificar que se tiene acceso a la url
    /** @test */
    function article_form_renders_properly()
    {
        $this->get(route('articles.create'))->assertSeeLivewire('article-form'); //que al entrar en la ruta articles.create nos devuelva el article-form

        $article = Article::factory()->create();
        $this->get(route('articles.edit', $article))->assertSeeLivewire('article-form'); //que al entrar en la ruta articles.create nos devuelva el article-form

    }

    //Verificar la conexion del componente hacia la vista de blade
    /** @test */
    function blade_template_is_wired_properly()
    {
        Livewire::test('article-form')
            ->assertSeeHtml('wire:submit.prevent="save"') //Verificar que el formulario(blade) exista este action
            ->assertSeeHtml('wire:model="article.title"') //este es para el input de title
            ->assertSeeHtml('wire:model="article.content"') //este es para el input de title
        ;
    }

    //Verificar si se puede crear un nuevo articulo
    /** @test */
    function can_create_new_articles()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New article') //agregar valor al title
            ->set('article.content', 'Article content') //agregar valor al content
            ->set('article.slug', 'new-article') //Agregando un slug
            ->call('save') //ejecutar el metodo sabe
            ->assertSessionHas('status') // Revisar si existe la sesion status
            ->assertRedirect(route('articles.index')); // Verificando la redireccion


        //Revisando el base de datos que se creo correctamente
        $this->assertDatabaseHas('articles', [
            'title' => 'New article',
            'slug' => 'new-article',
            'content' => 'Article content'
        ]);
    }

    /** @test */
    function can_update_articles()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form', ['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.slug', $article->slug)
            ->assertSet('article.content', $article->content)
            ->set('article.title', 'Updated title')
            ->set('article.slug', 'updated-slug')
            ->set('article.content', 'Updated content')
            ->call('save')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseCount('articles', 1); //Que solamente contenga un registro

        $this->assertDatabaseHas('articles', [
            'title' => 'Updated title',
            'slug' => 'updated-slug'
        ]);
    }

    /** @test */
    function title_is_required()
    {
        Livewire::test('article-form')
            // ->set('article.title', 'New')
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.title' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'title'])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    /** @test */
    function slug_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New article')
            ->set('article.slug', null)
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug'])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    //Verificando que el slug sea unico en la base de datos
    /** @test */
    function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form')
            ->set('article.title', 'New article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug'])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    /** @test */
    function slug_must_only_contain_letters_numbers_dashes_and_underscores()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New article')
            ->set('article.slug', 'new-article$$')
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.slug' => 'alpha_dash'])
            ->assertSeeHtml(__('validation.alpha_dash', ['attribute' => 'slug'])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    //Actualizacion de articulos en cuanto al campo slug
    /** @test */
    function unique_rule_should_be_ignored_when_updating_the_same_slug()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form', ['article' => $article])
            ->set('article.title', 'New article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasNoErrors(['article.slug' => 'unique']);
    }

    /** @test */
    function title_must_be_4_characters_min()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New')
            ->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.title' => 'min'])
            ->assertSeeHtml(__('validation.min.string', ['attribute' => 'title', 'min' => 4])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    /** @test */
    function content_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            //->set('article.content', 'Article content') //agregar valor al content
            ->call('save')
            ->assertHasErrors(['article.content' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'content'])); //Aqui esta validando los mensajes de error en la vista de blade
    }

    /** @test */
    function real_time_validation_works_for_title()
    {
        Livewire::test('article-form')
            ->set('article.title', '')
            //->set('article.content', 'Article content') //agregar valor al content
            //->call('save')
            ->assertHasErrors(['article.title' => 'required'])
            ->set('article.title', 'New')
            ->assertHasErrors(['article.title' => 'min'])
            ->set('article.title', 'New Article')
            ->assertHasNoErrors('article.title');
    }

    /** @test */
    function real_time_validation_works_for_content()
    {
        Livewire::test('article-form')
            ->set('article.content', '')
            //->set('article.content', 'Article content') //agregar valor al content
            //->call('save')
            ->assertHasErrors(['article.content' => 'required'])
            ->set('article.content', 'New Content')
            ->assertHasNoErrors('article.content');
    }

    /** @test  */
    function slug_is_generated_automatically()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Nuevo articulo')
            ->assertSet('article.slug', 'nuevo-articulo');
    }
}
