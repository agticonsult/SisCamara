@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="alert-message">
        <strong>Sucesso!</strong> {{ session('success') }}
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('erro'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="alert-message">
        <strong>Erro!</strong> {{ session('erro') }}
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('danger'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="alert-message">
            <strong>Atenção!</strong> {{ session('danger') }}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="alert-message">
            <strong>Atenção!</strong> {{ session('warning') }}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="alert-message">
            {{ session('info') }}
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('info-grupo-import'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="alert-message">
            <ul>
                @foreach (session('info-grupo-import') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


@if (session('info-anexo'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="alert-message">
            <ul>
                @foreach (session('info-anexo') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- @if (session('arquivo-falha'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="alert-message">
            <ul>
                @foreach (session('arquivo-falha') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif --}}
