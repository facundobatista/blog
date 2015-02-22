<?php

	/*
     * This is the array definition of the different mappings
     * action parameter->action class used by the controller.
     *
     * This would be very nice if implemented in PHP, but hey... let's not
     * make things too complicated yet :)
     **/
    $actions["Default"] = "DefaultAction";
    $actions["ViewArticle"] = "ViewArticleAction";
    $actions["Comment"] = "CommentAction";
    // sample action
    $actions["Sample"] = "SampleAction";
    // add a new comment
    $actions["AddComment"] = "AddCommentAction";
	// add a new trackback
	$actions["AddTrackback"] = "AddTrackbackAction";
    // loads the template specified
    $actions["Template"] = "TemplateAction";
    // shows the trackback information of an article
    $actions["Trackbacks"] = "ViewArticleTrackbacksAction";
    // shows an album
    $actions["ViewAlbum"] = "ViewAlbumAction";
    // shows a resource
    $actions["ViewResource"] = "ViewResourceAction";
    // executes a search
    $actions["Search"] = "SearchAction";
	// servers a resource
	$actions["ResourceServer"] = "ResourceServerAction";
?>