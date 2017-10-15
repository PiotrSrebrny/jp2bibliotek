<?php


use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Auth\Auth;
use Util\Message;
use Fuel\Core\Controller_Template;

class Controller_User extends Controller_Template
{
	public function before()
	{
		if (!Auth::has_access("right.admin"))
			return Response::redirect('login');
		
		return parent::before();
	}
	
	public function action_index()
	{
	
		$user_count = Model\Auth_User::query()->count();
	
		$num_links = 8;
		$show_first_and_last =  ($user_count / 10) > $num_links;
	
		$pagination = Pagination::forge('mypagination',
				array(
						'total_items'    => $user_count,
						'per_page'       => 10,
						'uri_segment'    => 'page',
						'num_links'      => $num_links,
						'show_first'     => $show_first_and_last,
						'show_last'      => $show_first_and_last,
				));
	
		$users = Model\Auth_User::query()
			->order_by('username')
			->rows_offset($pagination->offset)
			->rows_limit($pagination->per_page)
			->get();
				
		$current_view = '?' . Uri::build_query_string(Input::get());
	
		$data['pagination'] = $pagination;
		$data['users'] = $users;
	
		$this->template->title = 'Użytkownicy ('. $user_count. ')';
		$this->template->content = View::forge('userlist')
			->set('users', $users)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
	

	public function action_delete($username)
	{
		Auth::delete_user($username);
	}
	
	public function action_edit($username)
	{
		$groups_def = \Config::get('simpleauth.groups', false);
		$groups = Auth::groups();
	
		foreach ($groups as $group) {
			if ($groups_def[$group]['name'] == 'Gość')
				continue;
			
			$group_names[$group] = $groups_def[$group]['name'];
		}
		$user = Model\Auth_User::query()->where('username', $username)->get_one();
		
		$form = Fieldset::forge();
		$form->form()->set_attribute('class', 'form-horizontal');
	
		$form->add('fullname', 'Imię i nazwisko', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('email', 'Email', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('group', 'Grupa', array('class' => 'form-control', 'options' => $group_names, 'type' => 'select'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));
		
		$fullname = $user->profile_fields['fullname'];
	
		$input = array (
				'username' => $user->username,
				'email' => $user->email,
				'fullname' => $fullname,
		);
		
		$form->populate($input);
		
		if (Input::post())
		{
			$user->group = $form->field('group')->input();
			$user->save();
					
			\Util\Message::add_success('Zmieniono grupę');
		}
		
		$form->field('group')->set_value($user->get_group());
		
		$this->template->title = 'Użytkownik ' . $username;
		$this->template->content = $form;
	}
}
