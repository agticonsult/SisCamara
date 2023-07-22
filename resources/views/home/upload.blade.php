@extends('layout.main')

@section('content')


@include('errors.alerts')
@include('errors.errors')

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="row">
                <div class="form-group col-md-6">
                    <input type="file" name="documento" id="documento">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <button type="submit" class="btn btn-primary m-1">Cadastrar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
