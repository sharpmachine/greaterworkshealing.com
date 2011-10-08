<?php
/**
 * File Editor Module
 * 
 * @since 0.8
 */

if (class_exists('SU_Module')) {

class SU_Files extends SU_Module {

	var $htaccess_recovery = null;
	
	function get_module_title() { return __('File Editor', 'seo-ultimate'); }
	
	function init() {
		
		//If the custom robots.txt file is enabled...
		if ($this->get_setting('enable_custom_robotstxt')) {
			
			//...Remove WordPress's robots.txt handler and replace it with ours.
			remove_action('do_robots', 'do_robots');
			add_action('do_robots', array(&$this, 'do_robots'));
			
			//...And put a notice on the Privacy options page that the privacy settings won't take effect.
			add_action('admin_notices', array(&$this, 'privacy_options_notice'));
		}
		
		//We override the default behavior of saving the custom htaccess contents to the database and instead save the contents to the file
		add_filter('su_get_setting-files-htaccess', array(&$this, 'get_htaccess'));
		add_filter('su_custom_update_setting-files-htaccess', array(&$this, 'update_htaccess'), 10, 2);
	}
	
	function admin_page_contents() {
		
		global $is_apache;
		
		//Initialize variables
		$exists = $writable = false;
		$is_super_admin = !function_exists('is_super_admin') || !function_exists('is_multisite') || !is_multisite() || is_super_admin();
		
		//Does the server run Apache?
		if ($is_apache) {
			
			//The get_home_path() function is what WordPress uses to locate the htaccess file
			$htaccess = get_home_path().'.htaccess';
			
			//Does htaccess exist? Is it writable?
			$exists = file_exists($htaccess);
			$writable = is_writable($htaccess);
			
			if ($is_super_admin && $exists && !$writable) $this->queue_message('warning',
				__('A .htaccess file exists, but it&#8217;s not writable. You can edit it here once the file permissions are corrected.', 'seo-ultimate'));	
		}
		
		//WordPress's robots.txt file is dynamically generated, so we need URL rewriting for this to work
		//In the future, I'll likely add support for writing to a static robots.txt file
		if (!strlen(get_option('permalink_structure'))) $this->queue_message('error',
			__('WordPress won&#8217;t be able to display your robots.txt file because the default <a href="options-permalink.php" target="_blank">permalink structure</a> is in use.', 'seo-ultimate'));
		
		$this->print_messages();
		
		$this->admin_form_start();
		
		//Show robots.txt textarea
		$this->textarea('robotstxt', sprintf(__('robots.txt [<a href="%s" target="_blank">Open</a>]', 'seo-ultimate'), trailingslashit(get_bloginfo('url')).'robots.txt'));
		
		//Show robots.txt settings checkboxes
		$this->checkboxes(array(
			  'enable_custom_robotstxt' => __('Enable this custom robots.txt file and disable the default file', 'seo-ultimate')
			, 'enable_do_robotstxt_action' => __('Let other plugins add rules to my custom robots.txt file', 'seo-ultimate')
		), __('robots.txt Settings', 'seo-ultimate'));
		
		$this->queue_message('warning',
			__('Please realize that incorrectly editing your robots.txt file could block search engines from your site.', 'seo-ultimate'));
		
		//Of course, only bother with htaccess if we're running Apache.
		if ($is_super_admin && $is_apache && ($writable || !$exists)) {
			$this->textarea('htaccess', __('.htaccess', 'seo-ultimate'));
			
			$this->queue_message('warning',
				__('Also, incorrectly editing your .htaccess file could disable your entire website. Edit with caution!', 'seo-ultimate'));
		}
		
		$this->admin_form_end();
		
		//Print the caution message(s) at the end
		$this->print_messages();
	}
	
	function do_robots() {
		
		//Announce that this is a text file
		header( 'Content-Type: text/plain; charset=utf-8' );
		
		//Should we allow plugins to add custom rules?
		if ($this->get_setting('enable_do_robotstxt_action'))
			do_action('do_robotstxt');
		else
			do_action('su_do_robotstxt');
		
		//Print the custom robots.txt file
		echo $this->get_setting('robotstxt');
	}
	
	function get_htaccess() {
		if ($this->htaccess_recovery) return $this->htaccess_recovery;
		
		$htaccess = get_home_path().'.htaccess';
		if (is_readable($htaccess))
			return file_get_contents($htaccess);
		
		return false;
	}
	
	function update_htaccess($unused, $value) {
		
		//If the write fails, we don't want the user to lose their changes; we save the user's data to a variable so it can be displayed in the textarea
		$this->htaccess_recovery = $value;
		
		//Overwrite the file
		$htaccess = get_home_path().'.htaccess';
		$fp = fopen($htaccess, 'w');
		fwrite($fp, $value);
		fclose($fp);
		
		return true;
	}
	
	function privacy_options_notice() {
		global $pagenow;
		if ($pagenow == 'options-privacy.php') { //Shows on the "Settings > Privacy" page
			$this->print_message('info', sprintf(
				__('Please note that your privacy settings won&#8217;t have any effect on your robots.txt file, since you&#8217;re using <a href="%s">a custom one</a>.', 'seo-ultimate'),
				admin_url('admin.php?page='.$this->plugin->key_to_hook($this->get_module_key()))
			));
		}
	}
}

}
?>