<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <b><?php echo $text_critea; ?></b>
  <div class="content">
    <p><?php echo $entry_search; ?>
      <?php if ($search) { ?>
      <input type="text" name="search" value="<?php echo $search; ?>" />
      <?php } else { ?>
      <input type="text" name="search" value="<?php echo $search; ?>" onclick="this.value = '';" onkeydown="this.style.color = '000000'" style="color: #999;" />
      <?php } ?>
      <select name="category_id">
        <option value="0"><?php echo $text_category; ?></option>
        <?php foreach ($categories as $category_1) { ?>
        <?php if ($category_1['category_id'] == $category_id) { ?>
        <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
        <?php } ?>
        <?php foreach ($category_1['children'] as $category_2) { ?>
        <?php if ($category_2['category_id'] == $category_id) { ?>
        <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
        <?php } ?>
        <?php foreach ($category_2['children'] as $category_3) { ?>
        <?php if ($category_3['category_id'] == $category_id) { ?>
        <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
        <?php } ?>
        <?php } ?>
        <?php } ?>
        <?php } ?>
      </select>
      <?php if ($sub_category) { ?>
      <input type="checkbox" name="sub_category" value="1" id="sub_category" checked="checked" />
      <?php } else { ?>
      <input type="checkbox" name="sub_category" value="1" id="sub_category" />
      <?php } ?>
      <label for="sub_category"><?php echo $text_sub_category; ?></label>
    </p>
    <?php if ($description) { ?>
    <input type="checkbox" name="description" value="1" id="description" checked="checked" />
    <?php } else { ?>
    <input type="checkbox" name="description" value="1" id="description" />
    <?php } ?>
    <label for="description"><?php echo $entry_description; ?></label>
  </div>
  <div class="buttons">
    <div class="right"><input type="button" value="<?php echo $button_search; ?>" id="button-search" class="button" /></div>
  </div>
  <h2><?php echo $text_search; ?></h2>
  <?php if ($products) { ?>
  <div class="product-filter">
    <div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
    <div class="limit"><?php echo $text_limit; ?>
      <select onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    <div class="sort"><?php echo $text_sort; ?>
      <select onchange="location = this.value;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="product-compare"><a href="<?php echo $compare; ?>" id="compare-total"><?php echo $text_compare; ?></a></div>
  <?php  /* ?>
	<table class="cart">
     	<tr>
     		<th width="17%" style="font-weight: bold; font-size: 8pt;">Оригинальный номер</th>
     		<th width="17%" style="font-weight: bold; font-size: 8pt;">Производитель</th>
     		<th style="font-weight: bold; font-size: 8pt;">Наименование</th>
     		<th width="60" style="font-weight: bold; font-size: 8pt;">Наличие</th>
     		<th align="right" width="80" style="font-weight: bold; font-size: 8pt;">Цена</th>
     		<th width="5%"></th>
     	</tr>
     <?php $color = '#ffffff'; foreach ($products as $product) {  ?>
     	<tr style="background-color: <?=$color?>">
     		<td><?php echo $product['model']?></td>
     		<td>
     			<?php echo $product['_mancode']?>
     			<div><?=$product['_firm'] ? '<span style="font-size: 0.8em;">' . $product['_firm'] . '</span>' : ''?><?=$product['_country'] ? ($product['_firm'] ? '/' : '' ). '<span style="font-size: 0.8em;">' . $product['_country'] . '</span>' : ''?></div>

     		</td>
     		<td>
     			<a href="<?php echo $product['href']; ?>" style="font-weight: bold;"><?php echo $product['name']?></a>
     			<?=$product['_articul'] ? '<div style="color: #666666; font-size: 0.8em;">Артикул: ' . $product['_articul'] . '</div>' : ''?>
     		</td>
     		<td><?=($product['quantity'] > 0 ? $product['quantity'] : $product['stock'])?></td>
     		<td align="right"><?php echo $product['price']?></td>
     		<td align="center"><a class="button_add_small" href="<?php echo $product['add']; ?>" title="<?php echo $button_add_to_cart; ?>" >&nbsp;</a></td>
     	</tr>
     <?php if ($color == '#ffffff') { $color = '#f2f2f2'; } else { $color = '#ffffff';  } } ?>
     </table>
<?php  */ ?>
  <div class="product-list">
    <?php foreach ($products as $product) { ?>
    <div>
      <?php if ($product['thumb']) { ?>
      <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
      <?php } ?>
      <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                <?=$product['_articul'] ? '<div class="articul"><span  style="color: #666666; font-size: 0.8em;">Артикул: ' . $product['_articul'] . '</span></div>' : ''?>
                <div class="model"><?=$product['_mancode'] ? 'Оригинальный номер: '.$product['model']:''?></div>
                <div class="mancode">
                    <?=$product['_mancode'] ? 'Код производителя: '.$product['_mancode']:''?>
                    <div><?=$product['_firm'] ? '<span style="font-size: 0.8em;">' . $product['_firm'] . '</span>' : ''?><?=$product['_country'] ? ($product['_firm'] ? '/' : '' ). '<span style="font-size: 0.8em;">' . $product['_country'] . '</span>' : ''?></div>
                </div>
                <div class="quantity"><span  style="color: #666666; font-size: 0.9em;">Наличие: <?=($product['quantity'] > 0 ? $product['quantity'] : $product['stock'])?></ br></span></div>
      <div class="description"><?php echo $product['description']; ?></div>
      <?php if ($product['price']) { ?>
      <div class="price">
        <?php if (!$product['special']) { ?>
        <?php echo $product['price']; ?>
        <?php } else { ?>
        <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
        <?php } ?>
        <?php if ($product['tax']) { ?>
        <br />
        <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
        <?php } ?>
      </div>
      <?php } ?>
      <?php if ($product['rating']) { ?>
      <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
      <?php } ?>
      <div class="cart"><input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" /></div>
      <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
      <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
    </div>
    <?php } ?>
  </div>
<?php /* */ ?>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } else { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <?php }?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
$('#content input[name=\'search\']').keydown(function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').bind('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').attr('disabled', 'disabled');
		$('input[name=\'sub_category\']').removeAttr('checked');
	} else {
		$('input[name=\'sub_category\']').removeAttr('disabled');
	}
});

$('select[name=\'category_id\']').trigger('change');

$('#button-search').bind('click', function() {

    //checkTehnomir();

    
	url = 'index.php?route=product/search';

	var search = $('#content input[name=\'search\']').attr('value');

	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').attr('value');

	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	var sub_category = $('#content input[name=\'sub_category\']:checked').attr('value');

	if (sub_category) {
		url += '&sub_category=true';
	}

	var filter_description = $('#content input[name=\'description\']:checked').attr('value');

	if (filter_description) {
		url += '&description=true';
	}

	location = url;
    
});

function display(view) {
	if (view == 'list') {
		$('.product-grid').attr('class', 'product-list');

		$('.product-list > div').each(function(index, element) {
			html  = '<div class="right">';
			html += '  <div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '  <div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '  <div class="compare">' + $(element).find('.compare').html() + '</div>';
			html += '</div>';

			html += '<div class="left">';

			var image = $(element).find('.image').html();

			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}

			var price = $(element).find('.price').html();

			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}

			html += '  <div class="name">' + $(element).find('.name').html() + '</div>';
                                          if ($(element).find('.articul').html()) {
                                            html += '  <div class="articul">' + $(element).find('.articul').html() + '</div>';
                                          }
                                          if ($(element).find('.model').html()) {
                                            html += '  <div class="model">' + $(element).find('.model').html() + '</div>';
                                          }
                                          if ($(element).find('.mancode').html()) {
                                            html += '  <div class="mancode">' + $(element).find('.mancode').html() + '</div>';
                                          }
                                          html += '  <div class="quantity">' + $(element).find('.quantity').html() + '</div>';
			html += '  <div class="description">' + $(element).find('.description').html() + '</div>';

			var rating = $(element).find('.rating').html();

			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}

			html += '</div>';

			$(element).html(html);
		});

		$('.display').html('<b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display(\'grid\');"><?php echo $text_grid; ?></a>');

		$.totalStorage('display', 'list');
	} else {
		$('.product-list').attr('class', 'product-grid');

		$('.product-grid > div').each(function(index, element) {
			html = '';

			var image = $(element).find('.image').html();

			if (image != null) {
				html += '<div class="image">' + image + '</div>';
			}

			html += '<div class="name">' + $(element).find('.name').html() + '</div>';
                                          if ($(element).find('.articul').html()) {
                                            html += '<div class="articul">' + $(element).find('.articul').html() + '</div>';
                                          }
                                          if ($(element).find('.model').html()) {
                                            html += '<div class="model">' + $(element).find('.model').html() + '</div>';
                                          }
                                          if ($(element).find('.mancode').html()) {
                                            html += '<div class="mancode">' + $(element).find('.mancode').html() + '</div>';
                                          }
			html += '<div class="description">' + $(element).find('.description').html() + '</div>';

                                          html += '<div class="quantity">' + $(element).find('.quantity').html() + '</div>';

			var price = $(element).find('.price').html();

			if (price != null) {
				html += '<div class="price">' + price  + '</div>';
			}

			var rating = $(element).find('.rating').html();

			if (rating != null) {
				html += '<div class="rating">' + rating + '</div>';
			}

			html += '<div class="cart">' + $(element).find('.cart').html() + '</div>';
			html += '<div class="wishlist">' + $(element).find('.wishlist').html() + '</div>';
			html += '<div class="compare">' + $(element).find('.compare').html() + '</div>';

			$(element).html(html);
		});

		$('.display').html('<b><?php echo $text_display; ?></b> <a onclick="display(\'list\');"><?php echo $text_list; ?></a> <b>/</b> <?php echo $text_grid; ?>');

		$.totalStorage('display', 'grid');
	}
}

view = $.totalStorage('display');

if (view) {
	display(view);
} else {
	display('list');
}
//--></script>

<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />

<script type="text/javascript"><!--
function checkTehnomir() {

    var search = $('#content input[name=\'search\']').attr('value');
	$.ajax({
		url: 'index.php?route=module/tehnomir/checkNumber',
		type: 'post',
		dataType: 'html',
        data: 'search=' + search,
		beforeSend: function() {
		},
		complete: function() {
		},
		success: function(html) {
            $.colorbox({
            	overlayClose: true,
            	opacity: 0.5,
            	href: false,
            	html: html
            });
            //alert(data);
		}
	});
}
//--></script>

<?php echo $footer; ?>