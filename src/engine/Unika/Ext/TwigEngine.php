<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Twig Implementing \Illuminate\View\Enginer\EngineInterface
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Ext;

 class TwigEngine implements \Illuminate\View\Engines\EngineInterface
 {
 	protected $twig;

 	public function __construct(\Twig_Environment $twig)
 	{
 		$this->twig = $twig;
 	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string  $path
	 * @param  array   $data
	 * @return string
	 */
	public function get($path, array $data = array())
	{
        $template = $this->twig->loadTemplate($path);
		
        if($template instanceof \TwigTemplate){

            //Events are already fired by the View Environment
            $template->setFiredEvents(true);
        }
   
        return $template->render($data);		
	}

    public function __call($method, $arguments)
    {
        if (!method_exists($this->twig, $method)) {
            throw new \BadMethodCallException(sprintf('Method "%s::%s" does not exist.', get_class($this->twig), $method));
        }

        call_user_func_array(array($this->twig, $method), $arguments);

        return $this;
    }	
 }