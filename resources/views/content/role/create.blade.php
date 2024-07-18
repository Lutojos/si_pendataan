<x-layout.app-admin title="{!! __('Create Role') !!}">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{!! __('Create Role') !!}</h3>
        </div>
        <div class="card-body">
            @include('components.layout.message-admin')
            <form action="{{ route('role.store') }}" method="POST" id="form-create-branch">
                @csrf
                <div class="form-group">
                    <label for="inputRoleName">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="inputRoleName" class="form-control">
                </div>
                <a href="{{ route('role.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-create">Save</button>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    </script>
    @endpush
</x-layout.app-admin>
