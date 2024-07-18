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
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('reset.password.forget') }}" method="POST">
        @csrf

        <div class="input-group mb-3">
            <input type="text" id="email" class="form-control" name="email" value="{{ old('email') }}"
                placeholder="Masukan Email Anda" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
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
