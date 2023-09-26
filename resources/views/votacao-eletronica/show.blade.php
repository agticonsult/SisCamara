@extends('layout.main')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
    <script src="{{ asset('js/gmaps.js') }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css"
        integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ=="
        crossorigin="anonymous" />
    <style>
        .error {
            color: red
        }
    </style>
    @include('errors.alerts')
    @include('errors.errors')

    <div class="card" style="background-color:white">

        <div class="card-header">
            <h2 class="text-center">
                <div>
                    <span><i class="fas fa-address-book"></i></span>
                </div>
                <strong>Votação</strong>
            </h2>
        </div>

        <div class="card-body">
            <div class="col-md-12">
                <ul class="nav nav-pills nav" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="original-tab" data-toggle="tab" href="#original" role="tab"
                            aria-controls="original" aria-selected="true">Descrição</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="consolidada-tab" data-toggle="tab" href="#consolidada" role="tab"
                            aria-controls="consolidada" aria-selected="false">Resultado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="compilado-tab" data-toggle="tab" href="#compilado" role="tab"
                            aria-controls="compilado" aria-selected="false">Seu voto</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">

                    {{-- @php
                    $tags = array('<span style="text-decoration: line-through;">');
                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Campo_Grande');
                @endphp --}}

                    <div class="tab-pane fade show active" id="original" role="tabpanel" aria-labelledby="original-tab">
                        <div class="card mt-2">
                            <div class="card-body">
                                Descrição da proposição
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="consolidada" role="tabpanel" aria-labelledby="consolidada-tab">
                        <div class="card mt-2">
                            <div class="card-body">
                                <p class="voto-sim">SIM 0</p>
                                <p class="voto-nao">NÃO 0</p>
                                <p class="voto-abstencao">ABSTENÇÃO 0</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="compilado" role="tabpanel" aria-labelledby="compilado-tab">
                        <div class="card mt-2">
                            <div class="card-body">
                                <form action="{{ route('votacao_eletronica.votar', $votacao->id) }}" method="POST" class="form_prevent_multiple_submits">
                                    @csrf
                                    @method('POST')
                                    <p>Qual o seu voto?</p>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-lg btn-success"
                                            style="width: 15%; margin-bottom: 0.7rem">Sim</button>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-lg btn-danger"
                                            style="width: 15%; margin-bottom: 0.7rem">Não</button>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-lg btn-warning"
                                            style="width: 15%; margin-bottom: 0.7rem">Abstenção</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/jquery.validate.js') }}"></script>
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('jquery-mask/src/jquery.mask.js') }}"></script>

    <script>
        $('#cep').mask('00.000-000');

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd;
        }

        if (mm < 10) {
            mm = '0' + mm;
        }

        today = yyyy + '-' + mm + '-' + dd;
        $('.dataFormat').attr('max', today);

        $("#form").validate({
            rules: {
                titulo: {
                    required: true
                },
                ano: {
                    required: true
                },
                numero: {
                    required: true
                },
                id_grupo: {
                    required: true
                },
                id_tipo_ato: {
                    required: true
                },
                subtitulo: {
                    required: true
                },
                corpo_texto: {
                    required: true
                }
            },
            messages: {
                titulo: {
                    required: "Campo obrigatório"
                },
                ano: {
                    required: "Campo obrigatório"
                },
                numero: {
                    required: "Campo obrigatório"
                },
                id_grupo: {
                    required: "Campo obrigatório"
                },
                id_tipo_ato: {
                    required: "Campo obrigatório"
                },
                subtitulo: {
                    required: "Campo obrigatório"
                },
                corpo_texto: {
                    required: "Campo obrigatório"
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

        });
    </script>
@endsection
