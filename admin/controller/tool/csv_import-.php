<?php
#####################################################################################
#  Module CSV IMPORT PRO for Opencart 1.5.x From HostJars opencart.hostjars.com 	#
#####################################################################################

class ControllerToolCsvImport extends Controller {
	private $error = array();
	private $field_names = array();
	private $total_items_added = 0;
	private $total_items_updated = 0;
	private $total_items_skipped = 0;
	private $total_items_missed = 0;
    public  $date_available = false;

	private $discounts = array();

	public function index() {
		$this->load->language('tool/csv_import');

		$this->document->setTitle($this->language->get('heading_title'));


		$this->load->model('setting/setting');
		$this->load->model('localisation/stock_status'); //For getStockStatuses()
		$this->load->model('localisation/language'); //For getLanguages()
		$this->load->model('localisation/length_class'); //For getLengthClasses()
		$this->load->model('localisation/weight_class'); //For getWeightClasses()	
		$this->load->model('localisation/tax_class'); //For getTaxClasses()	
		$this->load->model('tool/csv_import'); //For getManufacturerId() and getCategoryId()
		$this->load->model('setting/store'); //For getStores()
		$this->load->model('catalog/product'); //For addProduct() and getProductCategories()
		$this->load->model('catalog/category'); //For addCategory()
		$this->load->model('catalog/manufacturer'); //For addManufacturer()
		$this->load->model('catalog/option');               //For addOption()
		$this->load->model('catalog/attribute'); //For addAttribute()
		$this->load->model('catalog/attribute_group'); //For addAttributeGroup()
		

		// DON'T SAVE US TO CONFIG AS IS
		$dont_save = array(
			'csv_import_store',
			'csv_import_field_cat',
			'csv_import_field_additional_image',
			'csv_import_field_attribute'
		);
		
		// GET CSV AND BULK SETTINGS
		$csv_settings = array(
			'csv_import_delimiter',
			'csv_import_stock_status_id',
			'csv_import_length_class',
			'csv_import_weight_class',
			'csv_import_tax_class',
			'csv_import_subtract',
			'csv_import_product_status',
			'csv_import_language',
			'csv_import_store',
			'csv_import_remote_images',
			'csv_import_type',
			'csv_import_update_field',
			'csv_import_feed_url',
			'csv_import_ignore_field',
			'csv_import_ignore_value',
			'csv_import_price_multiplier',
			'csv_import_image_remove',
			'csv_import_image_prepend',
			'csv_import_image_append',
			'csv_import_split_category',
            'csv_import_convert_status',
			'csv_import_top_categories',
			'csv_import_unzip_feed'
		);
		foreach ($csv_settings as $setting) {
			if (isset($this->request->post[$setting])) {
				$this->data[$setting] = $this->request->post[$setting];
				if (in_array($setting, $dont_save)) {
					$this->request->post[$setting] = json_encode($this->request->post[$setting]);
				}
			} else {
				$this->data[$setting] = $this->config->get($setting);
				if (in_array($setting, $dont_save)) {
					$this->data[$setting] = json_decode($this->data[$setting]);
				}
			}
		}

		// GET HEADINGS INFO
		$headings_info = array(
			'csv_import_field_name' => 'name',
			'csv_import_field_meta_desc' => 'meta_description',
			'csv_import_field_meta_keyw' => 'meta_keyword',
			'csv_import_field_image' => 'image',
			'csv_import_field_additional_image' => 'product_image',
			'csv_import_field_price' => 'price',
			'csv_import_field_import_price' => 'price_import',
			'csv_import_field_special_price' => 'product_special',
			'csv_import_field_desc' => 'description',
			'csv_import_field_cat' => 'category',
			'csv_import_field_manu' => 'manufacturer',
			'csv_import_field_attribute' => 'product_attribute',
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
			'csv_import_field_tags' => 'product_tag',
			'csv_import_field_upc' => 'upc',
			'csv_import_field_points' => 'points',
			'csv_import_field_option' => 'option'
		);

		foreach ($headings_info as $key => $value) {
			if (isset($this->request->post[$key])) {
				$this->data[$key] = $this->request->post[$key];
				$this->field_names[$value] = $this->request->post[$key];
				if (in_array($key, $dont_save)) {
					$this->request->post[$key] = json_encode($this->request->post[$key]);
				}
			} else {
				$this->data[$key] = $this->config->get($key);
				if (in_array($key, $dont_save)) {
					$this->data[$key] = json_decode($this->data[$key]);
				}
			}
		}
		
		// DO THE IMPORT
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			
			$this->model_setting_setting->editSetting('csv_import', $this->request->post);

			$filename = $this->fetchFeed();

			if ($filename) {
				//now we know there is a file, we will delete existing products if necessary
				if ($this->request->post['csv_import_type'] == 'reset') {
					$this->model_tool_csv_import->emptyTables();
				}
				
				$this->import($filename);

				$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->total_items_added, $this->total_items_updated, $this->total_items_skipped, $this->total_items_missed);
				if (!($this->total_items_added + $this->total_items_updated + $this->total_items_missed + $this->total_items_skipped)) {
					$this->session->data['success'] .= '<br/>Mac users: Make sure you save your file as CSV (Windows), not the default CSV format.';
				}
				$this->redirect($this->url->link('tool/csv_import', 'token=' . $this->session->data['token'], 'SSL'));
			}
			else {
				//no file or empty file, send warning
				$this->error['warning'] = $this->language->get('error_empty');
			}
		}

		// GET OTHER INFO
		$this->data['stock_status_selections'] = $this->model_localisation_stock_status->getStockStatuses();
		$this->data['language_selections'] = $this->model_localisation_language->getLanguages();
		$this->data['num_stores'] = $this->model_setting_store->getTotalStores();
		$this->data['store_selections'] = $this->model_setting_store->getStores();
		$this->data['weight_class_selections'] = $this->model_localisation_weight_class->getWeightClasses();
		$this->data['length_class_selections'] = $this->model_localisation_length_class->getLengthClasses();
		$this->data['tax_class_selections'] = $this->model_localisation_tax_class->getTaxClasses();

		// SPECIFY REQUIRED LANGUAGE TEXT
		$language_info = array(
     		'introcsv',
			'heading_title',
			'tab_config',
			'tab_map',
			'tab_adjust',
			'tab_import',
			'tab_export',
			'button_import',
            'button_export',
			'button_save',
			'button_cancel',
			'entry_import_file',
			'entry_import_url',
			'entry_ignore_fields',
			'entry_store',
			'entry_remote_images',
			'entry_remote_images_warning',
			'entry_language',
			'entry_stock_status',
			'entry_weight_class',
			'entry_length_class',
			'entry_tax_class',
			'entry_field_mapping',
			'entry_subtract',
			'entry_product_status',
			'entry_format',
			'entry_delimiter',
			'entry_data_feed',
			'entry_escape',
			'entry_qualifier',
			'entry_import_type',
			'entry_price_multiplier',
			'entry_image_remove',
			'entry_image_prepend',
			'entry_image_append',
			'entry_split_category',
			'entry_top_categories',
            'entry_convert_status',
			'text_add',
			'text_reset',
			'text_update',
			'text_update2',
			'text_update_explain',
			'text_field_oc_title',
			'text_field_csv_title',
			'text_field_name',
			'text_field_price',
			'text_field_import_price',
			'text_field_special_price',
			'text_field_model',
			'text_field_sku',
			'text_field_upc',
			'text_field_points',
			'text_field_manufacturer',
			'text_field_attribute',
			'text_field_category',
			'text_field_quantity',
			'text_field_quantity_class_id',
			'text_field_image',
			'text_field_additional_image',
			'text_field_description',
			'text_field_meta_desc',
			'text_field_meta_keyw',
			'text_field_weight',
			'text_field_length',
			'text_field_height',
			'text_field_width',
			'text_field_location',
			'text_field_keyword',
			'text_field_tags',
			'text_field_option',
            'text_yes',
            'text_no',
            'text_select_all',
            'text_unselect_all',

            'entry_export',
            'entry_category',
            'entry_category_help',
            'entry_format',
            'text_notes',
		);
		
		// GET REQUIRED LANGUAGE TEXT
		foreach ($language_info as $language) {
			$this->data[$language] = $this->language->get($language); 
		}
		
		// Warning or success message
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		// BCT
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('tool/csv_import', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		// Control buttons in admin
		$this->data['action'] = $this->url->link('tool/csv_import', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['export'] = $this->url->link('tool/csv_import/export', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('catalog/category');
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);

		// Render resposne with header and footer
		$this->template = 'tool/csv_import.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
		$this->response->setOutput($this->render());
	}
	
	private function import($file) {

		$this->load->model('localisation/discount');

		$results = $this->model_localisation_discount->getDiscounts();

		foreach ($results as $result) {
			$this->discounts[] = array(
				'discount_id'   => $result['discount_id'],
				'min_price'     => $result['min_price'],
				'max_price'     => $result['max_price'],
				'percent'       => $result['percent'],
				'sort_order'    => $result['sort_order'],
			);
		}

	    while (($raw_prod = $this->getNextProduct($file)) !== FALSE)
	    {
			$this->resetDefaultValues();

			if (isset($this->request->post['csv_import_delayed_date'])) {
                $delayed_date = $this->request->post['csv_import_delayed_date'];

                if (isset($this->request->post['csv_import_delayed_days']) && $this->request->post['csv_import_delayed_days']) {
                    $delayed_days = $this->request->post['csv_import_delayed_days'];
                    if ($delayed_days > 1) {
                        $delayed_days = rand(0, $delayed_days);
                    }
                } else {
                    $delayed_days = 0;
                }

			    $this->date_available = date('Y-m-d', strtotime($delayed_date) + (3600*24*$delayed_days) );
            }

			//skip if ignore_field == ignore_value
			if (isset($this->request->post['csv_import_ignore_field']) && isset($raw_prod[$this->request->post['csv_import_ignore_field']])) {
				if ($raw_prod[$this->request->post['csv_import_ignore_field']] == $this->request->post['csv_import_ignore_value']) {
					$this->total_items_skipped++;
					continue;
				}
			}
			
	    	//price - remove leading $ or pound or euro symbol, remove any commas.
			foreach (array('price', 'cost') as $price) {
				if (isset($this->field_names[$price]) && isset($raw_prod[$this->field_names[$price]])) {
					$raw_prod[$this->field_names[$price]] = preg_replace('/^[^\d]/', '', $raw_prod[$this->field_names[$price]]);
					$raw_prod[$this->field_names[$price]] = str_replace(',', '', $raw_prod[$this->field_names[$price]]);
				}
			}
			
			
			//ADMIN STEP 3: MODIFY DATA
			//price multiplier
			if ($this->request->post['csv_import_price_multiplier'] && isset($raw_prod[$this->field_names['price']])) {
				$raw_prod[$this->field_names['price']] *= $this->request->post['csv_import_price_multiplier'];
			}

            if (isset($this->field_names['image']) && isset($this->field_names['product_image'])) {
    			//image adjustments
    	    	$image_fields = array_merge(array($this->field_names['image']), $this->field_names['product_image']);
        		foreach ($image_fields as $img) {
    	    		if (isset($raw_prod[$img]) && $raw_prod[$img] != '') {
    		    		//image remove
    					$raw_prod[$img] = str_replace($this->request->post['csv_import_image_remove'], '', $raw_prod[$img]);
    					//image prepend & append
    					$raw_prod[$img] = $this->request->post['csv_import_image_prepend'] . $raw_prod[$img] . $this->request->post['csv_import_image_append'];
    					//fetch image
    					if ($this->request->post['csv_import_remote_images']) {
    						$raw_prod[$img] = $this->fetchImage($raw_prod[$img]);
    					}
    	    		}
    	    	}
            }

			// Is this an update?
			$update_id = 0;
			if ($this->request->post['csv_import_type'] == 'update' OR
                $this->request->post['csv_import_type'] == 'update2') {
				$update_value = $raw_prod[$this->field_names[$this->request->post['csv_import_update_field']]];
				$update_id = $this->model_tool_csv_import->getProductID($this->request->post['csv_import_update_field'], $update_value);
			}
			

			//UPDATE A PRODUCT:
			if ($update_id) {
				$product = $this->updateProduct($update_id, $raw_prod);
				$this->model_catalog_product->editProduct($update_id, $product);
				$this->total_items_updated++;
			}
			//ADD A PRODUCT
			else {

                if ($this->request->post['csv_import_type'] == 'update2') {
                    $this->total_items_skipped++;
                    continue;
                }

				$product = $this->addProduct($raw_prod);
				$this->model_catalog_product->addProduct($product);
				$this->total_items_added++;
			}
	    }

	    fclose($this->fh);
	}
	
	private function getNextProduct($file)
	{
		if (!$this->fh) {
			$this->delim = $this->request->post['csv_import_delimiter'];
			if ($this->delim == '\t' ) {
				$this->delim = "\t";
			} elseif ($this->delim == '') {
				$this->delim = ';';
			}

            if ($this->data['csv_import_convert_status'] ) {
                $content = iconv('windows-1251', 'UTF-8', file_get_contents($file));
                $this->fh = fopen('php://memory', 'r+');
                fputs($this->fh, $content);
                rewind($this->fh);
            } else {
    			$this->fh = fopen($file, 'r');
            }

			//$this->headings = fgetcsv($this->fh, 0, $this->delim);
            $this->headings = $this->fgetcsv($this->fh, 65536, $this->delim);

			$this->num_cols = count($this->headings);
		}
		//missed product if num columns in this row not the same as num headings
	    while (($row = $this->fgetcsv($this->fh, 65536, $this->delim)) !== FALSE && count($row) != $this->num_cols) {
		//while (($row = fgetcsv($this->fh, 0, $this->delim)) !== FALSE && count($row) != $this->num_cols) {
			$this->total_items_missed++;
    	}

    	return ($row === FALSE) ? FALSE : array_combine($this->headings, $row);
	}
	
	
	private function addProduct(&$raw_prod) {

    	$product = array();  // will contain new product to add
		//categories
		$multi_categories = array();
		foreach ($this->field_names['category'] as $category_field) {
			$categories = array();
			if (isset($this->request->post['csv_import_split_category']) && $this->request->post['csv_import_split_category']) {
				if ($this->request->post['csv_import_split_category'] == '&gt;') {
					$this->request->post['csv_import_split_category'] = '>';
				}
				$categories = explode($this->request->post['csv_import_split_category'], $raw_prod[$category_field[0]]);
			} else {
				//normal categories:
				foreach ($category_field as $cat) {
					if (isset($raw_prod[$cat])) $categories[] = $raw_prod[$cat];
				}
			}
			$multi_categories = array_merge($multi_categories, $this->getCategories($categories));
		}
		//if (!empty($multi_categories)) {
		//	$this->field_names['product_category'] = 'product_category';
		//	$raw_prod['product_category'] = array_unique($multi_categories);
		//}
		//if (!empty($raw_prod['product_category'])) {
		//	foreach ($raw_prod['product_category'] as $category_id) {
        //        $product['main_category_id'] = $category_id;
        //    }
		//}

		if (!empty($multi_categories)) {
			$this->field_names['product_category'] = 'product_category';
			$raw_prod['product_category'] = array_unique($multi_categories);
		}

    	if (!empty($raw_prod['product_category'])) {
            $last_category = end($raw_prod['product_category']);
            $product['main_category_id'] = $last_category;

            unset($raw_prod['product_category']);
            $raw_prod['product_category'][] = $last_category;
        }

		//end categories

		//manufacturer
		if (isset($raw_prod[$this->field_names['manufacturer']])) {
			$raw_prod['manufacturer_id'] = $this->getManufacturer($raw_prod[$this->field_names['manufacturer']]);
			$this->field_names['manufacturer_id'] = 'manufacturer_id';
		}
		//end manufacturer

        if (isset($this->field_names['product_attribute'])) {
    		//product attributes
    		$input_attributes = array();
    		foreach ($this->field_names['product_attribute'] as $attr) {
    			if (isset($raw_prod[$attr]) && $raw_prod[$attr] != '') {
    				$input_attributes[$attr] = $raw_prod[$attr];
    			}
    		}
    		$attributes = $this->getAttributes($input_attributes);
    		if (!empty($attributes)) {
    			$product['product_attribute'] = $attributes;
    		}
    		// end product attributes
        }
		
		// loop over prod_data array adding product table data
		foreach ($this->prod_data as $field => $default_value) {
			if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
				$product[$field] = $raw_prod[$this->field_names[$field]];
			}
			else {
				$product[$field] = $default_value;
			}
		}
		// loop over desc_data array adding description table data
		foreach ($this->desc_data as $field => $default_value) {
			if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
				$product['product_description'][$this->request->post['csv_import_language']][$field] = $raw_prod[$this->field_names[$field]];
			}
			else {
				$product['product_description'][$this->request->post['csv_import_language']][$field] = $default_value;
			}
		}
		
		//SEO Keyword
		if (isset($this->field_names['keyword']) && isset($raw_prod[$this->field_names['keyword']])) {
			$product['keyword'] = $raw_prod[$this->field_names['keyword']];
		}

        // Date Available
        if ($this->date_available) {
            $product['date_available'] = $this->date_available;
        }

		//Product Tags
		if (isset($this->field_names['product_tag']) && isset($raw_prod[$this->field_names['product_tag']])) {
			$product['product_tag'][$this->request->post['csv_import_language']] = $raw_prod[$this->field_names['product_tag']];
		} else {
			$product['product_tag'] = array();
		}
		//Product Specials
		$preserved_price_field_name = $this->field_names['price'];
		if (isset($this->field_names['product_special']) && isset($raw_prod[$this->field_names['product_special']]) && $raw_prod[$this->field_names['product_special']] != '') {
			$new_special = array();
			$this->field_names['price'] = $this->field_names['product_special']; // we're done with price now, need to hijack it for special_price table.
			foreach ($this->special_data as $field => $default_value) {
				if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
					$new_special[$field] = $raw_prod[$this->field_names[$field]];
				}
				else {
					$new_special[$field] = $default_value;
				}
			}
			$product['product_special'][] = $new_special;
		}
		$this->field_names['price'] = $preserved_price_field_name;

        //----------------------------------------------------------------------

            $price_in  = str_replace(',', '.', $raw_prod['price']);
            $price_out = str_replace(',', '.', $raw_prod['price']);
            $percent   = 0.0;

            foreach($this->discounts as $discount) {
                $price_in = $raw_prod['price'];
                if ($price_in > $discount['min_price'] && $price_in <= $discount['max_price']) {
                    $price_out = $price_in + ($price_in * (float)$discount['percent'])/100.0;
                    $percent = (float)$discount['percent'];
                    break;
                }
            }

            $product['price_import'] = $price_in;
            $product['price']        = $price_out;
            $product['percent']      = $percent;

        //----------------------------------------------------------------------

        if (isset($this->field_names['product_image'])) {
    		//Additional Images
    		$product['product_image'] = array();
    		foreach ($this->field_names['product_image'] as $image) {
    			if (isset($raw_prod[$image]) && $raw_prod[$image]) {

                    $image_array = explode(',', $raw_prod[$image]);
                    foreach($image_array as $image) {
        				$product['product_image'][] = array('sort_order' => '', 'image' => trim($image));
                    }

    			}
    		}

            if (count($product['product_image']) > 0) {
                $first_image = array_shift($product['product_image']);
                $product['image'] = $first_image['image'];
    		}

    		if (empty($product['product_image'])) {
    			unset($product['product_image']);
    		}
        }


		//SEO Keyword
		//if (isset($this->field_names['keyword']) && isset($raw_prod[$this->field_names['keyword']])) {
		//	$product['keyword'] = $raw_prod[$this->field_names['keyword']];
		//}


		if (isset($this->field_names['option']) && isset($raw_prod[$this->field_names['option']])) {

            // Options in one field with delimiter:
            // size=13:10,15:10,67:10^color=black:98,white:100
            unset($option_array);
            unset($option_values);
            unset($product_option);

            $option_array   = array();
            $option_values  = array();
            $product_option = array();
            $product['product_option'] = array();

            //echo $raw_prod[$this->field_names['option']] . '<br />';

            $option_array = explode('^', $raw_prod[$this->field_names['option']]);
            //print_r($option_array);
            //echo '<br /><br />';

            foreach($option_array as $option_block) {

                if (empty($option_block))
                    continue;

                //debug($option_block);
                $part = explode('=', $option_block);

                $part_name     = explode('|', $part[0]);
                $option_name   = $part_name[0];
                $option_type   = isset($part_name[1]) ? $part_name[1] : 'checkbox';
                $option_id     = $this->model_tool_csv_import->getOptionId($option_name);
                $option_values = explode(',', $part[1]);

                $product_option_value = array();
                foreach ($option_values as $option_value_part) {

                    $option_parts = explode(':', $option_value_part);
                    $option_value = $option_parts[0];
                    if (isset($option_parts[1])) {
                        $option_quantity = $option_parts[1];
                    } else {
                        $option_quantity = 1000;
                    }

                    $option_value_id = $this->model_tool_csv_import->getOptionValueId($option_value);
                    if ($option_value_id == 0) {

                        $option_value_data = array( 'image' => '', 'sort_order' => 0);
                        $option_value_descriptions[(int)$this->config->get('config_language_id')] = array( 'name' => $option_value);

                        $option_value_id = $this->model_tool_csv_import->addOptionValue($option_id, $option_value_data, $option_value_descriptions);

                    }

                    $product_option_value[] = array(
                        'option_value_id'   => $option_value_id,
                        'quantity'          => $option_quantity,
                        'subtract'          => 1,
                        'price'             => 0.0,
                        'price_prefix'      => '+',
                        'points'            => 0,
                        'points_prefix'     => '+',
                        'weight'            => 0.0,
                        'weight_prefix'     => 0
                    );
                }

	            $product_option[] = array(
                    'type'      => $option_type,
                    'option_id' => $option_id,
                    'required'  => 1,
                    'product_option_value' => $product_option_value
                );
            }
            $product['product_option'] = $product_option;
        }

        //print_r($product);
        //die('===');

		//NEW PRODUCT
		return $product;
	}

	private function updateProduct($update_id, &$raw_prod) {
		//update the product in place
		$product = $this->model_catalog_product->getProduct($update_id);
		$product['product_description'] = $this->model_catalog_product->getProductDescriptions($update_id);
		$product['product_category'] = $this->model_catalog_product->getProductCategories($update_id);
		$product['product_attribute'] = $this->model_catalog_product->getProductAttributes($update_id);
		$product['product_reward'] = $this->model_catalog_product->getProductRewards($update_id);
		$product['product_option'] = $this->model_catalog_product->getProductOptions($update_id);
		$product['product_download'] = $this->model_catalog_product->getProductDownloads($update_id);
		$product['product_related'] = $this->model_catalog_product->getProductRelated($update_id);
		$product['product_layout'] = $this->model_catalog_product->getProductLayouts($update_id);
		$product['product_discount'] = $this->model_catalog_product->getProductDiscounts($update_id);
		$product['product_store'] = $this->model_catalog_product->getProductStores($update_id);
		$product['product_tag'] = ''; //$this->model_catalog_product->getProductTags($update_id);
		// Product Specials
		$product_specials = $this->model_catalog_product->getProductSpecials($update_id);
		foreach ($product_specials as $product_special) {
			$new_special = array();
			foreach ($this->special_data as $field => $default_value) {
				$new_special[$field] = $product_special[$field];
			}
			$product['product_special'][] = $new_special;
		}
		$product['product_image'] = $this->model_catalog_product->getProductImages($update_id);
		// Additional Images from feed
		if ($this->field_names['product_image'][0]) {
			$product['product_image'] = array();
			foreach ($this->field_names['product_image'] as $image) {
				if (isset($raw_prod[$image]) && $raw_prod[$image]) {
					$product['product_image'][] = array('sort_order' => '', 'image' => $raw_prod[$image]);
				}
			}
		}
		if (empty($product['product_image'])) {
			unset($product['product_image']);
		}
		//Product Categories
		//categories
		$multi_categories = array();
		foreach ($this->field_names['category'] as $category_field) {
			$categories = array();
			if (isset($this->request->post['csv_import_split_category']) && $this->request->post['csv_import_split_category']) {
				if ($this->request->post['csv_import_split_category'] == '&gt;') {
					$this->request->post['csv_import_split_category'] = '>';
				}
				$categories = explode($this->request->post['csv_import_split_category'], $raw_prod[$category_field[0]]);
			} else {
				//normal categories:
				foreach ($category_field as $cat) {
					if (isset($raw_prod[$cat])) 
						$categories[] = $raw_prod[$cat];
				}
			}
			$multi_categories = array_merge($multi_categories, $this->getCategories($categories));
		}
		//if (!empty($multi_categories)) {
		//	$this->field_names['product_category'] = 'product_category';
		//	$raw_prod['product_category'] = array_unique($multi_categories);
		//}
		//if (!empty($raw_prod['product_category'])) {
		//	foreach ($raw_prod['product_category'] as $category_id) {
        //        $product['main_category_id'] = $category_id;
        //    }
		//}

		if (!empty($multi_categories)) {
			$this->field_names['product_category'] = 'product_category';
			$raw_prod['product_category'] = array_unique($multi_categories);
		}

  		if (!empty($raw_prod['product_category'])) {
            $last_category = end($raw_prod['product_category']);
            $product['main_category_id'] = $last_category;

            unset($raw_prod['product_category']);
            $raw_prod['product_category'][] = $last_category;
  		}
		//end categories
		
		//manufacturer
		if (isset($raw_prod[$this->field_names['manufacturer']])) {
			$raw_prod['manufacturer_id'] = $this->getManufacturer($raw_prod[$this->field_names['manufacturer']]);
			$this->field_names['manufacturer_id'] = 'manufacturer_id';
		}
		//end manufacturer

		//product attributes
		$input_attributes = array();
		foreach ($this->field_names['product_attribute'] as $attr) {
			if (isset($raw_prod[$attr]) && $raw_prod[$attr] != '') {
				$input_attributes[$attr] = $raw_prod[$attr];
			}
		}
		$attributes = $this->getAttributes($input_attributes);
		if (!empty($attributes)) {
			$product['product_attribute'] = $attributes;
		} elseif ($this->field_names['product_attribute'][0]) {
			unset($product['product_attribute']);
		}
		// end product attributes
		
		//Overwrite product data with imported data from csv
		// Product Data
		foreach ($this->prod_data as $field => $default_value) {
			if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
				$product[$field] = $raw_prod[$this->field_names[$field]];
			}
		}
		// Product Descriptions
		foreach ($this->desc_data as $field => $default_value) {
			if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
				$product['product_description'][$this->data['csv_import_language']][$field] = $raw_prod[$this->field_names[$field]];
			}
		}
		
		// SEO Keyword
		if (isset($this->field_names['keyword']) && isset($raw_prod[$this->field_names['keyword']])) {
			$product['keyword'] = $raw_prod[$this->field_names['keyword']];
		}

        // Date Available
        if ($this->date_available) {
            $product['date_available'] = $this->date_available;
        }

		// Product Tags
		if (isset($this->field_names['product_tag']) && isset($raw_prod[$this->field_names['product_tag']])) {
			$product['product_tag'][$this->request->post['csv_import_language']] = $raw_prod[$this->field_names['product_tag']];
		}
		// Product Special
		$preserved_price_field_name = $this->field_names['price'];
		if (isset($this->field_names['product_special']) && isset($raw_prod[$this->field_names['product_special']])) {
			$product['product_special'] = array(); // empty out current specials
			if ($raw_prod[$this->field_names['product_special']] != '') {
				$this->field_names['price'] = $this->field_names['product_special']; // we're done with price now, need to hijack it for special_price table.
				$new_special = array();
				foreach ($this->special_data as $field => $default_value) {
					if (isset($this->field_names[$field]) && isset($raw_prod[$this->field_names[$field]])) {
						$new_special[$field] = $raw_prod[$this->field_names[$field]];
					}
					else {
						$new_special[$field] = $default_value;
					}
				}
				$product['product_special'][] = $new_special;
			}
		}
		$this->field_names['price'] = $preserved_price_field_name;

        //----------------------------------------------------------------------

            $price_in  = str_replace(',', '.', $raw_prod['price']);
            $price_out = str_replace(',', '.', $raw_prod['price']);

            $percent   = 0.0;

            foreach($this->discounts as $discount) {
                $price_in = $raw_prod['price'];
                if ($price_in > $discount['min_price'] && $price_in <= $discount['max_price']) {
                    $price_out = $price_in + ($price_in * (float)$discount['percent'])/100.0;
                    $percent = (float)$discount['percent'];
                    break;
                }
            }

            $product['price_import'] = $price_in;
            $product['price']        = $price_out;
            $product['percent']      = $percent;

        //----------------------------------------------------------------------

        $product_option = array();
		if (isset($this->field_names['option']) && isset($raw_prod[$this->field_names['option']])) {

            // Options in one field with delimiter:
            // size=13:10,15:10,67:10^color=black:98,white:100
            unset($option_array);
            unset($option_values);
            unset($product_option);

            $option_array   = array();
            $option_values  = array();
            $product_option = array();
            $product['product_option'] = array();

            //echo $raw_prod[$this->field_names['option']] . '<br />';

            $option_array = explode('^', $raw_prod[$this->field_names['option']]);
            //print_r($option_array);
            //echo '<br /><br />';

            foreach($option_array as $option_block) {

                if (empty($option_block))
                    continue;

                //debug($option_block);
                $part = explode('=', $option_block);

                $part_name     = explode('|', $part[0]);
                $option_name   = $part_name[0];
                $option_type   = isset($part_name[1]) ? $part_name[1] : 'checkbox';
                $option_values = explode(',', $part[1]);

                $option_id          = $this->model_tool_csv_import->getOptionId($option_name);
                $product_option_id  = $this->model_tool_csv_import->getProductOptionId($update_id, $option_id);

                $product_option_value = array();
                foreach ($option_values as $option_value_part) {

                    $option_parts = explode(':', $option_value_part);
                    $option_value = $option_parts[0];
                    if (isset($option_parts[1])) {
                        $option_quantity = $option_parts[1];
                    } else {
                        $option_quantity = 1000;
                    }

                    $option_value_id = $this->model_tool_csv_import->getOptionValueId($option_value);
                    if ($option_value_id == 0) {

                        $option_value_data = array( 'image' => '', 'sort_order' => 0);
                        $option_value_descriptions[(int)$this->config->get('config_language_id')] = array( 'name' => $option_value);

                        $option_value_id = $this->model_tool_csv_import->addOptionValue($option_id, $option_value_data, $option_value_descriptions);
                    }

					$product_option_value_id  = $this->model_tool_csv_import->getProductOptionValueId($update_id, $option_id, $product_option_id);
                    $this->model_tool_csv_import->deleteProductOptionValueId($update_id, $option_id, $product_option_id);

                    $product_option_value[] = array(
                        'product_option_value_id' => $product_option_value_id,
                        'option_value_id'   => $option_value_id,
                        'quantity'          => $option_quantity,
                        'subtract'          => 1,
                        'price'             => 0.0,
                        'price_prefix'      => '+',
                        'points'            => 0,
                        'points_prefix'     => '+',
                        'weight'            => 0.0,
                        'weight_prefix'     => 0
                    );
                }
                $this->model_tool_csv_import->deleteProductOptionId($update_id, $option_id);

	            $product_option[] = array(
                    'product_option_id' => $product_option_id,
                    'type'      => $option_type,
                    'option_id' => $option_id,
                    'required'  => 1,
                    'product_option_value' => $product_option_value
                );
            }
        }

            $product['product_option'] = $product_option;

        //print_r($product);
        //die('');

		//UPDATED PRODUCT
		return $product;
	}

	private function fetchFeed() {
		if (is_uploaded_file($this->request->files['csv_import']['tmp_name'])) {
			//GET FEED FROM POSTED FILE
			$has_content = file_get_contents($this->request->files['csv_import']['tmp_name']);
			$filename = $this->request->files['csv_import']['tmp_name'];
		} 
		elseif (isset($this->request->post['csv_import_feed_url'])) {
			//GET FEED WITH CURL AND PARSE
			$ch = curl_init();
			$filename = "csv_feed.txt";
			$fp = fopen($filename, "w");
			curl_setopt($ch, CURLOPT_URL, $this->request->post['csv_import_feed_url']);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			if (isset($this->request->post['csv_import_unzip_feed'])) {
				$filename = $this->unzip($filename);
			}
			$has_content = file_get_contents($filename);
		}
		else {
			$has_content = false;
		}

		return (!$has_content) ? '' : $filename;
	}

	private function resetDefaultValues() {
		//required desc data
		$this->desc_data = array(
			'name' => 'No Title',
			'description' => '',
			'description2' => '',
			'short_description' => '',
			'meta_keyword' => '',
			'meta_description' => '',
            'seo_title' => '',
            'seo_h1' => '',
            'tag' => '',
		);

		//required product data
		$this->prod_data = array(
			'date_available' => date('Y-m-d', time()-86400),
			'model' => '',
			'sku'	=> '',
			'ean'	=> '',
			'jan'	=> '',
			'isbn'	=> '',
			'mpn'	=> '',
			'tag'	=> '',
			'upc'	=> '',
			'points'	=> 0,
			'location' => '',
			'manufacturer_id' => 0,
			'shipping' => 1,
			'image' => '',
			'quantity' => 1,
			'quantity_class_id' => 0,
			'minimum' => 1,
			'maximum' => 0,
			'subtract' => $this->data['csv_import_subtract'],
			'sort_order' => 1,
			'price' => 0.00,
			'price_import' => 0.00,
			'percent' => 0.00,
			'status' => $this->data['csv_import_product_status'],
			'tax_class_id' => $this->data['csv_import_tax_class'],
			'weight' => '',
			'weight_class_id' => $this->data['csv_import_weight_class'],
			'length' => '',
			'width' => '',
			'height' => '',
			'length_class_id' => $this->data['csv_import_length_class'],
			'product_category' => array(0),
			'keyword' => '',
			'stock_status_id' => $this->data['csv_import_stock_status_id'],
			'product_store' => $this->data['csv_import_store'],
            'currency_id' => '4',
			'source' => '1',
		);
		
		//required special price data
		$this->special_data = array(
			'customer_group_id' => 8,
			'priority' => 1,
			'price' => 0,
			'date_start' => date('Y-m-d', time()-86400),
			'date_end' => '0000-00-00',
		);
	}
	
	private function fetchImage($image_url) {
		$url_parts = explode("/", $image_url);
		$filename = "data/" . end($url_parts);
		if (!file_exists(DIR_IMAGE . $filename)) {
			$fp = fopen(DIR_IMAGE . $filename, 'w');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $image_url);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			fclose($fp);
			if($httpCode == 404) {
				unlink(DIR_IMAGE . $filename);
				$filename = '';
			}
		}
		return $filename;
	}

	private function getCategories($categories) {
		$parentid = 0;
		$temp_cat = array();
		foreach ($categories as $cat) {
			if ($cat != '') {
				$cat = str_replace('&', '&amp;', $cat);
				$cat = str_replace('&amp;amp;', '&amp;', $cat);
				$cat_id = (int)$this->model_tool_csv_import->getCategoryId($cat, $parentid);
				if ($cat_id == 0) {
					//doesn't exist so add it then get it's id
					$new_cat = array();
					$new_cat['parent_id'] = $parentid;
					$new_cat['top'] = ($parentid) ? 0 : $this->request->post['csv_import_top_categories'];
					$new_cat['sort_order'] = 0;
					$new_cat['status'] = 1;
					$new_cat['column'] = 1;
					$new_cat['keyword'] = $cat;
					$new_cat['category_description'][$this->request->post['csv_import_language']]['name'] = $cat;
					$new_cat['category_description'][$this->request->post['csv_import_language']]['description'] = '';
					$new_cat['category_description'][$this->request->post['csv_import_language']]['meta_description'] = '';
					$new_cat['category_description'][$this->request->post['csv_import_language']]['meta_keyword'] = '';
					$new_cat['category_description'][$this->request->post['csv_import_language']]['seo_title']  = $cat;
					$new_cat['category_description'][$this->request->post['csv_import_language']]['seo_h1']     = $cat;
					$new_cat['category_store'] = array($this->request->post['csv_import_store']);
					$this->model_catalog_category->addCategory($new_cat);
					$cat_id = (int)$this->model_tool_csv_import->getCategoryId($cat, $parentid);
				}
				$temp_cat[] = $cat_id;
				$parentid = $cat_id;
			}
		}
		return $temp_cat;
	}
	
	private function getManufacturer($manu) {
		$manu = str_replace('&', '&amp;', $manu);
		$manu = str_replace('&amp;amp;', '&amp;', $manu);
		$manu_id = $this->model_tool_csv_import->getManufacturerId($manu);
		if ($manu_id == 0) {
			//doesn't exist so add it then get its id
			$new_manu['name'] = $manu;
			$new_manu['keyword'] = '';
			$new_manu['sort_order'] = 1;
			$new_manu['manufacturer_description'][$this->request->post['csv_import_language']]['description'] = '';
			$new_manu['manufacturer_description'][$this->request->post['csv_import_language']]['meta_description'] = '';
			$new_manu['manufacturer_description'][$this->request->post['csv_import_language']]['meta_keyword'] = '';
			$new_manu['manufacturer_description'][$this->request->post['csv_import_language']]['seo_title'] = $manu;
			$new_manu['manufacturer_description'][$this->request->post['csv_import_language']]['seo_h1']    = $manu;
			$new_manu['manufacturer_store'] = array($this->request->post['csv_import_store']);
			$this->model_catalog_manufacturer->addManufacturer($new_manu);
			$manu_id = $this->model_tool_csv_import->getManufacturerId($manu);
		}
		return $manu_id;
	}
	
	private function getAttributes($input_attributes) {
		$attributes = array();
		foreach ($input_attributes as $name=>$value) {
			$name = ucwords($name);
			$name = str_replace('&', '&amp;', $name);
			$name = str_replace('&amp;amp;', '&amp;', $name);

			//find the attribute group based on the column name in the CSV feed
			$attr_group_id    = 0;
    		$default_group_id = $this->model_tool_csv_import->getAttributeGroupId('default_group');

            $attr_id = $this->model_tool_csv_import->getAttributeId($name, 0);
            if ($attr_id) {
                $attribute = $this->model_tool_csv_import->getAttribute($attr_id);
                if (isset($attribute)) {
                    $attr_group_id = $attribute['attribute_group_id'];
                }
            }

			if ($attr_group_id == 0) {
        		$attr_group_id = $this->model_tool_csv_import->getAttributeGroupIdByName($name);
            }

			if ($attr_group_id == 0) {
                $attr_group_id = $default_group_id;
            }

			if ($attr_group_id == 0) {
				//it doesn't exist, let's add it
				$attr_group['sort_order'] = 1;
				$attr_group['use_in_filter'] = 1;
				$attr_group['attribute_group_description'][$this->request->post['csv_import_language']]['name'] = 'default_group';
				$attr_group['attribute_group_description'][$this->request->post['csv_import_language']]['description'] = '';
				$this->model_catalog_attribute_group->addAttributeGroup($attr_group);
    			$attr_group_id = $this->model_tool_csv_import->getAttributeGroupId('default_group');
			}

			//find the attribute value based on the value in the attribute column in the CSV feed
			$attr_id = $this->model_tool_csv_import->getAttributeId($name, $attr_group_id);
			if ($attr_id == 0) {
				//it doesn't exist, let's add it
				$new_attr['attribute_group_id'] = $attr_group_id;
				$new_attr['sort_order'] = 1;
				$new_attr['use_in_filter'] = 1;
				$new_attr['output_mode']   = 1;
				$new_attr['attribute_description'][$this->request->post['csv_import_language']]['name'] = $name;
				$new_attr['attribute_description'][$this->request->post['csv_import_language']]['description'] = '';
				$new_attr['attribute_description'][$this->request->post['csv_import_language']]['value_list']  = '';
				$this->model_catalog_attribute->addAttribute($new_attr);
				$attr_id = $this->model_tool_csv_import->getAttributeId($name, $attr_group_id);
			}
			$new_attr = array(
				'attribute_id'=>$attr_id,
				'product_attribute_description'=>array($this->request->post['csv_import_language']=>array('text'=>$value))	
			);
			$attributes[] = $new_attr;
		}
		return $attributes;
	}
	
	private function unzip($file) 
	{
		$filename = $file;
		$zip = zip_open($file);
		if (is_resource($zip)) {
			$zip_entry = zip_read($zip);
			$filename = zip_entry_name($zip_entry);
			$fp = fopen($filename, "w");
		    if (zip_entry_open($zip, $zip_entry, "r")) {
		    	$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
		    	fwrite($fp,"$buf");
		    	zip_entry_close($zip_entry);
		    	fclose($fp);
		    }
			zip_close($zip);
		}
		return $filename;
	}
	
	private function validatePermission()
	{
		//have permission?
		if (!$this->user->hasPermission('modify', 'tool/csv_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return (!$this->error);
	}

	private function validate()
	{
		//have permission?
		if (!$this->user->hasPermission('modify', 'tool/csv_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		//if this is an update based on model, have you mapped a model field?
		elseif ($this->request->post['csv_import_type'] == 'update') {
			if ($this->request->post['csv_import_update_field'] == 'model') {
				if (!isset($this->request->post['csv_import_field_model']) || $this->request->post['csv_import_field_model'] == '') {
					$this->error['warning'] = sprintf($this->language->get('error_update_field_mapping'), 'Model', 'Model');
				}
			} 
			elseif ($this->request->post['csv_import_update_field'] == 'sku') {
				if (!isset($this->request->post['csv_import_field_sku']) || $this->request->post['csv_import_field_sku'] == ''){
					$this->error['warning'] = sprintf($this->language->get('error_update_field_mapping'), 'Sku', 'Sku');
				}
			} 
			elseif ($this->request->post['csv_import_update_field'] == 'name'){
				if (!isset($this->request->post['csv_import_field_name']) || $this->request->post['csv_import_field_name'] == ''){
					$this->error['warning'] = sprintf($this->language->get('error_update_field_mapping'), 'Name', 'Name');
				}
			}
		}
		//if you want to split on a delimiter, have you mapped a category field?
		elseif ($this->request->post['csv_import_split_category'] && !$this->request->post['csv_import_field_cat'][0]) {
			$this->error['warning'] = 'You have specified a Category Delimiter, but no Category field is mapped!';
		}
		//file upload may be too big?
		elseif ($this->request->files['csv_import']['name'] && !$this->request->files['csv_import']['tmp_name']) {
			$this->error['warning'] = 'The file upload failed - is the file larger than your webserver allows?';
		}

		return (!$this->error);
	}

	private function fgetcsv($f, $length, $d=";", $q='"') {
		$list = array();
		$st = fgets($f, $length);
		if ($st === false || $st === null) return $st;
		while ($st !== "" && $st !== false) {
			if ($st[0] !== $q) {
				# Non-quoted.
				list ($field) = explode($d, $st, 2);
				$st = substr($st, strlen($field)+strlen($d));
			} else {
				# Quoted field.
				$st = substr($st, 1);
				$field = "";
				while (1) {
					# Find until finishing quote (EXCLUDING) or eol (including)
					preg_match("/^((?:[^$q]+|$q$q)*)/sx", $st, $p);
					$part = $p[1];
					$partlen = strlen($part);
					$st = substr($st, strlen($p[0]));
					$field .= str_replace($q.$q, $q, $part);
					if (strlen($st) && $st[0] === $q) {
						# Found finishing quote.
						list ($dummy) = explode($d, $st, 2);
						$st = substr($st, strlen($dummy)+strlen($d));
						break;
					} else {
						# No finishing quote - newline.
						$st = fgets($f, $length);
					}
				}

			}
			$list[] = trim($field);
		}

		return $list;
	}

	public function export() {

		// DON'T SAVE US TO CONFIG AS IS
		$dont_save = array(
			'csv_import_store',
			'csv_import_field_cat',
			'csv_import_field_additional_image',
			'csv_import_field_attribute'
		);

		// GET HEADINGS INFO
		$headings_info = array(
			'csv_import_field_name' => 'name',
			'csv_import_field_meta_desc' => 'meta_description',
			'csv_import_field_meta_keyw' => 'meta_keyword',
			'csv_import_field_image' => 'image',
			'csv_import_field_additional_image' => 'product_image',
			'csv_import_field_price' => 'price',
			'csv_import_field_special_price' => 'product_special',
			'csv_import_field_desc' => 'description',
			'csv_import_field_cat' => 'category',
			'csv_import_field_manu' => 'manufacturer',
			'csv_import_field_attribute' => 'product_attribute',
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
			//'csv_import_field_tags' => 'product_tag',
			'csv_import_field_tags' => 'tag',
			'csv_import_field_upc' => 'upc',
			'csv_import_field_points' => 'points',
			'csv_import_field_option' => 'option'
		);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validatePermission()) {


		foreach ($headings_info as $key => $value) {
			if (isset($this->request->post[$key])) {
				$this->data[$key] = $this->request->post[$key];
				$this->field_names[$value] = $this->request->post[$key];
				if (in_array($key, $dont_save)) {
					$this->request->post[$key] = json_encode($this->request->post[$key]);
				}
			} else {
				$this->data[$key] = $this->config->get($key);
				if (in_array($key, $dont_save)) {
					$this->data[$key] = json_decode($this->data[$key]);
				}
			}
		}


            //echo '<pre>';
            //print_r($this->data);
            //echo '</pre>';

            if (isset($this->request->post['format_type'] )&& $this->request->post['format_type'] == 'xml') {
			    $product_export = date("Ymd-Hi").'_product_export.xml';
                $format_type = 'xml';
            } else {
			    $product_export = date("Ymd-Hi").'_product_export.csv';
                $format_type = 'csv';
            }

			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=' . $product_export);
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->load->model('tool/csv_export');

			if ( ! isset($this->request->post['product_category'])) {
				$product_category = NULL;
			} else {
				$product_category = $this->request->post['product_category'];
			}

			$this->response->setOutput($this->model_tool_csv_export->export($product_category, $format_type, $this->data, 'data/'));
		} else {
			return $this->forward('error/permission');
		}
	}

}
?>
