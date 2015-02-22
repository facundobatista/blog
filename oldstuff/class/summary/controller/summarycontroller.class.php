<?php

	lt_include( PLOG_CLASS_PATH."class/controller/controller.class.php" );
	
	/**
	 * \ingroup Controller
	 *
	 * Basic controller for the summary. It specifies the action map as well as as the folder
	 * where action classes for the summary can be found.
	 *
	 * @see Controller
	 */	
	class SummaryController extends Controller
	{
		function SummaryController()
		{
			
			// action map array for the controller
			$actions = Array(
			    "Default" => "SummaryDefaultAction",
			    "BlogList" => "BlogListAction",
			    "PostList" => "PostListAction",
				"UserList" => "UserListAction",	
				"UserProfile" => "UserProfileAction",
				"BlogProfile" => "BlogProfileAction",
				"checkUserNameAjax" => "checkUserNameAjaxAction",
				"Register" => "SummaryRegistrationAction",
				"resetPasswordForm" => "SummaryShowResetPasswordForm",
				"sendResetEmail" => "SummarySendResetEmail",
				"setNewPassword" => "SummarySetNewPassword",
				"updatePassword" => "SummaryUpdatePassword",
				"rss" => "SummaryRssAction",
				"summarySearch" => "SummarySearchAction",
				"activeAccount" => "ActiveAccountAction",
				"display" => "SummaryCustomPageAction"
			);

			$this->Controller( $actions, "op" );
			$this->setActionFolderPath( PLOG_CLASS_PATH."class/summary/action/" );
		}
	}	
?>