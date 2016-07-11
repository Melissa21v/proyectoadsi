<?php

require_once ("Person_controller.php");
class Employees extends Person_controller
{
	function __construct()
	{
		parent::__construct('employees');
	}
	
	function index()
	{
		$config['base_url'] = site_url('/employees/index');
		$config['total_rows'] = $this->Employee->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_people_manage_table( $this->Employee->get_all( $config['per_page'], $this->uri->segment( $config['uri_segment'] ) ), $this );
		$this->load->view('people/manage',$data);
	}
	
	
	/*
	Da sugerencias de búsqueda en base a lo que está siendo buscado
	*/
	function suggest()
	{
		$suggestions = $this->Employee->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	/*
	Carga el formulario de edición de los empleados
	*/
	function view($employee_id=-1)
	{
		$data['person_info']=$this->Employee->get_info($employee_id);
		$data['all_modules']=$this->Module->get_all_modules();
		$this->load->view("employees/form",$data);
	}
	
	/*
	Inserciones / actualiza un empleado
	*/
	function save($employee_id=-1)
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
		$permission_data = $this->input->post("permissions")!=false ? $this->input->post("permissions"):array();
		
		//La contraseña ha sido cambiada o contraseña establecida primera vez
		if($this->input->post('password')!='')
		{
			$employee_data=array(
			'username'=>$this->input->post('username'),
			'password'=>($this->input->post('password'))//md5 antes de el corchete de $this
			);
		}
		else //La contraseña no ha cambiado
		{
			$employee_data=array('username'=>$this->input->post('username'));
		}
		
		if($this->Employee->saveEmployee($person_data,$employee_data,$permission_data,$employee_id))
		{
			//nuevo empleado
			if($employee_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_adding').' '.
				$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>$employee_data['person_id']));
			}
			else //empleado anterior
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_updating').' '.
				$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>$employee_id));
			}
		}
		else//falla
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	/*
	elimina los empleados de la tabla de empleados
	*/
	function delete()
	{
		$employees_to_delete=$this->input->post('ids');
		
		if($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_deleted').' '.
			count($employees_to_delete).' '.$this->lang->line('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_cannot_be_deleted')));
		}
	}
	/*
	obtener el ancho de la forma de añadir / editar
	*/
	function get_form_width()
	{
		return 650;
	}
}
?>
