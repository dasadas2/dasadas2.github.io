<?php
class ModelToolUpdatePrice extends Model {
	public function getProducts($source = 1) {

        $sql  = "SELECT p.product_id, p.price, p.price_import FROM " . DB_PREFIX . "product p ";
        $sql .= "WHERE p.status = '1' ";
        $sql .= "AND p.source = '" . (int)$source ."'";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getUpdateProductPrice($product_id, $data) {
	    $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . (float)$data['price'] . "', percent = '" . (float)$data['percent'] . "' WHERE product_id = '" . (int)$product_id . "'");
    }

}
?>
