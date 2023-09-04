@extends('layout.main-publico')

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
                        @if (Count($ato->atos_relacionados_ativos()) != 0)
                            @foreach ($ato->atos_relacionados_ativos() as $ar)
                                <a class="dropdown-item" href="{{ route('web_publica.ato.show', $ar->id_ato_relacionado) }}"
                                    target="_blank">
                                    {{ $ar->ato_relacionado->titulo }}
                                </a>
                            @endforeach
                        @else
                            Não possui atos relacionados
                        @endif
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false"> <i class="fas fa-paperclip"></i> Anexos</a>
                    <div class="dropdown-menu">
                        @if (Count($ato->anexos_ativos()) != 0)
                            @foreach($ato->anexos_ativos() as $anexo)
                                <a class="dropdown-item" href="{{url('storage/Atos/Anexos/'.$anexo->nome_hash)}}"
                                target="_blank">
                                    {{ $anexo->nome_original }}
                                </a>
                            @endforeach
                        @else
                            Sem anexo cadastrado
                        @endif
                    </div>
                </li>
                <li class="nav-item ">
                    <a href="" class="nav-link" title="Imprimir"><i class="fas fa-print"></i></a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">

                @php
                    $tags = array('<span style="text-decoration: line-through;">');
                    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
                    date_default_timezone_set('America/Campo_Grande');
                @endphp

                <div class="tab-pane fade show active" id="original" role="tabpanel" aria-labelledby="original-tab">
                    <div class="card mt-2">
                        <div class="card-body">
                            <ul>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('ato.export.original.pdf', $ato->id) }}">PDF</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.original.html', $ato->id) }}">HTML</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.original.texto', $ato->id) }}">TXT</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.original.doc', $ato->id) }}">DOC</a>
                                    </div>
                                </li>
                            </ul>
                            {{ $ato->id_tipo_ato != null ? $ato->tipo_ato->descricao : 'Tipo de ato não informado' }}
                            Nº {{ $ato->numero != null ? $ato->numero : 'não informado' }},
                            de {{ strftime('%d de %B de %Y', strtotime($ato->created_at)) }} <br><br>
                            <p>{{ $ato->titulo }}</p>
                            @if (Count($ato->linhas_originais_ativas()) != 0)
                                @foreach($ato->linhas_originais_ativas() as $linha_original_ativa)
                                    <p>{{ $linha_original_ativa->texto }}</p>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="consolidada" role="tabpanel" aria-labelledby="consolidada-tab">
                    <div class="card mt-2">
                        <div class="card-body">
                            <ul>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('ato.export.consolidada.pdf', $ato->id) }}">PDF</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.consolidada.html', $ato->id) }}">HTML</a>
                                        {{-- <a class="dropdown-item" href="{{ route('ato.export.consolidada.texto', $ato->id) }}">TXT</a> --}}
                                        <a class="dropdown-item" href="{{ route('ato.export.consolidada.doc', $ato->id) }}">DOC</a>
                                    </div>
                                </li>
                            </ul>
                            {{ $ato->id_tipo_ato != null ? $ato->tipo_ato->descricao : 'Tipo de ato não informado' }}
                            Nº {{ $ato->numero != null ? $ato->numero : 'não informado' }},
                            de {{ strftime('%d de %B de %Y', strtotime($ato->created_at)) }} <br><br>
                            <p>{{ $ato->titulo }}</p>
                            @foreach($ato->todas_linhas_ativas() as $linha_ativa)
                                @if ($linha_ativa->alterado == 1)
                                    <p style="text-decoration: line-through">{{ $linha_ativa->texto }}</p>
                                @else
                                    <p>
                                        {{ $linha_ativa->texto }}
                                        @if ($linha_ativa->id_tipo_linha == 2)
                                            <a href="{{ route('ato.show', $linha_ativa->id_ato_add) }}">
                                                (Redação dada pela(o)
                                                {{ $linha_ativa->ato_add->id_tipo_ato != null ? $linha_ativa->ato_add->tipo_ato->descricao : 'Tipo de ato não informado' }}
                                                Nº {{ $linha_ativa->ato_add->numero != null ? $linha_ativa->ato_add->numero : 'não informado' }},
                                                de {{ strftime('%Y', strtotime($linha_ativa->ato_add->created_at)) }})
                                            </a>
                                        @endif
                                    </p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="compilado" role="tabpanel" aria-labelledby="compilado-tab">
                    <div class="card mt-2">
                        <div class="card-body">
                            <ul>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exportar</a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('ato.export.compilada.pdf', $ato->id) }}">PDF</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.compilada.html', $ato->id) }}">HTML</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.compilada.texto', $ato->id) }}">TXT</a>
                                        <a class="dropdown-item" href="{{ route('ato.export.compilada.doc', $ato->id) }}">DOC</a>
                                    </div>
                                </li>
                            </ul>
                            {{ $ato->id_tipo_ato != null ? $ato->tipo_ato->descricao : 'Tipo de ato não informado' }}
                            Nº {{ $ato->numero != null ? $ato->numero : 'não informado' }},
                            de {{ strftime('%d de %B de %Y', strtotime($ato->created_at)) }} <br><br>
                            <p>{{ $ato->titulo }}</p>
                            @foreach($ato->linhas_inalteradas_ativas() as $linha_inalterada_ativa)
                                <p>
                                    {{ $linha_inalterada_ativa->texto }}
                                    @if ($linha_inalterada_ativa->id_tipo_linha == 2)
                                        <a href="{{ route('ato.show', $linha_inalterada_ativa->id_ato_add) }}">
                                            (Redação dada pela(o)
                                            {{ $linha_inalterada_ativa->ato_add->id_tipo_ato != null ? $linha_inalterada_ativa->ato_add->tipo_ato->descricao : 'Tipo de ato não informado' }}
                                            Nº {{ $linha_inalterada_ativa->ato_add->numero != null ? $linha_inalterada_ativa->ato_add->numero : 'não informado' }},
                                            de {{ strftime('%Y', strtotime($linha_inalterada_ativa->ato_add->created_at)) }})
                                        </a>
                                    @endif
                                </p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{asset('js/popper.min.js')}}"></script>
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
