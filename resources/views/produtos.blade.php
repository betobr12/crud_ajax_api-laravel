@extends('layout.app', ["current" => "produtos" ])

@section('body')
<div class="card border">
    <div class="card-body">
        <h5 class="card-title">Cadastro de Produtos</h5>

@if(count($produtos) > 0)
<table class="table table-ordered table-hover" id="tabelaProdutos">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Quantidade</th>
            <th>Preço</th>
            <th>Departamento</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
       
    </tbody>
</table>
@endif        
    </div>
    <div class="card-footer">
        <button class="btn btn-sm btn-primary" role="button" onclick="novoProduto()">Novo Produto</button>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="dlgProdutos">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="formProduto" >
                <div class="modal-header">
                    <h5 class="modal-title">
                        Novo Produto
                    </h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" class="form-control">
                    <div class="form-group">
                        <label for="nomeProduto" class="control-label">Nome do Produto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="nomeProduto" placeholder="Nome do Produto">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="precoProduto" class="control-label">Preço do Produto</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="precoProduto" placeholder="Preço do Produto">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quantidadeProduto" class="control-label">Quantidade do Produto</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="quantidadeProduto" placeholder="Quantidade do Produto">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="categoriaProduto" class="control-label">Categoria</label>
                        <div class="input-group">
                            <select class="form-control" id="categoriaProduto" >
                            </select>    
                        </div>
                    </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="cancel" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
            </form>
        </div>

    </div>
</div>

@endsection

@section('javascript')
    <script type="text/javascript">

    //gerar token csrf_token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        //São os itens do form do modal
        function novoProduto(){
            $('#id').val('');
            $('#nomeProduto').val('');
            $('#precoProduto').val('');
            $('#quantidadeProduto').val('');
            $('#categoriaProduto').val('');        
            $('#dlgProdutos').modal('show');
        }
        //para fazer o select list do formulario de cadastro
        function carregarCategorias() {
            $.getJSON('/api/categorias', function(data) { 
                for(i=0;i<data.length;i++) {
                    opcao = '<option value ="' + data[i].id + '">' + 
                        data[i].nome + '</option>';
                    $('#categoriaProduto').append(opcao);
                }
            });
        }
        //monta a lista do tbody da tabela
        function montarLinha(p) {
            var linha = "<tr>" +
                "<td>" + p.id + "</td>" +
                "<td>" + p.nome + "</td>" +
                "<td>" + p.estoque + "</td>" +
                "<td>" + p.preco + "</td>" +
                "<td>" + p.categoria_id + "</td>" +
                "<td>" +
                  '<button class="btn btn-sm btn-primary" onclick="editar(' + p.id + ')"> Editar </button> ' +
                  '<button class="btn btn-sm btn-danger" onclick="remover(' + p.id + ')"> Apagar </button> ' +
                "</td>" +
                "</tr>";
            return linha;
        }

        //carrega o produto para editar pelo seu id
        function editar(id) {
                $.getJSON('/api/produtos/'+id, function(data) { 
                console.log(data);
                $('#id').val(data.id);
                $('#nomeProduto').val(data.nome);
                $('#precoProduto').val(data.preco);
                $('#quantidadeProduto').val(data.estoque);
                $('#categoriaProduto').val(data.categoria_id);
                $('#dlgProdutos').modal('show');            
            });        
        }


        //função para excluir o produto
        function remover(id) {
            $.ajax({
                type: "DELETE",
                url: "/api/produtos/" + id,
                context: this,
                success: function() {
                    console.log('Apagou OK');
                    linhas = $("#tabelaProdutos>tbody>tr");
                    e = linhas.filter( function(i, elemento) { 
                        return elemento.cells[0].textContent == id; 
                    });
                    if (e)
                        e.remove();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        //carrega os produtos da tabela conforme declarado acima
        function carregarProdutos() {
            $.getJSON('/api/produtos', function(produtos) { 
                for(i=0;i<produtos.length;i++) {
                    linha = montarLinha(produtos[i]);
                    $('#tabelaProdutos>tbody').append(linha);
                }
            });        
        }
        //função para criar os itens do form do modal
        function criarProduto() {
            prod = { 
                nome: $("#nomeProduto").val(), 
                preco: $("#precoProduto").val(), 
                estoque: $("#quantidadeProduto").val(), 
                categoria_id: $("#categoriaProduto").val() 
            };
            $.post("/api/produtos", prod, function(data) {
                produto = JSON.parse(data);
                linha = montarLinha(produto);
                $('#tabelaProdutos>tbody').append(linha);            
            });
        }
        //Salva o produto carregado para edição
        function salvarProduto() {
            prod = { 
                id : $("#id").val(), 
                nome: $("#nomeProduto").val(), 
                preco: $("#precoProduto").val(), 
                estoque: $("#quantidadeProduto").val(), 
                categoria_id: $("#categoriaProduto").val() 
            };
            $.ajax({
                type: "PUT",
                url: "/api/produtos/" + prod.id,
                context: this,
                data: prod,
                success: function(data) {
                    prod = JSON.parse(data);
                    linhas = $("#tabelaProdutos>tbody>tr");
                    e = linhas.filter( function(i, e) { 
                        return ( e.cells[0].textContent == prod.id );
                    });
                    if (e) {
                        e[0].cells[0].textContent = prod.id;
                        e[0].cells[1].textContent = prod.nome;
                        e[0].cells[2].textContent = prod.estoque;
                        e[0].cells[3].textContent = prod.preco;
                        e[0].cells[4].textContent = prod.categoria_id;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });        
        }
        //função chama formulario para execurar as ações abaixo
        $("#formProduto").submit( function(event){ 
            event.preventDefault(); 
            if ($("#id").val() != '')
                salvarProduto();
            else
                criarProduto();            
                $("#dlgProdutos").modal('hide');
        });

        //função anonima utilizada para carregar outras funções sem interação do usuario
        $(function(){
            carregarCategorias();
            carregarProdutos();
        });
    </script>
@endsection