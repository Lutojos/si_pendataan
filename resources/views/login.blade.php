<x-layout.auth-layout title="Login Password">
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @php echo implode('<br>', $errors->all()) @endphp
    </div>
    @endif
    <p class="login-box-msg">Masuk untuk memulai sesi anda</p>
    <form id="frmLogin" action="{{ route('auth.login') }}" method="post">
        @csrf
        <div class="input-group mb-3">
            <input type="text" id="email" name="email" class="form-control" value="{{ old('email', 'adminsambina@mail.com') }}" placeholder="Email">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="password" id="Password" name="password" class="form-control" value="admin123" placeholder="Password" autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <p align="right" style="margin-top:-10px">
            <a style="color:#007bff;text-decoration:none;background-color:transparent;font-size:14px;
        " href="{{ route('reset.password.form') }}">Lupa Password?</a>
        </p>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Login</button>

        </div>

    </form>
</x-layout.auth-layout>