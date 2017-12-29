<?php
class ModelCatalogMenu extends Model {
	public function getMenu($Menu_id) {
		return $this->getCategories((int)$menu_id, 'by_id');
	}

	public function getMenus($id = 0, $type = 'by_parent') {
		static $data = null;

		if ($data === null) {
			$data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md ON (m.menu_id = md.menu_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "' AND m.status = '1' ORDER BY m.parent_id, m.sort_order, md.name");

			foreach ($query->rows as $row) {
				$data['by_id'][$row['menu_id']] = $row;
				$data['by_parent'][$row['parent_id']][] = $row;
			}
		}

		return ((isset($data[$type]) && isset($data[$type][$id])) ? $data[$type][$id] : array());
	}

	public function getMenusByParentId($menu_id) {
		$menu_data = array();

		$menus = $this->getCategories((int)$menu_id);

		foreach ($menus as $menu) {
			$menu_data[] = $menu['menu_id'];

			$children = $this->getMenusByParentId($menu['menu_id']);

			if ($children) {
				$menu_data = array_merge($children, $menu_data);
			}
		}

		return $menu_data;
	}

	public function getTotalMenusByMenuId($parent_id = 0) {
		return count($this->getMenus((int)$parent_id));
	}
}
?>