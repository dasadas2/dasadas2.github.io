<?php
class ModelCatalogMenu extends Model {
	public function addMenu($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "menu 
		    (
		        parent_id,
		        href,
                class,
                type,
                subcategory,
		        top,
                `column`,
		        sort_order,
		        status
		    )
		    VALUES ('"
		           . (int)$data['parent_id'] .
            "', '" . $this->db->escape($data['href']) .
            "', '" . $this->db->escape($data['class']) .
            "', '" . (int)$data['type'] .
            "', '" . (int)$data['subcategory'] .
            "', '" . (int)$data['top'] .
            "', '" . (int)$data['column'] .
            "', '" . (int)$data['sort_order'] .
            "', '" . (int)$data['status'] .
            "')"
        );

		$menu_id = $this->db->getLastId();

      	foreach ($data['menu_description'] as $language_id => $value) {
        	$this->db->query("INSERT INTO " . DB_PREFIX . "menu_description SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
      	}

		$this->cache->delete('menu');
	}

	public function editMenu($menu_id, $data) {
      	$this->db->query("UPDATE " . DB_PREFIX . "menu SET " 
      	    . "parent_id = '" . (int)$data['parent_id'] . 
      	    "', href = '" . $this->db->escape($data['href']) .
      	    "', class = '" . $this->db->escape($data['class']) .
      	    "', type = '" . (int)$data['type'] .
      	    "', subcategory = '" . (int)$data['subcategory'] .
      	    "', top = '" . (int)$data['top'] .
      	    "', `column` = '" . (int)$data['column'] .
      	    "', sort_order = '" . (int)$data['sort_order'] .
      	    "', status = '" . (int)$data['status'] .
      	    "' WHERE menu_id = '" . (int)$menu_id . "'");

      	$this->db->query("DELETE FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

      	foreach ($data['menu_description'] as $language_id => $value) {
        	$this->db->query("INSERT INTO " . DB_PREFIX . "menu_description SET menu_id = '" . (int)$menu_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
      	}

		$this->cache->delete('menu');
	}

	public function deleteMenu($menu_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "menu WHERE menu_id = '" . (int)$menu_id . "'");
	  	$this->db->query("DELETE FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

		$this->cache->delete('menu');
	}

	public function getMenu($menu_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu WHERE menu_id = '" . (int)$menu_id . "'");

		return $query->row;
	}

	public function getMenus($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md ON (m.menu_id = md.menu_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'md.name',
			'm.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY m.sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMenuDescriptions($menu_id) {
		$menu_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu_description WHERE menu_id = '" . (int)$menu_id . "'");

		foreach ($query->rows as $result) {
			$menu_description_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $menu_description_data;
	}

	public function getTotalMenus() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "menu");

		return $query->row['total'];
	}

	public function getMenusByParentId($parent_id = 0) {
		$query = $this->db->query("SELECT *, (SELECT COUNT(parent_id) FROM " . DB_PREFIX . "menu WHERE parent_id = m.menu_id) AS children FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md ON (m.menu_id = md.menu_id) WHERE m.parent_id = '" . (int)$parent_id . "' AND md.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY m.sort_order, md.name");

		return $query->rows;
	}

	public function getAllMenus() {
		$menu_data = $this->cache->get('menu.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if (!$menu_data || !is_array($menu_data)) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "menu m LEFT JOIN " . DB_PREFIX . "menu_description md ON (m.menu_id = md.menu_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY m.parent_id, m.sort_order, md.name");

			$menu_data = array();
			foreach ($query->rows as $row) {
				$menu_data[$row['parent_id']][$row['menu_id']] = $row;
			}

			$this->cache->set('menu.all.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $menu_data);
		}

		return $menu_data;
	}

    function makeInstall() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "menu` (
                `menu_id` int(11) NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) NOT NULL DEFAULT '0',
                `href` varchar(255) NOT NULL,
                `class` varchar(64) NOT NULL,
                `type` int(3) NOT NULL DEFAULT '0',
                `subcategory` tinyint(1) NOT NULL DEFAULT '0',
                `top` tinyint(1) NOT NULL,
                `column` int(3) NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT '0',
                `status` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`menu_id`),
                UNIQUE KEY `menu_id` (`menu_id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "menu_description` (
              `menu_id` int(11) NOT NULL,
              `language_id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`menu_id`,`language_id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

    }

}
?>