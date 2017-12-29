<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
  <?php foreach ($breadcrumbs as $breadcrumb) { ?>
  <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
  <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">

      <table class="form">

        <tr>
          <td><?php echo $entry_status_enter; ?></td>
          <td><?php if ($alys_discount_box_status_enter) { ?>
            <input type="radio" name="alys_discount_box_status_enter" value="1" checked="checked" />
            <?php echo $text_yes; ?>
            <input type="radio" name="alys_discount_box_status_enter" value="0" />
            <?php echo $text_no; ?>
            <?php } else { ?>
            <input type="radio" name="alys_discount_box_status_enter" value="1" />
            <?php echo $text_yes; ?>
            <input type="radio" name="alys_discount_box_status_enter" value="0" checked="checked" />
            <?php echo $text_no; ?>
            <?php } ?></td>
        </tr>

        <tr>
          <td><?php echo $entry_status_time; ?></td>
          <td><?php if ($alys_discount_box_status_time) { ?>
            <input type="radio" name="alys_discount_box_status_time" value="1" checked="checked" />
            <?php echo $text_yes; ?>
            <input type="radio" name="alys_discount_box_status_time" value="0" />
            <?php echo $text_no; ?>
            <?php } else { ?>
            <input type="radio" name="alys_discount_box_status_time" value="1" />
            <?php echo $text_yes; ?>
            <input type="radio" name="alys_discount_box_status_time" value="0" checked="checked" />
            <?php echo $text_no; ?>
            <?php } ?></td>
        </tr>

        <tr>
          <td><?php echo $entry_time_delay; ?></td>
          <td><input type="text" name="alys_discount_box_time_delay" value="<?php echo $alys_discount_box_time_delay; ?>"/></td>
        </tr>
        <tr>
          <td><?php echo $entry_title; ?></td>
          <td><input type="text" name="alys_discount_box_title" value="<?php echo $alys_discount_box_title; ?>" size="80"/></td>
        </tr>
        <tr>
          <td><?php echo $entry_button; ?></td>
          <td><input type="text" name="alys_discount_box_button" value="<?php echo $alys_discount_box_button; ?>"/></td>
        </tr>
        <tr>
          <td><?php echo $entry_text; ?></td>
          <td><textarea name="alys_discount_box_text" id="alys_discount_box_text" cols="80" rows="6"><?php echo isset($alys_discount_box_text) ? $alys_discount_box_text : ''; ?></textarea></td>
        </tr>

      </table>

    </form>
  </div>
</div>

<script type="text/javascript" src="view/javascript/ckeditor/ckeditor.js"></script>
<script type="text/javascript"><!--
CKEDITOR.replace('alys_discount_box_text', {
	filebrowserBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashBrowseUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserImageUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>',
	filebrowserFlashUploadUrl: 'index.php?route=common/filemanager&token=<?php echo $token; ?>'
});
//--></script>

<?php echo $footer; ?>