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
            <h1 style="text-decoration: underline">LIBERAÇÃO DOS VEREADORES PARA VOTAÇÃO</h1>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">Vereador</th>
                        <th scope="col">Votação liberada</th>
                        {{-- <th scope="col">Tipo de Votação</th>
                        <th scope="col">Proposição</th>
                        <th scope="col">Legislatura</th>
                        <th scope="col">Cadastrado por</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($votacao->vereadores_ativos() as $vereador_votacao)
                        <tr>
                            <td>{{ $vereador_votacao->vereador->usuario->pessoa->nomeCompleto }}</td>
                            <td>
                                @if ($votacao->votacaoEncerrada != 1)
                                    @if ($votacao->votacaoIniciada != 1)
                                        <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                                    @else
                                        @if ($votacao->votacaoPausada != 1)
                                            @if ($vereador_votacao->votou != 1)
                                                @if ($vereador_votacao->votacaoAutorizada != 1)
                                                    <a href="{{ route('votacao_eletronica.vereador.liberarVotacao', $vereador_votacao->id) }}" class="btn btn-info">Liberar votação</a>
                                                @else
                                                    <button class="btn btn-light">
                                                        Votação autorizada
                                                        em <strong>{{ date('d/m/Y H:i:s', strtotime($vereador_votacao->autorizadaEm)) }}</strong>
                                                        por <strong>{{ $vereador_votacao->autorizadaPor->pessoa->nomeCompleto }}</strong> <br>
                                                        <strong>--AGUARDANDO VOTO--</strong>
                                                    </button>
                                                @endif
                                            @else
                                                <button class="btn btn-success">
                                                    Votação realizada em
                                                    <strong>{{ date('d/m/Y H:i:s', strtotime($vereador_votacao->votouEm)) }}</strong>
                                                </button>
                                            @endif
                                        @else
                                            <button class="btn btn-warning">
                                                <strong>--VOTAÇÃO PAUSADA--</strong>
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button class="btn btn-danger">
                                        Votação encerrada
                                        em <strong>{{ date('d/m/Y H:i:s', strtotime($votacao->dataHoraFim)) }}</strong><br>
                                        <strong>--VOTAÇÃO ENCERRADA--</strong>
                                    </button>
                                @endif

                            </td>
                            {{-- <td>
                                @if ($votacao->votacaoIniciada != 1)
                                    <button class="btn btn-danger"><i class="fas fa-times"></i></button>
                                @else
                                    @if ($vereador_votacao->votou != 1)
                                        @if ($vereador_votacao->votacaoAutorizada != 1)
                                            <a href="{{ route('votacao_eletronica.vereador.liberarVotacao', $vereador_votacao->id) }}" class="btn btn-info">Votar</a>
                                        @else
                                            <button class="btn btn-light">
                                                Votação autorizada
                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($vereador_votacao->autorizadaEm)) }}</strong>
                                                por <strong>{{ $vereador_votacao->autorizadaPor->pessoa->nomeCompleto }}</strong> <br>
                                                <strong>--AGUARDANDO VOTO--</strong>
                                            </button>
                                        @endif
                                    @else
                                        <button class="btn btn-success">
                                            Votação realizada em
                                            <strong>{{ date('d/m/Y H:i:s', strtotime($vereador_votacao->votouEm)) }}</strong>
                                        </button>
                                    @endif
                                @endif
                            </td> --}}

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
    </div>

    <div class="card-body">
        <div class="text-center">
            <h1 style="text-decoration: underline">INICIAR, PAUSAR OU ENCERRAR VOTAÇÃO</h1>
        </div>
    </div>

    <div class="card-body">
        <div class="text-center">
            {{-- @if ($votacao->votacaoIniciada != 1)
                <a href="{{ route('votacao_eletronica.gerenciamento.iniciarVotacao', $votacao->id) }}" class="btn btn-success mt-2" style="width: 100%; font-size: 1.3rem;">Iniciar Votação</a>
            @else
                <a href="{{ route('votacao_eletronica.gerenciamento.pausarVotacao', $votacao->id) }}" class="btn btn-warning mt-4" style="width: 100%; font-size: 1.3rem;">Pausar Votação</a>
                <a href="{{ route('votacao_eletronica.gerenciamento.encerrarVotacao', $votacao->id) }}" class="btn btn-danger mt-4" style="width: 100%; font-size: 1.3rem;">Encerrar Votação</a>
            @endif --}}

            @switch($votacao->id_status_votacao)
                @case('2')
                    <a href="{{ route('votacao_eletronica.gerenciamento.pausarVotacao', $votacao->id) }}" class="btn btn-warning mt-4" style="width: 100%; font-size: 1.3rem;">Pausar Votação</a>
                    <a href="{{ route('votacao_eletronica.gerenciamento.encerrarVotacao', $votacao->id) }}" class="btn btn-danger mt-4" style="width: 100%; font-size: 1.3rem;">Encerrar Votação</a>
                @break

                @case('5')
                    <a href="{{ route('votacao_eletronica.gerenciamento.iniciarVotacao', $votacao->id) }}" class="btn btn-success mt-2" style="width: 100%; font-size: 1.3rem;">Retomar Votação</a>
                @break

                @case('4')
                    <a href="#" class="btn btn-danger mt-4" style="width: 100%; font-size: 1.3rem;">Votação Encerrada</a>
                @break

                @default
                    <a href="{{ route('votacao_eletronica.gerenciamento.iniciarVotacao', $votacao->id) }}" class="btn btn-success mt-2" style="width: 100%; font-size: 1.3rem;">Iniciar Votação</a>
                @break
            @endswitch
        </div>
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

        $('#datatables-reponsive').dataTable({
            "oLanguage": {
                "sLengthMenu": "Mostrar _MENU_ registros por página",
                "sZeroRecords": "Nenhum registro encontrado",
                "sInfo": "Mostrando _START_ / _END_ de _TOTAL_ registro(s)",
                "sInfoEmpty": "Mostrando 0 / 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros)",
                "sSearch": "Pesquisar: ",
                "oPaginate": {
                    "sFirst": "Início",
                    "sPrevious": "Anterior",
                    "sNext": "Próximo",
                    "sLast": "Último"
                }
            },
        });

    });

</script>

@endsection
