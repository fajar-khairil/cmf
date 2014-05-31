<?php

namespace  Unika;
use Symfony\Component\HttpFoundation\Request;
class Filters {
	
	public static function mustLoggedIn(Request $request)
	{
		dd(get_class($request));
	}
}