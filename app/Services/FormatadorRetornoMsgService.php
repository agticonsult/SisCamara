<?php
namespace App\Services;

use App\Models\AnexoChat;
use App\Models\Chat;
use App\Models\FotoPerfil;
use App\Models\Mensagem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FormatadorRetornoMsgService {

    private $id_chat;
    private $mensagem;
    private $anexo;

    function __construct($id_chat, Mensagem $mensagem, AnexoChat $anexo = null)
    {
        $this->id_chat = $id_chat;
        $this->mensagem = $mensagem;
        $this->anexo = $anexo;
    }

    public function formatar() {

        if ($this->anexo == null) {
            $an = null;
            $ehAudio = 0;
            $src = null;
        }else {
            $an = [
                'id' => $this->anexo->id,
                'nome_original' => $this->anexo->nome_original,
                'nome_hash' => $this->anexo->nome_hash,
                'link' => route('mensagem.anexo_chat.get', $this->anexo->id),
            ];

            if ($this->anexo->ehAudio == 1) {
                $ehAudio = 1;

                $path = storage_path('app/public/anexos-chat/' . $this->anexo->nome_hash);
                if (file_exists($path)){
                    $base64 = base64_encode(file_get_contents($path));
                    $src = 'data:audio/mp3;;base64,' . $base64;
                }else{
                    $src = null;
                }
            }else {
                $ehAudio = 0;
                $src = null;
            }
        }

        $p_especificos = [];
        $p_especificos_all = [];

        $chat = Chat::find($this->id_chat);

        if ($chat->participantes_especificos != null) {
            foreach ($chat->participantes_especificos as $pe) {
                if ($pe->id_mensagem == $this->mensagem->id) {
                    array_push($p_especificos, $pe->user->pessoa->nome);
                    array_push($p_especificos_all, $pe);
                }
            }
        }

        $tem = 0;
        $foto = null;
        $temFoto = FotoPerfil::where('id_user', '=', $this->mensagem->enviadoPorUsuario)->where('ativo', '=', 1)->first();
        if ($temFoto){
            $existe = Storage::disk('public')->exists('foto-perfil/'.$temFoto->nome_hash);
            if ($existe){
                $pathFoto = storage_path('app/public/foto-perfil/' . $temFoto->nome_hash);
                if (File::exists($pathFoto)){
                    $base64 = base64_encode(file_get_contents($pathFoto));
                    $srcFoto = 'data:image/png;base64,' . $base64;
                    $tem = 1;
                    $foto = $srcFoto;
                }
            }
        }

        $msg = [
            'id' => $this->mensagem->id,
            'texto' => $this->mensagem->texto,
            'dm' => $this->mensagem->created_at->format('d/m'),
            'hia' => $this->mensagem->created_at->format('H:i a'),
            'enviadoPorId' => $this->mensagem->enviadoPorUsuario,
            'enviadoPor' => $this->mensagem->enviadoPor->pessoa->nome,
            'envioEspecifico' => $this->mensagem->envioEspecifico,
            'p_especificos' => $p_especificos,
            'p_especificos_all' => $p_especificos_all,
        ];

        return [
            'id_chat' => $this->id_chat,
            'mensagem' => $msg,
            'anexo' => $an,
            'ehAudio' => $ehAudio,
            'src' => $src,
            'temFoto' => $tem,
            'foto' => $foto
        ];
    }

}
