<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use App\Article;
use App\Achat;
use App\Categorie;

class ArticlesController extends Controller
{
    public function index() {
        $articles = null;

        if(request()->has('filter')) {
            switch(request('filter')) {
                case "price-asc":
                    $articles = Article::orderBy('prix', 'ASC')->get();
                    break;

                case "price-desc":
                    $articles = Article::orderBy('prix', 'DESC')->get();
                    break;

                case "Vêtements":
                    $articles = Article::where('categorie', 1)->get();
                    break;

                case "Goodies":
                    $articles = Article::where('categorie', 2)->get();
                    break;
                
                case "undefined":
                    $articles = Article::where('centre_id', env('CENTRE_ID', 1))->get();
                    break;

                default:
                    $articles = Article::where('centre_id', env('CENTRE_ID', 1))->get();
            }
        }
        
        else {
            $articles = Article::where('centre_id', env('CENTRE_ID', 1))->get();
        }
        
        $top_articles = Article::select('id')->where('centre_id', env('CENTRE_ID', 1))->orderBy('achat', 'DESC')->take(3)->get();
        $top_article0 = Article::find($top_articles[0]->id);
        $top_article1 = Article::find($top_articles[1]->id);
        $top_article2 = Article::find($top_articles[2]->id);

        return view('articles.index', compact('articles', 'top_articles', 'top_article0', 'top_article1', 'top_article2'));
    }

    public function create() {
        if(Auth::user() && Auth::user()->statut_id != 2)
            return back();

        $categories = Categorie::all();

        return view('articles.create', compact('categories'));
    }

    public function store(Request $request) {

        if(Auth::user() && Auth::user()->statut_id != 2)
            return back();

        request()->validate([
            'name' => 'required|max:40',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|integer',
            'description' => 'required|max:200',
            'pic' => 'required|image'
        ]);

        $extension =  request()->file('pic')->extension();
        $path = request('name') .'.'. $extension;
        Image::make(request()->file('pic'))->save(public_path('storage/'.$path));

        $article = new Article();

        $article->nom = request('name');
        $article->description = request('description');
        $article->categorie = request('category');
        $article->prix = request('price');
        $article->photo = $path;
        $article->stock = request('stock');
        $article->centre_id = env('CENTRE_ID', 1);

        $article->save();

        return redirect('/articles');

    }

    public function edit(Article $article) {
        if(Auth::user() && Auth::user()->statut_id != 2)
            return back();
        
        $categories = Categorie::all();
        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(Article $article) {
        if(Auth::user() && Auth::user()->statut_id != 2)
            return back();

        request()->validate([
            'name' => 'required|max:40',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|integer',
            'description' => 'required|max:200',
            'pic' => 'required|image'
        ]);

        $extension =  request()->file('pic')->extension();
        $path = request('name') .'.'. $extension;
        Image::make(request()->file('pic'))->save(public_path('storage/'.$path));


        $article->nom = request('name');
        $article->description = request('description');
        $article->categorie = request('category');
        $article->prix = request('price');
        $article->photo = $path;
        $article->stock = request('stock');
        $article->centre_id = env('CENTRE_ID', 1);

        $article->save();

        return redirect('/articles');
    }

    public function destroy(Article $article) {
        if(Auth::user() && Auth::user()->statut_id != 2)
            return back();

        $article->delete();

        return redirect('/articles');
    }
    
}