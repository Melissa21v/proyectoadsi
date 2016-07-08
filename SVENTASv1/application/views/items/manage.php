<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
    init_table_sorting();
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search('<?php echo site_url("$controller_name/suggest")?>','<?php echo $this->lang->line("common_confirm_search")?>');
    enable_delete('<?php echo $this->lang->line($controller_name."_confirm_delete")?>','<?php echo $this->lang->line($controller_name."_none_selected")?>');
    enable_bulk_edit('<?php echo $this->lang->line($controller_name."_none_selected")?>');

    $("#no_description").click(function()
    {
    	$('#items_filter_form').submit();
    });

    $("#low_inventory").click(function()
    {
    	$('#items_filter_form').submit();
    });  

});

function init_table_sorting()
{
	//Sólo iniciar si hay más de una fila
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{
			sortList: [[1,0]],
			headers:
			{
				0: { sorter: false},
				8: { sorter: false},
				9: { sorter: false}
			}

		});
	}
}

function post_item_form_submit(response)
{
	if(!response.success)
	{
		set_feedback(response.message,'error_message',true);
	}
	else
	{
		//Si se trata de una actualización , simplemente actualizar una fila
		if(jQuery.inArray(response.item_id,get_visible_checkbox_ids()) != -1)
		{
			update_row(response.item_id,'<?php echo site_url("$controller_name/get_row")?>');
			set_feedback(response.message,'success_message',false);

		}
		else //actualizar toda la tabla
		{
			do_search(true,function()
			{
				//resaltar nueva fila

				hightlight_row(response.item_id);
				set_feedback(response.message,'success_message',false);
			});
		}
	}
}

function post_bulk_form_submit(response)
{
	if(!response.success)
	{
		set_feedback(response.message,'error_message',true);
	}
	else
	{
		var selected_item_ids=get_selected_values();
		for(k=0;k<selected_item_ids.length;k++)
		{
			update_row(selected_item_ids[k],'<?php echo site_url("$controller_name/get_row")?>');
		}
		set_feedback(response.message,'success_message',false);
	}
}



</script>

<div id="title_bar">
	<div id="title" class="float_left"><?php echo $this->lang->line('common_list_of').' '.$this->lang->line('module_'.$controller_name); ?></div>
	<div id="new_button">
	<?php echo anchor("$controller_name/view/-1/width:$form_width",
		"<div class='big_button' style='float: left;'><span>".$this->lang->line($controller_name.'_new')."</span></div>",
		array('class'=>'thickbox none','title'=>$this->lang->line($controller_name.'_new')));
		?></div>
</div>
<?php echo $this->pagination->create_links();?>
<div id="table_action_header">
	<ul>
		<li class="float_left"><span><?php echo anchor("$controller_name/delete",$this->lang->line("common_delete"),array('id'=>'delete')); ?></span></li>
		
        <li class="float_left"><span><?php echo anchor("$controller_name/bulk_edit/width:$form_width",$this->lang->line("items_bulk_edit"),array('id'=>'bulk_edit','title'=>$this->lang->line('items_edit_multiple_items'))); ?></span></li>     
		
		<li class="float_right">
		<img src='<?php echo base_url()?>images/spinner_small.gif' alt='spinner' id='spinner' />
		<?php echo form_open("$controller_name/search",array('id'=>'search_form')); ?>
		<input type="text" name ='search' id='search' value="Buscar"/>
		</form>
		</li>
	</ul>
</div>

<div id="table_holder">
<?php echo $manage_table; ?>
</div>
<div id="feedback_bar"></div>
<?php $this->load->view("partial/footer"); ?>