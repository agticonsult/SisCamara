@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
<script src="{{ asset('js/gmaps.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-header">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Gerenciamento da Votação Eletrônica</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="text-center">
            <button class="btn btn-success mt-2" style="width: 100%; font-size: 1.3rem;">Iniciar Votação</button>
        </div>
    </div>


    <div class="card-body">



        @if (Count($vereadores) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Vereador</th>
                            {{-- <th scope="col">Status Votação</th> --}}
                            {{-- <th scope="col">Tipo de Votação</th>
                            <th scope="col">Proposição</th>
                            <th scope="col">Legislatura</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ações</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vereadores as $vereador)
                            <tr>
                                <td>{{ $vereador->usuario->pessoa->nomeCompleto }}</td>
                                {{-- @if ()

                                @else

                                @endif --}}
                                {{-- <td>
                                    <a href="{{ route('votacao_eletronica.gerenciar', $votacao->id) }}" class="btn btn-info m-1">Gerenciar Votação</a>
                                </td>
                                <td>
                                    <a href="{{ route('votacao_eletronica.edit', $votacao->id) }}" class="btn btn-warning m-1">Alterar</a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $votacao->id }}">Excluir</button>
                                </td> --}}
                            </tr>

                            {{-- <div class="modal fade" id="exampleModalExcluir{{ $votacao->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('votacao_eletronica.destroy', $votacao->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    <strong style="font-size: 1.2rem">Excluir Votação de ID <i>{{ $votacao->id }}</i></strong>
                                                </h5>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="motivo" class="form-label">Motivo</label>
                                                    <input type="text" class="form-control" name="motivo">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar
                                                </button>
                                                <button type="submit" class="button_submit btn btn-danger">Excluir</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>



    <div class="card-footer">
        <div class="col-md-12">
            <a href="{{ route('votacao_eletronica.index') }}" class="btn btn-light m-1">Voltar</a>
        </div>
    </div>

</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $("#form").validate({
        rules : {
            data:{
                required:true
            },
            id_tipo_votacao:{
                required:true
            },
            id_proposicao:{
                required:true
            },
        },
        messages:{
            data:{
                required:"Campo obrigatório"
            },
            id_tipo_votacao:{
                required:"Campo obrigatório"
            },
            id_proposicao:{
                required:"Campo obrigatório"
            },
        }
    });

    $(document).ready(function() {

        $('.select2').select2({
            language: {
                noResults: function() {
                    return "Nenhum resultado encontrado";
                }
            },
            closeOnSelect: true,
            width: '100%',
        });

    });

</script>

@endsection
