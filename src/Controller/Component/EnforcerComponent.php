<?php
declare(strict_types=1);

namespace Enforcer\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use \Enforcer\PermissionManager;
use DebugKit\DebugTimer;
use Cake\Event\EventInterface;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

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

    public function initialize(array $config): void
    {
    	$this->EnforcerConfig = $config;
    }

    public function beforeFilter(EventInterface $event): ?Response
    {
		// $event->stopPropagation();
		if(empty($this->EnforcerConfig['protectionMode']) || $this->EnforcerConfig['protectionMode'] == 'everything') {
			// dd($this->hasAccess($event, $this->Auth->user()));
			// return $this->hasAccess($event, $this->Auth->user());
			$access = $this->hasAccess($event, $this->Auth->user());
			$statusCode = method_exists($access, 'getStatusCode') ? $access->getStatusCode() : null;

			if(!empty($statusCode) && $statusCode == 302) $event->stopPropagation();
			return $access;
		}
    }

	/**
	 * Main entry point to the plugin. This should be called from the AppController beforeFilter OR
	 * from the beforeFilter of the controllers you wish to protect
	*/
    public function hasAccess(EventInterface $event, $auth) {
    	$requestObj = $event->getSubject()->getRequest();
    	$request = [];
    	DebugTimer::start('Enforcer-handle');

    	if(!empty($requestObj->getAttribute('params'))) {
    		$request = $requestObj->getAttribute('params');
    	}

    	$getController = $this->getController();
	    $session = $getController->getRequest()->getSession();
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
	   			'plugin' => !empty($plugin) ? $plugin : false,
	   			'prefix' => !empty($prefix) ? $prefix : false,
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

		   	// no access
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

	    		// create params for redirect
	    		$redirectUrl = '';

	    		if($unAuthConfig['controller'] !== str_replace('Controller', '', $requestInfo['controller']) && $unAuthConfig['action'] !== $requestInfo['action']) {
	    			$redirectUrl = \Cake\Routing\Router::url([
					    'plugin' => $requestInfo['plugin'],
					    'prefix' => $requestInfo['prefix'],
					    'controller' => str_replace('Controller', '', $requestInfo['controller']),
					    'action' => $requestInfo['action'],
					    'params' => $requestInfo['params'],
					]);

	    			if(empty($this->Auth->user('id'))) $rawUrl['?'] = ['redirect' => $redirectUrl];
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
    // checks the is_admin values of the groups
    public function isAdmin($id = null) {
        if(!Cache::getConfig('enforcer_admin_groups')) {
            Cache::setConfig('enforcer_admin_groups', [
                'className' => 'Cake\Cache\Engine\FileEngine',
                'duration' => '+1 week',
                'path' => CACHE . 'enforcer' . DS,
            ]);
        }

    	// $adminGroups = [1];
    	$adminGroups = [];
    	$enforcerGroups = TableRegistry::get('EnforcerGroups');
    	$adminGroupsQ = $enforcerGroups->find('all')->where(['is_admin' => 1])->enableHydration(false)
        ->cache(function($q) {
            return 'enforcer_admin_groups';
        }, 'enforcer_admin_groups')->toArray();

    	$adminGroups = array_unique(array_merge($adminGroups, array_column($adminGroupsQ, 'id')));

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

    	if($multipleGroupManagement && count(array_intersect($adminGroups, $group)) > 0) {
    		return true;
    	} elseif(!$multipleGroupManagement && in_array($group, $adminGroups)) {
    		return true;
    	}

    	return false;
    }

    /**
     * Returns the request details (plugin, controller, prefix, action, params)
     * @return [array] return the request details in array
     */
    public function requestDetails() {
    	$request = $this->RequestHandler;

    	if(!empty($request->params)) {
    		$request = $request->params;
    	} else {
	    	$request = $request->request->params;
    	}

		// handle params
    	$controller = $request['controller'];
    	$action = $request['action'];
    	$params = !empty($request['pass']) ? $request['pass'] : '';
    	$plugin = !empty($request['plugin']) ? $request['plugin'] : '';
    	$prefix = !empty($request['prefix']) ? $request['prefix'] : '';

   		$requestInfo = [
   			'plugin' => !empty($plugin) ? $plugin : false,
   			'prefix' => !empty($prefix) ? $prefix : false,
   			'controller' => $controller . 'Controller',
   			'action' => preg_replace('/\\.[^.\\s]{3,4}$/', '', $action),
   			'params' => $params,
   		];

   		return $requestInfo;
    }
}
