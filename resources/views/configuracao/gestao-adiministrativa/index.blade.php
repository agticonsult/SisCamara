@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    @if (!isset($departamentos[0]))
        <div class="alert alert-warning alert-dismissible" role="alert">
            {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
            <div class="alert-message">
                <strong>Sem DEPARTAMENTO(S) cadastrado!</strong>
                <a href="{{ route('configuracao.departamento.index') }}">Clique aqui para cadastrar</a>
            </div>
        </div>
    @endif

    <h1 class="h3 mb-3">Gestão Administrativa</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('configuracao.gestao_administrativa.store') }}" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <h5>Aprovação de cadastro e recebimento do documento de Usuário Externo</h5>
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-label">*Departamento</label>
                            <select name="id_departamento" class="form-control select2 @error('id_departamento') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                @foreach ($departamentosArray as $dep)
                                    <option value="{{ $dep->id }}">
                                        {{ $dep->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_departamento')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Aprovação de cadastro</label>
                            <select name="aprovacaoCadastro" class="form-control @error('aprovacaoCadastro') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            @error('aprovacaoCadastro')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">*Recebimento de documento</label>
                            <select name="recebimentoDocumento" class="form-control @error('recebimentoDocumento') is-invalid @enderror">
                                <option value="" selected disabled>--Selecione--</option>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                            @error('recebimentoDocumento')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                </div>
            </form>
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
                    <div class="table-responsive">
                        <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Departamento</th>
                                    <th scope="col">Aprovação de cadastro</th>
                                    <th scope="col">Recebimento do documento</th>
                                    <th scope="col">Cadastrado por</th>
                                    <th scope="col">Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gestoesAdm as $gestaoAdm)
                                    <tr>
                                        <td>
                                            {{ $gestaoAdm->id_departamento != null ? $gestaoAdm->departamento->descricao : 'não informado' }}
                                        </td>
                                        <td>{{ $gestaoAdm->aprovacaoCadastro == 1 ? 'Sim' : 'Não' }}</td>
                                        <td>{{ $gestaoAdm->recebimentoDocumento == 1 ? 'Sim' : 'Não' }}</td>
                                        <td>
                                            <strong>{{ $gestaoAdm->cadastradoPorUsuario != null ? $gestaoAdm->cad_usuario->pessoa->nome : 'cadastrado pelo sistema' }}</strong>
                                            em <strong>{{ $gestaoAdm->created_at != null ? $gestaoAdm->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                        </td>
                                        <td>
                                            <a href="{{ route('configuracao.gestao_administrativa.edit', $gestaoAdm->id) }}" class="btn btn-warning"><i class="align-middle me-2 fas fa-fw fa-pen"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

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

            $('#id_funcionalidade').select2({
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    }
                },
                closeOnSelect: false,
                width: '100%',
                dropdownCssClass: "bigdrop"
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
