<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <?php if ($thumb || $description) { ?>
  <div class="category-info">
    <?php if ($thumb) { ?>
    <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
    <?php } ?>
    <?php if ($description) { ?>
    <?php echo $description; ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($categories) { ?>
  <h2><?php echo $text_refine; ?></h2>
  <div class="category-list">
    <?php if (count($categories) <= 5) { ?>
    <ul>
      <?php foreach ($categories as $category) { ?>
      <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
      <?php } ?>
    </ul>
    <?php } else { ?>
    <?php for ($i = 0; $i < count($categories);) { ?>
    <ul>
      <?php $j = $i + ceil(count($categories) / 4); ?>
      <?php for (; $i < $j; $i++) { ?>
      <?php if (isset($categories[$i])) { ?>
      <li><a href="<?php echo $categories[$i]['href']; ?>"><?php echo $categories[$i]['name']; ?></a></li>
      <?php } ?>
      <?php } ?>
    </ul>
    <?php } ?>
    <?php } ?>
  </div>
  <?php } ?>
  <?php if ($products) { ?>
  <div class="product-filter">
  <?php /**/ ?>
    <div class="display"><b><?php echo $text_display; ?></b> <?php echo $text_list; ?> <b>/</b> <a onclick="display('grid');"><?php echo $text_grid; ?></a></div>
  <?php /**/ ?>
    <div class="limit"><b><?php echo $text_limit; ?></b>
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
    <div class="sort"><b><?php echo $text_sort; ?></b>
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
    <?php /* ?>
    <table class="product-list">
    <tr>
            <th width="60" style="font-weight: bold; font-size: 8pt;"></th>
            <th width="17%" style="font-weight: bold; font-size: 8pt;">Оригинальный номер</th>
            <th width="17%" style="font-weight: bold; font-size: 8pt;">Производитель</th>
            <th style="font-weight: bold; font-size: 8pt;">Наименование</th>
            <th width="60" style="font-weight: bold; font-size: 8pt;">Наличие</th>
            <th align="right" width="80" style="font-weight: bold; font-size: 8pt;">Цена</th>
            <th width="5%"></th>
                        <th width="9%"></th>
                        <th width="11%"></th>
    </tr>
 <?php $color = '#ffffff'; foreach ($products as $product) {   ?>
    <tr style="background-color: <?=$color?>">
            <td><?php if ($product['thumb']) { ?><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']?>" style="border: 1px solid #eeeeee; " width="60"></a><?php } ?></td>
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
            <td align="center" class="cart">
                <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" />
            </td>
            <td class="wishlist">
                <a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a>
            </td>
            <td class="compare">
                <a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a>
            </td>
    </tr>
 <?php if ($color == '#ffffff') { $color = '#f2f2f2'; } else { $color = '#ffffff';  } } ?>
 </table>
 <?php */ ?>
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
      <div class="cart">
        <input type="button" value="<?php echo $button_cart; ?>" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button" />
      </div>
      <div class="wishlist"><a onclick="addToWishList('<?php echo $product['product_id']; ?>');"><?php echo $button_wishlist; ?></a></div>
      <div class="compare"><a onclick="addToCompare('<?php echo $product['product_id']; ?>');"><?php echo $button_compare; ?></a></div>
    </div>
    <?php } ?>
  </div>
  <div class="pagination"><?php echo $pagination; ?></div>
  <?php } ?>
  <?php if (!$categories && !$products) { ?>
  <div class="content"><?php echo $text_empty; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
  </div>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<script type="text/javascript"><!--
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
<?php echo $footer; ?>