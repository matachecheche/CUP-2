@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swal !== 'undefined') {
        Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false,
            timer:2800, timerProgressBar:true })
            .fire({ icon:'success', title:@json(session('success')) });
    }
});
</script>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-times-circle"></i>
    <div><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
</div>
@endif
