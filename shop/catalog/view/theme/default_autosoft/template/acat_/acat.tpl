<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>


<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?echo $heading_title; ?></h1>
	
<div id="acatwrkspace" style="background:#eee; border:1px solid #666; text-align:left;">
	<div id="acatinnerspace" style="padding:10px 10px 10px 10px;"></div>
</div>

<?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
			AcatInit('<?php echo HTTP_SERVER.'scheme/'.AC_CHEMA.'/'?>');
			<?php global $ACAT_PARAMS; echo Render_Acat_Func($ACAT_PARAMS); ?>
</script>