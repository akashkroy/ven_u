<div id="main-navigation-3" class="center-align">
	<div class="btn-group" role="group" aria-label="">
	@foreach($items as $item)
		<a href="{{ $item['url'] }}" class="btn btn-default @if(Request::url() == $item['url']) active @endif">
		@if(Request::url() == $item['url'])
			<img src="{{ $item['image-active'] }}"/>
		@else
			<img src="{{ $item['image'] }}"/>
		@endif
			{{ $item['text'] }}
		</a>
	@endforeach
	</div>
</div>	
