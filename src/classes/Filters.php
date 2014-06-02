<?php

use Symfony\Component\HttpFoundation\Request;

class Filters {
	
	public function mustLoggedIn(Request $request)
	{
		throw new \RuntimeException('bot yet implemented');
	}
}