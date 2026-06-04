<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">

            {{-- USER --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("User")->class("control-label") !!}
                    {!! html()->p(optional($data->user())->name ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- AKSI --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("Aksi")->class("control-label") !!}
                    {!! html()->p($data->data['action'] ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->span("Keterangan")->class("control-label") !!}
                    {!! html()->p($data->data['description'] ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- URL --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("URL")->class("control-label") !!}
                    {!! html()->p($data->data['url'] ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- METHOD --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("Method")->class("control-label") !!}
                    {!! html()->p($data->data['method'] ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- IP --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("IP Address")->class("control-label") !!}
                    {!! html()->p($data->ip ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- BROWSER --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("Browser")->class("control-label") !!}
                    {!! html()->p($data->data['browser'] ?? '-')->class("form-control") !!}
                </div>
            </div>

            {{-- WAKTU --}}
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span("Waktu")->class("control-label") !!}
                    {!! html()->p($data->created_at->format('d-m-Y H:i:s'))->class("form-control") !!}
                </div>
            </div>

        </div>

        {{-- ================= BEFORE ================= --}}
        @if(!empty($data->data['before']))
        <hr>
        <h5><i class="fa fa-database text-danger"></i> Data Sebelum</h5>
        <div class="bg-light p-2 rounded">
            <pre>{!! json_encode($data->data['before'], JSON_PRETTY_PRINT) !!}</pre>
        </div>
        @endif

        {{-- ================= AFTER ================= --}}
        @if(!empty($data->data['after']))
        <hr>
        <h5><i class="fa fa-database text-success"></i> Data Sesudah</h5>
        <div class="bg-light p-2 rounded">
            <pre>{!! json_encode($data->data['after'], JSON_PRETTY_PRINT) !!}</pre>
        </div>
        @endif

    </div>
</div>

<style>
    .modal-lg {
        max-width: 1000px !important;
    }
    pre {
        font-size: 12px;
        max-height: 300px;
        overflow: auto;
    }
</style>

<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-search"></i> Detail Log {!! $page->title !!}');
</script>