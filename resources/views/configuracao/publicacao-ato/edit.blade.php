@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- script referente ao mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14="
crossorigin=""/>
<script src='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js'></script>
<link href='https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.css' rel='stylesheet' />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
    #map{
        background: #fff;
        border: none;
        height: 540px;
        width: 100%;

        box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
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
            <strong>Alteração da Publicação</strong>
        </h2>
    </div>

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('configuracao.publicacao_ato.update', $publicacao->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Nome</label>
                            <input class="form-control" type="text" name="descricao" id="descricao" value="{{ $publicacao->descricao != null ? $publicacao->descricao : old('descricao') }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                            <a href="{{ URL::previous() }}" class="btn btn-light">Voltar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js" integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
<script src="{{asset('jquery-mask/src/jquery.mask.js')}}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>

<script>
    $("#form").validate({
            rules: {
                descricao: {
                    required: true,
                    maxlength: 255,
                }
            },
            messages: {
                descricao: {
                    required: "Campo obrigatório",
                    maxlength: "Máximo de 255 caracteres"
                }
            }
        });
</script>

@endsection
