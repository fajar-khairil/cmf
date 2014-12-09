<?php 
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Ext;

use Closure;
use Pimple\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\EngineResolver;

class ViewFactory {

	/**
	 * The engine implementation.
	 *
	 * @var \Illuminate\View\Engines\EngineResolver
	 */
	protected $engines;

	/**
	 * The view finder implementation.
	 *
	 * @var \Illuminate\View\FileViewFinder
	 */
	protected $finder;

	/**
	 * The IoC container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * Data that should be available to all templates.
	 *
	 * @var array
	 */
	protected $shared = array();

	/**
	 * Array of registered view name aliases.
	 *
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * All of the registered view names.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * The extension to engine bindings.
	 *
	 * @var array
	 */
	protected $extensions = array('blade' => 'blade', 'php' => 'php');

	/**
	 * The view composer events.
	 *
	 * @var array
	 */
	protected $composers = array();

	/**
	 * All of the finished, captured sections.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * The stack of in-progress sections.
	 *
	 * @var array
	 */
	protected $sectionStack = array();

	/**
	 * The number of active rendering operations.
	 *
	 * @var int
	 */
	protected $renderCount = 0;

	/**
	 * Create a new view factory instance.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $engines
	 * @param  \Illuminate\View\ViewFinderInterface  $finder
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function __construct(EngineResolver $engines, FileViewFinder $finder)
	{
		$this->finder = $finder;
		$this->engines = $engines;

		$this->share('__env', $this);
	}

	/**
	 * Normalize a view name.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	protected function normalizeName($name)
	{
		$delimiter = FileViewFinder::HINT_PATH_DELIMITER;

		if (strpos($name, $delimiter) === false)
		{
			return str_replace('/', '.', $name);
		}

		list($namespace, $name) = explode($delimiter, $name);

		return $namespace . $delimiter . str_replace('/', '.', $name);
	}

	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  array   $mergeData
	 * @return \Illuminate\View\View
	 */
	public function make($view, $data = array(), $mergeData = array())
	{
		if (isset($this->aliases[$view])) $view = $this->aliases[$view];

		$view = $this->normalizeName($view);

		$path = $this->finder->find($view);

		$data = array_merge($mergeData, $this->parseData($data));

		$data['app'] = $this->container;

		$view = new \Unika\Ext\View($this,$this->getEngineFromPath($path),$view,$path,$data);

		return $view;
	}

	/**
	 * Parse the given data into a raw array.
	 *
	 * @param  mixed  $data
	 * @return array
	 */
	protected function parseData($data)
	{
		return $data instanceof Arrayable ? $data->toArray() : $data;
	}

	/**
	 * Get the evaluated view contents for a named view.
	 *
	 * @param  string  $view
	 * @param  mixed   $data
	 * @return \Illuminate\View\View
	 */
	public function of($view, $data = array())
	{
		return $this->make($this->names[$view], $data);
	}

	/**
	 * Register a named view.
	 *
	 * @param  string  $view
	 * @param  string  $name
	 * @return void
	 */
	public function name($view, $name)
	{
		$this->names[$name] = $view;
	}

	/**
	 * Add an alias for a view.
	 *
	 * @param  string  $view
	 * @param  string  $alias
	 * @return void
	 */
	public function alias($view, $alias)
	{
		$this->aliases[$alias] = $view;
	}

	/**
	 * Determine if a given view exists.
	 *
	 * @param  string  $view
	 * @return bool
	 */
	public function exists($view)
	{
		try
		{
			$this->finder->find($view);
		}
		catch (\InvalidArgumentException $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the rendered contents of a partial from a loop.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  string  $iterator
	 * @param  string  $empty
	 * @return string
	 */
	public function renderEach($view, $data, $iterator, $empty = 'raw|')
	{
		$result = '';

		// If is actually data in the array, we will loop through the data and append
		// an instance of the partial view to the final result HTML passing in the
		// iterated value of this data array, allowing the views to access them.
		if (count($data) > 0)
		{
			foreach ($data as $key => $value)
			{
				$data = array('key' => $key, $iterator => $value);

				$result .= $this->make($view, $data)->render();
			}
		}

		// If there is no data in the array, we will render the contents of the empty
		// view. Alternatively, the "empty view" could be a raw string that begins
		// with "raw|" for convenience and to let this know that it is a string.
		else
		{
			if (starts_with($empty, 'raw|'))
			{
				$result = substr($empty, 4);
			}
			else
			{
				$result = $this->make($empty)->render();
			}
		}

		return $result;
	}

	/**
	 * Get the appropriate view engine for the given path.
	 *
	 * @param  string  $path
	 * @return \Illuminate\View\Engines\EngineInterface
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getEngineFromPath($path)
	{
		if ( ! $extension = $this->getExtension($path))
		{
			throw new \InvalidArgumentException("Unrecognized extension in file: $path");
		}

		$engine = $this->extensions[$extension];

		return $this->engines->resolve($engine);
	}

	/**
	 * Get the extension used by the view file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	protected function getExtension($path)
	{
		$extensions = array_keys($this->extensions);

		return array_first($extensions, function($key, $value) use ($path)
		{
			return ends_with($path, $value);
		});
	}

	/**
	 * Add a piece of shared data to the environment.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function share($key, $value = null)
	{
		if ( ! is_array($key)) return $this->shared[$key] = $value;

		foreach ($key as $innerKey => $innerValue)
		{
			$this->share($innerKey, $innerValue);
		}
	}

	/**
	 * Start injecting content into a section.
	 *
	 * @param  string  $section
	 * @param  string  $content
	 * @return void
	 */
	public function startSection($section, $content = '')
	{
		if ($content === '')
		{
			if (ob_start())
			{
				$this->sectionStack[] = $section;
			}
		}
		else
		{
			$this->extendSection($section, $content);
		}
	}

	/**
	 * Inject inline content into a section.
	 *
	 * @param  string  $section
	 * @param  string  $content
	 * @return void
	 */
	public function inject($section, $content)
	{
		return $this->startSection($section, $content);
	}

	/**
	 * Stop injecting content into a section and return its contents.
	 *
	 * @return string
	 */
	public function yieldSection()
	{
		return $this->yieldContent($this->stopSection());
	}

	/**
	 * Stop injecting content into a section.
	 *
	 * @param  bool  $overwrite
	 * @return string
	 */
	public function stopSection($overwrite = false)
	{
		$last = array_pop($this->sectionStack);

		if ($overwrite)
		{
			$this->sections[$last] = ob_get_clean();
		}
		else
		{
			$this->extendSection($last, ob_get_clean());
		}

		return $last;
	}

	/**
	 * Stop injecting content into a section and append it.
	 *
	 * @return string
	 */
	public function appendSection()
	{
		$last = array_pop($this->sectionStack);

		if (isset($this->sections[$last]))
		{
			$this->sections[$last] .= ob_get_clean();
		}
		else
		{
			$this->sections[$last] = ob_get_clean();
		}

		return $last;
	}

	/**
	 * Append content to a given section.
	 *
	 * @param  string  $section
	 * @param  string  $content
	 * @return void
	 */
	protected function extendSection($section, $content)
	{
		if (isset($this->sections[$section]))
		{
			$content = str_replace('@parent', $content, $this->sections[$section]);
		}

		$this->sections[$section] = $content;
	}

	/**
	 * Get the string contents of a section.
	 *
	 * @param  string  $section
	 * @param  string  $default
	 * @return string
	 */
	public function yieldContent($section, $default = '')
	{
		$sectionContent = $default;

		if (isset($this->sections[$section]))
		{
			$sectionContent = $this->sections[$section];
		}

		return str_replace('@parent', '', $sectionContent);
	}

	/**
	 * Flush all of the section contents.
	 *
	 * @return void
	 */
	public function flushSections()
	{
		$this->sections = array();

		$this->sectionStack = array();
	}

	/**
	 * Flush all of the section contents if done rendering.
	 *
	 * @return void
	 */
	public function flushSectionsIfDoneRendering()
	{
		if ($this->doneRendering()) $this->flushSections();
	}

	/**
	 * Increment the rendering counter.
	 *
	 * @return void
	 */
	public function incrementRender()
	{
		$this->renderCount++;
	}

	/**
	 * Decrement the rendering counter.
	 *
	 * @return void
	 */
	public function decrementRender()
	{
		$this->renderCount--;
	}

	/**
	 * Check if there are no active render operations.
	 *
	 * @return bool
	 */
	public function doneRendering()
	{
		return $this->renderCount == 0;
	}

	/**
	 * Add a location to the array of view locations.
	 *
	 * @param  string  $location
	 * @return void
	 */
	public function addLocation($location)
	{
		$this->finder->addLocation($location);
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function addNamespace($namespace, $hints)
	{
		$this->finder->addNamespace($namespace, $hints);
	}

	/**
	 * Prepend a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string|array  $hints
	 * @return void
	 */
	public function prependNamespace($namespace, $hints)
	{
		$this->finder->prependNamespace($namespace, $hints);
	}

	/**
	 * Register a valid view extension and its engine.
	 *
	 * @param  string    $extension
	 * @param  string    $engine
	 * @param  \Closure  $resolver
	 * @return void
	 */
	public function addExtension($extension, $engine, $resolver = null)
	{
		$this->finder->addExtension($extension);

		if (isset($resolver))
		{
			$this->engines->register($engine, $resolver);
		}

		unset($this->extensions[$extension]);

		$this->extensions = array_merge(array($extension => $engine), $this->extensions);
	}

	/**
	 * Get the extension to engine bindings.
	 *
	 * @return array
	 */
	public function getExtensions()
	{
		return $this->extensions;
	}

	/**
	 * Get the engine resolver instance.
	 *
	 * @return \Illuminate\View\Engines\EngineResolver
	 */
	public function getEngineResolver()
	{
		return $this->engines;
	}

	/**
	 * Get the view finder instance.
	 *
	 * @return \Illuminate\View\ViewFinderInterface
	 */
	public function getFinder()
	{
		return $this->finder;
	}

	/**
	 * Set the view finder instance.
	 *
	 * @param  \Illuminate\View\ViewFinderInterface  $finder
	 * @return void
	 */
	public function setFinder(ViewFinderInterface $finder)
	{
		$this->finder = $finder;
	}

	/**
	 * Get the IoC container instance.
	 *
	 * @return \Illuminate\Container\Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Set the IoC container instance.
	 *
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Get an item from the shared data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function shared($key, $default = null)
	{
		return array_get($this->shared, $key, $default);
	}

	/**
	 * Get all of the shared data for the environment.
	 *
	 * @return array
	 */
	public function getShared()
	{
		return $this->shared;
	}

	/**
	 * Get the entire array of sections.
	 *
	 * @return array
	 */
	public function getSections()
	{
		return $this->sections;
	}

	/**
	 * Get all of the registered named views in environment.
	 *
	 * @return array
	 */
	public function getNames()
	{
		return $this->names;
	}

}
