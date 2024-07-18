<x-layout.auth-layout title="Forget Password">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            @php echo implode('<br>', $errors->all()) @endphp
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('error') }}
        </div>
    @endif
    @if (session('message'))
        <div class="alert alert-success">
            {!! session('message') !!}
        </div>
    @endif
    <form action="{{ route('reset.password.post') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-group mb-3">
            <input type="text" id="email_address" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="password" id="password" class="form-control" name="password" placeholder="Password" required
                autofocus autocomplete="off">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required
                autofocus autocomplete="off" placeholder="Confirm Password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">
                Reset Password
            </button>

        </div>
        <div class="form-group" align="center">
            <a href="{{ route('auth.form') }}" style="color:#007bff;text-decoration:none;background-color:transparent;font-size:14px;">Kembali halaman login</a>
        </div>
    </form>
</x-layout.auth-layout>
