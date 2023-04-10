<div class="card">
    <form>
        <x-form.modal-header title="Detail Produk" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="d-flex justify-content-center">
                <x-form.image-input name="photo" label="Foto" value="{{ $product->photo ?? '' }}" is-editable="f" />
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Master Kategori" name="name" value="{{ $product->category->name ?? '' }}" readonly/>
                    <x-form.input label="Nama" name="name" value="{{ $product->name ?? '' }}" readonly/>
                    <x-form.input label="Tanggal Produksi" name="production_date" value="{{ $product->production_date ?? '' }}" readonly/>
                    <x-form.input label="Isi (Liter)" name="volume" class="text-end" numeric value="{{ $product->volume ?? '' }}" readonly/>
                    <x-form.textarea name="composition" label="Komposisi" value="{{ $product->composition ?? '' }}" readonly />
                </div>
                <div class="col-md-6">
                    <x-form.input label="Master File" name="name" value="{{ $product->file->name ?? '' }}" readonly/>
                    <x-form.input label="Batch Number" name="number" value="{{ $product->number ?? '' }}" readonly/>
                    <x-form.input label="Tanggal Expired" name="expired_date" value="{{ $product->expired_date ?? '' }}" readonly/>
                    <x-form.input label="Stok Barang" name="stock" class="text-end" numeric value="{{ $product->stock ?? '' }}" readonly/>
                    <x-form.textarea name="description" label="Deskripsi" value="{{ $product->description ?? '' }}" readonly />
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <img src="{{ 'storage'.$product->qr ?? '' }}" class="img-fluid" alt="QR Code" width="200" height="200" />
            </div>
        </div>
    </form>
</div>
