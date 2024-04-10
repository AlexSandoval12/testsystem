@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <div>{!! ucfirst($error) !!}</div>
        @endforeach
    </div>
@endif
@if(session('success'))
	<div class="alert alert-success">
		{!! session('success') !!}
	</div>
@endif
