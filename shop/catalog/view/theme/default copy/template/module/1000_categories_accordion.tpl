<style>
.categories_1000 ul {
	display: block !important;
}
</style>
<div class="box">
  <div class="box-heading"><?php echo $heading_title ?></div>
  <div class="box-content">
    <div class="box-category"><?php echo $category_accordion_menu ?></div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('.expand-categ').click(function(e) {
		e.preventDefault();
		expandCategories($(this));
	})
	
	function expandCategories(categ) {
		var categ_id = $(categ).attr('category');
		var children = $('#children_' + categ_id);
		var path = $(categ).attr('path');
		if (!$(children).attr('loaded')) {
			$(children).html('<li><img src="catalog/view/theme/default/image/loading.gif" /></li>');
			$.post('<?php echo str_replace('&amp;', '&', $ajax_loader); ?>', { parent_id:categ_id, path:path }, function(data) {
				$(children).attr('loaded', 1);
				$(children).html(data);
				$(children).find('.expand-categ').click(function(e) {
					e.preventDefault();
					expandCategories($(this));
				})
			})
		}
		else {
			document.location.href = $(categ).attr('href');
		}
	}
	
});
</script>
