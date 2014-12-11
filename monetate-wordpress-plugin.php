<?php
/*
Plugin Name: Monetate
Plugin URI: http://www.pilch.co.uk/
Description: Enables Monetate script within the head on your WordPress site.
Version: 1.0
Author: Richard Hellier
Author URI: http://www.pilch.co.uk
License: GPL
*/

/*
 * Plugin Action Links 
 * See http://code.tutsplus.com/articles/integrating-with-wordpress-ui-the-basics--wp-26713
*/ 

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'monetate_plugin_action_links' );

function monetate_plugin_action_links( $links ) {
   $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=monetate_script') .'" title="View/edit your Monetate settings">Settings</a>';
   $links[] = '<a href="https://marketer.monetate.net" target="_blank" title="Login to your Monetate Dashboard">Monetate Dashboard</a>';
   return $links;
} 

/* Puts code on Wordpress pages */
add_action('wp_head', 'monetate_tracking_code');

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'mon_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook(__FILE__, 'mon_remove' );

if (is_admin()) {
  /* Call the html code */
  add_action('admin_menu', 'mon_admin_menu');

  function mon_admin_menu() {
    add_options_page('monetate', 'Monetate', 'administrator', 'monetate_script', 'mon_html_page');
  }
}

function mon_install() {
  /* Creates new database field */
  add_option("mon_domain_name", '', '', 'yes');
  add_option("mon_alphanumeric_name", '', '', 'yes');

}

function mon_remove() {
  /* Deletes the database field */
  delete_option('mon_domain_name');
  delete_option('mon_alphanumeric_name');
}

function mon_html_page() {
?>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>  
  <a title="Monetate for Wordpress" href="http://www.monetate.com"><img alt="Monetate logo" src="http://hub.suttons.co.uk/wp-content/uploads/2014/12/monetate-transparent-logo.png" style="width:50%"/></a>
  <h3>By providing values into the fields below, you'll add Monetate tracking within the &lt;head&gt;</h3>
  <p>The 'domain' and 'alpha-numeric' values can be retrieved by either logging in to your <a href="https://marketer.monetate.net" target="_blank">Monetate Dashboard</a> and reviewing the url or by contacting your Account Manager.</p>
  <form method="POST" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="mon_domain_name">Domain:</label>
        </th>
        <td>
          <input id="mon_domain_name" name="mon_domain_name" value="<?php echo get_option('mon_domain_name'); ?>" class="required regular-text" required="required" />
          <span class="description">( Note: http or https protocol is NOT required! )</span>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="mon_alphanumeric_name">Alpha Numeric:</label>
        </th>
        <td>
          <input id="mon_alphanumeric_name" name="mon_alphanumeric_name" value="<?php echo get_option('mon_alphanumeric_name'); ?>" class="required regular-text" required="required" />
          <span class="description">( ex. b-17f08c7d )</span>
        </td>
      </tr>
      <tr valign="top">
        <td colspan="3">
          <p>This is the part of the url highlighted in bold: <span class="description">ex. https://marketer.monetate.net/control/<b>b-17f08c7d</b>/p/domain.co.uk/</span></p>
        </td>
      </tr>      
    </table>    
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="mon_domain_name,mon_alphanumeric_name" />    
    <p class="submit">
      <input class="button-primary" type="submit" name="Save" value="<?php _e('Save'); ?>" />
    </p>
  </form>
</div>
<?php
}

function monetate_tracking_code() {
  $alpha_name  = get_option("mon_alphanumeric_name");
  $domain_name = get_option("mon_domain_name");
  if (!empty($domain_name)) {

    $account_alpha = $alpha_name;
    $account_path = $domain_name;
    $account_path = "js/2/".$account_alpha."/p/".$account_path."/entry.js";
    $script_host  = "e.monetate.net";

    echo '<script type="text/javascript">
  var monetateT = new Date().getTime();
  (function() {
    var p = document.location.protocol;
    if (p == "http:" || p == "https:") {
      var m = document.createElement("script"); m.type = "text/javascript"; m.src = (p == "https:" ? "https://s" : "http://") + "'.$script_host.'/'.$account_path.'";
      var e = document.createElement("div"); e.appendChild(m); document.write(e.innerHTML);
    }
  })();
</script>';
echo "\r\n";
}
}
?>