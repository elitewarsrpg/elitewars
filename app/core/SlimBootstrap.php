<?php
namespace Elite\Bootstrap;

use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Slim;
use Slim\Views\Twig;
use \PDO;

class SlimBootstrap
{
	protected $app;
	protected $session;
	protected $config = [];
	protected $capsule;
	
	
	/**
	 * Constructor
	 * @instance Slim $app
	 * @instance \Session $session
	 * @array $config
	 * @instance of Capsule $capsule
	 */
	public function __construct(Slim $app, \Session $session, $config, Capsule $capsule) 
	{
		$this->app = $app;
		$this->session = $session;
		$this->config = $config;
		$this->capsule = $capsule;
	}
	
	
	/**
	 * Loads all methods.
	 * @return $app
	 */
	public function bootstrap()
	{
		$app = $this->app;
		$session = $this->session;
		$config = $this->config;
		$capsule = $this->capsule;
		$this->configureView($app, $config);
		$this->addDefaultHeaders($app);
		$this->addHooks($app, $session);
		$this->addMiddleware($app);
		$this->addContainer($app, $session, $config);
		$this->addEloquent($app, $config);
		return $app;
	}
	
	
	/***
	 * Elitewars RPG view configuration - configured to use Twig.
	 * @instance of Slim $app
	 * @array $config
	 * @return void
	 */
	public function configureView(Slim $app, $config)
	{
		$app->view->parserOptions = $config['twig']['environment'];
		$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());
	}
		
		
	/**
	 * Elitewars RPG hook addons (actual hooks created by slim @ codeguy)
	 * @instance of Slim $app
	 * @instance of \Session $session
	 * @return $app->redirect() | false
	 */	
	public function addHooks(Slim $app, \Session $session)
	{
		// Authenticate if a user is logged in or not.
		// Block any attempts at the'index', '/', '/login' and '/register' routes.
		$app->hook('slim.before.dispatch', function() use ($app) {
			$pathInfo = $app->request()->getPathInfo();
			$basePath = array('/', '/index', '/login', '/register');
			if (!$this->session->isLoggedIn() && !in_array($pathInfo, $basePath)) {
				$app->flash('error', 'Login required to view the requested page.');
				return $app->redirect('login');
			}	
			
			// Block any attempt at the '/login' and '/register' route while a user is logged in.
			elseif ($this->session->isLoggedIn() && $pathInfo === '/login' OR $pathInfo === '/register') {
				$app->flash('error', 'You may not view this route while logged in!');
				return $app->redirect('members');
			}
		});
	}
	
	
	/**
	 * Set the default headers to utf8
	 * @instance of Slim $app
	 * @return void;
	 */
    	public function addDefaultHeaders(Slim $app)
    	{
        	$app->response->headers->set('Content-Type', 'text/html; charset=utf-8');
    	}
	
	
	/**
	 * Elitewars RPG addon middleware (actual middleware created by slim @codeguy)
	 * instance of Slim $app
	 * @return void
	 */
	public function addMiddleware(Slim $app)
	{
		$app->add(new \Navigation());
		$app->add(new \SessionMiddleware(['name' => 'EliteRpgSession']));
	}
	
	
	/**
	 * Elitewars RPG addon containers (actual container created by slim @ codeguy)
	 * @instance of Slim $app
	 * @instance of \Session $session
	 * @array $config
	 * @return session|capsule
	 */
	public function addContainer(Slim $app, \Session $session, $config)
	{
		// Add session to Slim
		$app->container->singleton('session', function() use ($app) {
			return new \Session;
		});
		
		// Add capsule to Slim. (todo: rename to db?)
		$app->container->singleton('capsule', function() use ($config) {
	    	    	$capsule = new Capsule;
		    	$capsule->setFetchMode(PDO::FETCH_OBJ);
		    	$capsule->addConnection($config['database']);
		    	$capsule->setAsGlobal();
		    	$capsule->bootEloquent();
	    
	    		return $capsule->getConnection();
		});	
	}
	
	
	/**
	 * Bootstrap Eloquent ORM to access the models.
	 * @instance of Slim
	 * @array $config
	 * @return void
	 */
	public function addEloquent(Slim $app, $config) 
	{
		// Needed to have access to the ORM.
		$capsule = new Capsule;
		$capsule->addConnection($config['database']);
		$capsule->bootEloquent();
	}
}
