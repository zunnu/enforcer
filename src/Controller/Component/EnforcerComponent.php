<?php
namespace Enforcer\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use \Enforcer\PermissionManager;
use DebugKit\DebugTimer;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

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
		if(empty($this->EnforcerConfig['protectionMode']) || $this->EnforcerConfig['protectionMode'] == 'everything') {
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
	    	$multipleGroupManagement = ((!empty($this->EnforcerConfig['groupManagement'])) && ($this->EnforcerConfig['groupManagement'] == 'multiple') ? true : false);
		    $permissionManager = new PermissionManager($multipleGroupManagement);

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
	   			'action' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $action),
	   			'params' => $params,
	   		];

		    // permission manager should handle this
		    $this->Auth->allow([
		    	!empty($requestInfo['plugin']) ? $requestInfo['plugin'] : false,
		    	!empty($requestInfo['prefix']) ? $requestInfo['prefix'] : false,
		    	!empty($requestInfo['controller']) ? $requestInfo['controller'] : '',
		    	!empty($requestInfo['action']) ? $requestInfo['action'] : '',
		    ]);
		   	// $this->Auth->allow('*');

	   		if(!$auth) {
	   			// guest access
	   			$group = 3;
	   		} else {
	   			if(!empty($this->EnforcerConfig['groupManagement'])) {
	   				if($this->EnforcerConfig['groupManagement'] == 'multiple') {
	   					$enforcerUsersGroups = TableRegistry::get('EnforcerUsersGroups');
	   					$groupQuery = $enforcerUsersGroups->find('all')->where(['user_id' => $auth['id']])->toArray();
	   					$group = array_column($groupQuery, 'group_id');

	   					if(empty($group)) {
	   						// if no group is found for user use the guest group by default
	   						$group = 3;
	   					}

	   				} else {
	   					$group = $auth['group_id'];
	   				}
	   			} else {
	   				// default use single group management
		   			$group = $auth['group_id'];
	   			}
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
				      	'msg' => $msg,
				    ]));
	    		}

	    		$session->write('permission_error_redirect', 'redirect');
	    		$this->Flash->error(__('You do not appear to have permission to view this page.'));
	    		DebugTimer::stop('Enforcer-handle');
	    		return $getController->redirect($rawUrl);
	    	}
	    }
    }

    // returns true or false
    // if nothing is given will check the logged in user
    public function isAdmin($id = null) {
    	if(empty($id)) {
    		$id = $this->Auth->user('id');
    	}

    	$multipleGroupManagement = ((!empty($this->EnforcerConfig['groupManagement'])) && ($this->EnforcerConfig['groupManagement'] == 'multiple') ? true : false);

    	if($multipleGroupManagement) {
			$enforcerUsersGroups = TableRegistry::get('EnforcerUsersGroups');
			$groupQuery = $enforcerUsersGroups->find('all')->where(['user_id' => $auth['id']])->toArray();
			$group = array_column($groupQuery, 'group_id');
    	} else {
			// default use single group management
   			$group = $this->Auth->user('group_id');
    	}

    	if(is_array($group) && in_array(1, $group)) {
    		return true;
    	} elseif(!is_array($group) && $group == 1) {
    		return true;
    	}

    	return false;
    }
}
