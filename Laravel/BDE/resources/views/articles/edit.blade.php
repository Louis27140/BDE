@extends('layout.duo')

@section('stylesheets')
    <link rel="stylesheet" href="/css/articles/form.css">
@endsection

@section('title')
    Modification d'un article
@endsection

@section('content_form')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/articles/{{$article->id}}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <label for="name" class="title_input_left">Nom</label>
        <input class="input_left" type="text" id="name" name="name" value="{{$article->nom}}">

        <div id="little-input-container">
            <div class="little-input">
                <label for="price" class="title_input_left">Prix</label>
                <input id="price" class="input_left" type="text" name="price" value="{{$article->prix}}">
            </div>
            <div class="little-input">
                <label for="stock" class="title_input_left">Stock</label>
                <input id="stock" class="input_left" type="text" name="stock" value="{{$article->stock}}">
            </div>
        </div>

        <label for="category" class="title_input_left">Catégorie</label>
        <div class="input_left" id="select">
            <select id="category" name="category">
                @foreach ($categories as $category)
                    <option value="{{$category->id}}" {{ $category->id == $article->categorie ? 'selected' : ''}}>{{$category->nom}}</option>
                @endforeach
            </select>
        </div>
        

        <label for="description-input" class="title_input_left">Description</label>
        <textarea class="input_left" id="description-input" name="description">{{$article->description}}</textarea>

        <label for="pic" class="title_input_left">Photo</label>
        <input id="pic" class="input_left" type="file" name="pic"><br>

        <div>
            <input class="btn_left" type="submit" value="Modifier">
        </div>
    </form>
    
@endsection

@section('img')
    <img id="img_right" src="/images/Logo_bde2.png" alt="Logo">
@endsection