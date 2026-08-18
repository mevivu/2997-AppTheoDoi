<input type="file" 
    class="form-control" 
    name="{{ $name }}" 
    accept="image/*"
    onchange="previewImage_{{ str_replace('-', '_', $showImage) }}(event)">
<div class="mt-2 text-center">
    <img id="{{ $showImage }}" 
        src="{{ $value ? asset($value) : asset(config('custom.images.avatar', '/public/assets/images/avatar-user.png')) }}" 
        style="width: 100%; max-width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 3px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
</div>
<script>
function previewImage_{{ str_replace('-', '_', $showImage) }}(event) {
    const output = document.getElementById('{{ $showImage }}');
    if (event.target.files && event.target.files[0]) {
        output.src = URL.createObjectURL(event.target.files[0]);
    }
}
</script>
