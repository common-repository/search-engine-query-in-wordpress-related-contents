<?php

if (function_exists("trailingslashit")) {
	$seqiwp_base_url = trailingslashit(get_bloginfo('wpurl')).PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
}

/**
 * widget dataobject
 */
class SEQ_widget {
	
	private $seqOptions;
	private $relatedPosts;
	private $moreLink;
	private $campaignParams;
	private $text;

	public function getOptions() {
		return $this->seqOptions;
	}
	public function setOptions(SEQ_Options $options) {
		$this->seqOptions = $options;
	}

	public function getRelatedPosts() {
		return $this->relatedPosts;
	}
	public function setRelatedPosts($relatedPosts) {
		$this->relatedPosts = $relatedPosts;
	}

	public function getMoreLink() {
		return $this->moreLink;
	}
	public function setMoreLink($moreLink) {
		$this->moreLink = $moreLink;
	}

	public function getCampaignParams() {
		return $this->campaignParams;
	}
	public function setCampaignParams($campaignParams) {
		$this->campaignParams = $campaignParams;
	}

	public function getText() {
		return $this->text;
	}
	public function setText($text) {
		$this->text = $text;
	}
}

/**
 * widget options dataobject
 */
class SEQ_Options {

	private $title;
	private $numberOfPosts;
	private $showCategory;
	private $trackClicks;
	private $useAjax;
	private $logLands;
	private $logLandsEmail;
	
	public function getTitle() {
		return wp_specialchars($this->title);
	}
	public function setTitle($title) {
		$this->title = strip_tags(stripslashes($title));
	}

	public function getNumberOfPosts() {
		return $this->numberOfPosts;
	}
	public function setNumberOfPosts($numberOfPosts) {
		$numberOfPosts = strip_tags(stripslashes($numberOfPosts));
		if (!is_numeric($numberOfPosts)) {
			$numberOfPosts = 4;
		} else {
			if ($numberOfPosts > 10 || $numberOfPosts < 1) {
				$numberOfPosts = 4;
			}
		}
		$this->numberOfPosts = $numberOfPosts;
	}

	public function isShowCategory() {
		if ($this->showCategory == null) {
			return false;
		}
		return $this->showCategory;
	}
	public function setShowCategory($showCategory) {
		if ($showCategory == "true") {
			$this->showCategory = true;
		} else {
			$this->showCategory = false;
		}
	}

	public function isTrackClicks() {
		if ($this->trackClicks == null) {
			return false;
		}
		return $this->trackClicks;
	}
	public function setTrackClicks($trackClicks) {
		if ($trackClicks == "true") {
			$this->trackClicks = true;
		} else {
			$this->trackClicks = false;
		}
	}

	public function isUseAjax() {
		if ($this->useAjax == null) {
			return false;
		}
		return $this->useAjax;
	}
	public function setUseAjax($useAjax) {
		if ($useAjax == "true") {
			$this->useAjax = true;
		} else {
			$this->useAjax = false;
		}
	}

	public function islogLands() {
		if ($this->logLands == null) {
			return false;
		}
		return $this->logLands;
	}
	public function setlogLands($logLands) {
		if ($logLands == "true") {
			$this->logLands = true;
		} else {
			$this->logLands = false;
		}
	}

	public function getLogLandsEmail() {
		return $this->logLandsEmail;
	}
	public function setLogLandsEmail($logLandsEmail) {
		$logLandsEmail = strip_tags(stripslashes($logLandsEmail));
		// email check 
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $logLandsEmail)) {
			$this->logLands = false;
			return;
		}
		$this->logLandsEmail = $logLandsEmail;
	}

	public function setDefaults() {
		$this->setTitle("Related Posts");
		$this->showCategory = true;
		$this->trackClicks = true;
		$this->numberOfPosts = 5;
		$this->useAjax = true;
		$this->logLands = false;
	}
}

/**
 * Get the search engine query from the referer server variable, if available
 * @return a 2 elements array: in first position the search engine name, in second position the search engine query
 */
function SEQ_getSearchEngineQueryString($referer = null)
{
	if ($referer == null) {
		if (isSet($_SERVER["HTTP_REFERER"]) && strlen($_SERVER['HTTP_REFERER']) > 5) {
			$referral = $_SERVER["HTTP_REFERER"];
		} else {
			return null;
		}
	} else {
		$referral = $referer;
	}

	// if somehow the referer has an anchor, strip it!
	if (strpos($referral, "#") !== false) {
		$referral = substr($referral, 0, strpos($referral, "#"));
	}

	if (strpos($referral, "google.") != false) {
		$engine = "Google";
	} elseif (strpos($referral, "yahoo.") != false)	{
		$engine = "Yahoo!";
	} elseif (strpos($referral, "msn.") != false) {
		$engine = "Msn";
	} elseif (strpos($referral, "ask.") != false) {
		$engine = "Ask";
	} elseif (strpos($referral, "live.") != false) {
		$engine = "Live Search";
	} elseif (strpos($referral, "lycos.") != false) {
		$engine = "Lycos";
	} elseif (strpos($referral, "aol.") != false) {
		$engine = "Aol";
	} elseif (strpos($referral, "dmoz.") != false) {
		$engine = "Dmoz";
	} elseif (strpos($referral, "altavista.") != false) {
		$engine = "Altavista";
	} elseif (strpos($referral, "gigablast.") != false) {
		$engine = "Gigablast";
	} elseif (strpos($referral, "alltheweb.") != false) {
		$engine = "AllTheWeb";
	} elseif (strpos($referral, "hotbot.") != false) {
		$engine = "HotBot";
	} else {
		// unknown search engine
		return null;
	}

	$token = null;

	if ($engine == "Google") {
		// ricerco il parametro usato per indicare la query cercata
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		} elseif (strpos($referral, "&as_q=") != false) {
			$token = "&as_q";
		} elseif (strpos($referral, "?as_q=") != false) {
			$token = "?as_q=";
		} elseif (strpos($referral, "&as_epq=") != false) {
			$token = "&as_epq=";
		} elseif (strpos($referral, "?as_epq=") != false) {
			$token = "?as_epq=";
		} elseif (strpos($referral, "&as_oq=") != false) {
			$token = "&as_oq=";
		} elseif (strpos($referral, "?as_oq=") != false) {
			$token = "?as_oq=";
		}
	} elseIf ($engine == "Yahoo!") {
		if (strpos($referral, "&p=") != false) {
			$token = "&p=";
		} elseif (strpos($referral, "?p=") != false) {
			$token = "?p=";
		}
	} elseIf ($engine == "Msn") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "Ask") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "Live Search") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "Lycos") {
		if (strpos($referral, "&query=") != false) {
			$token = "&query=";
		} elseif (strpos($referral, "?query=") != false) {
			$token = "?query=";
		}
	} elseIf ($engine == "Aol") {
		if (strpos($referral, "&query=") != false) {
			$token = "&query=";
		} elseif (strpos($referral, "?query=") != false) {
			$token = "?query=";
		}
	} elseIf ($engine == "Dmoz") {
		if (strpos($referral, "&search=") != false) {
			$token = "&search=";
		} elseif (strpos($referral, "?search=") != false) {
			$token = "?search=";
		}
	} elseIf ($engine == "Altavista") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "Gigablast") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "AllTheWeb") {
		if (strpos($referral, "&q=") != false) {
			$token = "&q=";
		} elseif (strpos($referral, "?q=") != false) {
			$token = "?q=";
		}
	} elseIf ($engine == "HotBot") {
		if (strpos($referral, "&query=") != false) {
			$token = "&query=";
		} elseif (strpos($referral, "?query=") != false) {
			$token = "?query=";
		}
	}

	if ($token == null) {
		return null;
	}

	$nextEndPos = strpos($referral, "&", strpos($referral, $token)+2);
	if ($nextEndPos == false) {
		// la query di ricerca è l'ultimo parametro
		$searchPhraseLen = strlen($referral) - strpos($referral, $token);
	} else {
		$searchPhraseLen = $nextEndPos - strpos($referral, $token) - strlen($token);
	}

	$searchPhrase = substr($referral, (strlen($token) + strpos($referral, $token)), $searchPhraseLen);
	$searchPhrase = trim(urldecode(strtolower($searchPhrase)));

	//elimino dalla search phrase per le ricerche con comandi speciali (site: link: cache: info: related: )

	$cmd = null;
	if (strpos($searchPhrase, "site:") !== false) {
		$cmd = "site:";
	}
	if (strpos($searchPhrase, "link:") !== false) {
		$cmd = "link:";
	}
	if (strpos($searchPhrase, "cache:") !== false) {
		$cmd = "cache:";
	}
	if (strpos($searchPhrase, "info:") !== false) {
		$cmd = "info:";
	}
	if (strpos($searchPhrase, "related:") !== false) {
		$cmd = "related:";
	}

	// ora devo estrarre il comando dalla searchPhrase
	if ($cmd != null) {
		$cmdStart = strpos($searchPhrase, $cmd);
		$nextSpace = strpos($searchPhrase, " ", (strpos($searchPhrase, $cmd) + 1));
		if ($nextSpace == false) {
			// il comando è alla fine
			$nextSpace = strlen($searchPhrase);
		}
		$cmd = substr($searchPhrase, $cmdStart, $nextSpace);
		$searchPhrase = trim(str_replace($cmd, "", $searchPhrase));
	}

	// elimino le virgolette che nella nostra ricerca non sono significative
	$searchPhrase = str_replace("\"", "", $searchPhrase);

	$searchPhrase = str_replace("'", "", $searchPhrase);

	// se l'utente ha inserito + come carattere di ricerca, non viene normalizzato dall'url decode
	$searchPhrase = str_replace("+", " ", $searchPhrase);

	// se la frase cercata è più corta di tre caratteri, non è interessante
	if (strlen($searchPhrase) < 3) {
		return null;
	}

	return array($engine, $searchPhrase, $referer);
}

/**
 * If the page is a single post, it returns the first category the post is in
 * hopefully without accessing the database (is there a way to check?)
 */
function SEQ_getPostCategory($postId = null) {
	if (is_single() || $postId != null) {
		foreach((get_the_category($postId)) as $category) {
			return $category;
			break;
		}
	}
	return null;
}

/**
 * return a SEQ_widget object
 */
function SEQ_getRelatedPosts(SEQ_Options $widgetOptions, $postId = null, $referer = null) {

	$searchQuery = SEQ_getSearchEngineQueryString($referer);

	$seqWidget = new SEQ_widget();
	$seqWidget->setOptions($widgetOptions); // not really necessary but I liked it :D

	// if the visitor lands from a known search engine, perform the internal search with search engine keywords
	if ($searchQuery != null) {
		$related = new WP_Query("s=".$searchQuery[1]."&showposts=".$widgetOptions->getNumberOfPosts()."&orderby=date");
		if ($related->have_posts() && $related->post_count > 1) {
			$seqWidget->setText("<p>".sprintf( __("You were looking for <strong>%s</strong> on <em>%s</em>. You might be interested in:",'search-engine-query-in-wp'), $searchQuery[1], $searchQuery[0])."</p>");
			$seqWidget->setMoreLink("/?s=".$searchQuery[1]);
			$seqWidget->setCampaignParams("&amp;utm_source=search&utm_medium=link&utm_campaign=related+posts+widget&utm_term=".urlencode($searchQuery[1]));
			$seqWidget->setRelatedPosts($related);
		} else {
			$related = null;
		}
	}

	// if the visitor does not land from a search engine OR the search by search engine keywords did not return any result, perform the internal search by
	// current post category
	if ($related == null && $seqWidget->getOptions()->isShowCategory()) {

		$category = SEQ_getPostCategory($postId);

		if ($category != null) {
			$related = new WP_Query("cat=".$category->cat_ID."&showposts=".$widgetOptions->getNumberOfPosts()."&orderby=date");
			if ($related->have_posts() && $related->post_count > 1) {
				if ($searchQuery == null) {
					$seqWidget->setText("<p>".sprintf( __("In the <strong>%s</strong> category:",'search-engine-query-in-wp'), $category->cat_name)."</p>");
				} else {
					// related posts are fetched by category because the internal search did not return anything with the search engine query string
					// let the user know ;)
					$seqWidget->setText("<p>".sprintf( __("You were looking for <strong>%s</strong> on <em>%s</em>. Have a look at the <strong>%s</strong> category:",'search-engine-query-in-wp'), $searchQuery[1], $searchQuery[0], $category->cat_name)."</p>");
				}
				$seqWidget->setMoreLink(get_category_link($category->cat_ID));
				$seqWidget->setCampaignParams("?utm_source=category&utm_medium=link&utm_campaign=Search+engine+query+in+WP+widget&utm_term=".urlencode($category->cat_name));
				$seqWidget->setRelatedPosts($related);
			}
		}
	}

	if (!$widgetOptions->isTrackClicks()) {
		$seqWidget->setCampaignParams("");
	}

	if ($widgetOptions->islogLands() && $widgetOptions->getLogLandsEmail() != null && function_exists(wp_mail) && $searchQuery != null) {
		$subject = "Search Engine Query in Wordpress Log";
		$message = "Dear *Search Engine Query in Wordpress* user,\n";
		$message .= "I just wanted you to know that a visitor landed on your site from a search engine! WoOooOoOOT!\n";
		$message .= "He/She was looking for [".$searchQuery[1]."] on ".$searchQuery[0].". To check it by yourself copy the following link and paste it on your browser:\n\n";
		$message .= $searchQuery[2]."\n\n";
		$message .= "Done? See nothing? Copy THE WHOLE LINK, it might be more than one line long ;)\n";
		$message .= "Remember that keeping this log on might slow your server down quite a lot, if search engines are the most popular traffic sources for your site.\n";
		$message .= "To turn this option down, log into the admin panel of your wordpress blog, goto \"Appearance\" -> \"Widgets\" -> \"Search engine query in WP\" and uncheck \"Log search engine landings by email\".\n";
		$message .= "If you're not the ".get_bloginfo('wpurl')." owner and don't know what is this, ask the site owner not to spam!\n\n";
		$message .= "_No animal were harmed in the sending of this email._\n\n";
		$message .= "Search engine query in wordpress widget";

		wp_mail($widgetOptions->getLogLandsEmail(), $subject, $message);
	}

	unset($related, $category, $searchQuery);
	return $seqWidget;
}

/**
 * display the widget xhtml both for ajax and traditional request
 */
function SEQ_showRelatedPosts(SEQ_widget $seqWidget, $postId = null) {

	$related = $seqWidget->getRelatedPosts();
	// there is more than one related post
	if ($related != null) {
?>
	<?php echo($seqWidget->getText()); ?>
	<ul>
		<?php while ($related->have_posts()) : $related->the_post(); ?>
			<?php if ($postId != get_the_ID()) { ?>
				<li><a href="<?php the_permalink(); ?><?php echo($seqWidget->getCampaignParams()); ?>"><?php the_title(); ?></a></li>
			<?php } ?>
		<?php endwhile; ?>
	</ul>
	<?php if ($seqWidget->getMoreLink() != null) { ?>
	<p class="more"><a href="<?php echo($seqWidget->getMoreLink()); ?><?php echo($seqWidget->getCampaignParams()); ?>"><?php _e("more...", "search-engine-query-in-wp"); ?></a></p>
	<?php } ?>
<?php
	} else {
		// no related posts :(
		echo("");
	}
}

/**
 * Map the old option variable (array) into the new one (object) without losing data
 */
function SEQ_in_wp_checkOptions($options = null, $return = false) {
	if ($options != false && $options != null && !is_object($options) && is_array($options)) {
		$widgetOptions = new SEQ_Options();
		$widgetOptions->setTitle($options['title']);
		$widgetOptions->setNumberOfPosts($options['number']);
		$widgetOptions->setShowCategory($options['show_category']);
		$widgetOptions->setTrackClicks($options['track_clicks']);
		$widgetOptions->setUseAjax($options['use_ajax']);
		update_option('seq_in_wp', $widgetOptions);
		if ($return == true) {
			return $widgetOptions;
		}
	} elseif (is_object($options) && get_class($options) == "SEQ_Options" ) {
		if ($return == true) {
			return $options;
		}
	} else {
		$widgetOptions = new SEQ_Options();
		$widgetOptions->setDefaults();
		if ($return == true) {
			return $widgetOptions;
		}
	}
}
?>