<x-layout.app-admin title="{!! __('Edit Role') !!}">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Role</h3>
        </div>
        <div class="card-body">
            @include('components.layout.message-admin')
            <form action="{{ route('role.update', ['token' => $role->_token]) }}" method="POST" id="form-edit-role">
                @csrf
                <div class="form-group">
                    <label for="inputRoleName">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="inputRoleName" class="form-control" value="{{ old('name', $role->name) }}">
                </div>
                <a href="{{ route('role.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-edit">Save</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    </script>
    @endpush
</x-layout.app-admin>
