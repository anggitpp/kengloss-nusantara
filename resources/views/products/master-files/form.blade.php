<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($master) ? route('products.master-files.store') : route('products.master-files.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($master) ? __('Tambah Master') : __('Edit Master') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Kode" class="text-uppercase" placeholder="Masukkan Kode" name="code" value="{{ $master->code ?? '' }}" required />
                </div>
                <div class="col-md-6">
                    <x-form.input label="Judul" placeholder="Masukkan Judul" name="name" value="{{ $master->name ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $master->description ?? '' }}" />
            <x-form.file label="File" name="file" value="{{ $master->file ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $master->status ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
