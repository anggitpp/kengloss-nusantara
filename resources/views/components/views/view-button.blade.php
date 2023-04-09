@props(['url'])
@if($url != '')
    <a class="btn btn-icon btn-light-dark w-30px h-30px me-1 btn-modal-iframe" data-url="{{ $url }}" data-bs-toggle="modal">
        <i class="fa-solid fa-download"></i>
    </a>
@else
    &nbsp;
@endif
