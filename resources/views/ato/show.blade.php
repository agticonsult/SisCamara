@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAUgxBPrGkKz6xNwW6Z1rJh26AqR8ct37A"></script>
<script src="{{ asset('js/gmaps.js') }}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
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
            <strong>Visualização de Ato</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <ul class="nav nav-pills nav" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="original-tab" data-toggle="tab" href="#original" role="tab"
                       aria-controls="original" aria-selected="true">Original</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="consolidada-tab" data-toggle="tab" href="#consolidada" role="tab"
                       aria-controls="consolidada" aria-selected="false">Consolidada</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="compilado-tab" data-toggle="tab" href="#compilado" role="tab"
                       aria-controls="compilado" aria-selected="false">Compilada</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"> <i class="fas fa-link"></i>Atos relacionados</a>
                    <div class="dropdown-menu">
                        {{-- @if (Count($ato->atosRelacionados) != 0)
                            @foreach ($ato->atosRelacionados as $ato_relacionado)
                                <a class="dropdown-item" href=""
                                    target="_blank">
                                    {{ $ato_relacionado->ato_principal->titulo }}
                                </a>
                            @endforeach
                        @else
                            Não possui atos relacionados
                        @endif --}}
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"> <i class="fas fa-paperclip"></i> Anexos</a>

                    <div class="dropdown-menu">

                        {{-- @foreach($ato->anexos as $anexo)
                            <a class="dropdown-item" href="{{url('storage/Atos/Anexos/'.$anexo->nome_hash)}}"
                               target="_blank">
                                {{ $anexo->nome_original }}
                            </a>
                        @endforeach --}}


                    </div>

                </li>

                <li class="nav-item ">
                    <a href="" class="nav-link" title="Imprimir"> <i class="fas fa-print"></i> </a>
                </li>


            </ul>


            <div class="tab-content" id="myTabContent">

                @php
                    $tags = array('<span style="text-decoration: line-through;">');
                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Sao_Paulo');
                @endphp

                <!-- COMPILADO -->
                <div class="tab-pane fade" id="compilado" role="tabpanel" aria-labelledby="compilado-tab">


                    <div class="card mt-2">

                        <div class="card-body">


                            <ul>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="">PDF</a>
                                        <a class="dropdown-item" href="">HTML</a>
                                        <a class="dropdown-item" href="">TXT</a>
                                        <a class="dropdown-item" href="">DOC</a>
                                    </div>
                                </li>
                            </ul>
                            @php echo strftime('%A, %d de %B de %Y', strtotime($ato->created_at)); @endphp
                            {{-- @foreach($arquivos as $arquivo)
                                @php echo str_replace($tags, "", $arquivo->corpo)  ; @endphp
                            @endforeach --}}

                        </div>
                    </div>

                </div>
                <!-- COMPILADO -->


                <!-- CONSOLIDADA -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="consolidada">


                    <ul>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="">PDF</a>
                                <a class="dropdown-item" href="">HTML</a>
                                <a class="dropdown-item" href="">TXT</a>
                                <a class="dropdown-item" href="">DOC</a>
                            </div>
                        </li>
                    </ul>


                    <div class="card mt-2">
                        <div class="card-body">
                            @php echo strftime('%A, %d de %B de %Y', strtotime($ato->created_at)); @endphp
                            <br>
                            {{-- @foreach($arquivos as $arquivo)
                                <a href="{{ url('arquivo/verArquivo', $arquivo->id) }}"
                                   title="Ver arquivo"> @php echo $arquivo->titulo ; @endphp</a>
                                @php echo  $arquivo->corpo ; @endphp
                            @endforeach --}}
                        </div>
                    </div>
                </div>
                <!-- CONSOLIDADA -->


                <!-- COMPILADA -->
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                    <ul>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="">PDF</a>
                                <a class="dropdown-item" href="">HTML</a>
                                <a class="dropdown-item" href="">TXT</a>
                                <a class="dropdown-item" href="">DOC</a>
                            </div>
                        </li>
                    </ul>
                    <div class="card mt-2">
                        <div class="card-body">

                            @php echo strftime('%A, %d de %B de %Y', strtotime($ato->created_at)); @endphp
                            <br>
                            {{-- @foreach($arquivos as $arquivo)
                                <a href="{{ url('arquivo/verArquivo', $arquivo->id) }}"
                                   title="Ver arquivo"> @php echo $arquivo->titulo ; @endphp</a>
                                @php echo str_replace($tags, "", $arquivo->corpo)  ; @endphp
                            @endforeach --}}

                        </div>
                    </div>
                </div>
                <!-- COMPILADA -->


            </div>

        </div>
    </div>

</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{asset('js/jquery.validate.js')}}"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>

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
        rules : {
            titulo:{
                required:true
            },
            ano:{
                required:true
            },
            numero:{
                required:true
            },
            id_grupo:{
                required:true
            },
            id_tipo_ato:{
                required:true
            },
            subtitulo:{
                required:true
            },
            corpo_texto:{
                required:true
            }
        },
        messages:{
            titulo:{
                required:"Campo obrigatório"
            },
            ano:{
                required:"Campo obrigatório"
            },
            numero:{
                required:"Campo obrigatório"
            },
            id_grupo:{
                required:"Campo obrigatório"
            },
            id_tipo_ato:{
                required:"Campo obrigatório"
            },
            subtitulo:{
                required:"Campo obrigatório"
            },
            corpo_texto:{
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

    });

</script>

@endsection
