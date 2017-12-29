<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/alys_discount_box.css" />
<?php if ($enable_box_enter || $enable_box_time) { ?>
<!-- Форма которая выводится при старте или после паузы -->
<div id="podpiska" class="region-confirm-form" style="display:none;">
    <p class="city-name"><?php echo $title; ?></p>
    <a href="#" id="close_popup" class="city-variant yes"><span></span><?php echo $button; ?></a>
    <?php echo $text; ?>
</div>
<?php } ?>

<?php if ($enable_box_enter) { ?>
<script>
    $(document).ready(function () {
        al_show_popup_box();
    });
</script>
<?php } ?>

<?php if ($enable_box_time) { ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery.timers.js"></script>
<script>
    $(document).oneTime("<?php echo $time_delay; ?>" + "s", "popup_box_timer_start", function() {
        al_show_popup_box();
    });
</script>
<?php } ?>

<script>
function al_show_popup_box() {

    if (document.cookie.indexOf('visited=true') == -1) {
        $('#podpiska').fadeIn('slow');
    }
}

// Данная функция закрывает окно
$("#close_popup").click(function () {
    $('#podpiska').fadeOut('slow');
    fixed_answer();
});

function fixed_answer() {
    var fifteenDays = 1000*60*60*24*1 ;//10*1000;
    var expires = new Date((new Date()).valueOf() + fifteenDays);
    document.cookie = "visited=true;expires=" + expires.toUTCString();
}

function save_email() {

    $.ajax({
        url: 'index.php?route=module/alys_discount_box/save_email',
        data: '&email=' + $('#mail-skidka').val(),
        type: 'POST',
        dataType: 'json',
        success: function(json) {
            if (json['success']) {
                $(".poluchite-skidku").text(json['title_exit']);
                $(".poluchit-skidku-box").hide();
                $(".pod-off").show();

                $(document).oneTime("3s", "popup_box_timer_close", function() {
                    $('#podpiska').fadeOut('slow');
                    fixed_answer();
                });

            }

            if (json['error_save']) {
                $(".poluchite-skidku").text(json['error_save']);
                $(".poluchit-skidku-box").hide();
            }
        }
    });
}
</script>