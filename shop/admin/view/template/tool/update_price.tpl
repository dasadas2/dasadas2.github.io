<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($success) { ?>
  <div class="success"><?php echo $success; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/log.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>
    <div class="content">
      <table>
        <tr>
          <td>
            <b style="font-size:16px;">Выберите источник:</b>&nbsp;
	        <select name="source">
		      <option value="1">Импортированнные товары</option>
			  <option value="0">Товары добавленные вручную</option>
		    </select>
          </td>
          <td>
            <div class="buttons"><a href="<?php echo $update; ?>" class="button"><?php echo $button_update; ?></a></div>
          </td>
        </tr>
      </table>

    </div>
  </div>
</div>
<?php echo $footer; ?>