<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Achat;
use App\Article;

class PurchaseController extends Controller
{
    public function index() {
        $achats = Achat::where('user_id', Auth::user()->id)->get();

        $articles = [];
        $price = 0;

        foreach($achats as $achat) {
            $article = Article::find($achat->article_id);
            $price += $article->prix * $achat->quantite;

            array_push($articles, $article);
        }


        return view('purchase', compact('articles', 'achats', 'price'));
    }

    public function store() {

        $article = Article::find(request('id'));

        if($article->stock <= 0) {
            return back();
        }

        $achat = new Achat();

        $achat->article_id = request('id');
        $achat->user_id = Auth::user()->id;
        $achat->quantite = 1;

        $achat->save();

        $article->stock--;
        $article->save();

        return redirect('/articles');
    }

    public function update(Achat $purchase) {
        if(Auth::user()->statut_id != 2)
            return back();
        
        $article = Article::find($purchase->article_id);

        if(request()->has('quantity')) {
            if(request('quantity') == '+') {
                $purchase->quantite++;
                $article->stock--;
                $purchase->save();
                $article->save();
            }

            elseif (request('quantity') == '-') {
                $purchase->quantite--;
                $article->stock++;
                $purchase->save();
                $article->save();

                if($purchase->quantite == 0)
                    $purchase->delete();

            }
        }

        return redirect('/purchase');
    }

    public function destroy(Achat $purchase) {
        if(Auth::user()->statut_id != 2)
            return back();

        $article = Article::find($purchase->article_id);
        $quantity = $purchase->quantite;

        $article->stock += $quantity;

        $article->save();
        
        $purchase->delete();
        

        return redirect('/purchase');

    }

    public function payment() {
        return view('payment-confirmation');
    }

    public function paymentCash() {
        if(Achat::where('user_id', Auth::user()->id)->count() == 0) 
            return back();

        request()->validate(['condition' => 'required']);

        $purchases = Achat::where('user_id', Auth::user()->id)->get();

        $achats = "Voici le résumé de la commande : <ul>";

        foreach($purchases as $purchase) {
            $article = Article::find($purchase->article_id);
            $article->achat += $purchase->quantite;
            $article->save();
            $achats .= "<li>" . $article->nom . " x" . $purchase->quantite . "</li>";
            $purchase->delete();
        }

        $achats .= "</ul>";

        dd($achats);

        $data = array('title' => 'Une commande a été effectuée' , 'subtitle' => 'Commande de ' . Auth::user()->prenom, "description" => $achats, "url" => "mailto:" . Auth::user()->email, 'linkText' => "Contacter");

        Mail::send('layout.mail', $data, function($message) {
            $message->to(env('ADMIN_MAIL', ''), 'Administrator')->subject('Commande effectuée');
            $message->from(env('MAIL_USERNAME', 'bde@bde.fr'), 'BDE');
        });

        return view('payment-cash');
    }
}
