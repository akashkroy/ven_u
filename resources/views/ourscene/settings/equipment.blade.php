<div class="settings-tab-container" id="">

	<div id="file-size-exceeded-error" class="error-field" style="display: none;">
		Your total upload file size exceeded 320 MB.
	</div>

	<div class="row" id="equipment-list">


		<table id="equipment-list-table" class="col s12 m10 l10 eqpmnt-table striped highlight centered">
		    <thead>
		      <tr>
		        <th data-field="name">Name</th>
		        <th data-field="inclusions">Contents</th>
				<th>Option</th>
		      </tr>
		    </thead>
		    <tbody>
				<div class='preloader col s12 m10 l10' style="display:none;">
					<div class="progress">
							<div class="indeterminate"></div>
					</div>
				</div>
				<!-- <tr class=""></tr> -->
				@if(count($equipments) > 0)
					@foreach($equipments as $equipment)
					<tr>
						<td class="hide"><input type="hidden" class="equipment_id" value="{{$equipment['_id']}}"></td>
						<td class="hide"><input type="hidden" class="equipment_type" value="{{$equipment['type']}}"></td>
						<td class="equip_name">{!! $equipment->name !!}</td>
					   	<td>
				      		<ul>
				      		@if(isset($equipment->inclusion))
					      		@foreach($equipment->inclusion as $inclusion)
					      			<li>{!! $inclusion !!}</li>
					      		@endforeach
					      	@endif
				      		</ul>
				      	</td>
						<td>
							<a href="javascript:void(0);" class="btn-flat edit-equipment-trigger"><i class="material-icons">mode_edit</i></href>
							<a href="javascript:void(0);" class="btn-flat delete-equipment-trigger"><i class="material-icons">delete</i></href>
						</td>
					 </tr>
					@endforeach
				@else
					<tr><td colspan="3">No Equipment Yet.<td><tr>
				@endif
			</tbody>
	    </table>
	</div>


	<div class="row">
		<div class=" col s12 m4 l4">
			<button data-target="add-update-equipment-modal" class="btn btn-link add-more-media-btn modal-trigger">+ ADD EQUIPMENT</button>
		</div>
	</div>

	<!-- MATERIALS FOR ARTIST -->
	@if(Session::get('user_type') === 'artist')
	<div class="section">
		{!! Form::open(array(
				'url'		=> action('UserController@updateArtistMaterials'),
				'method'	=> 'POST',
				'files'		=> 'true',
				'id' => 'update-media-form'
			)) !!}

		<div class="row" id="register-materials-images">
			<div class="col s12 m12 l12 label" style="margin-bottom:10px;">
				<span >Photos<span style="color:#aaa;"> Max 5 Photos (.jpg | .jpeg | .png | .gif)</span></span>
			</div>
			<div id="material-images" class="row material-upload">
				@for($i=1; $i<=count($images) ; $i++)
				<div class="col s6 m2 l2 " id="images-container">
					<input type="hidden" value="{{$images[$i-1]->id}}">
					<a class="media_images" href="{{$images[$i-1]->url}}">
						<div alt="" style="background-image: url('{{$images[$i-1]->url}}'); background-size:cover; background-position: 50%; width: 100%;height: 100%; margin:0 auto; align: baseline; top:0; left:0"/></div>
					</a>
					<a href="#!" class="remove-material-images" id='remove-material-href'>Remove <img src="{{asset('images/icons/media_loader.svg')}}" style="margin-top: 5px; display:none;" alt="" width="13px" /></a>
				</div>
				@endfor

			</div>
			<?php $image_counter = count($images); ?>
		</div>
		<div class="row">
			<label class="col s10 m4 l4">
				<a href="javascript:void(0);" id="add-more-images" class="btn btn-link add-more-media-btn @if($image_counter>=5) disabled @endif">+ Add Photo</a>
			</label>
		</div>

		<div class="row" id="register-materials-videos">
			<div class="col s12 m12 l12 label" style="margin-bottom:10px;">
				<span >Videos<span style="color:#aaa;"> Max 5 Videos (.mp4 | .mov)</span></span>
			</div>
			<div class="col s12 m12 l12">
				<div class="row material-upload">
					@for($i=1; $i<=count($videos) ; $i++)
					<div class="col s6 m6 l4 " id="videos-container">
						<input type="hidden" value="{{$videos[$i-1]->id}}">
						<video class="responsive-video"  width="320" height="240" style="height:240px" src="{{ $videos[$i-1]->url }}" controls>
							<!-- <source src="{{ $videos[$i-1]->url }}" type="video/mp4">
							Sorry. Your browser does not support this kind of video. -->
						</video>
						<a href="#!" class="remove-material-videos" id='remove-material-href'>Remove<img src="{{asset('images/icons/media_loader.svg')}}" style="margin-top: 5px; display:none;" alt="" width="13px" /></a>
					</div>
					@endfor
				</div>
			</div>
			<?php $video_counter = count($videos); ?>
		</div>
		<div class="row">
			<label class="col s10 m4 l4">
				<a href="javascript:void(0);" id="add-more-videos" class="btn btn-link add-more-media-btn @if($video_counter>=5) disabled @endif">+ Add Video</a>
			</label>
		</div>

		<div class="row" id="register-materials-songs">
			<div class="col s12 m12 l12 label" style="margin-bottom:10px;">
				<span >Songs<span style="color:#aaa;"> Max 5 Songs (.mp3 | .m4a | .wav) </span></span>
			</div>
			<div class="col s12 m12 l12">
				<div class="row material-upload">
					@for($i=1; $i<=count($songs) ; $i++)
					<div class="col s12 m6 l6  song-file-container" id="songs-container">
						<input type="hidden" value="{{$songs[$i-1]->id}}">
						<span class="song-title row"><b>{{$songs[$i-1]->title}}</b></span>
						<audio class="" id="music{{$i}}" controls>	<source src="{{$songs[$i-1]->url}}" type="audio/mp3">	</audio>
						<a href="#!" class="remove-material-songs">Remove<img src="{{asset('images/icons/media_loader.svg')}}" style="margin-top: 5px; display:none;" alt="" width="13px" /></a>
					</div>
					@endfor
				</div>
			</div>
			<?php $song_counter = count($songs); ?>
		</div>
		<div class="row">
			<label class="col s10 m4 l4">
				<a href="javascript:void(0);" id="add-more-songs" class="btn btn-link add-more-media-btn @if($song_counter>=5) disabled @endif">+ Add Song</a>
			</label>
		</div>

		<div class="row">
			<button type="submit" id="media-update-form-submit-btn" class="col s6 m2 l2 btn ourscene-btn-1" required/>UPLOAD</button>
		</div>
		{!! Form::close() !!}
	</div>
	<script>
		var song = <?php echo $song_counter; ?>;
		var image = <?php echo $image_counter; ?>;
		var video = <?php echo $video_counter; ?>;
		var media_uploader = "{{asset('images/icons/media_loader.svg')}}"
		var delete_icon = "{{asset('images/icons/delete.svg')}}"
		var delete_material_action = "{{action('UserController@postAjaxDeleteMaterial')}}"
	</script>
	@endif
	<!-- END OF MATERIALS FOR ARTIST -->
</div>
