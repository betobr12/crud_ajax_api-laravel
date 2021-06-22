<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Produto;
use Illuminate\Http\Request;

class ControladorProduto extends Controller
{

    public function indexView()
    {
        $produtos = Produto::all(); 
        return view('produtos', compact('produtos'));
    }

    public function index()
    {
        $produtos = Produto::all(); 
        return json_encode($produtos);
    }

    public function create()
    {

        $categorias = Categoria::all();

        return view('novoproduto',compact('categorias'));
    }

    public function store(Request $request)
    {
        $prod = new Produto();
        $prod->nome = $request->input('nome');
        $prod->preco = $request->input('preco');
        $prod->estoque = $request->input('estoque');
        $prod->categoria_id = $request->input('categoria_id');
        $prod->save();
        return json_encode($prod);
    }

    public function show($id)
    {
        $prod = Produto::find($id);
        if (isset($prod)) {
            return json_encode($prod);            
        }
        return response('Produto não encontrado', 404);
    }

    public function edit($id)        
    {
        $categorias = Categoria::all();
        $produto = Produto::find($id);
        if(isset($produto)) {
            return view('editarproduto', compact('produto','categorias'));
        }
        return redirect('/produtos');
    }

    public function update(Request $request, $id)
    {
        $prod = Produto::find($id);
        if (isset($prod)) {
            $prod->nome = $request->input('nome');
            $prod->preco = $request->input('preco');
            $prod->estoque = $request->input('estoque');
            $prod->categoria_id = $request->input('categoria_id');
            $prod->save();
            return json_encode($prod);
        }
        return response('Produto não encontrado', 404);
    }

    public function destroy($id)
    {
        $prod = Produto::find($id);
        if (isset($prod)) {
            $prod->delete();
            return response('OK', 200);
        }
        return response('Produto não encontrado', 404);
    }

}
