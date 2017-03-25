<?php


use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Fuel\Core\Fieldset;
use Fuel\Core\Validation;
use Auth\Auth;
use Auth\Auth_Driver;
use Auth\Model;
use Message\Message;


class Controller_Newaccount extends Controller_Template
{
	public function action_index()
	{
		if (Auth::check())
			Response::redirect('home');

		$account_form = Fieldset::forge();

		$account_form->form()->set_attribute('class', 'form-horizontal');

		$account_form->add('username', 'Użytkownik', array('class' => 'form-control'));
		$account_form->add('fullname', 'Imię i nazwisko', array('class' => 'form-control'));
		$account_form->add('pass', 'Hasło', array('class' => 'form-control', 'type' => 'password'));
		$account_form->add('pass_rep', 'Hasło powtórz', array('class' => 'form-control', 'type' => 'password'));
		$account_form->add('email', 'Email', array('class' => 'form-control'));
		$account_form->add('submit', ' ', array('type' => 'submit', 'value' => 'Stwórz', 'class' => 'btn btn-primary'));

 		if (Input::post()) {
			$val = Validation::forge($account_form);
			
			$val->field('username')->add_rule('required');
			$val->field('fullname')
				->add_rule('required')
				->add_rule('trim');
			$val->field('pass')
				->add_rule('required');
			$val->field('pass_rep')
				->add_rule('match_field', 'pass')
				->add_rule('required');
			$val->field('email')
				->add_rule('required')
				->add_rule('trim')
				->add_rule('valid_email');
			
			if (!$val->run()) {
				Message::add_danger($val->show_errors());
				
			} else {
				try
				{
					$created = \Auth::create_user(
						$account_form->validated('username'),
						$account_form->validated('pass'),
						$account_form->validated('email'),
						1, /* Assign a user to the user group (Banned) */
						array('fullname' => $account_form->validated('fullname'))
					);
				
					// if a user was created succesfully
					if ($created)	{
						Message::add_success('Zaktualizowano ustawienia konta');
						
						\Response::redirect_back('home');
					}
				}
				
				catch (\SimpleUserUpdateException $e)	{
					// duplicate email address
					if ($e->getCode() == 2)	{
						Message::add_danger('Address email już istnieje w systemie');
					}
				
					// duplicate username
					elseif ($e->getCode() == 3)	{
						Message::add_danger('Użytkownik już istnieje w systemie');

					}
				
					// this can't happen, but you'll never know...
					else {
						Message::add_danger('Nieznany błąd');
					}
				}
			}
		}
		
		$account_form->repopulate();
		

		$this->template->content = $account_form;
		$this->template->title = 'Ustawienia konta';
	}
}