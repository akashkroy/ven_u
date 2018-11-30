<?php
use OurScene\Models\User;
?>

<!-- Equipment Edit Modal -->
<div id="equipment-edit-modal" class="modal">
  <div class="modal-content">
    <div class="ourscene-modal-title-1">Edit equipment</div>
    <br>

    <div class="row input-field">
        
        <label class="col s12 m6 l6">Equipment type</label>
        <br/><br/>

        <div class="col s12 m6 l6">
          <input type="checkbox" id="edit-equipment-type-default" name="default[]" value="Yes" class="filled-in"/>
          <label for="edit-equipment-type-default" >Default</label>
        </div>

        <br/><br/><br/>
    </div>

    <div class="row">
      <div class="col s12 m8 l8 input-field">
  			<label> <span class="required-color">*</span> Name</label>
  			<input type="text" id="equipment_name" name="equipment_name" class="registration-txtbx-1" placeholder="" value=""/>
  		</div>
    </div>
    <div class="row input-field">
      <label for="contents" class="required col s12 m12 l12"> <span class="required-color">*</span> Contents</label>
      <br/>
    </div>
    <div id="inclusion_edit_field" class="row input-field">
		
    </div>
    <div class="row">
        <a href="javascript:void(0);" id="edit-add-more-contents" class="btn ourscene-btn-2 col s12 m4 l3 depth-1">Add More Contents</a>
    </div>
	</div>
	<div class="modal-footer">
      <a href="javascript:void(0);" class="modal-action modal-close waves-effect btn-flat" style="margin-right: 1em;">Cancel</a>
      <a href="javascript:void(0);" id="update-equipment-btn" class="modal-action modal-close btn ourscene-btn-1" style="margin-right: 1em;">Save</a>
  </div>
</div>

<script>
// Append the input file for upload
  $(document).ready(function() {
      $('#edit-add-more-contents').click(function(){
        console.log("add more contents");
          $("#inclusion_edit_field").append('<div><input type="text" id="inclu" name="inclusions[]"  class="registration-txtbx-1 col s8 m8 l8 " placeholder="Content"/><a href="javascript:void(0);" id="remove-equipment-content" class="btn ourscene-btn-plain-1 col s4 m2 l2">Remove</a></div>');
      });

      $("#inclusion_edit_field").on('click', '#remove-equipment-content', function(e){
        e.preventDefault();
        $(this).parent('div').remove();
      });

  });

</script>
