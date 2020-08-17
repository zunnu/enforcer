<?php
namespace Enforcer\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use \Enforcer\PermissionManager;
use DebugKit\DebugTimer;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use Cake\Http\Response;

/**
 * Enforcer component
 */
class EnforcerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public $components = ['RequestHandler', 'Flash', 'Auth'];

    public function initialize(array $config) {
    	$this->EnforcerConfig = $config;
    	// return $this->hasAccess($this->RequestHandler, $this->Auth->user());
    }

    public function beforeFilter(Event $event) {
		// $event->stopPropagation();
		if($this->EnforcerConfig['protectionMode'] == 'everything') {
			return $this->hasAccess($this->RequestHandler, $this->Auth->user());
		}
    }

	/**
	 * Main entry point to the plugin. This should be called from the AppController beforeFilter OR
	 * from the beforeFilter of the controllers you wish to protect
	*/
    public function hasAccess($request, $auth) {
    	DebugTimer::start('Enforcer-handle');

    	if(!empty($request->params)) {
    		$request = $request->params;
    	} else {
	    	$request = $request->request->params;
    	}

    	$getController = $this->getController();
	    $session = $getController->request->session();
	    $pageRedirect = $session->read('permission_error_redirect');
	    $session->delete('permission_error_redirect');

	    if(empty($pageRedirect) && !empty($request['controller'])) {
		    $permissionManager = new PermissionManager();

		    // handle params
	    	$controller = $request['controller'];
	    	$action = $request['action'];
	    	$params = !empty($request['pass']) ? $request['pass'] : '';
	    	$plugin = !empty($request['plugin']) ? $request['plugin'] : '';
	    	$prefix = !empty($request['prefix']) ? $request['prefix'] : '';

	   		$requestInfo = [
	   			'plugin' => $plugin,
	   			'prefix' => $prefix,
	   			'controller' => $controller . 'Controller',
	   			'action' => $action,
	   			'params' => $params,
	   		];

		    // permission manager should handle this
		   	$this->Auth->allow([$action]);

	   		if(!$auth) {
	   			// quest access
	   			$group = 3;
	   		} else {
		   		$group = $auth['group_id'];
		   	}

	    	if(!$permissionManager->checkAccess($requestInfo, $group)) {
		    	$response = $getController->getResponse();
	    		$unAuthConfig = $this->EnforcerConfig['unauthorizedRedirect'];

	    		$rawUrl = [
	    			'prefix' => !empty($unAuthConfig['prefix']) ? $unAuthConfig['prefix'] : false,
	    			'plugin' => !empty($unAuthConfig['plugin']) ? $unAuthConfig['plugin'] : false,
	    			'controller' => !empty($unAuthConfig['controller']) ? $unAuthConfig['controller'] : false,
	    			'action' => !empty($unAuthConfig['action']) ? $unAuthConfig['action'] : false,
	    		];

	    		// ajax handle
	    		if($getController->getRequest()->is('ajax')) {
					$response = $response->withStatus(403)->withoutHeader('Location');
					$status = $response->getStatusCode();
					$url = \Cake\Routing\Router::url($rawUrl, true);
					$msg = __d('Enforcer', 'You do not appear to have permission to view this page.');
					$this->getController()->setResponse($response);
					$this->getController()->viewBuilder()->disableAutoLayout();
					$this->getController()->set('_redirect', compact('url', 'status', 'msg'));

			     	// $event = new Event('Enforcer.Permissions', $this->getController());
					// $event->stopPropagation();

					DebugTimer::stop('Enforcer-handle');

				  	return $response->withType('application/json')->withStringBody(json_encode([
				    	'status' => $status,
				      	'msg' => $msg
				    ]));
	    		}

	    		$session->write('permission_error_redirect', 'redirect');
	    		$this->Flash->error(__('You do not appear to have permission to view this page.'));
	    		DebugTimer::stop('Enforcer-handle');
	    		return $getController->redirect($rawUrl);
	    	}
	    }
    }
}
