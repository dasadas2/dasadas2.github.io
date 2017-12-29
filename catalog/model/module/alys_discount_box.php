<?php
class ModelModuleAlysDiscountBox extends Model {
	public function addCustomerDiscount($data) {
      	$this->db->query("INSERT INTO " . DB_PREFIX . "customer_discount SET " .
            "email = '" . $this->db->escape($data['email']) . "'" .
            ", `ip` = '" . $this->db->escape($data['ip']) . "'" .
            ", status = '" . (int)$data['status'] . "'" .
            ", date_added = NOW()");

      	$customer_discount_id = $this->db->getLastId();

        return $customer_discount_id;
	}

	public function getTotalCustomerDiscountsByIp($ip) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_discount WHERE ip = '" . $this->db->escape($ip) . "'");

		return $query->row['total'];
	}

}
?>