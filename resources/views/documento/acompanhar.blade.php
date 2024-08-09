@extends('layout.main')

@section('content')

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

        .aprovado {
            background-color: rgba(0, 0, 255, 0.6);
            color: white;
        }

        .reprovado {
            background-color: rgba(255, 0, 0, 0.6);
            color: white;
        }

        .criado {
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
        }

        .finalizado {
            background-color: rgba(0, 255, 0, 0.6);
            color: white;
        }

        .atualizado {
            background-color: rgba(121, 121, 121, 0.6);
            color: white;
        }

        .on-click:hover {
            transform: scale(1.02);
            cursor: pointer;
        }
    </style>
     @include('sweetalert::alert')

    @if ($documentoEdit->reprovado_em_tramitacao)
        @if ($documentoEdit->cadastradoPorUsuario == auth()->user()->id)
            <a href="{{ route('documento.edit', $documentoEdit->id) }}" style="text-decoration: none">
                <div class="col-md-12 warn">
                    ESTE DOCUMENTO FOI REPROVADO EM TRAMITAÇÃO E ENCAMINHADO AO AUTOR
                </div>
            </a>
        @else
            <div class="col-md-12 warn">
                ESTE DOCUMENTO FOI REPROVADO EM TRAMITAÇÃO E ENCAMINHADO AO AUTOR
            </div>
        @endif
    @endif

    @if ($documentoEdit->finalizado)
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
                                <input type="text" class="form-control" placeholder="Título do documento" value="{{ $documentoEdit->titulo }}" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Tipo de Documento</label>
                                <select name="id_tipo_documento" id="id_tipo_documento" class="select2 form-control" disabled>
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($tipoDocumentos as $tipoDocumento)
                                        <option value="{{ $tipoDocumento->id }}" {{ $documentoEdit->id_tipo_documento == $tipoDocumento->id ? 'selected' : '' }}>{{ $tipoDocumento->nome }} - Nível: {{ $tipoDocumento->nivel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Protocolo</label>
                                <input type="text" class="form-control" placeholder="Título do documento" value="{{ $documentoEdit->protocolo }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label" for="body">Conteúdo</label>
                                <textarea name="conteudo" class="form-control" cols="30" rows="30" id="conteudo">{{ $documentoEdit->conteudo }}</textarea>
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
                @if ($documentoEdit->id_tipo_workflow == 1)
                    @include('documento.departamentos-auto')
                @endif
                @if ($documentoEdit->id_tipo_workflow == 2)
                    @include('documento.departamentos-manual')
                @endif
            </div>
        </div>
    </div>

    {{-- só mostra a tramitação se o usuario estiver presente no departamento atual, o documento não estiver reprovado e nem finalizado --}}
    @if ($documentoEdit->podeTramitar(auth()->user()->id) && !$documentoEdit->reprovado_em_tramitacao && !$documentoEdit->finalizado)
        <div id="accordion3">
            <div class="card">
                <div class="card-header" id="heading3">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                            Tramitação do documento
                        </button>
                    </h5>
                </div>
                <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion3">

                    @if ($documentoEdit->id_tipo_workflow == 1)
                        @include('documento.tramitacao-auto')
                    @endif
                    @if ($documentoEdit->id_tipo_workflow == 2)
                        @include('documento.tramitacao-manual')
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
                                <th class="text-center">Status (clique para ver mais)</th>
                                <th>Usuário</th>
                                <th>Departamento</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todoHistoricoMovDocumento as $historico)
                                <tr class="on-click" data-toggle="modal" data-target="#verDetalhes{{ $historico->id }}">
                                    @if ($historico->id_status != null)
                                        @if ($historico->id_status == 1)
                                            <td class="aprovado text-center" >{{ $historico->status->descricao }}</td>
                                        @endif
                                        @if ($historico->id_status == 2)
                                            <td class="reprovado text-center">{{ $historico->status->descricao }}</td>
                                        @endif
                                        @if ($historico->id_status == 3)
                                            <td class="criado text-center">{{ $historico->status->descricao }}</td>
                                        @endif
                                        @if ($historico->id_status == 4)
                                            <td class="finalizado text-center">{{ $historico->status->descricao }}</td>
                                        @endif
                                        @if ($historico->id_status == 5)
                                            <td class="atualizado text-center">{{ $historico->status->descricao }}</td>
                                        @endif
                                    @else
                                        <td class="text-center">-</td>
                                    @endif
                                    <td>{{ $historico->id_usuario != null ? $historico->usuario->pessoa->nome : '-' }}</td>
                                    <td>{{ $historico->id_departamento != null ? $historico->departamento->descricao : '-' }}</td>
                                    {{-- <td>{{ $historico->dataReprovado != null ? date('d/m/Y H:i:s', strtotime($historico->dataReprovado)) : '-' }}</td> --}}
                                    <td>{{ $historico->created_at != null ? $historico->created_at->format('d/m/Y H:i:s') : 'não informado' }}</td>
                                </tr>
                                <div class="modal fade" id="verDetalhes{{ $historico->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-lg" role="document">
                                        <div class="modal-content">
                                            @if ($historico->id_status == 1)
                                                <div class="modal-header aprovado">
                                                    Detalhes da movimentação
                                                </div>
                                            @endif
                                            @if ($historico->id_status == 2)
                                                <div class="modal-header reprovado">
                                                    Detalhes da movimentação
                                                </div>
                                            @endif
                                            @if ($historico->id_status == 3)
                                                <div class="modal-header criado">
                                                    Detalhes da movimentação
                                                </div>
                                            @endif
                                            @if ($historico->id_status == 4)
                                                <div class="modal-header finalizado">
                                                    Detalhes da movimentação
                                                </div>
                                            @endif
                                            @if ($historico->id_status == 5)
                                                <div class="modal-header atualizado">
                                                    Detalhes da movimentação
                                                </div>
                                            @endif
                                            <div class="modal-body text-justify" style="color: black">
                                                <p class="mb-1"><strong>Usuário:</strong> {{ $historico->id_usuario != null ? $historico->usuario->pessoa->nome : ' não informado' }}</p>
                                                <p class="mb-1"><strong>Departamento:</strong> {{ $historico->id_departamento != null ? $historico->departamento->descricao : ' não informado' }}</p>
                                                <p class="mb-1"><strong>Data:</strong> {{ $historico->created_at != null ? $historico->created_at->format('d/m/Y H:i:s')  : ' não informado' }}</p>
                                                <p class="mb-1"><strong>Parecer:</strong> {{ $historico->parecer }}</p>
                                                @if ($historico->anexo != null)
                                                    <p class="text-left mb-1">
                                                        <strong>Anexo:</strong>
                                                        <a href="{{ route('documento.obterAnexo', $historico->anexo->id) }}" class="mx-2">
                                                            <i class="fas fa-file link"></i>
                                                            {{ $historico->anexo->nome_original }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Voltar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <a href="{{ route('documento.index') }}" class="btn btn-secondary">Voltar</a>
    </div>

@endsection

@section('scripts')
    <script>
        $('#conteudo').trumbowyg({
            lang: 'pt_br',
        }).trumbowyg('disable'); // Desabilita a edição, deixando apenas leitura

        $(document).ready(function() {
            $('#datatables-reponsive').DataTable({
                order: [],
                columnDefs: [
                    { orderable: false, targets: '_all' }
                ],
                oLanguage: {
                    sLengthMenu: "Mostrar _MENU_ registros por página",
                    sZeroRecords: "Nenhum registro encontrado",
                    sInfo: "Mostrando _START_ / _END_ de _TOTAL_ registro(s)",
                    sInfoEmpty: "Mostrando 0 / 0 de 0 registros",
                    sInfoFiltered: "(filtrado de _MAX_ registros)",
                    sSearch: "Pesquisar: ",
                    oPaginate: {
                        sFirst: "Início",
                        sPrevious: "Anterior",
                        sNext: "Próximo",
                        sLast: "Último"
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
