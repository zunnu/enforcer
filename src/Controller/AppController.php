<?php
declare(strict_types=1);

namespace Enforcer\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\EventInterface;

class AppController extends BaseController
{
    public function initialize(): void {
        parent::initialize();
    }

	public function beforeRender(EventInterface $event): void
    {
	    parent::beforeRender($event);
	}

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout('enforce');
    }
}
