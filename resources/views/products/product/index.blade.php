@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex">
                        <x-views.search />
                        <x-form.select name="combo_1" :datas="$categories" value="{{ $category_id ?? '' }}" class="w-200px" option="- Semua Kategori -" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        @can('add '.$menu_path)
                            <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah Data" />
                        @endcan
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-20px">No</th>
                            <th class="min-w-50px">Photo</th>
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-150px">Kategori</th>
                            <th class="min-w-150px">Batch Number</th>
                            <th class="min-w-100px">Isi (Liter)</th>
                            <th class="min-w-50px">Stok</th>
                            <th class="min-w-150px">Tanggal Produksi</th>
                            <th class="min-w-150px">Tanggal Expired</th>
                            <th class="min-w-150px">File</th>
                            <th class="min-w-50px">QR</th>
                            <th class="min-w-150px text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.data');
                    $datas = array("photo","name", "category_id", "number", "volume", "stock", "production_date", "expired_date", "file_id", "qr","action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
