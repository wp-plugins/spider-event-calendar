<?php

if (function_exists('current_user_can')) {
  if (!current_user_can('manage_options')) {
    die('Access Denied');
  }
}

function add_spider_calendar() {
  html_add_spider_calendar();
}

function show_spider_calendar() {
  global $wpdb;
  $order = " ORDER BY title ASC";
  $sort["default_style"] = "manage-column column-autor sortable desc";
  $sort["sortid_by"] = "title";
  $sort["custom_style"] = "manage-column column-title sorted asc";
  $sort["1_or_2"] = "2";
  if (isset($_POST['page_number'])) {
    if ($_POST['asc_or_desc'] && ($_POST['asc_or_desc'] == 1)) {
      if (isset($_POST['order_by'])) {
        $sort["sortid_by"] = $wpdb->escape($_POST['order_by']);
      }
      $sort["custom_style"] = "manage-column column-title sorted asc";
      $sort["1_or_2"] = "2";
      $order = "ORDER BY " . $sort["sortid_by"] . " ASC";
    }
    else {
      $sort["custom_style"] = "manage-column column-title sorted desc";
      $sort["1_or_2"] = "1";
      $order = "ORDER BY " . $sort["sortid_by"] . " DESC";
    }
    if ($_POST['page_number']) {
      $limit = ($_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
  }
  else {
    $limit = 0;
  }
  if (isset($_POST['search_events_by_title'])) {
    $search_tag = esc_html($_POST['search_events_by_title']);
  }
  else {
    $search_tag = "";
  }
  if ($search_tag) {
    $where = ' WHERE title LIKE "%' . $search_tag . '%"';
  }
  else {
    $where = ' ';
  }
  // Get the total number of records.
  $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "spidercalendar_calendar" . $where;
  $total = $wpdb->get_var($query);
  $pageNav['total'] = $total;
  $pageNav['limit'] = $limit / 20 + 1;
  $query = "SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar" . $where . " " . $order . " " . " LIMIT " . $limit . ",20";
  $rows = $wpdb->get_results($query);
  // display function
  html_show_spider_calendar($rows, $pageNav, $sort);
}

// Edit calendar.
function edit_spider_calendar($id) {
  global $wpdb;
  $row = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE id=\'' . $id . '\'');
  html_edit_spider_calendar($row);
}

// Delete calendar.
function remove_spider_calendar($id) {
  global $wpdb;
  $sql_remov_vid = "DELETE FROM " . $wpdb->prefix . "spidercalendar_calendar WHERE id='" . $id . "'";
  $sql_remov_eve = "DELETE FROM " . $wpdb->prefix . "spidercalendar_event WHERE calendar='" . $id . "'";
  if (!$wpdb->query($sql_remov_vid)) {
    ?>
    <div id="message" class="error"><p>Calendar Not Deleted.</p></div>
    <?php
  }
  else {
    ?>
    <div class="updated"><p><strong>Calendar Deleted.</strong></p></div>
    <?php
    $count_eve = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'spidercalendar_event WHERE calendar=' . $id);
    if ($count_eve) {
      if (!$wpdb->query($sql_remov_eve)) {
        ?>
        <div id="message" class="error"><p>Events Not Deleted.</p></div>
        <?php
      }
    }
  }
}

// Save calendar.
function apply_spider_calendar($id) {
  if (!$id) {
    echo '<h1 style="color:#00C">Error. ID does not exist.</h1>';
    exit;
  }
  $title = (isset($_POST["title"]) ? esc_html(stripslashes($_POST["title"])) : '');
  $user_type = (isset($_POST["user_type"]) ? esc_html($_POST["user_type"]) : '');
  $time_format = (isset($_POST["time_format"]) ? (int) $_POST["time_format"] : 0);
  $def_year = (isset($_POST["def_year"]) ? esc_html($_POST["def_year"]) : '');
  $def_month = (isset($_POST["def_month"]) ? esc_html($_POST["def_month"]) : '');
  $allow_publish = (isset($_POST["allow_publish"]) ? esc_html($_POST["allow_publish"]) : '');
  $published = (isset($_POST["published"]) ? (int) $_POST["published"] : 1);
  global $wpdb;
  if ($id === -1) {
    $save_or_no = $wpdb->insert($wpdb->prefix . 'spidercalendar_calendar', array(
      'id' => NULL,
      'title' => $title,
      'gid' => $user_type,
      'def_year' => $def_year,
      'def_month' => $def_month,
      'time_format' => $time_format,
      'allow_publish' => $allow_publish,
      'published' => $published,
    ), array(
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%d'
    ));
  }
  else {
    $save_or_no = $wpdb->update($wpdb->prefix . 'spidercalendar_calendar', array(
      'title' => $title,
      'gid' => $user_type,
      'time_format' => $time_format,
      'def_year' => $def_year,
      'def_month' => $def_month,
      'allow_publish' => $allow_publish,
      'published' => $published,
    ), array('id' => $id), array(
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%d'
    ));
  }
  if ($save_or_no === FALSE) {
    ?>
    <div class="updated"><p><strong>Error. Please install plugin again.</strong></p></div>
    <?php
    return FALSE;
  }
  else {
    ?>
    <div class="updated"><p><strong>Calendar Saved.</strong></p></div>
    <?php
    return TRUE;
  }
}

// Publish/Unpublish calendar.
function spider_calendar_published($id) {
  global $wpdb;
  $publish = $wpdb->get_var($wpdb->prepare('SELECT published FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE `id`="%d"', $id));
  if ($publish) {
    $publish = 0;
    $publish_unpublish = 'Calendar unpublished.';
  }
  else {
    $publish = 1;
    $publish_unpublish = 'Calendar published.';
  }
  $save_or_no = $wpdb->update($wpdb->prefix . 'spidercalendar_calendar', array(
      'published' => $publish,
    ), array('id' => $id), array(
      '%d',
    ));
  if ($save_or_no !== FALSE) {
    ?>
    <div class="updated"><p><strong><?php echo $publish_unpublish; ?></strong></p></div>
    <?php
  }
}

// Event in table
function show_spider_event($calendar_id) {
  global $wpdb;
  $order = " ORDER BY title ASC";
  $sort["default_style"] = "manage-column column-autor sortable desc";
  $sort["sortid_by"] = "title";
  $sort["custom_style"] = "manage-column column-title sorted asc";
  $sort["1_or_2"] = "2";
  if (isset($_POST['page_number'])) {
    if (isset($_POST['asc_or_desc']) && ($_POST['asc_or_desc'] == 1)) {
      $sort["sortid_by"] = ((isset($_POST['order_by'])) ? $wpdb->escape($_POST['order_by']) : 'title');
      $sort["custom_style"] = "manage-column column-title sorted asc";
      $sort["1_or_2"] = "2";
      $order = "ORDER BY " . $sort["sortid_by"] . " ASC";
    }
    else {
      $sort["custom_style"] = "manage-column column-title sorted desc";
      $sort["1_or_2"] = "1";
      $order = "ORDER BY " . $sort["sortid_by"] . " DESC";
    }
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
  }
  else {
    $limit = 0;
  }
  if (isset($_POST['search_events_by_title'])) {
    $search_tag = $_POST['search_events_by_title'];
  }
  else {
    $search_tag = "";
  }
  if ($search_tag) {
    $where = ' AND title LIKE "%' . $search_tag . '%"';
  }
  else {
    $where = '';
  }
  if (isset($_POST['startdate']) && $_POST['startdate']) {
    $where .= ' AND date > \'' . $_POST['startdate'] . '\' ';
  }
  if (isset($_POST['enddate']) && $_POST['enddate']) {
    $where .= ' AND date < \'' . $_POST['enddate'] . '\' ';
  }
  // Get the total number of records.
  $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "spidercalendar_event WHERE calendar=" . $calendar_id . " " . $where . " ";
  $total = $wpdb->get_var($query);
  $pageNav['total'] = $total;
  $pageNav['limit'] = $limit / 20 + 1;
  $query = "SELECT * FROM " . $wpdb->prefix . "spidercalendar_event WHERE calendar=" . $calendar_id . " " . $where . " " . $order . " " . " LIMIT " . $limit . ",20";
  $rows = $wpdb->get_results($query);
  $cal_name = $wpdb->get_var('SELECT title' . ' FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE id=' . $calendar_id);
  html_show_spider_event($rows, $pageNav, $sort, $calendar_id, $cal_name);
}

// Add an event.
function add_spider_event($calendar_id) {
  global $wpdb;
  $cal_name = $wpdb->get_var('SELECT title' . ' FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE id=' . $calendar_id);
  html_add_spider_event($calendar_id, $cal_name);
}

// Edit event.
function edit_spider_event($calendar_id, $id) {
  global $wpdb;
  $row = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'spidercalendar_event WHERE id=\'' . $id . '\'');
  $calendar = $row->calendar;
  $query = 'SELECT title FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE id=' . $calendar;
  $calendar_name = $wpdb->get_var($query);
  $cal_name = $wpdb->get_var('SELECT title' . ' FROM ' . $wpdb->prefix . 'spidercalendar_calendar WHERE id=' . $calendar_id);
  html_edit_spider_event($row, $calendar_id, $id, $cal_name);
}

// Save event.
function apply_spider_event($calendar_id, $id) {
  global $wpdb;
  $title = ((isset($_POST['title'])) ? esc_html(stripslashes($_POST['title'])) : '');
  $text_for_date = ((isset($_POST['text_for_date'])) ? stripslashes($_POST['text_for_date']) : '');
  $published = ((isset($_POST['published'])) ? (int) $_POST['published'] : 1);
  $repeat = ((isset($_POST['repeat'])) ? esc_html($_POST['repeat']) : '');
  $week = ((isset($_POST['week'])) ? esc_html($_POST['week']) : '');
  $month = ((isset($_POST['month'])) ? esc_html($_POST['month']) : '');
  $monthly_list = ((isset($_POST['monthly_list'])) ? esc_html($_POST['monthly_list']) : '');
  $month_type = ((isset($_POST['month_type'])) ? esc_html($_POST['month_type']) : '');
  $month_week = ((isset($_POST['month_week'])) ? esc_html($_POST['month_week']) : '');
  $year_month = ((isset($_POST['year_month'])) ? esc_html($_POST['year_month']) : '');
  $repeat_method = ((isset($_POST['repeat_method'])) ? esc_html($_POST['repeat_method']) : 'no_repeat');
  $date = ((isset($_POST['date'])) ? esc_html($_POST['date']) : '');
  $date_end = ((isset($_POST['date_end'])) ? esc_html($_POST['date_end']) : '');
  if ($date_end == '' && $repeat_method != 'no_repeat') {
    $date_end = '2070-12-12';
  }
  $select_from = ((isset($_POST['select_from'])) ? esc_html($_POST['select_from']) : '');
  $select_to = ((isset($_POST['select_to'])) ? esc_html($_POST['select_to']) : '');
  $selhour_from = ((isset($_POST['selhour_from'])) ? esc_html($_POST['selhour_from']) : '');
  $selhour_to = ((isset($_POST['selhour_to'])) ? esc_html($_POST['selhour_to']) : '');
  $selminute_from = ((isset($_POST['selminute_from'])) ? esc_html($_POST['selminute_from']) : '');
  $selminute_to = ((isset($_POST['selminute_to'])) ? esc_html($_POST['selminute_to']) : '');
  if ($selhour_from) {
    if ($selhour_to) {
      $time = $selhour_from . ':' . $selminute_from . '' . $select_from . '-' . $selhour_to . ':' . $selminute_to . '' . $select_to;
    }
    else {
      $time = $selhour_from . ':' . $selminute_from . ' ' . $select_from;
    }
  }
  else {
    $time = '';
  }
  if ($id === -1) {
    $save = $wpdb->insert($wpdb->prefix . 'spidercalendar_event', array(
      'id' => NULL,
      'title' => $title,
      'time' => $time,
      'calendar' => $calendar_id,
      'date' => $date,
      'text_for_date' => $text_for_date,
      'published' => $published,
      'repeat' => $repeat,
      'week' => $week,
      'date_end' => $date_end,
      'month' => $month,
      'monthly_list' => $monthly_list,
      'month_week' => $month_week,
      'month_type' => $month_type,
      'year_month' => $year_month,
      'repeat_method' => $repeat_method,
      'userID' => ''
    ), array(
      '%d',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
    ));
  }
  else {
    $save = $wpdb->update($wpdb->prefix . 'spidercalendar_event', array(
      'title' => $title,
      'time' => $time,
      'calendar' => $calendar_id,
      'date' => $date,
      'text_for_date' => $text_for_date,
      'published' => $published,
      'repeat' => $repeat,
      'week' => $week,
      'date_end' => $date_end,
      'month' => $month,
      'monthly_list' => $monthly_list,
      'month_type' => $month_type,
      'month_week' => $month_week,
      'year_month' => $year_month,
      'repeat_method' => $repeat_method
    ), array('id' => $id), array(
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%d',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s',
      '%s'
    ));
  }
  if ($save !== FALSE) {
    ?>
    <div class="updated"><p><strong>Item Saved.</strong></p></div>
    <?php
    return TRUE;
  }
  else {
    ?>
    <div class="updated"><p><strong>Error. Please install plugin again.</strong></p></div>
    <?php
    return FALSE;
  }
}

// Publish/Unpublish event.
function published_spider_event($id) {
  global $wpdb;
  $publish = $wpdb->get_var('SELECT published FROM ' . $wpdb->prefix . 'spidercalendar_event WHERE `id`=' . $id);
  if ($publish) {
    $publish = 0;
    $publish_unpublish = 'Event unpublished.';
  }
  else {
    $publish = 1;
    $publish_unpublish = 'Event published.';
  }
  $save_or_no = $wpdb->update($wpdb->prefix . 'spidercalendar_event', array(
      'published' => $publish,
    ), array('id' => $id), array(
      '%d',
    ));
  if ($save_or_no !== FALSE) {
    ?>
    <div class="updated"><p><strong><?php echo $publish_unpublish; ?></strong></p></div>
    <?php
  }
}

// Delete event.
function remove_spider_event($calendar_id, $id) {
  global $wpdb;
  $sql_remove_vid = "DELETE FROM " . $wpdb->prefix . "spidercalendar_event WHERE id='" . $id . "'";
  if (!$wpdb->query($sql_remove_vid)) {
    ?>
    <div id="message" class="error"><p>Event Not Deleted.</p></div>
    <?php
  }
  else {
    ?>
    <div class="updated"><p><strong>Event Deleted.</strong></p></div>
    <?php
  }
}

?>