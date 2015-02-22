Plugin: Recent Comments
Author: Mark Wu
Release Date: 2007/04/06
Version: 20070406

This plugin offers the most recently article comments (for regular
templates and an RSS feed). Usage as follow:

You can use:
1. $recentcomments->isEnabled() to check the plugin is enabled or not. 
2. $recentcomments->getRecentComments() to get the recent comments.
3. $recentcomments->getRssFeedUrl() to get the URL to the recent comments feed for the current blog

Example:
Add the following code to your template file:
{if $recentcomments->isEnabled()}
<h2>Recent Comments [<a href="{$recentcomments->getRssFeedUrl()}">rss</a>]</h2>
{assign var=comments value=$recentcomments->getRecentComments()}
<ul>
{foreach from=$comments item=comment}
{assign var=commentpostid value=$comment->getArticleId()}
{assign var=commentpost value=$recentcomments->getArticle($commentpostid)}
<li><a title="View comments by {$comment->getUsername()}" href="{$url->postPermalink($commentpost)}#{$comment->getId()}"><b>{$comment->getUsername()}:</b>{$comment->getText()|truncate:100:"..."|strip_tags}</a></li>
{/foreach}
</ul>            
{/if}
