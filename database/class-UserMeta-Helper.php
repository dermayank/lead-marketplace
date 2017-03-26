<?php

class UserMeta_Helper
{
	public function getMetaDetails($user_id, $key)
	{

	}

	public function getMetaDetailsForCurrentUser($key)
	{
		$userObject = wp_get_current_user();
		$user_id = $userObject->ID;
		return $this->getMetaDetails($user_id, $key);
	}

	public function setMetaDetails($user_id, $key, $value)
	{

	}

	public function setMetaDetailsForcurrentUser($key, $value)
	{
		$userObject = wp_get_current_user();
		$user_id = $userObject->ID;
		return $this->setMetaDetails($user_id, $key, $value);
	}

}


?>
