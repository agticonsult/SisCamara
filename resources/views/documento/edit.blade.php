@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <style>
        .card-header-reprovacao {
            border-bottom: 1px solid #dae4f3;
            border-radius: 20px 20px 0px 0px !important;
            background-color: rgba(255, 94, 0, 0.3)
        }
        .card-reprovacao {
            border-radius: 20px !important;
        }
        select[readonly] {
            background: #eee;
            pointer-events: none;
            touch-action: none;
        }
    </style>

    <h1 class="h3 mb-3">Edição de documento</h1>

    @if ($documento->reprovacao())
        <div class="card card-reprovacao">
            <div class="card-header card-header-reprovacao">
                <h5 class="card-title mb-0">Este documento foi reprovado em tramitação. Segue as informações da reprovação:</h5>
            </div>
            <div class="card-body text-justify">
                <p><strong>Usuário:</strong> {{ $documento->reprovacao()->id_usuario != null ? $documento->reprovacao()->usuario->pessoa->nome : ' não informado' }}</p>
                <p><strong>Data:</strong> {{ $documento->reprovacao()->created_at != null ? $documento->reprovacao()->created_at->format('d/m/Y H:i:s')  : ' não informado' }}</p>
                <p><strong>Parecer:</strong> {{ $documento->reprovacao()->parecer }}</p>
                @if ($documento->reprovacao()->anexo != null)
                    <p class="text-left">
                        <strong>Anexo:</strong>
                        <a href="{{ route('documento.obterAnexo', $documento->reprovacao()->anexo->id) }}" class="mx-2">
                            <i class="fas fa-file link"></i>
                            {{ $documento->reprovacao()->anexo->nome_original }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('documento.update', $documento->id) }}" id="form" method="POST" class="form_prevent_multiple_submits" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">*Título</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" name="titulo" placeholder="Título do documento" value="{{ $documento->titulo }}">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Workflow de tramitação</label>
                            <select class="form-control" name="id_tipo_workflow" readonly="readonly" tabindex="-1" aria-disabled="true">
                                <option value="{{ $documento->id_tipo_workflow }}" selected>{{ $documento->tipoWorkflow->descricao }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Tipo de Documento</label>
                            <select class="form-control" name="id_tipo_documento" readonly="readonly" tabindex="-1" aria-disabled="true">
                                <option value="{{ $documento->id_tipo_documento }}" selected>{{ $documento->tipoDocumento->nome }}</option>
                            </select>
                        </div>
                        @if ($documento->id_tipo_workflow == 2)
                            <div class="form-group col-md-4">
                                <label class="form-label">*Selecione o primeiro departamento da tramitação</label>
                                <select name="id_departamento" id="id_departamento" class="select2 form-control @error('id_departamento') is-invalid @enderror">
                                    <option value="" selected disabled>-- Selecione --</option>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id_departamento }}">{{ $dep->departamento->descricao }}</option>
                                    @endforeach
                                </select>
                                @error('id_departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="col-md-12">
                            <label class="form-label" for="body">Conteúdo</label>
                            <textarea name="conteudo" class="form-control @error('conteudo') is-invalid @enderror" cols="30" rows="20" id="conteudo">{{ $documento->conteudo }}</textarea>
                            @error('conteudo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ route('documento.index') }}" class="btn btn-light">Voltar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('#conteudo').trumbowyg({
            lang: 'pt_br',
        });

    </script>

@endsection
