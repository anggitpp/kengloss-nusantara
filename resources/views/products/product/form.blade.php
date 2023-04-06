@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($product) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($product))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($product) ? __('Tambah Data Produk') : __('Edit Data Produk') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-center">
                    <x-form.image-input name="photo" label="Foto" value="{{ $product->photo ?? '' }}" />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nama" name="name" value="{{ $product->name ?? '' }}" required/>
                        <x-form.datepicker label="Tanggal Produksi" class="w-50" name="production_date" value="{{ $product->production_date ?? '' }}" required/>
                        <x-form.input label="Isi (Liter)" name="volume" class="w-50 text-end" value="{{ $product->volume ?? '' }}"/>
                        <x-form.textarea name="composition" label="Komposisi" value="{{ $product->composition ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Batch Number" name="number" value="{{ $product->number ?? '' }}" required/>
                        <x-form.datepicker label="Tanggal Expired" class="w-25" name="expired_date" value="{{ $product->expired_date ?? '' }}" required/>
                        <x-form.input label="Stok Barang" name="stock" class="w-50 text-end" numeric value="{{ $product->stock ?? '' }}"/>
                        <x-form.textarea name="description" label="Deskripsi" value="{{ $product->description ?? '' }}" />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
