<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since	 2.0.0
 * @author	 Christopher Castro <chris@quickapps.es>
 * @link	 http://www.quickappscms.org
 * @license	 http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace Menu\Controller\Admin;

use Menu\Controller\AppController;

/**
 * Menu manager controller.
 *
 * Allow CRUD for menus.
 */
class ManageController extends AppController {

/**
 * Shows a list of all the nodes.
 *
 * @return void
 */
	public function index() {
		$this->loadModel('Menu.Menus');
		$menus = $this->Menus->find()->all();

		$this->set('menus', $menus);
		$this->Breadcrumb->push('/admin/menu/manage');
	}

/**
 * Adds a new menu.
 *
 * @return void
 */
	public function add() {
		$this->loadModel('Menu.Menus');
		$menu = $this->Menus->newEntity();
		$menu->set('handler', 'Menu');

		if ($this->request->data) {
			$data = $this->_prepareData();
			$menu = $this->Menus->patchEntity($menu, $data, [
				'fieldList' => [
					'title',
					'description',
					'settings',
				],
			]);

			if ($this->Menus->save($menu, ['atomic' => true])) {
				$this->alert(__d('menu', 'Menu has been created, now you can start adding links!'), 'success');
				$this->redirect(['plugin' => 'Menu', 'controller' => 'links', 'action' => 'add', $menu->id]);
			} else {
				$this->alert(__d('menu', 'Menu could not be created, please check your information'), 'danger');
			}
		}

		$this->set('menu', $menu);
		$this->Breadcrumb->push('/admin/menu/manage');
		$this->Breadcrumb->push(__d('menu', 'Creating new menu'), '#');
	}

/**
 * Edits the given menu by ID.
 *
 * @return void
 */
	public function edit($id) {
		$this->loadModel('Menu.Menus');
		$menu = $this->Menus->get($id);

		if ($this->request->data) {
			$data = $this->_prepareData();
			$menu = $this->Menus->patchEntity($menu, $data, [
				'fieldList' => [
					'title',
					'description',
					'settings',
				],
			]);

			if ($this->Menus->save($menu, ['atomic' => true])) {
				$this->alert(__d('menu', 'Menu has been saved!'), 'success');
				$this->redirect($this->referer());
			} else {
				$this->alert(__d('menu', 'Menu could not be saved, please check your information'), 'danger');
			}
		}

		$this->set('menu', $menu);
		$this->Breadcrumb->push('/admin/menu/manage');
		$this->Breadcrumb->push(__d('menu', 'Editing menu %s', $menu->title), '#');
	}

/**
 * Prepares incoming data from Form's POST.
 *
 * Any input field that is not a column in the "menus" table will be moved
 * to the "settings" column. For example, `random_name` becomes `settings.random_name`.
 *
 * @return array
 */
	protected function _prepareData() {
		$this->loadModel('Block.Blocks');
		$columns = $this->Blocks->schema()->columns();
	 	$data = [];

		foreach ($this->request->data as $coulumn => $value) {
			if (in_array($coulumn, $columns)) {
				$data[$coulumn] = $value;
			} else {
				$data['settings'][$coulumn] = $value;
			}
		}

		return $data;
	}

}