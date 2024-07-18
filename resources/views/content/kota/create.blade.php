<x-layout.app-admin title="{!! __('Tambah Kota') !!}">
    <div class="row" style="margin-left: 8px; margin-right: 8px;">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <form action="{{ route('kota.store') }}" method="POST" id="form-input" enctype="multipart/form-data">
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
                                            <select class="form-control select2-provinsi" name="provinsi_id"
                                                id="provinsi_id">
                                                <option value="">Pilih Provinsi</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="task_category">Nama Kota<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="kota_name" id="kota_name" class="form-control"
                                                value="{{ old('kota_name') }}" placeholder="Nama Kota">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right"
                                        id="btn_save">Simpan</button>
                                    <a href="{{ route('kota.index') }}" type="button"
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
            $("#form-input").submit(function(e) {
                e.preventDefault();
                var form = document.getElementById("form-input");

                let postData = new FormData();
                $.each($('#form-input :input').serializeObject(), function(x, y) {
                    postData.append(x, y);
                });

                processingform(form);
                ajax({
                    url: $("#form-input").attr("action"),
                    postData: postData,
                    processData: false,
                    contentType: false,
                    alert: false,
                    success: function(response) {

                        alert(response.msg);
                        unprocessingform(form);
                        window.location.href = "{{ route('kota.index') }}";

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

            $(document).ready(function() {
                $('.select2-provinsi').select2({
                    minimumInputLength: 0,
                    allowClear: true,
                    placeholder: 'Masukkan nama provinsi',
                    theme: 'bootstrap4',
                    ajax: {
                        dataType: 'json',
                        url: "{{ route('provinsi.option') }}",
                        delay: 800,
                        data: function(params) {
                            return {
                                search: params.term
                            }
                        },
                        processResults: function(data, page) {
                            return {
                                results: data
                            };
                        },
                    }
                }).on('select2-provinsi:select', function(evt) {
                    var data = $(".select2-provinsi option:selected").text();

                }).on('select2-provinsi:clear', function(evt) {

                });
            });
        </script>
    @endpush
</x-layout.app-admin>
