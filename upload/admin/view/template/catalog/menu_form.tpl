<?php echo $header; ?>

<style type="text/css">
.expand0 { display:none; }
.expand2 { display:none; }
.expand3 { display:none; }
.expand4 { display:none; }
</style>

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
      <h1><img src="view/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <table class="form">

            <tr>
              <td><span class="required">*</span> <?php echo $entry_name; ?></td>
              <td><?php foreach ($languages as $language) { ?>
                <input type="text" size="80" name="menu_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($menu_description[$language['language_id']]) ? $menu_description[$language['language_id']]['name'] : ''; ?>" />
                <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
                <?php if (isset($error_name[$language['language_id']])) { ?>
                <span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
                <?php } ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_parent; ?></td>
              <td><select name="parent_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($menus as $menu) { ?>
                  <?php if ($menu['menu_id'] == $parent_id) { ?>
                  <option value="<?php echo $menu['menu_id']; ?>" selected="selected"><?php echo $menu['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $menu['menu_id']; ?>"><?php echo $menu['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_top; ?></td>
              <td><select name="top">
                  <?php if ($top) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <option value="0"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_column; ?></td>
              <td><input type="text" name="column" value="<?php echo $column; ?>" size="1" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_type; ?></td>
              <td><select name="type" id="type">
              	  <?php if ($type == 1) { ?>
                    <option value="1" selected="selected"><?php echo $entry_type_1; ?></option>
                  <?php } else { ?>
                    <option value="1"><?php echo $entry_type_1; ?></option>
                  <?php } ?>
                  <?php if ($type == 2) { ?>
                    <option value="2" selected="selected"><?php echo $entry_type_2; ?></option>
                  <?php } else { ?>
                    <option value="2"><?php echo $entry_type_2; ?></option>
                  <?php } ?>
                  <?php if ($type == 0) { ?>
                    <option value="0" selected="selected"><?php echo $entry_type_0; ?></option>
                  <?php } else { ?>
                    <option value="0"><?php echo $entry_type_0; ?></option>
                  <?php } ?>
                  <?php if ($type == 4) { ?>
                    <option value="4" selected="selected"><?php echo $entry_type_4; ?></option>
                  <?php } else { ?>
                    <option value="4"><?php echo $entry_type_4; ?></option>
                  <?php } ?>
                  <?php if ($type == 5) { ?>
                    <option value="5" selected="selected"><?php echo $entry_type_5; ?></option>
                  <?php } else { ?>
                    <option value="5"><?php echo $entry_type_5; ?></option>
                  <?php } ?>
                  <?php if ($type == 3) { ?>
                    <option value="3" selected="selected"><?php echo $entry_type_3; ?></option>
                  <?php } else { ?>
                    <option value="3"><?php echo $entry_type_3; ?></option>
                  <?php } ?>

                  <?php if ($type == 11) { ?>
                    <option value="11" selected="selected"><?php echo $entry_type_11; ?></option>
                  <?php } else { ?>
                    <option value="11"><?php echo $entry_type_11; ?></option>
                  <?php } ?>
                  <?php if ($type == 12) { ?>
                    <option value="12" selected="selected"><?php echo $entry_type_12; ?></option>
                  <?php } else { ?>
                    <option value="12"><?php echo $entry_type_12; ?></option>
                  <?php } ?>

                </select></td>
            </tr>
            <tr class="expand2">
              <td><span class="required">*</span> <?php echo $entry_href; ?></td>
              <td><select name="cat_href" style="width:437px;">
                  <option value="#"><?php echo $text_none; ?></option>
                  <?php foreach ($categories as $category) { ?>
                  <?php if ($category['href'] == $cat_href) { ?>
                  <option value="<?php echo $category['href']; ?>" selected="selected"><?php echo $category['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr class="expand3">
              <td><span class="required">*</span> <?php echo $entry_href; ?></td>
              <td><select name="man_href" style="width:437px;">
                  <option value="#"><?php echo $text_none; ?></option>
                  <?php foreach ($manufacturers as $manufacturer) { ?>
                  <?php if ($manufacturer['href'] == $man_href) { ?>
                  <option value="<?php echo $manufacturer['href']; ?>" selected="selected"><?php echo $manufacturer['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr class="expand4">
              <td><span class="required">*</span> <?php echo $entry_href; ?></td>
              <td><select name="info_href" style="width:437px;">
                  <option value="#"><?php echo $text_none; ?></option>
                  <?php foreach ($informations as $information) { ?>
                  <?php if ($information['href'] == $info_href) { ?>
                  <option value="<?php echo $information['href']; ?>" selected="selected"><?php echo $information['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $information['href']; ?>"><?php echo $information['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr class="expand0">
              <td><?php echo $entry_subcategory; ?></td>
              <td><select name="subcategory">
                  <?php if ($subcategory) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <option value="0"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr class="expand1">
              <td><span class="required">*</span> <?php echo $entry_href; ?></td>
              <td><input type="text" size="80" name="href" value="<?php echo $href; ?>" size="40" />
                <?php if ($error_href) { ?>
                <span class="error"><?php echo $error_href; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_class; ?></td>
              <td><input type="text" name="class" value="<?php echo $class; ?>" size="20" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_sort_order; ?></td>
              <td><input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="1" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td><select name="status">
                  <?php if ($status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
          </table>

      </form>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
var mode = <?php echo $type; ?>;
$(document).ready(function () {

    if (mode == 0) { select_0(); }
    if (mode == 1) { select_1(); }
    if (mode == 2) { select_2(); }
    if (mode == 3) { select_3(); }
    if (mode == 4) { select_0(); }
    if (mode == 5) { select_0(); }

});
//--></script>

<script type="text/javascript"><!--
$('#type').bind('change', function() {
    mode = $("[name='type']").val();

    if (mode == 0) { select_0(); }
    if (mode == 1) { select_1(); }
    if (mode == 2) { select_2(); }
    if (mode == 3) { select_3(); }
    if (mode == 4) { select_0(); }
    if (mode == 5) { select_0(); }

});
//--></script>

<script type="text/javascript"><!--
    function select_0() {
        $('.expand1').each(function() {
            $(this).css('display', 'table-row');
        });

        $('.expand2').each(function() { $(this).css('display', 'none'); });
        $('.expand3').each(function() { $(this).css('display', 'none'); });
        $('.expand4').each(function() { $(this).css('display', 'none'); });
    }

    function select_1() {
        $('.expand1').each(function() { $(this).css('display', 'none'); });

        $('.expand2').each(function() {
            $(this).css('display', 'table-row');
        });

        $('.expand3').each(function() { $(this).css('display', 'none'); });
        $('.expand4').each(function() { $(this).css('display', 'none'); });
    }

    function select_2() {
        $('.expand1').each(function() { $(this).css('display', 'none'); });
        $('.expand2').each(function() { $(this).css('display', 'none'); });

        $('.expand3').each(function() {
            $(this).css('display', 'table-row');
        });

        $('.expand4').each(function() { $(this).css('display', 'none'); });
    }

    function select_3() {
        $('.expand1').each(function() { $(this).css('display', 'none'); });
        $('.expand2').each(function() { $(this).css('display', 'none'); });
        $('.expand3').each(function() { $(this).css('display', 'none'); });

        $('.expand4').each(function() {
            $(this).css('display', 'table-row');
        });
    }

//--></script>

<script type="text/javascript"><!--
$('#tabs a').tabs(); 
$('#languages a').tabs();
//--></script> 
<?php echo $footer; ?>