<?php
/**
* This file contains user views functionality
* Extended by the user class
*/
class UserView extends Translation {

	/**
	* Get translation for file
	*/
	function __construct() {
		$this->addTranslation(__FILE__);
//		$this->translater->__construct(__FILE__);
	}

	/**
	* View item with item id
	* Item held in query result
	*
	* @return string HTML view
	*/
	/*
	function viewItem() {
		global $HTML;
		global $page;

		$_ = '';
		$_ .= $HTML->head($this->translate("View user"));

		$_ .= '<div class="c">';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["email"], $this->getQueryResult(0, "email"));
			$_ .= $HTML->block($this->varnames["mobile"], stringOr($this->getQueryResult(0, "mobile"), "-"));

			$_ .= $HTML->separator();

			$_ .= $HTML->block($this->varnames["nickname"], $this->getQueryResult(0, "nickname"));
			$_ .= $HTML->block($this->varnames["firstname"], $this->getQueryResult(0, "firstname"));
			$_ .= $HTML->block($this->varnames["lastname"], $this->getQueryResult(0, "lastname"));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["country_id"], $this->countryClass->getItemName($this->getQueryResult(0, "country_id")));
			$_ .= $HTML->block($this->varnames["language_id"], $this->languageClass->getItemName($this->getQueryResult(0, "language_id")));

			$_ .= $HTML->separator();

			$_ .= $HTML->block($this->varnames["access_level_id"], $this->accesslevelClass->getItemName($this->getQueryResult(0, "access_level_id")));
			$_ .= $HTML->block($this->varnames["status"], Generic::getStatusValue($this->getQueryResult(0, "status"), $this->getStatusOptions()));
		$_ .= '</div>';
		$_ .= '</div>';

		return $_;
	}
*/

	/**
	* View profile (for indivisual user data update)
	*
	* @return string HTML view
	*/
	/*
	function viewProfile() {
		global $HTML;
		global $page;

		$_ = '';
		$_ .= $HTML->head($this->translate("View user").": ".$this->getQueryResult(0, "nickname"));

		$_ .= '<div class="c">';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["email"], $this->getQueryResult(0, "email"));
			$_ .= $HTML->block($this->varnames["mobile"], stringOr($this->getQueryResult(0, "mobile"), "-"));

			$_ .= $HTML->separator();

			$_ .= $HTML->block($this->varnames["nickname"], $this->getQueryResult(0, "nickname"));
			$_ .= $HTML->block($this->varnames["firstname"], $this->getQueryResult(0, "firstname"));
			$_ .= $HTML->block($this->varnames["lastname"], $this->getQueryResult(0, "lastname"));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->block($this->varnames["country_id"], $this->countryClass->getItemName($this->getQueryResult(0, "country_id")));
			$_ .= $HTML->block($this->varnames["language_id"], $this->languageClass->getItemName($this->getQueryResult(0, "language_id")));

		$_ .= '</div>';
		$_ .= '</div>';

		return $_;
	}
	*/

	/**
	* Edit item
	* Item held in query result
	*
	* @return string HTML view
	*/
	/*
	function editItem() {
		global $HTML;

		$HTML->details(1);
		$default_value = array("", $this->translate("Select"));

		$_ = '';
		$_ .= $HTML->head($this->translate("Edit user"));

		$_ .= '<div class="c">';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->input($this->varnames["email"], "email", stringOr($this->vars["email"], $this->getQueryResult(0, "email")));
			$_ .= $HTML->input($this->varnames["mobile"], "mobile", stringOr($this->vars["mobile"], $this->getQueryResult(0, "mobile")));

			$_ .= $HTML->separator();

			$_ .= $HTML->input($this->varnames["nickname"], "nickname", stringOr($this->vars["nickname"], $this->getQueryResult(0, "nickname")));
			$_ .= $HTML->input($this->varnames["firstname"], "firstname", stringOr($this->vars["firstname"], $this->getQueryResult(0, "firstname")));
			$_ .= $HTML->input($this->varnames["lastname"], "lastname", stringOr($this->vars["lastname"], $this->getQueryResult(0, "lastname")));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->select($this->varnames["country_id"], "country_id", $this->countryClass->getItems(), stringOr($this->vars["country_id"], $this->getQueryResult(0, "country_id")));
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", $this->languageClass->getItems(), stringOr($this->vars["language_id"], $this->getQueryResult(0, "language_id")));

			$_ .= $HTML->separator();

			$_ .= $HTML->select($this->varnames["access_level_id"], "access_level_id", $this->accesslevelClass->getItems(), stringOr($this->vars["access_level_id"], $this->getQueryResult(0, "access_level_id")));
			$_ .= $HTML->select($this->varnames["status"], "status", $this->getStatusOptions(), stringOr($this->vars["status"], $this->getQueryResult(0, "status")));
		$_ .= '</div>';
		$_ .= '</div>';

		return $_;
	}
	*/

	/**
	* Edit item
	* Item held in query result
	*
	* @return string HTML view
	*/
	/*
	function editProfile() {
		global $HTML;

		$HTML->details(1);
		$default_value = array("", $this->translate("Select"));

		$_ = '';
		$_ .= $HTML->head($this->translate("Edit user"));

		$_ .= '<div class="c">';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->input($this->varnames["email"], "email", stringOr($this->vars["email"], $this->getQueryResult(0, "email")));
			$_ .= $HTML->input($this->varnames["mobile"], "mobile", stringOr($this->vars["mobile"], $this->getQueryResult(0, "mobile")));

			$_ .= $HTML->separator();

			$_ .= $HTML->input($this->varnames["nickname"], "nickname", stringOr($this->vars["nickname"], $this->getQueryResult(0, "nickname")));
			$_ .= $HTML->input($this->varnames["firstname"], "firstname", stringOr($this->vars["firstname"], $this->getQueryResult(0, "firstname")));
			$_ .= $HTML->input($this->varnames["lastname"], "lastname", stringOr($this->vars["lastname"], $this->getQueryResult(0, "lastname")));
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->select($this->varnames["country_id"], "country_id", $this->countryClass->getItems(), stringOr($this->vars["country_id"], $this->getQueryResult(0, "country_id")));
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", $this->languageClass->getItems(), stringOr($this->vars["language_id"], $this->getQueryResult(0, "language_id")));
		$_ .= '</div>';
		$_ .= '</div>';

		return $_;
	}
	*/

	/**
	* New item form
	*
	* @return string HTML view
	*/
	/*
	function newItem() {
		global $HTML;

		$HTML->details(1);
		$default_value = array("", $this->translate("Select"));

		$_ = '';
		$_ .= $HTML->head($this->translate("New user"));

		$_ .= '<div class="c">';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->input($this->varnames["email"], "email", $this->vars["email"]);
			$_ .= $HTML->input($this->varnames["mobile"], "mobile", $this->vars["mobile"]);

			$_ .= $HTML->separator();

			$_ .= $HTML->inputPassword($this->varnames["password"], "password", $this->vars["password"]);
			$_ .= $HTML->inputPassword($this->varnames["password2"], "password2", $this->vars["password2"]);

			$_ .= $HTML->separator();

			$_ .= $HTML->input($this->varnames["firstname"], "firstname", $this->vars["firstname"]);
			$_ .= $HTML->input($this->varnames["lastname"], "lastname", $this->vars["lastname"]);
		$_ .= '</div>';
		$_ .= '<div class="ci50">';
			$_ .= $HTML->select($this->varnames["country_id"], "country_id", $this->countryClass->getItems(), stringOr($this->vars["country_id"], Session::getCountryISO()));
			$_ .= $HTML->select($this->varnames["language_id"], "language_id", $this->languageClass->getItems(), stringOr($this->vars["language_id"], Session::getLanguageISO()));

			$_ .= $HTML->separator();

			$_ .= $HTML->select($this->varnames["access_level_id"], "access_level_id", $this->accesslevelClass->getItems(), $this->vars["access_level_id"], $default_value);
			$_ .= $HTML->select($this->varnames["status"], "status", $this->getStatusOptions(), $this->vars["status"]);
		$_ .= '</div>';
		$_ .= '</div>';
		
		return $_;
	}
	*/

	/**
	* New password form
	*
	* @return string HTML view
	*/
	/*
	function newPassword() {
		global $HTML;
		$HTML->details(1);
		$_ = '';
		$_ .= $HTML->head($this->translate("Change password"));
		$_ .= $HTML->inputPassword($this->varnames["password"], "password", $this->vars["password"]);
		$_ .= $HTML->inputPassword($this->varnames["password2"], "password2", $this->vars["password2"]);
		
		return $_;
	}
	*/

	/**
	* Make table listing of items
	*
	* @param string $link Optional item link (function will append item id to link)
	* @param array $validate Optional Validation information
	* @return string HTML view
	* @uses Generic::listItemsExtended()
	*/
	function listItems($link, $validate=false) {
		global $HTML;

		$items = $this->getItems();
		/*
		$status = $this->getStatusOptions();
		foreach($items["status"] as $key => $value){
			$items["status"][$key] = $status["values"][array_search($value, $status["id"])];
		}
		*/
		$_ = '';
		$_ .= $HTML->head($this->translate("Users2"));
		$_ .= Generic::listItemsExtended($link, $validate, $items["id"], array($items["user_id"], $items["access_level"]), array($this->translate("User-ID"), $this->translate("Search")), array("sortby max", "search acenter"));
		return $_;
	}
}

?>