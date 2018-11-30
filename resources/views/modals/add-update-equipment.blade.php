<!-- Add/Update Equipment Modal -->

<div id="add-update-equipment-modal" class="modal">

  <div class="modal-content">

    <div class="ourscene-modal-title-1">Add equipment</div>


    {!! Form::open( array(
      'id' => 'equipment-form',
      'method' => 'post',
      'url' => action('EquipmentController@postAjaxAddEquipment')
    )) !!}


    <input type="hidden" id="token" value="{{ csrf_token() }}">

    <div class="row input-field">

        <label class="col s12 m6 l6">Equipment type</label>
        <br/><br/>

        <div class="col s12 m6 l6">
          <input type="checkbox" id="default" name="default[]" value="Yes" checked class="filled-in"/>
          <label for="default" >Default</label>
        </div>

        <br/><br/><br/>
    </div>

    <div class="row input-field">
        <div class="col s12 m8 l6">
          <label for="name" class="required"> <span class="required-color">*</span> Name </label>
          <input type="text" name="equipment_name" class="" placeholder="Name" autofocus required/>
        </div>
    </div>

    <div class="row input-field">
      <label for="contents" class="required col s12 m8 l10"> <span class="required-color">*</span> Contents </label>
      <br/>
    </div>

    <div class="row input-field">
      <div class="col s12 m8 l8" id="eq-contents">
        <input type="text" name="contents[]" class="col s12 m8 l8" placeholder="Content" required/>
      </div>
    </div>

    <div class="row">
      <a href="javascript:void(0);" id="add-more-contents" class="btn ourscene-btn-2 col s12 m4 l3 depth-1">Add More Contents</a>
    </div>

  </div>

  <div class="modal-footer">
    <a href="javascript:void(0);" id="cancel-add-update-equipment" class="waves-effect waves-green btn-flat">Cancel</a>
    <button type="submit" id="add-equipment-btn" class="btn ourscene-btn-1">Save</button>
  </div>

  {!! Form::close() !!}
</div>

<script>
// Append the input file for upload
  $(document).ready(function() {
      var x = 1;

      $('#add-more-contents').click(function(){
        console.log("add more contents");
          x++;
          $('#eq-contents').append('<div class="additional-content"><input type="text" class="col s12 m8 l8" name="contents[]" placeholder="Content" required/><div class="col s12 m4 l3"><a href="javascript:void(0);" id="remove-equipment-content" class="btn ourscene-btn-plain-1 right">Remove</a></div><div>');
      });

      $('#eq-contents').on('click', '#remove-equipment-content', function(e){
        e.preventDefault();
        $(this).parents('.additional-content').remove();
        x--;
      });

      $('#cancel-add-update-equipment').click(function(){
        $('#add-update-equipment-modal').closeModal();
      });


  });

</script>
