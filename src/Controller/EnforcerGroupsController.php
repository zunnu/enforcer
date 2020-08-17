<?php
namespace Enforcer\Controller;

use Enforcer\Controller\AppController;

/**
 * EnforcerGroups Controller
 *
 * @property \Enforcer\Model\Table\EnforcerGroupsTable $EnforcerGroups
 *
 * @method \Enforcer\Model\Entity\EnforcerGroup[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EnforcerGroupsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $groups = $this->EnforcerGroups->find('all')->toArray();

        $this->set(compact('groups'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $group = $this->EnforcerGroups->newEntity();
        if ($this->request->is('post')) {
            $group = $this->EnforcerGroups->patchEntity($group, $this->request->getData());
            if ($this->EnforcerGroups->save($group)) {
                $this->Flash->success(__d('Enforcer', 'The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('Enforcer', 'The group could not be saved. Please, try again.'));
        }
        $this->set(compact('group'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Enforcer Group id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if($id == 3) {
            $this->Flash->error(__d('Enforcer', "You can't edit the default quest group!"));
            return $this->redirect(['action' => 'index']);
        }

        $group = $this->EnforcerGroups->get($id, [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $group = $this->EnforcerGroups->patchEntity($group, $this->request->getData());
            if ($this->EnforcerGroups->save($group)) {
                $this->Flash->success(__d('Enforcer', 'The group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__d('Enforcer', 'The group could not be saved. Please, try again.'));
        }
        $this->set(compact('group'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Enforcer Group id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if($id == 3) {
            $this->Flash->error(__d('Enforcer', "You can't delete the default quest group!"));
            return $this->redirect(['action' => 'index']);
        } elseif($id == 1) {
            $this->Flash->error(__d('Enforcer', "You can't delete the default admin group!"));
            return $this->redirect(['action' => 'index']);
        }

        $this->request->allowMethod(['post', 'delete']);
        $enforcerGroup = $this->EnforcerGroups->get($id);
        if ($this->EnforcerGroups->delete($enforcerGroup)) {
            $this->Flash->success(__d('Enforcer', 'The group has been deleted.'));
        } else {
            $this->Flash->error(__d('Enforcer', 'The group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
