<form action="{{ route('home.updatePj', $user->id) }}" id="form" method="POST" class="form_prevent_multiple_submits">
    @csrf
    @method('POST')
    <div class="row">
        <div class="col-md-6">
            <h5>Dados Pessoais</h5>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="form-label">*Nome</label>
                    <input class="form-control @error('nome') is-invalid @enderror" type="text" name="nome" id="nome" placeholder="Informe seu nome" value="{{ $user->pessoa->nome != null ? $user->pessoa->nome : old('nome') }}">
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">Nome Fantasia</label>
                    <input class="form-control @error('apelidoFantasia') is-invalid @enderror" type="text" name="apelidoFantasia" id="apelidoFantasia" placeholder="Nome fantasia" value="{{ $user->pessoa->apelidoFantasia != null ? $user->pessoa->apelidoFantasia : old('apelidoFantasia') }}">
                    @error('apelidoFantasia')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">*CNPJ</label>
                    <input class="cnpj form-control @error('cnpj') is-invalid @enderror" type="text" name="cnpj" id="cnpj" placeholder="Informe seu CNPJ" value="{{ $user->cnpj != null ? $user->cnpj: old('cnpj') }}">
                    @error('cnpj')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">*Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" placeholder="Informe um email válido" value="{{ $user->email != null ? $user->email : old('email') }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">Celular/Telefone</label>
                    <input class="telefone form-control @error('telefone_celular') is-invalid @enderror" type="text"  name="telefone_celular" value="{{ $user->telefone_celular != null ? $user->telefone_celular : old('telefone_celular') }}">
                    @error('telefone_celular')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">Celular/Telefone Recado</label>
                    <input class="telefone form-control @error('telefone_celular2') is-invalid @enderror" type="text" name="telefone_celular2" value="{{ $user->telefone_celular2 != null ? $user->telefone_celular2 : old('telefone_celular2') }}">
                    @error('telefone_celular2')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <h5>Endereço</h5>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="cep">CEP</label>
                    <input type="text" name="cep" id="cep" class="form-control @error('cep') is-invalid @enderror" placeholder="Informe o CEP" value="{{ $user->pessoa->cep != null ? $user->pessoa->cep : old('cep') }}">
                    @error('cep')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="endereco">Endereço (Rua/Avenida)</label>
                    <input type="text" name="endereco" id="endereco" class="form-control @error('endereco') is-invalid @enderror" placeholder="Informe o endereço" value="{{ $user->pessoa->endereco != null ? $user->pessoa->endereco : old('endereco') }}">
                    @error('endereco')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="numero">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control @error('numero') is-invalid @enderror" placeholder="Informe o número" value="{{ $user->pessoa->numero != null ? $user->pessoa->numero : old('numero') }}">
                    @error('numero')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="bairro">Bairro / Comunidade</label>
                    <input type="text" name="bairro" id="bairro" class="form-control @error('bairro') is-invalid @enderror" placeholder="Informe o bairro" value="{{ $user->pessoa->bairro != null ? $user->pessoa->bairro : old('bairro') }}">
                    @error('bairro')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="complemento">Complemento</label>
                    <input type="text" name="complemento" id="complemento" class="form-control @error('complemento') is-invalid @enderror" placeholder="Informe o complemento" value="{{ $user->pessoa->complemento != null ? $user->pessoa->complemento : old('complemento') }}">
                    @error('complemento')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-12">
                    <label for="ponto_referencia">Ponto de Referência</label>
                    <input type="text" name="ponto_referencia" class="form-control @error('ponto_referencia') is-invalid @enderror" placeholder="Informe o ponto de referência" value="{{ $user->pessoa->ponto_referencia != null ? $user->pessoa->ponto_referencia : old('ponto_referencia') }}">
                    @error('ponto_referencia')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h5>Troca de senha</h5>
            <ul>
                <li>
                    Mínimo 6 caracteres e máximo 35 caracteres
                </li>
            </ul>
            <div class="row">
                <div class="form-group col-md-4">
                    <label class="form-label">Senha antiga</label>
                    <input class="form-control" type="password" name="senha_antiga" placeholder="Informe a senha antiga" value="{{ old('senha_antiga') }}">
                    {{-- @error('senha_antiga')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror --}}
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">Senha</label>
                    <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Nova senha" value="{{ old('password') }}">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">Confirme a senha</label>
                    <input class="form-control @error('confirmacao') is-invalid @enderror" type="password" name="confirmacao" placeholder="Confirme nova senha" value="{{ old('confirmacao') }}">
                    @error('confirmacao')
                        <div class="invalid-feedback">{{ $message }}</div><br>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12">
        <button type="submit" class="button_submit btn btn-primary" style="width: 20%">Salvar</button>
    </div>
    <br>
</form>
