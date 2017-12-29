<?php
class ModelLocalisationDiscount extends Model {
	public function addDiscount($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "discount SET min_price = '" . (int)$data['min_price'] . "', max_price = '" . (int)$data['max_price'] . "', percent = '" . (float)$data['percent'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
	}

	public function editDiscount($discount_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "discount SET min_price = '" . (int)$data['min_price'] . "', max_price = '" . (int)$data['max_price'] . "', percent = '" . (float)$data['percent'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE discount_id = '" . (int)$discount_id . "'");
	}

	public function deleteDiscount($discount_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "discount WHERE discount_id = '" . (int)$discount_id . "'");
	}

	public function getDiscount($discount_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "discount WHERE discount_id = '" . (int)$discount_id . "'");

		return $query->row;
	}

	public function getDiscounts($data = array()) {

        $discount_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "discount ORDER BY sort_order ASC");

        foreach ($query->rows as $result) {
            $discount_data[] = array(
                'discount_id'   => $result['discount_id'],
      		    'min_price'     => $result['min_price'],
          		'max_price'     => $result['max_price'],
          		'percent'       => $result['percent'],
              	'sort_order'    => $result['sort_order'],
        	);
      	}

		return $discount_data;
	}

	public function getTotalDiscounts() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "discount");
		
		return $query->row['total'];
	}
}
?>