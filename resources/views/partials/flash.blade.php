@if (session('success'))
    <div class="alert alert--success" role="status" data-dismissible>{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert--error" role="alert" data-dismissible>{{ session('error') }}</div>
@endif

@if ($errors->has('ids'))
    <div class="alert alert--error" role="alert" data-dismissible>{{ $errors->first('ids') }}</div>
@endif
