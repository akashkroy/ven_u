<?php
use OurScene\Models\User;
?>

<!-- Artist Genres Modal -->
  <div id="artist-genre-modal" class="modal">
    <div class="modal-content">
    	<h5>GENRE</h5>
		<div class="row input-field">
      <div id="genre-form">
			<ul id="genre-collapsible" class="collapsible col s12 m12 l12" data-collapsible="accordion">
				<?php
					$i = 0;
				?>

				@foreach($genres as $main => $sub)
				<li>
			      <div class="collapsible-header" style="padding-bottom: 1em;">
			     	<input type="checkbox" name="genre[]" id="genre{{$i}}" value="{{$i}}" @if(User::where('_id',Session::get('id'))->first()['artist_genre'] && array_key_exists($i,User::where('_id',Session::get('id'))->first()['artist_genre'])) checked @endif />
			     	<label for="genre{{$i}}"> {{ $main }} </label>
			     	<img class="dropdown-icon" src="{{ asset('images/icons/dropdown.svg') }}" style="float:right;width:15px;margin-top:10px;">

			       <?php $i++;?>

			      </div>
			      <div class="collapsible-body">
			      		<div class="row">

					      @foreach($sub as $genre)
					       <div class="input-field col s12 m6 l4">
                  			@if(User::where('_id',Session::get('id'))->first()['artist_genre'])
			                    @if(array_key_exists($i,User::where('_id',Session::get('id'))->first()['artist_genre']))
			                     <input type="checkbox" name="genre[]" id="genre{{$i}}" value="{{$i}}" checked/>
			                    @else
			                      <input type="checkbox" name="genre[]" id="genre{{$i}}" value="{{$i}}" />
			                    @endif
			                @else
			                    <input type="checkbox" name="genre[]" id="genre{{$i}}" value="{{$i}}" />
			                @endif

								<label for="genre{{$i}}"> {{ $genre }} </label>
					       </div>
					       <?php $i++;?>
					      @endforeach
					    </div>
			      </div>
			    </li>
				@endforeach
			</ul>
      </div>
		</div>
	</div>
	<div class="modal-footer">
      <a href"" id="add-artist-genre-btn" class="modal-action modal-close waves-effect waves-green btn">Done</a>
  </div>
</div>
