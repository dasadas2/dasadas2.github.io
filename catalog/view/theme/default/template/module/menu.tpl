<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/menu2nd.css" />

<?php if ($menus) { ?>
<div id="menu2nd">
  <ul>
    <?php foreach ($menus as $menu) { ?>

    <?php
        if ($menu['href'] != '#') {
            $href = 'href="' . $menu['href'] . '"';
        } else {
            $href = '';
        }

        if ($menu['class'] != '') {
            $class = $menu['class'];
        } else {
            $class = '';
        }

        if ($menu['active']) {
            $class .= " active";
        }
    ?>

    <li><?php if ($class) { ?>
	    <a <?php echo $href; ?> class="<?php echo $class; ?>"><?php echo $menu['name']; ?></a>
	<?php } else { ?>
	    <a <?php echo $href; ?>><?php echo $menu['name']; ?></a>
	<?php } ?>

      <?php if ($menu['children']) { ?>
      <div>
        <?php for ($i = 0; $i < count($menu['children']);) { ?>
        <ul>
          <?php $j = $i + ceil(count($menu['children']) / $menu['column']); ?>
          <?php for (; $i < $j; $i++) { ?>
          <?php if (isset($menu['children'][$i])) { ?>
          <li>
            <a href="<?php echo $menu['children'][$i]['href']; ?>"><?php echo $menu['children'][$i]['name']; ?></a>

                <?php if( $menu['children'][$i]['children'] ) { ?>
                <div class="top-sub-menu">
                  <ul>
                    <?php foreach( $menu['children'][$i]['children'] as $menu3item ) { ?>
                      <li><a href="<?php echo $menu3item['href']; ?>"><?php echo $menu3item['name']; ?></a></li>
                    <?php } ?>
                  </ul>
                </div>
                <?php } ?>

          </li>
          <?php } ?>
          <?php } ?>
        </ul>
        <?php } ?>
      </div>
      <?php } ?>
    </li>
    <?php } ?>
  </ul>
</div>
<?php } ?>

<script type="text/javascript"><!--
var mode = <?php echo $menu_mode; ?>;
$(document).ready(function() {

    //$('#menu').html($('#menu2nd').html());
    //$('#menu2nd').hide();

    if (mode == 1)
    {
        $('#menu').hide();
    }
    $('#menu').after($('#menu2nd'));

});
//--></script>
