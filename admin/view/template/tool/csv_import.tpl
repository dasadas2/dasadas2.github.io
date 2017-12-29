<?php
#####################################################################################
#  Module CSV IMPORT PRO for Opencart 1.5.x From HostJars opencart.hostjars.com 	#
#####################################################################################
?>
<?php echo $header; ?>
<div id="content">

<?php

    setLocale(LC_ALL, 'ru_RU.CP1251');

    $text=strToUpper(iconv('windows-1251', 'UTF-8', ' '));
 //   echo $text;
 //   echo '&nbsp;-&nbsp;';

    setlocale(LC_ALL, 'ru_RU.utf8');
   // echo 'Locale=' . setlocale(LC_ALL, NULL);
?>


  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($success) { ?>
<div class="success"><?php echo $success; ?></div>
<?php } ?>
<script type="text/javascript">
	function updateText() {
		var action = document.settings_form.csv_import_type.value;
		if (action == 'update') {
			$("#update_text").css("display", "inline");
			$("#add_text").css("display", "none");
			$("#reset_text").css("display", "none");
			$("#fullreset_text").css("display", "none");
		}
		else if (action == 'update2') {
			$("#update_text").css("display", "inline");
			$("#add_text").css("display", "none");
			$("#reset_text").css("display", "none");
			$("#fullreset_text").css("display", "none");
		}
		else if (action == 'add') {
			$("#update_text").css("display", "none");
			$("#add_text").css("display", "inline");
			$("#reset_text").css("display", "none");
			$("#fullreset_text").css("display", "none");
		}
		else if (action == 'reset') {
			$("#update_text").css("display", "none");
			$("#add_text").css("display", "none");
			$("#reset_text").css("display", "inline");
			$("#fullreset_text").css("display", "none");
		}
		else if (action == 'fullreset') {
			$("#update_text").css("display", "none");
			$("#add_text").css("display", "none");
			$("#reset_text").css("display", "none");
			$("#fullreset_text").css("display", "inline");
		}
	}
	function addCat(currentCat, el) {
		newcat = '<tr id="cat'+(currentCat+1)+'"><td style="width:240px;"><?php echo $text_field_category; ?>&nbsp;<a style="float:right;" onclick="return addCat('+(currentCat+1)+', this);" class="button"><span>More&nbsp;&darr;&nbsp;</span></a></td>';
		newcat += '<td><input type="text" name="csv_import_field_cat['+(currentCat+1)+'][]">';
		newcat += '&nbsp;<a onclick="return addSub(\'cat['+(currentCat+1)+']\', this);" class="button"><span>More&nbsp;&rarr;&nbsp;</span></a></td></tr>';
		$("#cat"+currentCat).after(newcat);
		$(el).hide();
		return false;
	}
	function addSub(name, el) {
		sub = '&nbsp;&rarr;&nbsp;<input type="text" name="csv_import_field_'+name+'[]" />&nbsp;';
		$(el).before(sub);
		return false;
	}
	function addVert(name, el) {
		newEl = '<tr id="'+name+'x">' + $("#"+name).html() + '</tr>';
		if (newEl.indexOf("value") != -1) {
			newEl = newEl.replace(/value="[^"]*"/, '');
		}
		newEl = newEl.replace("', this", "x', this");
		$(el).hide();
		$("#"+name).after(newEl);
		return false;
	}
	
	$(document).ready(function() {
		updateText();
	});
</script>

<div class="box">
  <div class="intro"><?php echo $introcsv; ?></div>
  <div class="heading">
    <h1><img src='view/image/feed.png' /><?php echo $heading_title; ?></h1>
  </div>
  <div class="content">
  	<div id="tabs" class="htabs">
        <a href="#tab_config"><?php echo $tab_config; ?></a>
        <a href="#tab_map"><?php echo $tab_map; ?></a>
        <a href="#tab_adjust"><?php echo $tab_adjust; ?></a>
        <a href="#tab_import"><?php echo $tab_import; ?></a>
        <a href="#tab_export"><?php echo $tab_export; ?></a>
    </div>
      <form action="<?php echo $action; ?>" method="post" name="settings_form" enctype="multipart/form-data" id="csv_import">
	    <div id="tab_config">
        <table class="form">
        <tr class="instructions">
        	<td colspan="3">Эти настройки применяются к каждому из добавляемых товаров. Обновляемые товары сохранят свои существующие настройки.</td>
        </tr>
      	<!-- delimiter -->
		<tr>
			<td><?php echo $entry_delimiter; ?></td>
			<td colspan="2">
				<select name="csv_import_delimiter">
					<option value=";" <?php if ($csv_import_delimiter == ';') { echo 'selected="true"'; } ?>>;</option>
					<option value="\t" <?php if ($csv_import_delimiter == '\t') { echo 'selected="true"'; } ?>>Tab</option>
					<option value="|" <?php if ($csv_import_delimiter == '|') { echo 'selected="true"'; } ?>>|</option>
					<option value="," <?php if ($csv_import_delimiter == ',') { echo 'selected="true"'; } ?>>,</option>
					<option value="^" <?php if ($csv_import_delimiter == '^') { echo 'selected="true"'; } ?>>^</option>
				</select>
			</td>
		</tr>
		<!-- stock status (stock_status_id) -->
		<tr>
			<td><?php echo $entry_stock_status; ?></td>
			<td colspan="2">
				<select name="csv_import_stock_status_id">
					<?php foreach ($stock_status_selections as $status) { ?>
					<option value="<?php echo $status['stock_status_id']; ?>" <?php if ($csv_import_stock_status_id == $status['stock_status_id']) { echo "selected='true'"; } ?>><?php echo $status['name']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<!-- subtract stock default setting -->
		<tr>
			<td><?php echo $entry_subtract; ?></td>
			<td colspan="2">
				<select name="csv_import_subtract">
					<option value="1" <?php if ($csv_import_subtract == 1) { echo "selected='true'"; } ?>>Yes</option>
					<option value="0" <?php if ($csv_import_subtract == 0) { echo "selected='true'"; } ?>>No</option>
				</select>
			</td>
		</tr>
		<!-- product status default setting -->
		<tr>
			<td><?php echo $entry_product_status; ?></td>
			<td colspan="2">
				<select name="csv_import_product_status">
					<option value="1" <?php if ($csv_import_product_status == 1) { echo "selected='true'"; } ?>>Enabled</option>
					<option value="0" <?php if ($csv_import_product_status == 0) { echo "selected='true'"; } ?>>Disabled</option>
				</select>
			</td>
		</tr>
		<!-- language -->
		<?php if (count($language_selections) > 1) { ?>
			<tr>
				<td><?php echo $entry_language; ?></td>
				<td colspan="2">
					<select name="csv_import_language">
						<?php foreach ($language_selections as $lang) { ?>
						<option value="<?php echo $lang['language_id']; ?>" <?php if ($csv_import_language === $lang['language_id']) { echo 'selected="true"'; }?>><?php echo $lang['name']; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
		<?php }	else { foreach ($language_selections as $lang) { ?>
				<input type="hidden" name="csv_import_language" value="<?php echo $lang['language_id']; ?>">
		<?php }} ?>
		
		<?php if ($weight_class_selections) { ?>
		<!-- store -->
		<tr>
			<td><?php echo $entry_weight_class; ?></td>
			<td colspan="2">
				<select name="csv_import_weight_class">
					<?php foreach ($weight_class_selections as $weight) { ?>
					<option value="<?php echo $weight['weight_class_id']; ?>" <?php if ($csv_import_weight_class == $weight['weight_class_id']) { echo 'selected="true"'; }?>><?php echo $weight['title']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		<?php if ($length_class_selections) { ?>
		<!-- store -->
		<tr>
			<td><?php echo $entry_length_class; ?></td>
			<td colspan="2">
				<select name="csv_import_length_class">
					<?php foreach ($length_class_selections as $length) { ?>
					<option value="<?php echo $length['length_class_id']; ?>" <?php if ($csv_import_length_class == $length['length_class_id']) { echo 'selected="true"'; }?>><?php echo $length['title']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		
		<?php if ($tax_class_selections) { ?>
		<!-- store -->
		<tr>
			<td><?php echo $entry_tax_class; ?></td>
			<td colspan="2">
				<select name="csv_import_tax_class">
					<?php foreach ($tax_class_selections as $tax) { ?>
					<option value="<?php echo $tax['tax_class_id']; ?>" <?php if ($csv_import_tax_class == $tax['tax_class_id']) { echo 'selected="true"'; }?>><?php echo $tax['title']; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<?php } ?>
		
		<!-- store -->
		<tr>
			<td><?php echo $entry_store; ?></td>
			<td colspan="2">
				<input type="checkbox" name="csv_import_store[]" value="0" checked="true" /><label>Default</label>
				<?php foreach ($store_selections as $store) { ?>
				<input type="checkbox" name="csv_import_store[]" value="<?php echo $store['store_id']; ?>"><label><?php echo $store['name']; ?></label>
				<?php } ?>
			</td>
		</tr>
		
		<!-- top categories -->
		<tr>
			<td><?php echo $entry_top_categories; ?></td>
			<td colspan="2">
				<select name="csv_import_top_categories">
					<option value="0" <?php if (!$csv_import_top_categories) echo "selected='true'"; ?>>No</option>
					<option value="1" <?php if ($csv_import_top_categories) echo "selected='true'"; ?>>Yes</option>
				</select>
			</td>
		</tr>
		
		<!-- download images -->
		<tr>
			<td><?php echo $entry_remote_images; ?><span class="help"><?php echo $entry_remote_images_warning; ?></span></td>
			<td colspan="2">
				<select name="csv_import_remote_images">
					<option value="0" <?php if (!$csv_import_remote_images) echo "selected='true'"; ?>>No</option>
					<option value="1" <?php if ($csv_import_remote_images) echo "selected='true'"; ?>>Yes</option>
				</select>
			</td>
		</tr>

		</table>
    </div>
	<div id="tab_map">
		<table class="form">
        <tr class="instructions">
        	<td colspan="3"><p>Укажите ниже  напротив соответствующих полей, идентификаторы загружаемых столбцов,  затем установите их в первой строке импортируемого файла Эксель, <br />
        	  затем сохраните файл эксель в формате CSV(разделитель запятые). <br />
       	    Поля в которых не указаны идентифификаторы столбцов, загружены не будут. Идентификаторы столбцов в экселевском файле могут располагаться в любом порядке в первой строке файла, столбца в экселе которые не имеют идентификаторы, загружены не будут.</p></td>
        </tr>
      	<!-- mapping fields to names -->
		<tr>
			<td colspan="3">
            <table>
				<tr><td style="width:240px;"><h2><?php echo $text_field_oc_title; ?></h2></td><td><h2><?php echo $text_field_csv_title; ?></h2></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_name; ?></td><td><input type="text"  name="csv_import_field_name" value="<?php if ($csv_import_field_name) {echo $csv_import_field_name; } ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_price; ?></td><td><input type="text" name="csv_import_field_price" value="<?php echo $csv_import_field_price; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_model;?></td><td><input type="text" name="csv_import_field_model" value="<?php echo $csv_import_field_model; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_manufacturer; ?></td><td><input type="text" name="csv_import_field_manu" value="<?php echo $csv_import_field_manu; ?>"></td></tr>
				<?php 
				
				//CATEGORIES
				if (count($csv_import_field_cat) == 0 || $csv_import_field_cat[0][0] == '') {
					echo '<tr id="cat0"><td style="width:240px;">';
					echo $text_field_category;
					echo '&nbsp;<a style="float:right;" onclick="return addCat(0, this);" class="button"><span>Еще &nbsp;&darr;&nbsp;</span></a>';
					echo '</td><td>';
					echo '<input type="text" name="csv_import_field_cat[0][]">';
					echo '&nbsp;<a onclick="return addSub(\'cat[0]\', this);" class="button"><span>Еще &nbsp;&rarr;&nbsp;</span></a>';
					echo '</td></tr>';
				} else {
					for ($i=0; $i<count($csv_import_field_cat); $i++) {
						if ($csv_import_field_cat[$i][0] != '') {
							echo '<tr id="cat' . $i  . '"><td style="width:240px;">';
							echo $text_field_category;
							if ($i == count($csv_import_field_cat)-1 || $csv_import_field_cat[$i+1][0] == '') {
								echo '&nbsp;<a style="float:right;" onclick="return addCat(' . $i . ', this);" class="button"><span>Еще &nbsp;&darr;&nbsp;</span></a>';
							}
							echo '</td><td>';
							foreach ($csv_import_field_cat[$i] as $subcat) {
								if ($subcat != '') {
									if ($subcat != $csv_import_field_cat[$i][0]) {
										echo '&nbsp;&rarr;&nbsp;';
									}
									echo '<input type="text" name="csv_import_field_cat[' . $i . '][]" value="' . $subcat . '">';
								}
							}
							echo '&nbsp;<a onclick="return addSub(\'cat[' . $i . ']\', this);" class="button"><span>Еще &nbsp;&rarr;&nbsp;</span></a>';
							echo '</td></tr>';
						}
					}
				}
				
					?>
				<tr><td style="width:240px;"><?php echo $text_field_image; ?></td><td><input type="text" name="csv_import_field_image" value="<?php echo $csv_import_field_image; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_quantity; ?></td><td><input type="text" name="csv_import_field_quantity" value="<?php echo $csv_import_field_quantity; ?>"></td></tr>
                <tr><td style="width:240px;"><?php echo $text_field_quantity_class_id; ?></td><td><input type="text" name="csv_import_field_quantity_class_id" value="<?php echo $csv_import_field_quantity_class_id; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_description; ?></td><td><input type="text" name="csv_import_field_desc" value="<?php echo $csv_import_field_desc; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_meta_desc; ?></td><td><input type="text" name="csv_import_field_meta_desc" value="<?php echo $csv_import_field_meta_desc; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_meta_keyw; ?></td><td><input type="text" name="csv_import_field_meta_keyw" value="<?php echo $csv_import_field_meta_keyw; ?>"></td></tr>
				<tr><td style="width:240px;"><?php echo $text_field_tags; ?></td><td><input type="text" name="csv_import_field_tags" value="<?php echo $csv_import_field_tags; ?>"></td></tr>
			</table></td>
		</tr>
		</table>
	</div>
	<div id="tab_adjust">
		<table class="form">
		<tr class="instructions">
			<td colspan="3">This step allows you to edit certain columns' data as the CSV file is read in. You can adjust prices with a multiplier or prepend/append text to your image field.</td>
		</tr>
		<tr>
			<td><?php echo $entry_split_category; ?><span class="help">If your category and subcategories are in the same field, you can split them on a delimiter</span></td>
            <td><input type="text" value="<?php echo $csv_import_split_category; ?>" name="csv_import_split_category" /></td>
        </tr>
		<tr>
			<td><?php echo $entry_price_multiplier; ?><span class="help">e.g: To add 10% use 1.10, to remove 10% use 0.90</span></td>
            <td><input type="text" value="<?php echo $csv_import_price_multiplier; ?>" name="csv_import_price_multiplier" /></td>
        </tr>
		<tr>
			<td><?php echo $entry_image_remove; ?><span class="help">e.g: If your CSV has image urls with extra text at the start (like http://servername.com/...) you can remove this by entering the text to remove here</span></td>
            <td><input type="text" value="<?php echo $csv_import_image_remove; ?>" name="csv_import_image_remove" /></td>
        </tr>
		<tr>
			<td><?php echo $entry_image_prepend; ?><span class="help">e.g: If your CSV only has image names you can prepend data/ to this to make it work in OpenCart</span></td>
            <td><input type="text" value="<?php echo $csv_import_image_prepend; ?>" name="csv_import_image_prepend" /></td>
        </tr>
		<tr>
			<td><?php echo $entry_image_append; ?><span class="help">e.g: If your image names are the same as your SKUs, you can append .jpg to your SKU and use it as images</span></td>
            <td><input type="text" value="<?php echo $csv_import_image_append; ?>" name="csv_import_image_append" /></td>
        </tr>
		</table>
	</div>
	<div id="tab_import">
		<table class="form">
        <tr class="instructions">
        	<td colspan="3">This page is where you run the import from once your settings and mappings are all ready. First, choose what type of import you want to run - add, update or reset. Next, if there are any products you want to exclude from the import, you can say exclude any products which have &lt;field&gt; with this &lt;value&gt;.
        	For example, you may want to exclude all items where stock is 0. You would put stock in the column field and 0 in the value field. Finally, either upload a file or enter a url in the field provided and click import.</td>
        </tr>
		<!-- update/reset/add -->
		<tr>
			<td><?php echo $entry_import_type; ?></td>
			<td colspan="2">
				<select name="csv_import_type" onchange="updateText(this);">
					<option value="add"><?php echo $text_add; ?></option>
					<option value="update"><?php echo $text_update; ?></option>
					<option value="update2" selected><?php echo $text_update2; ?></option>
					<option value="reset"><?php echo $text_reset; ?></option>
					<option value="fullreset"><?php echo $text_fullreset; ?></option>
				</select>
				<span id="update_text">
				&nbsp;&nbsp;&nbsp;&nbsp;Обновить товары совпадающие по:
				<select name="csv_import_update_field">
					<option value="model" selected><?php echo $text_field_model; ?></option>
					<option value="sku"><?php echo $text_field_sku; ?></option>
					<option value="name"><?php echo $text_field_name; ?></option>
				</select>
				&nbsp;&nbsp;Обновятся все товары совпадающие по выбранному правилу, остальные добавятся в базу
				</span>
				<span id="add_text">&nbsp;&nbsp;Добавить загружаемые товары в БД без проверки на совпадение</span>
				<span id="reset_text">&nbsp;&nbsp;Очистить БД товаров перед загрузкой и загрузить все товары в чистую БД</span>
				<span id="fullreset_text">&nbsp;&nbsp;Полная очистка БД</span>
		</tr>
		<!-- ignore where FIELD equals VALUE -->
		<tr>
			<td><?php echo $entry_ignore_fields; ?></td>
			<td colspan="2"><input type="text" name="csv_import_ignore_field" value="COLUMN">&nbsp;содержит&nbsp;<input type="text" name="csv_import_ignore_value" value="VALUE"></td>
		</tr>
		<!-- File.. -->
		<tr>
            <td><?php echo $entry_convert_status; ?></td>
            <td>
              <?php if ($csv_import_convert_status ) { ?>
                <input type="radio" name="csv_import_convert_status" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="csv_import_convert_status" value="0" />
                <?php echo $text_no; ?>
              <?php } else { ?>
                <input type="radio" name="csv_import_convert_status" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="csv_import_convert_status" value="0" checked="checked" />
                <?php echo $text_no; ?>
              <?php } ?>
            </td>
        </tr>
		<tr>

            <td><?php echo $entry_import_file; ?></td>
            <td colspan="2"><input type="file" name="csv_import" /></td>
        </tr>
		<!-- ..or URL -->
		<tr>
            <td><?php echo $entry_import_url; ?></td>
			<td><input type="text" size="70" name="csv_import_feed_url" value="<?php echo $csv_import_feed_url ?>" />&nbsp;Распаковать загруженный файл <input type="checkbox" name="csv_import_unzip_feed" <?php if ($csv_import_unzip_feed) echo 'checked="1" '; ?>/></td>
            <td><a onclick="$('#csv_import').submit();" class="button"><span><?php echo $button_import; ?></span></a></td>
        </tr>
        </table>
       </div>

      </form>

      <form action="<?php echo $export; ?>" method="post" enctype="multipart/form-data" id="export">

	<div id="tab_export">

		<table class="form">
            <tr>
              <td></td>
              <td><?php echo $text_notes; ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_category; ?><br/><span class="help"><?php echo $entry_category_help; ?></span></td>
              <td>
                <div class="scrollbox" style="width:600px;height:300px;">
                  <?php $class = 'odd'; ?>
                  <?php foreach ($categories as $category) { ?>
                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                  <div class="<?php echo $class; ?>">
                    <input type="checkbox" name="product_category[]" value="<?php echo $category['category_id']; ?>" />
                    <?php echo $category['name']; ?>

                  </div>
                  <?php } ?>
                </div>
                <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
              </td>
            </tr>
            <tr>
              <td><?php echo $entry_format; ?></td>
              <td class="left">
                <select name="format_type">
                  <option value="csv" selected="selected">CSV</option>
                  <option value="xml">XML</option>
                </select>
              </td>
            </tr>
            <tr>
              <td></td>
              <td align="left">
                <div class="buttons"><a onclick="$('#export').submit();" class="button"><span><?php echo $button_export; ?></span></a></div>
              </td>
            </tr>
		</table>
	</div>

      </form>

  </div>
</div><script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script>
<?php echo $footer; ?>