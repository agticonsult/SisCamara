@extends('layout.main-publico')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css"
        integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
    <style>
        .error {
            color: red
        }
    </style>
    @include('errors.alerts')
    @include('errors.errors')

    <div class="card" style="background-color:white">

        <div class="card-header" style="background-color:white">
            <h2 class="text-center">
                <div>
                    <span><i class="fas fa-chess-king"></i></span>
                </div>
                <strong>Resultado da Votação</strong>
            </h2>
        </div>

        <div id="accordion">
            <div class="card">
                <div class="card-header" id="heading">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse"
                            aria-expanded="false" aria-controls="collapse">
                            Resultado
                        </button>
                    </h5>
                </div>
                <div id="collapse" class="collapse show" aria-labelledby="heading" data-parent="#accordion">
                    <div class="card-body">
                        <div class="col-md-12">
                            <p class="voto-sim">SIM = {{ $votosSim }}</p>
                            <p class="voto-nao">NÃO = {{ $votosNao }}</p>
                            <p class="voto-abstencao">ABSTENÇÃO = {{ $votosAbs }}</p>
                            <p class="voto-total">TOTAL = {{ $votosSim + $votosNao + $votosAbs }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div id="accordion2">
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo"
                            aria-expanded="false" aria-controls="collapseTwo">
                            Votos
                        </button>
                    </h5>
                </div>
                <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion2">
                    <div class="card-body">
                        @if (Count($vereadorVotacaos) == 0)
                            <div>
                                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há
                                    cadastros no sistema.</h1>
                            </div>
                        @elseif ($votacao->tipo_votacao->descricao == 'Fechada')
                            <div>
                                <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Esta
                                    votação é fechada.</h1>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col">Vereador</th>
                                            <th scope="col">Voto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vereadorVotacaos as $vereadorVotacao)
                                            <tr>
                                                <td>{{ $vereadorVotacao->id_vereador != null ? $vereadorVotacao->vereador->usuario->pessoa->nome : 'não informado' }}
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn
                                                        {{ $vereadorVotacao->voto == 'Sim' ? 'btn-success' : '' }}
                                                        {{ $vereadorVotacao->voto == 'Não' ? 'btn-danger' : '' }}
                                                        {{ $vereadorVotacao->voto == 'Abstenção' ? 'btn-warning' : '' }}
                                                        m-1">{{ $vereadorVotacao->voto != null ? $vereadorVotacao->voto : 'não informado' }}
                                                    </button>
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
        </div> --}}
        <div class="card-footer">
            <div class="col-md-12">
                <a href="{{ route('web_publica.votacao_eletronica.indexPublico') }}" class="btn btn-light m-1">Voltar</a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>

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
