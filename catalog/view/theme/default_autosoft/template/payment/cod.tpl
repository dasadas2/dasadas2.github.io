<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		type: 'get',
                                dataType: 'json',
		url: 'index.php?route=payment/cod/confirm',
		success: function(json) {
                                                if (json && json['error']) {
                                                        alert(json['error']);
                                                } else {
                                                    location = '<?php echo $continue; ?>';
                                                }
		}
	});
});
//--></script>
