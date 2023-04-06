@props(['photo'])
<div class="symbol symbol-50px symbol-md-50px me-6 rounded-2">
    <img src="{{ !empty($photo) ? asset('storage'.$photo) : asset('assets/media/blank-image.svg') }}" alt="photo" />
</div>