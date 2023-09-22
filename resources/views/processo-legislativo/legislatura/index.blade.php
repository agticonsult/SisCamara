@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
@include('errors.errors')

<div class="card" style="background-color:white">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Listagem de Legislaturas</strong>
        </h2>
    </div>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="heading">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse" aria-expanded="false" aria-controls="collapse">
                    Cadastro
                </button>
                </h5>
            </div>
            <div id="collapse" class="collapse" aria-labelledby="heading" data-parent="#accordion">
                <div class="card-body">
                    <div class="col-md-12">
                        <form action="{{ route('processo_legislativo.legislatura.store') }}" id="form" method="POST" class="form_prevent_multiple_submits">
                            @csrf
                            @method('POST')

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Início do mandato</label>
                                    <input type="text" class="ano form-control" name="inicio_mandato" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">*Fim do mandato</label>
                                    <input type="text" class="ano form-control" name="fim_mandato" required>
                                </div>
                            </div>

                            <br>
                            <div class="col-md-12">
                                <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Listagem
                </button>
                </h5>
            </div>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                <div class="card-body">
                    @if (Count($legislaturas) == 0)
                        <div>
                            <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Mandato</th>
                                        <th scope="col">Cadastrado por</th>
                                        <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($legislaturas as $legislatura)
                                        <tr>
                                            <td>Início: <strong>{{ $legislatura->inicio_mandato }}</strong> - Fim: <strong>{{ $legislatura->fim_mandato }}</strong></td>
                                            <td>
                                                <strong>{{ $legislatura->cadastradoPorUsuario != null ? $legislatura->cad_usuario->pessoa->nomeCompleto : 'não informado' }}</strong>
                                                em <strong>{{ $legislatura->created_at != null ? $legislatura->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('processo_legislativo.legislatura.edit', $legislatura->id) }}" class="btn btn-warning m-1">Alterar</a>
                                                <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $legislatura->id }}">Excluir</button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="exampleModalExcluir{{ $legislatura->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST" class="form_prevent_multiple_submits" action="{{ route('processo_legislativo.legislatura.destroy', $legislatura->id) }}">
                                                        @csrf
                                                        @method('POST')
                                                        <div class="modal-header btn-danger">
                                                            <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                                <strong style="font-size: 1.2rem">
                                                                    Excluir Legislatura ID
                                                                    <i>{{ $legislatura->id != null ? $legislatura->id : 'não informado' }}</i>
                                                                    Início:
                                                                    <i>{{ $legislatura->inicio_mandato }}</i>
                                                                    - Fim:
                                                                    <i>{{ $legislatura->fim_mandato }}</i>
                                                                </strong>
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
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
    $('.ano').mask('0000');

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
