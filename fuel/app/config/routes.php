<?php
return array(
	'_root_'  => 'home/index',  // The default route
	'_404_'   => 'home/404',    // The main 404 route
	'borrow/list/(:any)/(:num)'   => 'borrow/info/$1/$2',
	'reader/info/:num/(:any)/(:num)' => 'borrow/info/$1/$2'
);