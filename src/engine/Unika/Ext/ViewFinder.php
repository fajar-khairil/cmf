<?php
/**
 *  This file is part of the Unika-CMF project.
 *  View Finder Blade and Twig Finder
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

namespace Unika\Ext;

class ViewFinder extends \Illuminate\View\FileViewFinder 
{

    protected $extensions = array('blade','twig', 'php');

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {   
    	return $this->files->get($this->getFullPath($name));	
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name)
    {
    	return md5($this->getFullPath($name));
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return bool    true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
    	return filemtime($this->getFullPath($name)) <= $time;
    }

    //its bad need improvement
    protected function getFullPath( $path )
    {
        if( !is_file($path) ){
            $path = $this->find($path);
        }

        return $path;
    }

    /**
     * Check if we have the source code of a template, given its name.
     *
     * @param string $name The name of the template to check if we can load
     *
     * @return bool    If the template source code is handled by this loader or not
     */
    public function exists($name)
    {   
    	return (boolean)$this->getFullPath($name);
    }
}