<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>

        .titulo {
            text-align: center;
        }
    </style>

    <title>Documento</title>
</head>
<body>
<h2 class="titulo"> Câmara Municipal de XXXXXX</h2>

@php
    $tags = array('<span style="text-decoration: line-through;">');
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Campo_Grande');
@endphp

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


</body>
</html>
