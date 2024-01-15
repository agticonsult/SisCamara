@extends('layout.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.4/select2-bootstrap.min.css" integrity="sha512-eNfdYTp1nlHTSXvQD4vfpGnJdEibiBbCmaXHQyizI93wUnbCZTlrs1bUhD7pVnFtKRChncH5lpodpXrLpEdPfQ==" crossorigin="anonymous" />
<style>
    .error{
        color:red
    }
</style>
@include('errors.alerts')
{{-- @include('errors.errors') --}}

<h1 class="h3 mb-3">Alteração do Assunto</h1>
<div class="card" style="background-color:white">

    <div class="card-body">
        <div class="col-md-12">
            <form action="{{ route('configuracao.assunto_ato.update', $assunto->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
                @csrf
                @method('POST')

                <div class="col-md-12">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-label">*Nome</label>
                            <input class="form-control @error('descricao') is-invalid @enderror" type="text" name="descricao" id="descricao" value="{{ $assunto->descricao != null ? $assunto->descricao : old('descricao') }}">
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div><br>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="button_submit btn btn-primary">Salvar</button>
                    <a href="{{ route('configuracao.assunto_ato.index') }}" class="btn btn-light">Voltar</a>
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

@endsection
