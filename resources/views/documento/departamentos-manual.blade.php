<div class="card-body">
    <div class="col-md-12">
        <strong><h4>Departamento(s)</h4></strong>
        <ul style="list-style: none; color: black">
            @foreach ($departamentos as $dep)
                @if ($documentoEdit->dep_atual() != null &&
                    $dep->id_departamento == $documentoEdit->dep_atual()->id_departamento)
                    <li><strong>{{ $dep->ordem != null ? $dep->ordem.'. ' : '' }}</strong>{{ $dep->departamento->descricao }} -> <strong>atual</strong></li>
                @else
                    <li><strong>{{ $dep->ordem != null ? $dep->ordem.'. ' : '' }}</strong>{{ $dep->departamento->descricao }}</li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
