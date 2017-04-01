<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Fieldset;
use Fuel\Core\Validation;
use Auth\Auth;
use Auth\Auth_Driver;
use Message\Message;

class Controller_Account extends Controller_Template
{
	public function action_index()
	{
		if (!Auth::check())
			Response::redirect('home');
		
		$account_form = Fieldset::forge();
		
		$account_form->form()->set_attribute('class', 'form-horizontal');
		
		$account_form->add('username', 'Użytkownik', array('class' => 'form-control', 'readonly' => 'readonly'));
		$account_form->add('fullname', 'Imię/nazwisko', array('class' => 'form-control', 'readonly' => 'readonly'));
		$account_form->add('email', 'Email', array('class' => 'form-control', 'readonly' => 'readonly'));
		
		$bt_e = html_tag('a', array('href' => 'account/update_information', 'class' => 'btn btn-default'), 'Edytuj');
		$bt_w = html_tag('a', array('href' => 'account/update_password', 'class' => 'btn btn-default'), 'Zmień hasło');
		
		$td_e = html_tag('td', '', $bt_e);
		$td_w = html_tag('td', '', $bt_w);
		
		$buttons = html_tag('p', '', 
			html_tag('table', '', 
				html_tag('tr', '', $td_e . $td_w)));
		
		$shift = html_tag('div', array('class' => 'col-sm-offset-1 col-sm-4'), $buttons);
		
		$profile_fields = Auth::get_profile_fields();
		
		$input = array (
				'username' => Auth::get_screen_name(),
				'email' => Auth::get_email(),
				'fullname' => isset($profile_fields['fullname']) ? $profile_fields['fullname'] : '',
		);
		
		$account_form->populate($input);
		
		$buttons = View::forge('buttons')
			->set('offset', 1)
			->set('buttons', array(
				array('account/update_information', 'Edytuj'),
				array('account/update_password', 'Zmień hasło')));
		
		$this->template->content =  $account_form . $buttons;
		$this->template->title = 'Ustawienia konta';
	}
	
	public  function action_update_information()
	{
		if (!Auth::check())
			Response::redirect('home');
		
		$form = Fieldset::forge();
		
		$form->form()->set_attribute('class', 'form-horizontal');
		
		$form->add('username', 'Użytkownik', array('class' => 'form-control', 'readonly' => 'readonly'));
		$form->add('fullname', 'Imię/nazwisko', array('class' => 'form-control'));
		$form->add('email', 'Email', array('class' => 'form-control'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zapisz', 'class' => 'btn btn-success'));
		
		if (Input::post()) {
		
			Auth::update_user(array(
				'email' => $form->input('email'),
				'fullname' => $form->input('fullname')
				));
		
			\Message\Message::add_success('Zaktualizowano ustawienia konta');
			
			Response::redirect('account');
		}
		
		$profile_fields = Auth::get_profile_fields();
		
		$input = array (
				'username' => Auth::get_screen_name(),
				'email' => Auth::get_email(),
				'fullname' => isset($profile_fields['fullname']) ? $profile_fields['fullname'] : '',
		);
		
		$form->populate($input);
		
		$this->template->content = $form;
		$this->template->title = 'Ustawienia konta';
	}
	
	public  function action_update_password()
	{
		if (!Auth::check())
			Response::redirect('home');

		$form = Fieldset::forge();
		
		$form->form()->set_attribute('class', 'form-horizontal');
		
		$form->add('old_password', 'Stare hasło', array('class' => 'form-control', 'type' => 'password'));
		$form->add('password', 'Nowe hasło', array('class' => 'form-control', 'type' => 'password'));
		$form->add('password_rep', 'Powtórz hasło', array('class' => 'form-control', 'type' => 'password'));
		$form->add('submit', ' ', array('type' => 'submit', 'value' => 'Zaktualizuj', 'class' => 'btn btn-primary'));
		
		$val = Validation::forge($form);
			
		$val->field('old_password')->add_rule('required');
		$val->field('password')->add_rule('required');
		$val->field('password_rep')->add_rule('match_field', 'password')->add_rule('required');
			
		if (Input::post()) {
			if (!$val->run()) {
				Message::add_danger($val->show_errors());
			
			} else {
				try {
					\Auth::update_user(array(
						'old_password' => $form->input('old_password'),
						'password' => $form->input('password')
					));
				} catch (Exception $e) {
					if ($e->getCode() == 6) 
						Message::add_danger('Hasło nie może być puste');
					
					else if ($e->getMessage() == 'Old password is invalid')
						Message::add_danger('Stare hasło jest niepoprawne');
					
					$error_occured = true;
				}
			
				if (!isset($error_occured))
					Message::add_success('Zaktualizowano hasło');
			}
		}
				
		$profile_fields = Auth::get_profile_fields();
		
		$this->template->content = $form;
		$this->template->title = 'Ustawienia konta';
	}
}
