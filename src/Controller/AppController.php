<?php

namespace Enforcer\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;

class AppController extends BaseController
{
    public function initialize() {
        parent::initialize();
    }

	public function beforeRender(Event $event) {
	    parent::beforeRender($event);
	}

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout('enforce');
    }
}
