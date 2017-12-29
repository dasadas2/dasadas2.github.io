<?php if ($products) { ?>
    <table border="1" cellpadding="5">
      <tr>
        <td align="center">Бренд</td>
        <td align="center">Поставщик</td>
        <td align="center">Номер детали</td>
        <td align="center">Название</td>
        <td align="center">Кол-во</td>
        <td align="center">Цена</td>
        <td align="center">Вес</td>
        <td align="center"></td>
      </tr>

    <?php foreach ($products as $item) { ?>
      <tr>
        <td align="left"><?php echo $item['Brand']; ?></td>
        <td align="left"><?php echo $item['SupplierCode']; ?></td>
        <td align="left"><?php echo $item['Number']; ?></td>
        <td align="left"><?php echo $item['Name']; ?></td>
        <td align="center"><?php echo $item['Quantity']; ?></td>
        <td align="center"><?php echo $item['Price'] . ' ' . $item['Currency']; ?></td>
        <td align="center"><?php echo $item['Weight']; ?></td>
        <td align="center"><a onclick="
            addProduct(
            '<?php echo $item['Brand']; ?>',
            '<?php echo $item['SupplierCode']; ?>',
            '<?php echo $item['Number']; ?>',
            '<?php echo $item['Name']; ?>',
            '<?php echo $item['Quantity']; ?>',
            '<?php echo $item['Price']; ?>',
            '<?php echo $item['Weight']; ?>');" class="button"><span>Добавить в корзину</span></a></td>
      </tr>
    <?php } ?>

    </table>
<?php } ?>

<script type="text/javascript"><!--
function addProduct(brand, supplierCode, number, name, quantity, price, weight) {

	$.ajax({
		url: 'index.php?route=module/tehnomir/addProduct',
		type: 'post',
		dataType: 'json',
        data: 'brand=' + brand + '&supplierCode=' + supplierCode + '&number=' + number + '&name=' + name + '&quantity=' + quantity + '&price=' + price + '&weight=' + weight,
		beforeSend: function() {
		},
		complete: function() {
		},
		success: function(data) {
            addToCart(data['product_id']);
            //alert(data['product_id']);
            $.colorbox.close();
		}
	});
}
//--></script>
