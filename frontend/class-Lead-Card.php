<?php
$appear = '';

class Lead_Card implements JsonSerializable
{

	private $id, $name, $contact_no, $email, $query, $category_list, $category, $location_list, $location, $date_time, $isUnlocked, $isHidden;

	function __construct($id, $name, $contact_no, $email, $query, $category, $location, $date_time, $isUnlocked = false, $isHidden = false)
	{
		$this->create_card($id, $name, $contact_no, $email, $query, $category, $location, $date_time, $isUnlocked ? true : false, $isHidden ? true : false);
	}

	public function isHidden()
	{
		return $this->isHidden;
	}

	public function setHidden($x)
	{
		$this->isHidden = $x;
	}

	public function isUnlocked()
	{
		return $this->isUnlocked;
	}

	public function setUnlocked($x)
	{
		$this->isUnlocked = $x;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($x)
	{
		$this->id = $x;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setEmail($x)
	{
		$this->email = $x;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getContactNo()
	{
		return $this->contact_no;
	}

	public function setContactNo($x)
	{
		$this->contact_no = $x;
	}

	public function setName($x)
	{
		$this->name = $x;
	}

	public function getLocationList()
	{
		return $this->location_list;
	}

	public function getLocationName()
	{
		return $this->location;
	}

	public function setLocation($location_id)
	{
		$locationIdArray = explode(',', $location_id);
		$locationArray = array(); //Array of location objects
		$locationListArray = array(); //Array of location name strings
		foreach ($locationIdArray as $locationId) {
			$location_data = get_term_by('id', $locationId, 'locations');
			$resultData = array();
			if (!$location_data) {
				$resultData["loc_id"] = -1;
				$resultData["loc_name"] = "Unknown Location";
				$locationListArray[] = "Unknown Location";
			} else {
				$resultData["loc_id"] = $location_data->term_id;
				$resultData["loc_name"] = html_entity_decode($location_data->name);
				$locationListArray[] = html_entity_decode($location_data->name);
			}
			//$location_data->name = html_entity_decode($location_data->name);
			$locationArray[] = $resultData;
		}
		$locationList = implode(', ', $locationListArray);
		$this->location = $locationArray;
		$this->location_list = $locationList;
	}

	public function getCategoryList()
	{
		return $this->category_list;
	}

	public function getCategoryName()
	{
		return $this->category;
	}

	public function setCategory($category_id)
	{
		$categoryIdArray = explode(',', $category_id);
		$categoryArray = array(); //Array of category objects
		$categoryListArray = array(); //Array of category name strings
		foreach ($categoryIdArray as $categoryId) {
			$category_data = get_term_by('id', $categoryId, 'listing_categories');
			$categoryData = array();
			if (!$category_data) {
				$categoryData["cat_id"] = -1;
				$categoryData["cat_name"] = "Unknown Category";
				$categoryListArray[] = "Unknown Category";
			} else {
				$categoryData["cat_id"] = $category_data->term_id;
				$categoryData["cat_name"] = html_entity_decode($category_data->name);
				$categoryListArray[] = html_entity_decode($category_data->name);
			}
			//$category_data->name = html_entity_decode($category_data->name);
			$categoryArray[] = $categoryData;
		}
		$categoryList = implode(', ', $categoryListArray);
		$this->category = $categoryArray;
		$this->category_list = $categoryList;
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function setQuery($x)
	{
		$this->query = $x;
	}

	public function getDateTime()
	{
		return $this->date_time;
	}

	public function setDateTime($x)
	{
		$this->date_time = $x;
	}

	private function create_card($_id, $_name, $_contact_no, $_email, $_query, $_category, $_location, $_date_time, $_isUnlocked, $_isHidden)
	{
		$this->setId($_id);
		$this->setName($_name);
		$this->setContactNo($_contact_no);
		$this->setEmail($_email);
		$this->setQuery($_query);
		$this->setCategory($_category);
		$this->setLocation($_location);
		$this->setDateTime($_date_time);
		$this->setUnlocked($_isUnlocked);
		$this->setHidden($_isHidden);
	}

	public function edu_shortcode($appear)
	{
		if (is_user_logged_in()) {
			include 'html/lead-portal.html';
		} else {
			$redirecting_url = home_url("/login");
			echo '<script>location.href="' . $redirecting_url . '";</script>';
		}
		return null;
	}

	public function jsonSerialize()
	{
		return [
			'lead_card' => [
				'leadId' => $this->getId(),
				'name' => $this->getName(),
				'contact_no' => $this->getContactNo(),
				'email' => $this->getEmail(),
				'query' => $this->getQuery(),
				'categoryDetails' => $this->getCategoryName(),
				'locationDetails' => $this->getLocationName(),
				'categoryList' => $this->getCategoryList(),
				'locationList' => $this->getLocationList(),
				'date_time' => $this->getDateTime(),
				'isUnlocked' => $this->isUnlocked(),
				'isHidden' => $this->isHidden()
			]
		];
	}

}

$shrt_code1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana', '', '', '', '');
//$shrt_code2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Life');
add_shortcode('edugorilla_leads', array($shrt_code1, 'edu_shortcode'));


?>