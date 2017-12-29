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
          <td><?php echo $entry_status; ?></td>
          <td><?php if ($tehnomir_status) { ?>
            <input type="radio" name="tehnomir_status" value="1" checked="checked" />
            <?php echo $text_yes; ?>
            <input type="radio" name="tehnomir_status" value="0" />
            <?php echo $text_no; ?>
            <?php } else { ?>
            <input type="radio" name="tehnomir_status" value="1" />
            <?php echo $text_yes; ?>
            <input type="radio" name="tehnomir_status" value="0" checked="checked" />
            <?php echo $text_no; ?>
            <?php } ?></td>
        </tr>
        <tr>
            <td class="left" colspan="2"><b><?php echo $text_info; ?></b></td>
        </tr>

        <tr>
          <td><span class="required">*</span> <?php echo $entry_username; ?></td>
          <td><input type="text" name="tehnomir_username" value="<?php echo $tehnomir_username; ?>" />
            <?php if ($error_username) { ?>
            <span class="error"><?php echo $error_username; ?></span>
            <?php } ?></td>
        </tr>

        <tr>
          <td><span class="required">*</span> <?php echo $entry_password; ?></td>
          <td><input type="text" name="tehnomir_password" value="<?php echo $tehnomir_password; ?>" />
            <?php if ($error_password) { ?>
            <span class="error"><?php echo $error_password; ?></span>
            <?php } ?></td>
        </tr>

      </table>

    </form>
  </div>
</div>
<?php echo $footer; ?>