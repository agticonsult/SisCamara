@extends('layout.main')

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css"
        integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ=="
        crossorigin="anonymous" />
    <style>
        .error {
            color: red
        }
    </style>
    @include('sweetalert::alert')

    <h1 class="h3 mb-3">Votações Eletrônicas</h1>
    <div class="card" style="background-color:white">
        <div class="card-body">
            @if (Count($vereador_votacaos) == 0)
                <div>
                    <h1 class="alert-info px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Não há cadastros no
                        sistema.</h1>
                </div>
            @else
                <div class="table-responsive">
                    <table id="datatables-reponsive" class="table table-bordered" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Tipo de Votação</th>
                                <th scope="col">Proposição</th>
                                <th scope="col">Legislatura</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vereador_votacaos as $vereador_votacao)
                                <tr>
                                    <td>{{ $vereador_votacao->votacao->data != null ? date('d/m/Y', strtotime($vereador_votacao->votacao->data)) : 'não informado' }}
                                    </td>
                                    <td>{{ $vereador_votacao->votacao->id_tipo_votacao != null ? $vereador_votacao->votacao->tipo_votacao->descricao : 'não informado' }}
                                    </td>
                                    <td>{{ $vereador_votacao->votacao->id_proposicao != null ? $vereador_votacao->votacao->proposicao->titulo : 'não informado' }}
                                    </td>
                                    <td>Início:
                                        <strong>{{ $vereador_votacao->votacao->legislatura->inicio_mandato }}</strong> -
                                        Fim: <strong>{{ $vereador_votacao->votacao->legislatura->fim_mandato }}</strong>
                                    </td>
                                    <td>
                                        {{-- @if ($vereador_votacao->votacaoAutorizada != null)
                                            @if ($vereador_votacao->votou == null)
                                                <a href="{{ route('votacao_eletronica.vereador.votacao', $vereador_votacao->id) }}"
                                                    class="btn btn-info m-1">Votar</a>
                                            @else
                                                <button type="button"
                                                    class="btn {{ $vereador_votacao->voto == 'Sim' ? 'btn-success' : '' }}
                                                    {{ $vereador_votacao->voto == 'Não' ? 'btn-danger' : '' }}
                                                    {{ $vereador_votacao->voto == 'Abstenção' ? 'btn-warning' : '' }}">Votou
                                                    <strong>{{ $vereador_votacao->voto }}</strong> em
                                                    {{ date('d/m/Y H:i:s', strtotime($vereador_votacao->votouEm)) }}
                                                </button>
                                            @endif
                                        @else
                                            {{ $vereador_votacao->votacao->id_status_votacao != null ? $vereador_votacao->votacao->status->descricao : 'não iniciada' }}
                                        @endif --}}
                                        {{-- @if ($vereador_votacao->votacao->votacaoEncerrada != 1) --}}
                                            @if ($vereador_votacao->votacao->votacaoPausada != 1)
                                                @if ($vereador_votacao->votou == null)
                                                    <a href="{{ route('votacao_eletronica.vereador.votacao', $vereador_votacao->id) }}" class="btn btn-info m-1">Votar</a>
                                                @else
                                                    <button type="button"
                                                        class="btn {{ $vereador_votacao->voto == 'Sim' ? 'btn-success' : '' }}
                                                        {{ $vereador_votacao->voto == 'Não' ? 'btn-danger' : '' }}
                                                        {{ $vereador_votacao->voto == 'Abstenção' ? 'btn-warning' : '' }}">Votou
                                                        <strong>{{ $vereador_votacao->voto }}</strong> em
                                                        {{ date('d/m/Y H:i:s', strtotime($vereador_votacao->votouEm)) }}
                                                    </button>
                                                @endif
                                            @else
                                                <button class="btn btn-warning" style="width: 100%">
                                                    <strong>--VOTAÇÃO PAUSADA--</strong>
                                                </button>
                                            @endif
                                        {{-- @else
                                            <button class="btn btn-danger" style="width: 100%">
                                                Votação encerrada
                                                em <strong>{{ date('d/m/Y H:i:s', strtotime($votacao->dataHoraFim)) }}</strong><br>
                                                <strong>--VOTAÇÃO ENCERRADA--</strong>
                                            </button>
                                        @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- <div class="card-footer">
        <a href="{{ route('votacao_eletronica.create') }}" class="btn btn-primary">Cadastrar Votação Eletrônica</a>
    </div> --}}

    </div>

    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>

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
