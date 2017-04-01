<?php

use Fuel\Core\Controller_Template;
use Fuel\Core\Response;
use Fuel\Core\View;
use Model\Books;
use Auth\Auth;
use Message\Message;

class Controller_Login extends Controller_Template
{
	public function action_index()
	{
		$recovery = false;
		// If so, you pressed the submit button. Let's go over the steps.
		if (Input::post()) 
		{
			// Check the credentials. This assumes that you have the previous table created and
			// you have used the table definition and configuration as mentioned above.
			if (Auth::login()) 
			{
				if (Auth::member(-1)) {
					\Message\Message::add_danger('Dostęp zablokowany');
					Auth::logout();
				} 
				else
				{
					\Message\Message::add_success('Zalogowano');
				}
				// Credentials ok, go right in.
				Response::redirect('home');
			}
			else
			{
				// Oops, no soup for you. Try to login again. Set some values to
				// repopulate the username field and give some error text back to the view.
				Message::add_danger('Nieprawidłowa kombinacja hasło/użytkownik');
				
				$recovery = true;
			}
		}
		
		// Show the login form.
		$this->template->title = 'Logowanie';
		$this->template->content = View::forge('auth/login')->set('recovery', $recovery);
	}
	
	public function action_recovery()
	{
		if (\Input::post()) {
			// do we have a posted email address?
			if ($email = \Input::post('email'))
			{
				// do we know this user?
				if ($user = \Model\Auth_User::find_by_email($email))
				{
					$new_password = Auth::reset_password($user->username);
					
					\Package::load('email');
					$email = \Email::forge();
					
					// use a view file to generate the email message
					$email->html_body(
							View::forge('lostpassword')
							->set('pass', $new_password, false)
							->set('user', $user, false)
							->render()
					);
					
					// give it a subject
					$email->subject('Nowe hasło');
					
					// add from- and to address
					$email->from('admin@jp2bibliotek.no', 'admin');
					$email->to($user->email, $user->username);
					
					// and off it goes (if all goes well)!
					try
					{
						// send the email
						$email->send();
					}
					
					// this should never happen, a users email was validated, right?
					catch(\EmailValidationFailedException $e)
					{
						Message::add_danger('Niepoprawny adres email');
						\Response::redirect_back();
					}
					
					// what went wrong now?
					catch(\Exception $e)
					{
						// log the error so an administrator can have a look
						logger(\Fuel::L_ERROR, '*** Error sending email ('.__FILE__.'#'.__LINE__.'): '.$e->getMessage());
					
						Message::add_danger('Nie powiodło się wysłanie emaila');
						\Response::redirect_back();
					}
					
				} else {
					Message::add_danger('Nieznany użytkownik');
					$error_occured = true;
				}
			}
			
			if (!isset($error_occured))
				Message::add_success('Wysłano email z nowym hasłem');
				
		} else {
			$form = Fieldset::forge();
			
			$form->form()->set_attribute('class', 'form-horizontal');
			$form->add('email', 'Email', array('class' => 'form-control'));
			$form->add('submit', ' ', array(
					'type' => 'submit', 
					'value' => 'Wyślij', 
					'class' => 'btn btn-primary'
			));
			
			$this->template->title = 'Odzyskiwanie hasła';
			$this->template->content = $form;
		}
	}
}
