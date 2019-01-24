<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\Achat;

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

                case "clothes":
                    $articles = Article::where('categorie', 1)->get();
                    break;

                case "goodies":
                    $articles = Article::where('categorie', 2)->get();
                    break;
                
                case "undefined":
                    $articles = Article::all();
                    break;

                default:
                    $articles = Article::all();
            }
        }
        
        else {
            $articles = Article::all();
        }
        
        $top_articles = Article::select('id')->orderBy('achat', 'DESC')->take(3)->get();
        $top_article0 = Article::find($top_articles[0]->id);
        $top_article1 = Article::find($top_articles[1]->id);
        $top_article2 = Article::find($top_articles[2]->id);

        return view('articles.index', compact('articles', 'top_articles', 'top_article0', 'top_article1', 'top_article2'));
    }
    
}
