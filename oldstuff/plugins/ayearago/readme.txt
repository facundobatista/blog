Plugin: A Year Ago
Author: Mariano Draghi
Release Date: 2006/11/02
Current Version: 20070502

This plugin offers various methods to get a list of posts written a year
before a given post, or a year before based on the current date.
Usage as follow:

You can use:
1.   $ayearago->isEnabled() 
     To check if the plugin is enabled or not. 

2.a. $ayearago->getOneYearAgoPosts( $post, $maxPosts )
     To get the posts (if any) written EXACTLY a year ago the given post.
     Useful if you post very frecuently.

2.b. $ayearago->getOneYearAgoPostsFuzzy( $post, $maxPosts )
     To get the posts (if any) written between the given post and the
     previous one, but a year before.
     This alternative is more useful if you doesn't post that frequently.

Both 2.a & 2.b are meant to be used in the post.template

3.   $ayearago->getRecentArticlesAYearAgo( $maxPosts )
     To get the posts written a year ago, based on the current date.
     This is similar to the normal Recent Articles feature, but for
     the previous year. This is meant to be used in the header or
     footer template.

Where:
1. $post is the current post
2. $maxPosts is the max. quantity of posts to return. It's optional.
   If ommitted, for 2.a & 2.b the plugin uses the
   plugin_ayearago_maxposts setting. For 3, recent_posts_max is used
   (same setting as the standard Recent Articles feature).

Example:
Add the following code to post.template:
{if $ayearago->isEnabled()}
{assign var="yearAgoPosts" value=$ayearago->getOneYearAgoPostsFuzzy($post)}
{if sizeof($yearAgoPosts) > 0}
<div class="ayearago">
{foreach name=ayearago from=$yearAgoPosts item=yearAgoPost}
 {if $smarty.foreach.ayearago.first}A year ago I was writting: {/if}
 <a href="{$url->postPermalink($yearAgoPost)}">{$yearAgoPost->getTopic()}</a>
 {if !$smarty.foreach.ayearago.last} :: {/if}
{/foreach} 
</div>
{/if}
{/if}

Changelog
=========
20070502 - Migrated to LifeType 1.2 and minor bugfixing
1.0      - Initial version
