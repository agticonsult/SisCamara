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
    <div class="card-body">
        <h5 class="card-title mb-0">Departamento Documento</h5><br>
        @if (Count($departamentoDocumentos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Título</th>
                            <th scope="col">Tipo de documento</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departamentoDocumentos as $depDoc)
                            <tr>
                                <td>{{ $depDoc->titulo }}</td>
                                <td>
                                    {{ $depDoc->id_tipo_documento != null ? $depDoc->tipoDocumento->nome : 'não informado' }} <br>
                                    <strong>{{ $depDoc->id_tipo_workflow != null ? $depDoc->tipoWorkflow->descricao : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $depDoc->cadastradoPorUsuario != null ? $depDoc->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                    em <strong>{{ $depDoc->created_at != null ? $depDoc->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('departamento_documento.show', $depDoc->id) }}" class="btn btn-info m-1"><i class="fas fa-eye"></i></a>
                                    <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $depDoc->id }}"><i class="fas fa-trash"></i></button>
                                    {{-- <a href="{{ route('departamento_documento.acompanharDoc', $depDoc->id) }}" class="btn btn-info m-1">Acompanhar documento</a> --}}
                                </td>
                            </tr>

                            {{-- <div class="modal fade" id="exampleModalExcluir{{ $reparticao->id }}"
                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form method="POST" class="form_prevent_multiple_submits" action="{{ route('reparticao.destroy', $reparticao->id) }}">
                                            @csrf
                                            @method('POST')
                                            <div class="modal-header btn-danger">
                                                <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                    Excluir <strong>{{ $reparticao->descricao != null ? $reparticao->descricao : 'não informado' }} - {{ $reparticao->id_tipo_reparticao != null ? $reparticao->tipo_reparticao->descricao : 'não informado' }}</strong>
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
        <a href="{{ route('departamento_documento.create') }}" class="btn btn-primary">Cadastrar documento</a>
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
