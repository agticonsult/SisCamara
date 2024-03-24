@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }

    .warn {
        display: flex;
        text-align: center;
        justify-content: center;
        align-items: center;
        background-color: rgba(255, 94, 0, 0.795);
        color: white;
        margin-bottom: 1rem;
        padding: 0.5rem 0.5rem !important;
        text-transform: uppercase;
    }

    .closed {
        display: flex;
        text-align: center;
        justify-content: center;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.726);
        color: white;
        margin-bottom: 1rem;
        padding: 0.5rem 0.5rem !important;
        text-transform: uppercase;
    }
</style>
@include('errors.alerts')
@include('errors.errors')

@if ($departamentoDocumentoEdit->reprovado_em_tramitacao)
    <div class="col-md-12 warn">
        ESTE DOCUMENTO FOI REPROVADO EM TRAMITAÇÃO E ENCAMINHADO AO AUTOR
    </div>
@endif

@if ($departamentoDocumentoEdit->finalizado)
    <div class="col-md-12 closed">
        ESTE DOCUMENTO FOI FINALIZADO
    </div>
@endif

<div id="accordion1">
    <div class="card">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Dados do documento
                </button>
            </h5>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion1">
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" placeholder="Título do documento" value="{{ $departamentoDocumentoEdit->titulo }}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Tipo de Documento</label>
                            <select name="id_tipo_documento" id="id_tipo_documento" class="select2 form-control" disabled>
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($tipoDocumentos as $tipoDocumento)
                                    <option value="{{ $tipoDocumento->id }}" {{ $departamentoDocumentoEdit->id_tipo_documento == $tipoDocumento->id ? 'selected' : '' }}>{{ $tipoDocumento->nome }} - Nível: {{ $tipoDocumento->nivel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Protocolo</label>
                            <input type="text" class="form-control" placeholder="Título do documento" value="{{ $departamentoDocumentoEdit->protocolo }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label" for="body">Conteúdo</label>
                            <textarea name="conteudo" class="form-control" cols="30" rows="30" id="conteudo">{{ $departamentoDocumentoEdit->conteudo }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="accordion2">
    <div class="card">
        <div class="card-header" id="heading2">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                    Departamento(s) vinculado(s) ao documento
                </button>
            </h5>
        </div>
        <div id="collapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordion2">
            @if ($departamentoDocumentoEdit->id_tipo_workflow == 1)
                @include('departamento-documento.departamentos-auto')
            @endif
            @if ($departamentoDocumentoEdit->id_tipo_workflow == 2)
                @include('departamento-documento.departamentos-manual')
            @endif
        </div>
    </div>
</div>

@if (!$departamentoDocumentoEdit->reprovado_em_tramitacao && !$departamentoDocumentoEdit->finalizado)
    <div id="accordion3">
        <div class="card">
            <div class="card-header" id="heading3">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                        Aprovar e encaminhar ao próximo departamento
                    </button>
                </h5>
            </div>
            <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion3">

                @if ($departamentoDocumentoEdit->id_tipo_workflow == 1)
                    @include('departamento-documento.tramitacao-auto')
                @endif
                @if ($departamentoDocumentoEdit->id_tipo_workflow == 2)
                    @include('departamento-documento.tramitacao-manual')
                @endif
            </div>
        </div>
    </div>
@endif

<div id="accordion4">
    <div class="card">
        <div class="card-header" id="heading4">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                    Histórico de movimentações
                </button>
            </h5>
        </div>
        <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordion4">
            <div class="card-body">
                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col">Usuário</th>
                            <th scope="col">Departamento</th>
                            <th scope="col">Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($todoHistoricoMovDocumento as $historico)
                            <tr>
                                <td>{{ $historico->id_status != null ? $historico->status->descricao : '-' }}</td>
                                <td>{{ $historico->id_usuario != null ? $historico->usuario->pessoa->nome : '-' }}</td>
                                <td>{{ $historico->id_departamento != null ? $historico->departamento->descricao : '-' }}</td>
                                {{-- <td>{{ $historico->dataReprovado != null ? date('d/m/Y H:i:s', strtotime($historico->dataReprovado)) : '-' }}</td> --}}
                                <td>{{ $historico->created_at != null ? $historico->created_at->format('d/m/Y H:i:s') : 'não informado' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <a href="{{ route('departamento_documento.index') }}" class="btn btn-secondary">Voltar</a>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="https://cdn.tiny.cloud/1/hh6dctatzptohe71nfevw76few6kevzc4i1q1utarze7tude/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    tinymce.init({
        selector: 'textarea',
        readonly: true, // Define o editor como somente leitura (modo de visualização)
        menubar: false, // Esconde a barra de ferramentas
        toolbar: false, // Desativa a barra de ferramentas
        setup: function(editor) {
            editor.on('init', function() {
                editor.getBody().setAttribute('contenteditable', false); // Torna o conteúdo do editor não editável
            });
        }
    });

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
