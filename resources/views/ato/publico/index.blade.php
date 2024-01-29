@extends('layout.main-publico')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red,

    }
</style>
@include('errors.alerts')
@include('errors.errors')

@php
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Campo_Grande');
@endphp

<div class="card" style="background-color:white">

    <div class="card-header" style="background-color:white">
        <h2 class="text-center">
            <div>
                <span><i class="fas fa-address-book"></i></span>
            </div>
            <strong>Listagem de Atos</strong>
        </h2>
    </div>

    <div id="accordion">
        <div class="card">
            <div class="card-header" id="heading">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse"
                        data-target="#collapse" aria-expanded="false"
                        aria-controls="collapse">
                        Filtros
                    </button>
                </h5>
            </div>
            <div id="collapse" class="collapse show" aria-labelledby="heading"
                data-parent="#accordion">
                <div class="card-body">
                    <form action="{{ route('web_publica.ato.busca.livre') }}" method="POST" id="form-buscar">
                        @csrf
                        @method('POST')

                        <h4 style="text-align: center" class="m-3">Filtro por Busca Livre</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label">Pesquisar por palavras</label>
                                <input type="text" placeholder="Pesquisar por palavras" class="form-control" name="palavra" id="palavra" value="{{ $filtros['palavra'] ?? '' }}">
                                @if(isset($filtros['palavra']))
                                    <span class="badge bg-warning">Filtro aplicado</span>
                                @endif
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Exclusão de palavras</label>
                                <input type="text" placeholder="Palavras a serem excluídas" class="form-control" name="exclusao" id="exclusao">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fas fa-search-location"></i>
                                    &nbspPesquisar
                                </button>
                            </div>
                        </div>
                    </form>

                    <br><hr><br>

                    <form action="{{ route('web_publica.ato.busca.especifica') }}" method="POST" id="form-buscar">
                        @csrf
                        @method('POST')

                        <h4 style="text-align: center" class="m-3">Filtro Específico</h4>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Classificação do Ato</label>
                                <select name="id_classificacao" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($classificacaos as $classificacao)
                                        <option value="{{ $classificacao->id }}">{{ $classificacao->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Ano</label>
                                <input type="text" class="form-control" name="ano" id="ano">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control" name="numero">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Tipo de Ato</label>
                                <select name="id_tipo_ato" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($tipo_atos as $tipo_ato)
                                        <option value="{{ $tipo_ato->id }}">{{ $tipo_ato->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Assunto</label>
                                <select name="id_assunto" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($assuntos as $assunto)
                                        <option value="{{ $assunto->id }}">{{ $assunto->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="altera_dispositivo">Altera dispositivo</label>
                                <select name="altera_dispositivo" class="form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Órgão que editou o ato</label>
                                <select name="id_orgao" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($orgaos as $orgao)
                                        <option value="{{ $orgao->id }}">{{ $orgao->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Forma de Publicação</label>
                                <select name="id_forma_publicacao" class="select2 form-control">
                                    <option value="" selected disabled>--Selecione--</option>
                                    @foreach ($forma_publicacaos as $forma_publicacao)
                                        <option value="{{ $forma_publicacao->id }}">{{ $forma_publicacao->descricao }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Data de Publicação</label>
                                <input type="date" class="form-control" name="data_publicacao">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fas fa-search-location"></i>
                                    &nbspPesquisar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br><hr><br>

    <div id="accordion2">
        <div class="card">
            <div class="card-header" id="heading2">
                <h5 class="mb-0">
                    <button class="btn btn-link collapsed" data-toggle="collapse"
                        data-target="#collapse2" aria-expanded="false"
                        aria-controls="collapse">
                        Listagem
                    </button>
                </h5>
            </div>
            <div id="collapse2" class="collapse show" aria-labelledby="heading2" data-parent="#accordion2">
                <div class="card-body">
                    @if (Count($atos) == 0)
                        <div>
                            <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no sistema.</h1>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Ato</th>
                                        <th scope="col">Título</th>
                                        <th scope="col">Assunto</th>
                                        <th scope="col">Dados gerais</th>
                                        <th scope="col">Altera dispositivo</th>
                                        <th scope="col">Visualizar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($atos as $ato)
                                        <tr>
                                            <td>
                                                {{ $ato['tipo_ato'] }}
                                                Nº {{ $ato['numero'] }},
                                                de {{ $ato['created_at'] != null ? strftime('%d de %B de %Y', strtotime($ato['created_at'])) : 'não informado' }}
                                            </td>
                                            <td>{{ $ato['titulo'] != null ? $ato['titulo'] : 'não informado' }}</td>
                                            <td>{{ $ato['assunto'] != null ? $ato['assunto'] : 'não informado'  }}</td>
                                            <td>
                                                Órgão que editou o ato: <strong>{{ $ato['orgao'] }} </strong> <br>
                                                Forma de Publicação: <strong>{{ $ato['forma_publicacao'] != null ? $ato['forma_publicacao'] : 'não informado'  }} </strong> <br>
                                                Data de Publicação: <strong>{{ $ato['data_publicacao'] != null ? $ato['data_publicacao'] : 'não informado'  }}</strong>
                                            </td>
                                            <td>{{ $ato['altera_dispositivo'] == 1 ? 'Sim' : 'Não' }}</td>
                                            <td>
                                                <a href="{{ route('web_publica.ato.show', $ato['id']) }}" class="btn btn-secondary m-1"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <div class="card-footer">
        <a href="{{ route('ato.create') }}" class="btn btn-primary">Cadastrar Ato</a>
    </div>

</div>

<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="https://cdn.datatables.net/v/bs5/dt-1.11.0/r-2.2.9/rr-1.2.8/datatables.min.js"></script>

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
