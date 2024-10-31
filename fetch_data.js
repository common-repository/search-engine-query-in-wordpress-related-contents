function getRelatedPosts(url) {
	jQuery("#relatedPosts").load(url, "", function (responseText, textStatus) {
		if (textStatus == "success" && responseText.length > 10) {
			jQuery("#relatedPostsLoading").slideUp("slow", function () {
				jQuery("#relatedPosts").slideDown("slow");
			});
		} else {
			jQuery("#relatedPostsLoading").slideUp("slow", function () {
				jQuery("#relatedPosts").html("<p>No related posts :'(</p>");
				jQuery("#relatedPosts").slideDown("slow", function() { 
					setTimeout('dataError("search-engine-query-in-wp")', 2000);
				});
			});
		}
	});
}

function dataError(elementId) {
	jQuery("#"+elementId).slideUp("slow");
}

// everything starts here: writing the "Loading..." text
jQuery("#relatedPostsLoading").html("<p><img src=\""+seqInWpUrl+"ajax-loader.gif\" alt=\"\" /><br />"+loadingText+"</p>");

// compose the url to be called for the http request
seqInWpUrl += "ajax-response.php?postId=" + postId;
if (document.referrer && document.referrer!="") {
	seqInWpUrl += "&referer="+escape(document.referrer);
}

// add the function to onload action
// not using jquery ready by purpose: I want the whole page to be loaded, not just the box container
//addOnloadAction('getRelatedPosts("'+url+'")');

jQuery(document).ready(function () {
    getRelatedPosts(seqInWpUrl);
});