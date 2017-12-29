<?php

static $config = NULL;
static $log = NULL;

// Error Handler
function error_handler_for_export($errno, $errstr, $errfile, $errline) {
	global $config;
	global $log;

	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$errors = "Notice";
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$errors = "Warning";
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$errors = "Fatal Error";
			break;
		default:
			$errors = "Unknown";
			break;
	}

	if (($errors=='Warning') || ($errors=='Unknown')) {
		return true;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $errors . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}

	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $errors . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}


function fatal_error_shutdown_handler_for_export()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		// fatal error
		error_handler_for_export(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}

class ModelToolCSVExport extends Model {
	private $CSV_SEPARATOR = ';';
	private $CSV_ENCLOSURE = '"';
	private $data = array();
	
	public function import($fn) {

		if (($handle = fopen($fn, "r")) !== FALSE) {
			$row = 0;
		    
		    while (($data = fgetcsv($handle, 1000, $this->CSV_SEPARATOR, $this->CSV_ENCLOSURE)) !== FALSE) {
				$num = count($data);
				$row++;
				$item = array();
				
				for ($c=0; $c < $num; $c++) {
					$item[] = $data[$c];
				}

				// Update Price
				if( count($item) == 6 ) {
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[4].'", price = '.$item[5].' WHERE product_id = '.(int)$item[0];
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[4].'", price = '.$item[5].' WHERE product_id = '.(int)$item[0]);
				} elseif ( count($item) == 3 ){
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[1].'", price = '.$item[2].' WHERE model = "'.$item[0].'"';
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET quantity = "'.$item[1].'", price = '.$item[2].' WHERE model = "'.iconv('cp1251', 'UTF-8', $item[0]).'"');
				}elseif ( count($item) == 2 ){
					//$sql = 'UPDATE '. DB_PREFIX . 'product SET price = '.$item[1].' WHERE model = "'.$item[0].'"';
					$this->db->query('UPDATE '. DB_PREFIX . 'product SET price = '.$item[1].' WHERE model = "'.iconv('cp1251', 'UTF-8', $item[0]).'"');
				}
				
				unset($item);
			}
		    fclose($handle);
		}
		$this->cache->delete('product');
	}
	
	function download() {
		global $config;
		global $log;
		$config = $this->config;
		$log = $this->log;
		set_error_handler('error_handler_for_export',E_ALL);
		register_shutdown_function('fatal_error_shutdown_handler_for_export');
		$database =& $this->db;
		//$languageId = $this->getDefaultLanguageId($database);

		// We use the package from http://pear.php.net/package/Spreadsheet_Excel_Writer/
		chdir( '../system/pear' );
		require_once "Spreadsheet/Excel/Writer.php";
		chdir( '../../admin' );

		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		$workbook->setTempDir(DIR_CACHE);
		$workbook->setVersion(8); // Use Excel97/2000 BIFF8 Format
		$priceFormat =& $workbook->addFormat(array('Size' => 10,'Align' => 'right','NumFormat' => '######0.00'));
		$boxFormat =& $workbook->addFormat(array('Size' => 10,'vAlign' => 'vequal_space' ));
		$weightFormat =& $workbook->addFormat(array('Size' => 10,'Align' => 'right','NumFormat' => '##0.00'));
		$textFormat =& $workbook->addFormat(array('Size' => 10, 'NumFormat' => "@" ));

		// sending HTTP headers
		$workbook->send('export_products.xls');

		// Creating the products worksheet
		$worksheet =& $workbook->addWorksheet('Products');
		$worksheet->setInputEncoding ( 'UTF-8' );
		//$this->populateProductsWorksheet( $worksheet, $database, $priceFormat, $boxFormat, $weightFormat, $textFormat );
		$worksheet->freezePanes(array(1, 1, 1, 1));

		// Let's send the file
		$workbook->close();

		// Clear the spreadsheet caches
		$this->clearSpreadsheetCache();
		exit;
	}



	public function export($product_category, $format_type, $heading, $image_prepend = '') {
		$output = '';
		$search = array(';', '');

		$headings_info = array(
			'csv_import_field_name' => 'name',
			'csv_import_field_meta_desc' => 'meta_description',
			'csv_import_field_meta_keyw' => 'meta_keyword',
			'csv_import_field_image' => 'image',
			//'csv_import_field_additional_image' => 'product_image',
			'csv_import_field_price' => 'price',
			'csv_import_field_special_price' => 'product_special',
			'csv_import_field_desc' => 'description',
			//'csv_import_field_cat' => 'category',
			'csv_import_field_manu' => 'manufacturer',
			//'csv_import_field_attribute' => 'product_attribute',
			'csv_import_field_model' => 'model',
			'csv_import_field_sku' => 'sku',
			'csv_import_field_quantity' => 'quantity',
			'csv_import_field_quantity_class_id' => 'quantity_class_id',
			'csv_import_field_weight' => 'weight',
			'csv_import_field_length' => 'length',
			'csv_import_field_height' => 'height',
			'csv_import_field_width' => 'width',
			'csv_import_field_location' => 'location',
			'csv_import_field_keyword' => 'keyword',
			'csv_import_field_tags' => 'tag',
			'csv_import_field_upc' => 'upc',
			'csv_import_field_ean' => 'ean',
			'csv_import_field_jan' => 'jan',
			'csv_import_field_points' => 'points',
			'csv_import_field_option' => 'option'
		);

		if($product_category) {
			$where = ' AND (';
			foreach ($product_category as $category) {
				$where .= " p2c.category_id = '".$category."' OR ";
			}
			$where .= " p2c.category_id = '".$category."')";
			$sql = "SELECT DISTINCT p.product_id, p.model, p.quantity, p.price, pd.name, m.name AS manufacturer FROM " . DB_PREFIX . "product p
				    LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				    LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
				    LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
				    WHERE pd.language_id = '" . (int)$this->config->get('config_language_id'). "'" . $where." ORDER BY pd.name";
		} else {
			$sql = "SELECT p.product_id, p.model, p.quantity, p.price, pd.name, m.name AS manufacturer FROM " . DB_PREFIX . "product p 
				    LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				    LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
				    WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name DESC" ;
		}
		$query = $this->db->query($sql);
        
        if ($format_type == 'xml') {
            $output .= '<?xml version="1.0" encoding="windows-1251"?>';
            $output .= "\n";
            $output .= '<channel>';
            $output .= "\n";

            $delimiter = "\n";
        } else {
            $delimiter = ";";

            $csv_headig = '';

            $csv_headig .= $heading['csv_import_field_cat'][0][0] . $delimiter;

    		foreach ($headings_info as $key => $value) {
                if (!empty($heading[$key])) {
                    $csv_headig .= $heading[$key] . $delimiter;
                }
            }

            if (!empty($heading['csv_import_field_additional_image'])) {
                $csv_headig .= $heading['csv_import_field_additional_image'][0] . $delimiter;
            }

            foreach ($heading['csv_import_field_attribute'] as $item) {
                $csv_headig .= $item . $delimiter;
            }

            $csv_headig .= "\n";

            $output .= $csv_headig;
        }

        
		foreach ($query->rows as $result) {
           
            $product_info = $this->getProductInfo($result['product_id']);

            if ($format_type == 'xml') {

                $href = str_replace('&', '&amp;', $product_info['href']);

                $output .= '  <offer>' . $delimiter;
                $output .= '    <category>' . $product_info['category'] . '</category>' . $delimiter;
                $output .= '    <firm>'     . $result['manufacturer'] . '</firm>' . $delimiter;
                $output .= '    <model>'    . $product_info['model'] . '</model>' . $delimiter;
                $output .= '    <price>'    . $product_info['price'] . '</price>' . $delimiter;
                $output .= '    <warranty>' . '12' . '</warranty>' . $delimiter;
                $output .= '    <url>'      . $href . '</url>' . $delimiter;
                $output .= '    <name>'     . $product_info['name'] . '</name>' . $delimiter;
                $output .= '    <color>'    . '</color>' . $delimiter;
                $output .= '  </offer>' . $delimiter;
                
            } else {

                $csv_line  = '';

                $csv_line .= $product_info['category'] . $delimiter;

          		foreach ($headings_info as $key => $value) {
                    if (!empty($heading[$key])) {
                        if ($value == 'description') {
                            $csv_line .= '"' . $product_info[$value] . '"';
                        } elseif ($value == 'image') {
                            $csv_line .= $product_info[$value];
                            foreach($product_info['images'] as $image){
                                $csv_line .= ',' . $image;
                            }
                        } else {
                            $csv_line .= $product_info[$value];
                        }
                        $csv_line .= $delimiter;
                    }
                }

          		foreach ($heading['csv_import_field_additional_image'] as $item) {
                    $csv_line .= str_replace($image_prepend, '', $product_info['image']);
                    foreach($product_info['images'] as $image){
                        $csv_line .= ',' . str_replace($image_prepend, '', $image);
                    }
                }
                $csv_line .= $delimiter;

          		foreach ($heading['csv_import_field_attribute'] as $item) {
                    if (isset($product_info['attribute'][$item])) {
                        $csv_line .= $product_info['attribute'][$item];
                    }
                    $csv_line .= $delimiter;
                }


                $csv_line .= "\n";
                $output .= $csv_line;
            }

		}
        
        if ($format_type == 'xml') {

            $output .= '</channel>';
            $output .= "\n";
        }

		return iconv('UTF-8', 'cp1251//TRANSLIT', $output);
		//return $output;
	}

	public function getProductInfo($product_id) {
    
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

        $result         = $this->model_catalog_product->getProduct($product_id);
        $result_d       = $this->model_catalog_product->getProductDescriptions($product_id);
        $result_m       = $this->model_catalog_manufacturer->getManufacturer($result['manufacturer_id']);

        if ($result_m) {
            $manufacturer = $result_m['name'];
        } else {
            $manufacturer = '';
        }

        $result_a  = $this->getAttributesByProductId($product_id);
        $attribute = array();
        foreach($result_a as $item) {
            $attribute[$item['name']] = $item['text'];
        }

        $result_i = $this->model_catalog_product->getProductImages($product_id);
        $images   = array();
        foreach($result_i as $item) {
            $images[] = $item['image'];
        }

        $language_id      = $this->config->get('config_language_id');

		$description      = $result_d[$language_id]['description'];
        $name             = $this->html_special_replace($result['name']);

        $description      = $this->html_special_replace($description);

        $name             = trim(strip_tags( $name ));
        $description      = trim(strip_tags( $description ));

        $categories = $this->model_catalog_product->getProductCategories($result['product_id']);
        
        $category_name = '';
        foreach ($categories as $category) { 
            if (!empty($category)) {
                $path = $this->getPath($category);
                if (!empty($category_name)) $category_name .= ',';
                $category_name .= $path;

                break;
            }
        }
        
        $category_name = str_replace('&gt;', '-', $category_name);
        
        $product = $result;

        $product['manufacturer'] = $manufacturer;
        $product['category']     = $category_name;
        $product['attribute']    = $attribute;
        $product['images']       = $images;

        //echo '<pre>';
        //print_r($product);
        //echo '</pre>';

        return $product;
    }

	public function getPath($category_id) {

		$query = $this->db->query("SELECT name, parent_id FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, cd.name ASC");

        if (!isset($query->row['parent_id']))
            return '';

		if ($query->row['parent_id']) {

			return $this->getPath($query->row['parent_id'], $this->config->get('config_language_id')) . ',' . $query->row['name'];

		} else {

			return $query->row['name'];

		}

	}

    function html_special_replace($string)
    {
        $array_search  = array( '&laquo;', '&raquo;', '&lt;', '&gt;', '&amp;', '&#039;', '&quot;', '&lt;', '&gt;', '&ndash;', '&nbsp;', '&trade;', '&times;' );
        $array_replace = array( '<',       '>',       '<',    '>',    '&',     '\'',     '\'',     '<',    '>',    '-',       ' ',      '(tm)',    'x' );
        
        $string = str_replace($array_search, $array_replace, htmlspecialchars_decode($string, ENT_NOQUOTES)); 
    
        return $string; 
       
    }

	public function getAttributesByProductId($product_id) {

        $sql = "";
        $sql .= "SELECT DISTINCT ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id = pa.attribute_id) ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (ad.attribute_id = pa.attribute_id) ";
        $sql .= "WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
        $sql .= "AND pa.product_id = '" . (int)$product_id . "' ";
        $sql .= "ORDER BY a.sort_order ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getAttributeValueByProductId($product_id, $name) {

        $sql = "";
        $sql .= "SELECT DISTINCT pa.text FROM " . DB_PREFIX . "product_attribute pa ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "attribute a ON (a.attribute_id = pa.attribute_id) ";
        $sql .= "LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (ad.attribute_id = pa.attribute_id) ";
        $sql .= "WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
        $sql .= "AND pa.product_id = '" . (int)$product_id . "' ";
        $sql .= "AND LCASE(ad.name) = '" . $this->db->escape(utf8_strtolower($name)) . "' ";

		$query = $this->db->query($sql);

		return $query->row['text'];
	}

}
?>
