<?php
require_once ("Person_controller.php");
class Suppliers extends Person_controller
{
	function __construct()
	{
		parent::__construct('suppliers');
	}
	
	function index()
	{
		$config['base_url'] = site_url('/suppliers/index');
		$config['total_rows'] = $this->Supplier->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_supplier_manage_table( $this->Supplier->get_all( $config['per_page'], $this->uri->segment( $config['uri_segment'] ) ), $this );
		$this->load->view('suppliers/manage',$data);
	}
	
	/*
	retorna filas de datos de la tabla. Esto se llama con AJAX.
	*/
	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_supplier_manage_table_data_rows($this->Supplier->search($search),$this);
		echo $data_rows;
	}
	
	/*
	Da sugerencias de búsqueda en base a lo que está siendo buscado

	*/
	function suggest()
	{
		$suggestions = $this->Supplier->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	/*
	Carga el formulario de edición de proveedor
	*/
	function view($supplier_id=-1)
	{
		$data['person_info']=$this->Supplier->get_info($supplier_id);
		$this->load->view("suppliers/form",$data);
	}
	
	/*
	Inserciones / actualiza un proveedor

	*/
	function save($supplier_id=-1)
	{
		$person_data = array(
		'first_name'=>$this->input->post('first_name'),
		'last_name'=>$this->input->post('last_name'),
		'documento'=>$this->input->post('documento'),
		'email'=>$this->input->post('email'),
		'phone_number'=>$this->input->post('phone_number'),
		'address_1'=>$this->input->post('address_1'),
		'city'=>$this->input->post('city'),
		'state'=>$this->input->post('state'),
		'country'=>$this->input->post('country'),
		'comments'=>$this->input->post('comments')
		);
		$supplier_data=array(
		'company_name'=>$this->input->post('company_name'),
		'account_number'=>$this->input->post('account_number')=='' ? null:$this->input->post('account_number'),
		);
		if($this->Supplier->saveSupplier($person_data,$supplier_data,$supplier_id))
		{
			//nuevo proveedor
			if($supplier_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('suppliers_successful_adding').' '.
				$supplier_data['company_name'],'person_id'=>$supplier_data['person_id']));
			}
			else //anterior proveedor
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('suppliers_successful_updating').' '.
				$supplier_data['company_name'],'person_id'=>$supplier_id));
			}
		}
		else//fallo
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('suppliers_error_adding_updating').' '.
			$supplier_data['company_name'],'person_id'=>-1));
		}
	}
	
	/*
	elimina los proveedores de la tabla proveedores
	*/
	function delete()
	{
		$suppliers_to_delete=$this->input->post('ids');
		
		if($this->Supplier->delete_list($suppliers_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('suppliers_successful_deleted').' '.
			count($suppliers_to_delete).' '.$this->lang->line('suppliers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('suppliers_cannot_be_deleted')));
		}
	}
	
	/*
	trae una fila por un proveedor de Table. Esto se conoce como el uso de AJAX para actualizar una fila.
	*/
	function get_row()
	{
		$person_id = $this->input->post('row_id');
		$data_row=get_supplier_data_row($this->Supplier->get_info($person_id),$this);
		echo $data_row;
	}
	
	/*
	asigna el ancho de la forma de añadir / editar
	*/
	function get_form_width()
	{			
		return 360;
	}
}
?>