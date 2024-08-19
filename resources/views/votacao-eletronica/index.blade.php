@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('sweetalert::alert')

<h1 class="h3 mb-3"><span class="caminho">Votação Eletrônica > Gerenciar Votações > </span>Votações</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        @if (Count($votacaos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Data</th>
                            <th scope="col">Tipo de Votação</th>
                            <th scope="col">Proposição</th>
                            <th scope="col">Legislatura</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Status</th>
                            <th scope="col">Resultado</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($votacaos as $votacao)
                            <tr>
                                <td>{{ $votacao->data != null ? date('d/m/Y', strtotime($votacao->data)) : 'não informado' }}</td>
                                <td>{{ $votacao->id_tipo_votacao != null ? $votacao->tipo_votacao->descricao : 'não informado' }}</td>
                                <td>{{ $votacao->id_proposicao != null ? $votacao->proposicao->titulo : 'não informado' }}</td>
                                <td>Início: <strong>{{ $votacao->legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $votacao->legislatura->fim_mandato }}</strong></td>
                                <td>
                                    <strong>{{ $votacao->cadastradoPorUsuario != null ? $votacao->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                    em <strong>{{ $votacao->created_at != null ? $votacao->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>{{ $votacao->id_status_votacao != null ? $votacao->status->descricao : 'não iniciada' }}</td>
                                <td>
                                    <a href="{{ route('votacao_eletronica.resultado', $votacao->id) }}" class="btn btn-secondary m-1" style="width: 100%">Resultado</a>
                                </td>
                                <td>
                                    @if ($votacao->votacaoEncerrada != 1)
                                        <a href="{{ route('votacao_eletronica.edit', $votacao->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        <a href="{{ route('votacao_eletronica.edit', $votacao->id) }}" class="btn btn-danger" data-toggle="modal" data-target="#exampleModalExcluir{{ $votacao->id }}"><i class="align-middle me-2 fas fa-fw fa-trash"></i></a>
                                        @if (Auth::user()->temPermissao('VotacaoEletronica', 'Alteração'))
                                            <a href="{{ route('votacao_eletronica.gerenciamento.gerenciar', $votacao->id) }}" class="btn btn-info m-1">Gerenciar Votação</a>
                                        @endif
                                    @else
                                        @if (Auth::user()->temPermissao('VotacaoEletronica', 'Alteração'))
                                            <a href="{{ route('votacao_eletronica.gerenciamento.gerenciar', $votacao->id) }}" class="btn btn-secondary m-1" style="width: 100%">Visualizar</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            <div class="modal fade" id="exampleModalExcluir{{ $votacao->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('votacao_eletronica.destroy', $votacao->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    Excluir Votação: <strong>{{ date('d/m/Y', strtotime($votacao->data)) }} - {{ $votacao->id_proposicao != null ? $votacao->proposicao->titulo : 'não informado' }} - Início: <strong>{{ $votacao->legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $votacao->legislatura->fim_mandato }}</strong>
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
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ route('votacao_eletronica.create') }}" class="btn btn-primary">Cadastrar Votação Eletrônica</a>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>

    $('.cpf').mask('000.000.000-00');

    $(document).ready(function() {

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
