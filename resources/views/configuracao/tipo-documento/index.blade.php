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
{{-- @include('errors.errors') --}}

<h1 class="h3 mb-3">Tipo de Documentos</h1>
<div class="card" style="background-color:white">
    <div class="card-body">
        @if (Count($tipoDocumentosAtivos) == 0)
            <div>
                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
            </div>
        @else
            <div class="table-responsive">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Tipo de documento</th>
                            <th scope="col">Nível</th>
                            <th scope="col">Tramitação</th>
                            <th scope="col">Cadastrado por</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tipoDocumentosAtivos as $tp)
                            <tr>
                                <td>{{ $tp->nome != null ? $tp->nome : '-' }}</td>
                                <td>{{ $tp->tipoDocumento != null ? $tp->tipoDocumento : '-' }}</td>
                                <td>{{ $tp->nivel != null ? $tp->nivel : '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModalVisualizar{{ $tp->id }}">Visualizar</i></button>
                                </td>
                                <td>
                                    <strong>{{ $tp->cadastradoPorUsuario != null ? $tp->cad_usuario->pessoa->nome : '-' }}</strong> em
                                    <strong>{{ $tp->created_at != null ? $tp->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('configuracao.tipo_documento.edit', $tp->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                </td>
                            </tr>
                            <div class="modal fade" id="exampleModalVisualizar{{ $tp->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelVisualizar" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header btn-info">
                                            <h5 class="modal-title text-center" id="exampleModalLabelVisualizar">
                                                <strong>Tramitações</strong>
                                            </h5>
                                        </div>
                                        <div class="modal-body">
                                           @if (count($tp->departamentoVinculados) != null)
                                                @foreach ($tp->departamentoVinculados as $dpVinc)
                                                    <ul>
                                                        <li>
                                                            {{ $dpVinc->departamento->descricao }}
                                                        </li>
                                                    </ul>
                                                @endforeach
                                           @else
                                                Sem tramitações
                                           @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">voltar</button>
                                        </div>
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
        <a href="{{ route('configuracao.tipo_documento.create') }}" class="btn btn-primary">Cadastrar</a>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

<script>
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
