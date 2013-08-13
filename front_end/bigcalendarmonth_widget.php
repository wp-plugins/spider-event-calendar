<?php
function big_calendar_month_widget() {
  require_once("frontend_functions.php");
  global $wpdb;
  $widget = ((isset($_GET['widget']) && (int) $_GET['widget']) ? (int) $_GET['widget'] : 0);
  $many_sp_calendar = ((isset($_GET['many_sp_calendar']) && is_numeric(esc_html($_GET['many_sp_calendar']))) ? esc_html($_GET['many_sp_calendar']) : 1);
  $calendar_id = (isset($_GET['calendar']) ? (int) $_GET['calendar'] : '');
  $theme_id = (isset($_GET['theme_id']) ? (int) $_GET['theme_id'] : 1);
  $date = ((isset($_GET['date']) && IsDate_inputed(esc_html($_GET['date']))) ? esc_html($_GET['date']) : '');
  $view_select = (isset($_GET['select']) ? esc_html($_GET['select']) : 'month,');
  $path_sp_cal = (isset($_GET['cur_page_url']) ? esc_html($_GET['cur_page_url']) : '');

  $theme = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'spidercalendar_widget_theme WHERE id=%d', $theme_id));
  $weekstart = $theme->week_start_day;
  $bg = '#' . $theme->header_bgcolor;
  $bg_color_selected = '#' . $theme->bg_color_selected;
  $color_arrow = '#' . $theme->arrow_color;
  $evented_color = '#' . $theme->text_color_this_month_evented;
  $evented_color_bg = '#' . $theme->bg_color_this_month_evented;
  $sun_days = '#' . $theme->text_color_sun_days;
  $text_color_other_months = '#' . $theme->text_color_other_months;
  $text_color_this_month_unevented = '#' . $theme->text_color_this_month_unevented;
  $text_color_month = '#' . $theme->text_color_month;
  $color_week_days = '#' . $theme->text_color_week_days;
  $text_color_selected = '#' . $theme->text_color_selected;
  $border_day = '#' . $theme->border_day;
  $calendar_width = $theme->width;
  $calendar_bg = '#' . $theme->footer_bgcolor;
  $weekdays_bg_color = '#' . $theme->weekdays_bg_color;
  $weekday_su_bg_color = '#' . $theme->su_bg_color;
  $cell_border_color = '#' . $theme->cell_border_color;
  $year_font_size = $theme->year_font_size;
  $year_font_color = '#' . $theme->year_font_color;
  $year_tabs_bg_color = '#' . $theme->year_tabs_bg_color;
  $font_year = $theme->font_year;
  $font_month = $theme->font_month;
  $font_day = $theme->font_day;
  $font_weekday = $theme->font_weekday;
  
  $popup_width = $theme->popup_width;
  $popup_height = $theme->popup_height;

  __('January', 'sp_calendar');
  __('February', 'sp_calendar');
  __('March', 'sp_calendar');
  __('April', 'sp_calendar');
  __('May', 'sp_calendar');
  __('June', 'sp_calendar');
  __('July', 'sp_calendar');
  __('August', 'sp_calendar');
  __('September', 'sp_calendar');
  __('October', 'sp_calendar');
  __('November', 'sp_calendar');
  __('December', 'sp_calendar');
  if ($date != '') {
    $date_REFERER = $date;
  }
  else {
    $date_REFERER = date("Y-m");
    $date = date("Y") . '-' . php_Month_num(date("F")) . '-' . date("d");
  }
  
  $year_REFERER = substr($date_REFERER, 0, 4);
  $month_REFERER = Month_name(substr($date_REFERER, 5, 2));
  $day_REFERER = substr($date_REFERER, 8, 2);

  $year = substr($date, 0, 4);
  $month = Month_name(substr($date, 5, 2));
  $day = substr($date, 8, 2);

  $this_month = substr($year . '-' . add_0((Month_num($month))), 5, 2);
  $prev_month = add_0((int) $this_month - 1);
  $next_month = add_0((int) $this_month + 1);

  $cell_width = $calendar_width / 7;
  $cell_width = (int) $cell_width - 2;

  $view = 'bigcalendarmonth_widget';
  $views = explode(',', $view_select);
  $defaultview = 'month';
  array_pop($views);
  $display = '';
  if (count($views) == 0) {
    $display = "display:none";
  }
  if(count($views) == 1 && $views[0] == $defaultview) {
    $display = "display:none";
  }
  ?>
  <style type='text/css'>
    #calendar_<?php echo $many_sp_calendar; ?> table {
      border-collapse: initial;
      border:0px;
    }
    #calendar_<?php echo $many_sp_calendar; ?> table td {
      padding: 0px;
      vertical-align: none;
      border-top:none;
      line-height: none;
      text-align: none;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .cell_body td {
      border:1px solid <?php echo $cell_border_color; ?>;
      font-family: <?php echo $font_day; ?>;
    }
    #calendar_<?php echo $many_sp_calendar; ?> p, ol, ul, dl, address {
      margin-bottom: 0;
    }
    #calendar_<?php echo $many_sp_calendar; ?> td,
    #calendar_<?php echo $many_sp_calendar; ?> tr,
    #spiderCalendarTitlesList_<?php echo $many_sp_calendar; ?> td,
    #spiderCalendarTitlesList_<?php echo $many_sp_calendar; ?> tr {
       border:none;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .cala_arrow a:link,
    #calendar_<?php echo $many_sp_calendar; ?> .cala_arrow a:visited {
      color: <?php echo $color_arrow; ?>;
      text-decoration: none;
      background: none;
      font-size: 16px;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .cala_arrow a:hover {
      color: <?php echo $color_arrow; ?>;
      text-decoration:none;
      background:none;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .cala_day a:link,
    #calendar_<?php echo $many_sp_calendar; ?> .cala_day a:visited {
      text-decoration:underline;
      background:none;
      font-size:11px;
    }
    #calendar_<?php echo $many_sp_calendar; ?> a {
      font-weight: normal;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .cala_day a:hover {
      font-size:12px;
      text-decoration:none;
      background:none;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .calyear_table {
      border-spacing:0;
      width:100%;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .calmonth_table {	
      border-spacing: 0;
      vertical-align: middle;
      width: 100%;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .calbg {
      background-color:<?php echo $bg; ?>;
      text-align:center;
      vertical-align: middle;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .caltext_color_other_months {
      color:<?php echo $text_color_other_months; ?>;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .caltext_color_this_month_unevented {
      color:<?php echo $text_color_this_month_unevented; ?>;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .calsun_days {
      color:<?php echo $sun_days; ?>;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .calborder_day {
      border: solid <?php echo $border_day; ?> 1px;
    }
    #TB_window {
      z-index: 10000;
    }
    #calendar_<?php echo $many_sp_calendar; ?> .views {
      float: right;
      background-color: <?php echo $calendar_bg; ?>;
      height: 25px;
      width: <?php echo ($calendar_width / 4) - 2; ?>px;
      margin-left: 2px;
      text-align: center;
      cursor: pointer;
      position: relative;
      top: 3px;
      font-family: <?php echo $font_month; ?>;
    }
    #calendar_<?php echo $many_sp_calendar; ?> table tr {
      background: transparent !important;
    }
  </style>
  <div id="calendar_<?php echo $many_sp_calendar; ?>" style="width:<?php echo $calendar_width; ?>px;">
    <table cellpadding="0" cellspacing="0" style="border-spacing:0; width:<?php echo $calendar_width; ?>px; height:190px; margin:0; padding:0;background-color:<?php echo $calendar_bg; ?>">
      <tr style="background-color:#FFFFFF;">
        <td style="background-color:#FFFFFF;">
          <div id="views_tabs" style="<?php echo $display; ?>">
            <div class="views" style="<?php if (!in_array('day', $views) AND $defaultview != 'day') echo 'display:none;'; if ($view == 'bigcalendarday_widget') echo 'background-color:' . $bg . ';height:28px;top:0;'; ?>"
              onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar; ?>', '<?php echo add_query_arg(array(
                'action' => 'spiderbigcalendar_day_widget',
                'theme_id' => $theme_id,
                'calendar' => $calendar_id,
                'select' => $view_select,
                'date' => $year . '-' . add_0((Month_num($month))) . '-' . date('d'),
                'many_sp_calendar' => $many_sp_calendar,
                'cur_page_url' => $path_sp_cal,
                'widget' => $widget,
                ), admin_url('admin-ajax.php'));?>')" ><span style="position:relative;top:15%;color:<?php echo $text_color_month; ?>;"><?php echo __('Day', 'sp_calendar'); ?></span>
            </div>
            <div class="views" style="<?php if (!in_array('week', $views) AND $defaultview != 'week') echo 'display:none;'; if ($view == 'bigcalendarweek_widget') echo 'background-color:' . $bg . ';height:28px;top:0;'; ?>"
              onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar; ?>', '<?php echo add_query_arg(array(
                'action' => 'spiderbigcalendar_week_widget',
                'theme_id' => $theme_id,
                'calendar' => $calendar_id,
                'select' => $view_select,
                'months' => $prev_month . ',' . $this_month . ',' . $next_month,
                'date' => $year . '-' . add_0((Month_num($month))) . '-' . date('d'),
                'many_sp_calendar' => $many_sp_calendar,
                'cur_page_url' => $path_sp_cal,
                'widget' => $widget,
                ), admin_url('admin-ajax.php'));?>')" ><span style="position:relative;top:15%;color:<?php echo $text_color_month; ?>;"><?php echo __('Week', 'sp_calendar'); ?></span>
            </div>
            <div class="views" style="<?php if (!in_array('list', $views) AND $defaultview != 'list') echo 'display:none;'; if ($view == 'bigcalendarlist_widget') echo 'background-color:' . $bg . ';height:28px;top:0;'; ?>"
              onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar; ?>', '<?php echo add_query_arg(array(
                'action' => 'spiderbigcalendar_list_widget',
                'theme_id' => $theme_id,
                'calendar' => $calendar_id,
                'select' => $view_select,
                'date' => $year . '-' . add_0((Month_num($month))),
                'many_sp_calendar' => $many_sp_calendar,
                'cur_page_url' => $path_sp_cal,
                'widget' => $widget,
                ), admin_url('admin-ajax.php'));?>')"><span style="position:relative;top:15%;color:<?php echo $text_color_month; ?>;"><?php echo __('List', 'sp_calendar'); ?></span>
            </div>
            <div class="views" style="<?php if (!in_array('month', $views) AND $defaultview != 'month') echo 'display:none;'; if ($view == 'bigcalendarmonth_widget') echo 'background-color:' . $bg . ';height:28px;top:0;'; ?>"
              onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar; ?>', '<?php echo add_query_arg(array(
                'action' => 'spiderbigcalendar_month_widget',
                'theme_id' => $theme_id,
                'calendar' => $calendar_id,
                'select' => $view_select,
                'date' => $year . '-' . add_0((Month_num($month))),
                'many_sp_calendar' => $many_sp_calendar,
                'cur_page_url' => $path_sp_cal,
                'widget' => $widget,
                ), admin_url('admin-ajax.php'));?>')" ><span style="position:relative;top:15%;color:<?php echo $text_color_month; ?>;"><?php echo __('Month', 'sp_calendar'); ?></span>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td width="100%" style="padding:0; margin:0;">
          <form action="" method="get" style="background:none; margin:0; padding:0;">
            <table cellpadding="0" cellspacing="0" border="0" style="border-spacing:0; font-size:12px; margin:0; padding:0;" width="<?php echo $calendar_width; ?>" height="190">
              <tr height="28px" style="width:<?php echo $calendar_width; ?>px;">
                <td class="calbg" colspan="7" style="background-image:url('<?php echo plugins_url('/images/Stver.png', __FILE__); ?>');margin:0; padding:0;background-repeat: no-repeat;background-size: 100% 100%;" >
                  <?php //MONTH TABLE ?>
                  <table cellpadding="0" cellspacing="0" border="0" align="center" class="calmonth_table"  style="width:100%; margin:0; padding:0">
                    <tr>
                      <td style="text-align:left; margin:0; padding:0; line-height:16px" class="cala_arrow" width="20%">
                        <a href="javascript:showbigcalendar('bigcalendar<?php echo $many_sp_calendar ?>','<?php  
                          if (Month_num($month) == 1) {
                            $needed_date = ($year - 1) . '-12';
                          }
                          else {
                            $needed_date = $year . '-' . add_0((Month_num($month) - 1));
                          }
                          echo add_query_arg(array(
                            'action' => 'spiderbigcalendar_' . $defaultview . '_widget',
                            'theme_id' => $theme_id,
                            'calendar' => $calendar_id,
                            'select' => $view_select,
                            'date' => $needed_date,
                            'many_sp_calendar' => $many_sp_calendar,
                            'cur_page_url' => $path_sp_cal,
                            'widget' => $widget,
                            ), admin_url('admin-ajax.php'));
                            ?>')">&#9668;
                        </a>
                      </td>
                      <td width="60%" style="text-align:center; margin:0; padding:0; font-family:<?php echo $font_month; ?>">
                        <input type="hidden" name="month" readonly="" value="<?php echo $month; ?>"/>
                        <span style="font-size:<?php echo $year_font_size; ?>px;?>; color:<?php echo $text_color_month; ?>;"><?php echo __($month, 'sp_calendar'); ?></span>
                      </td>
                      <td style="text-align:right; margin:0; padding:0; line-height:16px"  class="cala_arrow" width="20%">
                        <a href="javascript:showbigcalendar('bigcalendar<?php echo $many_sp_calendar ?>','<?php
                          if (Month_num($month) == 12) {
                            $needed_date = ($year + 1) . '-01';
                          }
                          else {
                            $needed_date = $year . '-' . add_0((Month_num($month) + 1));
                          }
                          echo add_query_arg(array(
                            'action' => 'spiderbigcalendar_' . $defaultview . '_widget',
                            'theme_id' => $theme_id,
                            'calendar' => $calendar_id,
                            'select' => $view_select,
                            'date' => $needed_date,
                            'many_sp_calendar' => $many_sp_calendar,
                            'cur_page_url' => $path_sp_cal,
                            'widget' => $widget,
                            ), admin_url('admin-ajax.php'));
                            ?>')">&#9658;
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr class="cell_body" align="center"  height="10%" style="background-color:<?php echo $weekdays_bg_color; ?>;width:<?php echo $calendar_width; ?>px">
                <?php if ($weekstart == "su") { ?>
                <td style="font-family:<?php echo $font_weekday; ?>;background-color:<?php echo $weekday_su_bg_color; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Su', 'sp_calendar'); ?> </b></div>
                </td>
                <?php } ?>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Mo', 'sp_calendar'); ?> </b></div>
                </td>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Tu', 'sp_calendar'); ?> </b></div>
                </td>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('We', 'sp_calendar'); ?> </b></div>
                </td>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Th', 'sp_calendar'); ?> </b></div>
                </td>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Fr', 'sp_calendar'); ?> </b></div>
                </td>
                <td style="font-family:<?php echo $font_weekday; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Sa', 'sp_calendar'); ?> </b></div>
                </td>
                <?php if ($weekstart == "mo") { ?>
                <td style="font-family:<?php echo $font_weekday; ?>;background-color:<?php echo $weekday_su_bg_color; ?>;width:<?php echo $cell_width; ?>px; color:<?php echo $color_week_days; ?>; margin:0; padding:0">
                  <div class="calbottom_border" style="text-align:center; width:<?php echo $cell_width; ?>px; margin:0; padding:0;"><b> <?php echo __('Su', 'sp_calendar'); ?> </b></div>
                </td>
                <?php } ?>
              </tr>
  <?php
  $month_first_weekday = date("N", mktime(0, 0, 0, Month_num($month), 1, $year));
  if ($weekstart == "su") {
    $month_first_weekday++;
    if ($month_first_weekday == 8) {
      $month_first_weekday = 1;
    }
  }
  $month_days = date("t", mktime(0, 0, 0, Month_num($month), 1, $year));
  $last_month_days = date("t", mktime(0, 0, 0, Month_num($month) - 1, 1, $year));
  $weekday_i = $month_first_weekday;
  $last_month_days = $last_month_days - $weekday_i + 2;
  $percent = 1;
  $sum = $month_days - 8 + $month_first_weekday;
  if ($sum % 7 <> 0) {
    $percent = $percent + 1;
  }
  $sum = $sum - ($sum % 7);
  $percent = $percent + ($sum / 7);
  $percent = 107 / $percent;
  $all_calendar_files = php_getdays(1, $calendar_id, $date, $theme_id, $widget);
  $array_days = $all_calendar_files[0]['array_days'];
  $array_days1 = $all_calendar_files[0]['array_days1'];
  $title = $all_calendar_files[0]['title'];
  $ev_ids = $all_calendar_files[0]['ev_ids'];
  echo '      <tr class="cell_body" height="' . $percent . 'px" style="line-height:' . $percent . 'px">';
  for ($i = 1; $i < $weekday_i; $i++) {
    echo '          <td class="caltext_color_other_months" style="text-align:center;">' . $last_month_days . '</td>';
    $last_month_days = $last_month_days + 1;
  }
  for ($i = 1; $i <= $month_days; $i++) {
    if (isset($title[$i])) {
      $ev_title = explode('</p>', $title[$i]);
      array_pop($ev_title);
      $k = count($ev_title);
      $ev_id = explode('<br>', $ev_ids[$i]);
      array_pop($ev_id);
      $ev_ids_inline = implode(',', $ev_id);
    }
    if (($weekday_i % 7 == 0 and $weekstart == "mo") or ($weekday_i % 7 == 1 and $weekstart == "su")) {
      if ($i == $day_REFERER and $month == $month_REFERER and $year == $year_REFERER) {
        echo  ' <td class="cala_day" style="background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <div class="calborder_day" style="text-align:center; width:' . $cell_width . 'px; margin:0; padding:0;">
                    <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . '; text-decoration:underline;"
                      href="' . add_query_arg(array(
                        'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                        'theme_id' => $theme_id,
                        'calendar_id' => $calendar_id,
                        'ev_ids' => $ev_ids_inline,
                        'eventID' => $ev_id[0],
                        'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                        'many_sp_calendar' => $many_sp_calendar,
                        'cur_page_url' => $path_sp_cal,
                        'widget' => $widget,
                        'TB_iframe' => 1,
                        'tbWidth' => $popup_width,
                        'tbHeight' => $popup_height,
                        ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                    </a>
                  </div>
                </td>';
      }
      elseif ($i == date('j') and $month == date('F') and $year == date('Y')) {
        if (in_array($i, $array_days)) {
          if (in_array ($i, $array_days1)) {
            echo '
                <td class="cala_day" style="color:' . $text_color_selected . ';background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit; border: 2px solid ' . $border_day . '">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . ';text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
          }
          else {
            echo '
                <td class="cala_day" style="color:' . $text_color_selected . ';background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit; border: 2px solid ' . $border_day . '">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . ';text-align:center;text-decoration:underline;" 
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
          }
        }
        else {
          echo '
                <td class="calsun_days" style="color:' . $text_color_selected . ';background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit; border: 2px solid ' . $border_day . '">
                  <b>' . $i . '</b>
                </td>';
        }
      }
      else {
        if (in_array ($i, $array_days)) {
          if (in_array ($i, $array_days1)) {
            echo '
                <td class="cala_day" style="background-color:' . $evented_color_bg . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $evented_color . ';text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
          }
          else {
            echo '
                <td class="cala_day" style="background-color:' . $evented_color_bg . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $evented_color . ';text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
          }
        }
        else {
          echo  '
                <td class="calsun_days" style="text-align:center;padding:0; margin:0;line-height:inherit;">
                  <b>' . $i . '</b>
                </td>';
        }
      }
    }
    elseif ($i == $day_REFERER and $month == $month_REFERER and $year == $year_REFERER) {
      echo '    <td style="background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <div class="calborder_day" style="text-align:center; width:' . $cell_width . 'px; margin:0; padding:0;">
                    <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . '; text-decoration:underline;"
                      href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
    }
    else {
			if ($i == date('j') and $month == date('F') and $year == date('Y')) {
				if (in_array ($i, $array_days)) {
          if (in_array ($i, $array_days1)) {
            echo '
                <td class="cala_day" style="color:' . $text_color_selected . ';background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit; border: 2px solid ' . $border_day . '">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . '; text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
          }
          else {
            echo '
                <td class="cala_day" style="color:' . $text_color_selected . ';background-color:' . $bg_color_selected . ';text-align:center;padding:0; margin:0;line-height:inherit; border: 2px solid ' . $border_day . '">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $text_color_selected . '; text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b></a>
                </td>';
          }
        }
        else {
          echo '<td style="text-align:center; color:' . $text_color_selected . ';padding:0; margin:0; line-height:inherit; border: 2px solid ' . $border_day . '">
                  <b>' . $i . '</b>
                </td>';
        }
      }
      elseif (in_array($i, $array_days)) {
        if (in_array ($i, $array_days1)) {
          echo '<td class="cala_day" style="background-color:' . $evented_color_bg . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $evented_color . '; text-align:center;text-decoration:underline;"
                  href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b>
                  </a>
                </td>';
        }
        else {
          echo '<td class="cala_day" style="background-color:' . $evented_color_bg . ';text-align:center;padding:0; margin:0;line-height:inherit;">
                  <a class="thickbox-previewbigcalendar' . $many_sp_calendar . '" style="background:none;color:' . $evented_color . '; text-align:center;text-decoration:underline;"
                    href="' . add_query_arg(array(
                      'action' => ((isset($ev_id[1])) ? 'spiderseemore' : 'spidercalendarbig'),
                      'theme_id' => $theme_id,
                      'calendar_id' => $calendar_id,
                      'ev_ids' => $ev_ids_inline,
                      'eventID' => $ev_id[0],
                      'date' => $year . '-' . add_0(Month_num($month)) . '-' . $i,
                      'many_sp_calendar' => $many_sp_calendar,
                      'cur_page_url' => $path_sp_cal,
                      'widget' => $widget,
                      'TB_iframe' => 1,
                      'tbWidth' => $popup_width,
                      'tbHeight' => $popup_height,
                      ), admin_url('admin-ajax.php')) . '"><b>' . $i . '</b></a>
                </td>';
        }
			}
      else {
        echo '  <td style="text-align:center; color:' . $text_color_this_month_unevented . ';padding:0; margin:0; line-height:inherit;">
                  <b>' . $i . '</b>
                </td>';
      }
    }
    if ($weekday_i % 7 == 0 && $i <> $month_days) {
      echo   '</tr>
              <tr class="cell_body" height="' . $percent . 'px" style="line-height:' . $percent . 'px">';
      $weekday_i = 0;
    }
    $weekday_i++;
  }
  $weekday_i;
  $next_i = 1;
  if ($weekday_i != 1) {
    for ($i = $weekday_i; $i <= 7; $i++) {
      echo '    <td class="caltext_color_other_months" style="text-align:center;">' . $next_i . '</td>';
      $next_i++;
    }
  }
  echo '      </tr>';
  ?>
              <tr style="font-family: <?php echo $font_year; ?>;">
                <td colspan="2" onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar ?>','<?php 
                  echo add_query_arg(array(
                    'action' => 'spiderbigcalendar_' . $defaultview . '_widget',
                    'theme_id' => $theme_id,
                    'calendar' => $calendar_id,
                    'select' => $view_select,
                    'date' => ($year - 1) . '-' . add_0((Month_num($month))),
                    'many_sp_calendar' => $many_sp_calendar,
                    'cur_page_url' => $path_sp_cal,
                    'widget' => $widget,
                    ), admin_url('admin-ajax.php'));?>')" style="cursor:pointer;font-size:<?php echo $year_font_size; ?>px;color:<?php echo $year_font_color; ?>;text-align: center;background-color:<?php echo $year_tabs_bg_color; ?>">
                  <?php echo ($year - 1); ?>
                </td>
                <td colspan="3" style="font-size:<?php echo $year_font_size + 2; ?>px;color:<?php echo $year_font_color; ?>;text-align: center;border-right:1px solid <?php echo $cell_border_color; ?>;border-left:1px solid <?php echo $cell_border_color; ?>">
                  <?php echo $year; ?>
                </td>
                <td colspan="2" onclick="showbigcalendar('bigcalendar<?php echo $many_sp_calendar ?>','<?php
                  echo add_query_arg(array(
                    'action' => 'spiderbigcalendar_' . $defaultview . '_widget',
                    'theme_id' => $theme_id,
                    'calendar' => $calendar_id,
                    'select' => $view_select,
                    'date' => ($year + 1) . '-' . add_0((Month_num($month))),
                    'many_sp_calendar' => $many_sp_calendar,
                    'cur_page_url' => $path_sp_cal,
                    'widget' => $widget,
                    ), admin_url('admin-ajax.php'));?>')" style="cursor:pointer;font-size:<?php echo $year_font_size; ?>px;text-align: center;background-color:<?php echo $year_tabs_bg_color; ?>;color:<?php echo $year_font_color; ?>">
                  <?php echo ($year + 1); ?>
                </td>
              </tr>
            </table>
            <input type="text" value="1" name="day" style="display:none" />
          </form>
        </td>
      </tr>
    </table>
  </div>
  <?php
  die();
}

?>