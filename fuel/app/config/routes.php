<?php
return array(
	'_root_'  => 'home/index',  // The default route
	'_404_'   => 'home/404',    // The main 404 route
	'borrow/list/book/(:any)/(:num)'   => 'borrow/info/$1/$2',
	'borrow/list/reader/id/:num/book/(:any)/(:num)'   => 'borrow/info/$1/$2',
	'borrow/list/reader/(:any)/(:num)'   => 'reader/info/$1/$2',
	'reader/list/reader/id/:num/book/(:any)/(:num)' => 'borrow/info/$1/$2',
	'reader/list/reader/(:any)/(:num)' => 'reader/info/$1/$2'
);