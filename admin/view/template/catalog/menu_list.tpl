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
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons">
          <a onclick="location = '<?php echo $insert; ?>'" class="button"><?php echo $button_insert; ?></a>
          <a onclick="$('form').submit();" class="button"><?php echo $button_delete; ?></a>
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="center">ID</td>
              <td class="center">TYPE</td>
              <td class="left"><?php echo $column_name; ?></td>
              <td class="left"><?php echo $column_href; ?></td>
              <td class="right"><?php echo $column_sort_order; ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($menus) { ?>
            <?php foreach ($menus as $menu) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($menu['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $menu['menu_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $menu['menu_id']; ?>" />
                <?php } ?></td>
              <td class="center"><?php echo $menu['menu_id']; ?></td>
              <td class="center"><?php echo $menu['type']; ?></td>
              <?php if ($menu['href']) { ?>
                <td class="left"><?php echo $menu['indent']; ?><a href="<?php echo $menu['href']; ?>"><?php echo $menu['name']; ?></a></td>
              <?php } else { ?>
                <td class="left"><?php echo $menu['indent']; ?><?php echo $menu['name']; ?></td>
              <?php } ?>
              <td class="left"><?php echo $menu['link']; ?></td>
              <td class="right"><?php echo $menu['sort_order']; ?></td>
              <td class="right"><?php foreach ($menu['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>