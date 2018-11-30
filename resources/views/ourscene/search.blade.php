@extends('ourscene/layouts.main')

@section('head')
<style>
#my-events-events > div > div.card-action.title > div > i{
	color: #534d93 !important;
}
#test:after{
	visibility: hidden;
	display: block;
	font-size: 0;
	content: " ";
	clear: both;
	height: 0;
}
</style>
@endsection

@section('content')
<div id="search-results" class="container-fluid">

		<!-- Search form -->

		<div id="search-form" class="card">
			
			<!-- Search input -->
			
			<div class="input-field col s12 m12 l12">
				<i class="material-icons prefix" style="top:.3em;">search</i>
				<input id="search-results-search-input" type="text" class="validate" >
				<label for="search">Search</label>
    		</div>

			<!-- Search dropdown -->

			<ul id="search-results-search-dropdown" class="dropdown-content search-dropdown"></ul>
		</div>
			
		<!-- Search results items -->
	
		<div id="search-results-item" class="card">
			<div class="card-action title">
				<img src="{{ asset('images/icons/pending.svg') }}"/>
				@if (count($all) < 1)
					No
				@endif

				@if($singleParam == "")
					Results
				@else
					Results for "{{$singleParam}}"
				@endif
			</div>
			<div class="card-action content">
				@if (count($all) > 0)
				<table>
					<tbody>
						@if ((isset($all)))
							@foreach($all as $result)
							<tr><td>
								<div class="row search-table-row">
									<div class="col s2 m1 l1 search-table-row-element">
										<div class="circular-img-container profile-pic" style="background-image: url('{{ getProfilePicture($result['_id']) }}')"></div>
									</div>
									<div class="col s8 m9 l9 search-table-row-element">
										<div class="row">
											<div class="col s12 m12 l12">
												<a href="{{ action('UserController@getPublicProfile', array('id' => $result['_id'])) }}" class="profile-link search_user_name" >{{ $result['name'] }}</a>
											</div>
											<div class="col s12 m12 l12">
												<span class="user_type_label">@if($result['user_type'] == 'artist') Artist @else Venue @endif</span>
											</div>
										</div>
									</div>
									<div class="col s2 m2 l2 action right-align search-table-row-element" style="height: 40px;">
										<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $result['_id'])) }}" class="btn ourscene-btn-1 l-display-only">
											Message
										</a>
										<a href="{{ action('MessageController@getMessageConversationWithUser', array('id' => $result['_id'])) }}" class="message-btn l-no-display">
											<img src="{{ asset('images/icons/message-purple.svg') }}" class="table-icon center-element-vertically">
										</a>
									</div>
								</div>
							</td></tr>
							@endforeach
						@endif
					</tbody>
				</table>
				@endif
			</div>
		</div>
		
		@if ($isSingleParam)
			{!! $all->appends(['params' => $singleParam])->render() !!}
		@else
			{!! $all->appends(['name' => $name,'genre' => $genre, 'locality' => $locality])->render() !!}
		@endif
</div>

<script>
$(document).on('keyup','#search-results-search-input', function(e){
	if(e.keyCode == 13){
		location.href = "{{action('SearchController@getSearch')}}?params=" + $('#search-results-search-input').val()
	}
})
</script>
@endsection

@section('scripts')

<!-- Search results search script -->



@endsection
