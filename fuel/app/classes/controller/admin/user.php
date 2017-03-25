<?php


use Fuel\Core\Response;
use Fuel\Core\Input;
use Fuel\Core\Fieldset;
use Fuel\Core\Fieldset_Field;
use Auth\Auth;
use Message\Message;

class Controller_Admin_User extends Controller_Admin
{
	public function action_index()
	{
		if (!Auth::has_access("right.admin"))
			return Response::redirect('404');
		
		$user = Input::get('user');
	
		$user_count = Model\Auth_User::query()
			->where('username', 'like', '%'.$user.'%')
			->count();
	
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
			->where('username', 'like', '%'.$user.'%')
			->order_by('username')
			->rows_offset($pagination->offset)
			->rows_limit($pagination->per_page)
			->get();
				
		$current_view = '?' . Uri::build_query_string(Input::get());
	
		$data['pagination'] = $pagination;
		$data['users'] = $users;
	
		$this->template->menu = Presenter::forge('menu');
		$this->template->title = 'Użytkownicy ('. $user_count. ')';
		$this->template->content = View::forge('userlist')
			->set('users', $users)
			->set('pagination', $pagination)
			->set('current_view', $current_view);
	}
	

	public function action_delete($username)
	{
		if (Auth::has_access("right.admin") == false)
			return Response::redirect('404');

		Auth::delete_user($username);
	}
	
	public function action_edit($username)
	{
		if (Auth::has_access("right.admin") == false)
			return Response::redirect('404');

		$groups_def = \Config::get('simpleauth.groups', false);
		$groups = Auth::groups();
	
		foreach ($groups as $group) {
			if ($groups_def[$group]['name'] == 'Gość')
				continue;
			
			$group_names[$group] = $groups_def[$group]['name'];
		}
		$user = Model\Auth_User::query()->where('username', $username)->get_one();
		
		$user_form = Fieldset::forge();
		$user_form->form()->set_attribute('class', 'form-horizontal');
	
		$user_form->add('fullname', 'Imię/nazwisko', array('class' => 'form-control', 'readonly' => 'readonly'));
		$user_form->add('email', 'Email', array('class' => 'form-control', 'readonly' => 'readonly'));
		$user_form->add('group', 'Grupa', array('class' => 'form-control', 'options' => $group_names, 'type' => 'select'));
		$user_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-primary'));
		
		$fullname = $user->profile_fields['fullname'];
	
		$input = array (
				'username' => $user->username,
				'email' => $user->email,
				'fullname' => $fullname,
		);
		
		$user_form->populate($input);
		
		if (Input::post())
		{
			$user->group = $user_form->field('group')->input();
			$user->save();
					
			\Message\Message::add_success('Zmieniono grupę');
		}
		
		$user_form->field('group')->set_value($user->get_group());
		
		$this->template->title = 'Użytkownik ' . $username;
		$this->template->content = $user_form;
	}
}
