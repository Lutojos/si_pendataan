<x-layout.app-admin title="{!! __('Edit Provinsi') !!}">
    <div class="row" style="margin-left: 8px; margin-right: 8px;">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('provinsi.update', ['token' => $datas->_token]) }}" method="POST" id="form-edit"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        @include('components.layout.message-admin')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Provinsi<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="provinsi_name" id="provinsi_name"
                                                class="form-control"
                                                value="{{ old('provinsi_name', $datas->provinsi_name) }}"
                                                placeholder="Nama Provinsi">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right"
                                        id="btn_save">Simpan</button>
                                    <a href="{{ route('provinsi.index') }}" type="button"
                                        class="btn btn-default float-right mr-2">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.card-body -->
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            $("#form-edit").submit(function(e) {
                e.preventDefault();
                var form = document.getElementById("form-edit");

                let postData = new FormData();
                $.each($('#form-edit :input').serializeObject(), function(x, y) {
                    postData.append(x, y);
                });

                processingform(form);
                ajax({
                    url: $("#form-edit").attr("action"),
                    postData: postData,
                    processData: false,
                    contentType: false,
                    alert: false,
                    success: function(response) {

                        alert(response.msg);
                        unprocessingform(form);
                        window.location.href = "{{ route('provinsi.index') }}";

                    },
                    error: function(err) {
                        unprocessingform(form);
                        loadAlert(err.msg, true);
                    },
                    failure: function(err) {
                        unprocessingform(form);
                        loadAlert(err.msg, true);
                    }
                });

            });
        </script>
    @endpush
</x-layout.app-admin>
