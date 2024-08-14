@extends('layout.main')

@section('content')

    @include('sweetalert::alert')

    <h1 class="h3 mb-3"><span class="caminho">Processo Legislativo > </span>Alteração de Pleito Eleitoral</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            <div class="col-md-12">
                <form action="{{ route('processo_legislativo.pleito_eleitoral.update', $pleito_eleitoral->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Ano do Pleito Eleitoral</label>
                            <input type="text" class="ano form-control" name="ano_pleito" value="{{ $pleito_eleitoral->ano_pleito }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Pleito Especial</label>
                            <select name="pleitoEspecial" class="form-control">
                                @if ($pleito_eleitoral->pleitoEspecial == 1)
                                    <option value="0">Não</option>
                                    <option value="1" selected>Sim</option>
                                @else
                                    <option value="0" selected>Não</option>
                                    <option value="1">Sim</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data do primeiro turno</label>
                            <input type="date" class="form-control" name="dataPrimeiroTurno" value="{{ $pleito_eleitoral->dataPrimeiroTurno }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">*Data do segundo turno</label>
                            <input type="date" class="form-control" name="dataSegundoTurno" value="{{ $pleito_eleitoral->dataSegundoTurno }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Legislatura</label>
                            <select name="id_legislatura" class="select2 form-control">
                                @foreach ($legislaturas as $legislatura)
                                    <option value="{{ $legislatura->id }}" {{ $legislatura->id == $pleito_eleitoral->id_legislatura ? 'selected' : '' }}>
                                        Início: <strong>{{ $legislatura->inicio_mandato }}</strong> -
                                        Fim: <strong>{{ $legislatura->fim_mandato }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Cargos eletivos</label>
                            <select name="id_cargo_eletivo[]" class="select2 form-control" multiple>
                                @foreach ($cargo_eletivos as $cargo_eletivo)
                                    <option value="{{ $cargo_eletivo->id }}">{{ $cargo_eletivo->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <br>
                    <div class="col-md-12">
                        <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                        <a href="{{ route('processo_legislativo.pleito_eleitoral.index') }}" class="btn btn-light m-1">Voltar</a>
                    </div>
                    <br>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md-12">
                <hr><br>
                <h5>Listagem de Cargos Eletivos do Pleito</h5>
                <br>
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                {{-- <th scope="col">ID</th> --}}
                                <th scope="col">Cargo Eletivo</th>
                                <th scope="col">Cadastrado por</th>
                                <th scope="col">Status <br>(para desativar este perfil deste usuário, clique no botão "Ativo")</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pleito_eleitoral->cargos_eletivos() as $pleito_cargo)
                                <tr>
                                    {{-- <td>{{ $pleito_cargo->id }}</td> --}}
                                    <td>{{ $pleito_cargo->cargo_eletivo->descricao }}</td>
                                    <td>
                                        <strong>{{ $pleito_cargo->cadastradoPorUsuario != null ? $pleito_cargo->cad_usuario->pessoa->nome : 'não informado' }}</strong>
                                        em <strong>{{ $pleito_cargo->created_at != null ? $pleito_cargo->created_at->format('d/m/Y H:i:s') : 'não informado' }}</strong>
                                    </td>
                                    <td>
                                        @if ($pleito_cargo->ativo == 1)
                                            <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#exampleModalExcluir{{ $pleito_cargo->id }}">
                                                Ativo
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-info">
                                                Desativado
                                                por <strong>{{ $pleito_cargo->inativadoPorUsuario != null ? $pleito_cargo->inativadoPor->pessoa->nome : 'não informado' }}</strong>
                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($pleito_cargo->dataInativado)) }}</strong>
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                <div class="modal fade" id="exampleModalExcluir{{ $pleito_cargo->id }}"
                                    tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelExcluir"
                                    aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" class="form_prevent_multiple_submits" action="{{ route('processo_legislativo.pleito_eleitoral.cargo_eletivo.destroy', $pleito_cargo->id) }}">
                                                @csrf
                                                @method('POST')
                                                <div class="modal-header btn-danger">
                                                    <h5 class="modal-title text-center" id="exampleModalLabelExcluir">
                                                        <strong style="font-size: 1.2rem">
                                                            Excluir o Cargo de <i>{{ $pleito_cargo->cargo_eletivo->descricao }}</i>
                                                            deste Pleito Eleitoral
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
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $('.ano').mask('0000');

        $("#form").validate({
            rules : {
                ano_pleito:{
                    required:true
                },
                id_legislatura:{
                    required:true
                },
                dataPrimeiroTurno:{
                    required:true
                },
                dataSegundoTurno:{
                    required:true
                }
            },
            messages:{
                ano_pleito:{
                    required:"Campo obrigatório"
                },
                id_legislatura:{
                    required:"Campo obrigatório"
                },
                dataPrimeiroTurno:{
                    required:"Campo obrigatório"
                },
                dataSegundoTurno:{
                    required:"Campo obrigatório"
                }
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
