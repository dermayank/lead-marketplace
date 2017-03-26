function showHint(str) {
    if (str.length == 0) {
        document.getElementById("rspayment").innerHTML = "0";
        document.getElementById("karmapayment").innerHTML = "0";
    } else {
        var rs = document.getElementById("rspay").value;
        var karma = document.getElementById("karmapay").value;
        document.getElementById("rspayment").innerHTML = parseInt(str)*parseInt(rs);
        document.getElementById("karmapayment").innerHTML = parseInt(str)*parseInt(karma);
    }
}

(function (angular) {
	'use strict';
	angular.module('leadPortalModule', ['ngAnimate'])
		.controller('leadsFromAPI', ['$scope', '$http', function ($scope, $http) {
			$scope.cardSelectionCriteria = function(card){
				$scope.cardsNotEmptyVariable = false;
				if(!$scope.user_hidden && card.isHidden) {
					return false;
				}
				if ($scope.user_purchased && !card.isUnlocked) {
					return false;
				}
				if ($scope.userSelectedLocations != 0 && !($scope.locContainsInArray($scope.userSelectedLocations, card.locationDetails))) {
					return false;
				}
				if ($scope.userSelectedCategories != 0 && !($scope.catContainsInArray($scope.userSelectedCategories, card.categoryDetails))) {
					return false;
				}
				$scope.cardsNotEmptyVariable = true;
				return true;
			};
			$scope.cardHiddenStatus = function (card) {
				return card.isHidden;
			};
			$scope.catContainsInArray = function (userCategories, cardCategories) {
				for (var i = 0; i < userCategories.length; i++) {
					var userCategory = userCategories[i];
					for (var j = 0; j < cardCategories.length; j++) {
						var cardCategory = cardCategories[j];
						var cardCategoryName = cardCategory.cat_name;
						if (userCategory == cardCategoryName) {
							return true;
						}
					}
				}
				return false;
			};
			$scope.locContainsInArray = function (userLocations, cardLocations) {
				for (var i = 0; i < userLocations.length; i++) {
					var userLocation = userLocations[i];
					for (var j = 0; j < cardLocations.length; j++) {
						var cardLocation = cardLocations[j];
						var cardLocationName = cardLocation.loc_name;
						if (userLocation == cardLocationName) {
							return true;
						}
					}
				}
				return false;
			};
			var domURL = new Url;
			var locationsStringFromURL = domURL.query.loctn;
			var categoriesStringFromURL = domURL.query.catgr;
			if (locationsStringFromURL != undefined && locationsStringFromURL.length > 0) {
				$scope.userSelectedLocations = locationsStringFromURL.split(',');
			} else {
				$scope.userSelectedLocations = [];
			}
			if (categoriesStringFromURL != undefined && categoriesStringFromURL.length > 0) {
				$scope.userSelectedCategories = categoriesStringFromURL.split(',');
			} else {
				$scope.userSelectedCategories = [];
			}
			$scope.setSelectedCategories = function(prop){
				var currentCategoryName = prop.Name;
				var dummyList = [
					{cat_name: currentCategoryName}
				];
				addCategoryToURLParameter(currentCategoryName);
				if (!($scope.catContainsInArray($scope.userSelectedCategories, dummyList))) {
					$scope.userSelectedCategories.push(currentCategoryName);
				}else{
					removeItemFromArray($scope.userSelectedCategories, currentCategoryName);
				}
			};
			$scope.setSelectedLocations = function(prop){
				var currentLocationName = prop.Name;
				var dummyList = [
					{loc_name: currentLocationName}
				];
				addLocationToURLParameter(currentLocationName);
				if (!($scope.locContainsInArray($scope.userSelectedLocations, dummyList))) {
					$scope.userSelectedLocations.push(currentLocationName);
				}else {
					removeItemFromArray($scope.userSelectedLocations, currentLocationName);
				}
			};
			$scope.toggle_card_hidden = function (card) {
				function hideSuccessCallback(response) {
					//success code
					card.isHidden = !card.isHidden;
				}

				function hideErrorCallback(error) {
					//error code
					customLoadDialog("Unable to set the hidden status.");
				}
				$http({
					method: 'POST',
					url: '/wp-json/marketplace/v1/leads/sethidden',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					transformRequest: function (obj) {
						var str = [];
						for (var p in obj)
							str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
						return str.join("&");
					},
					data: {lead_id: card.leadId, hidden_status: !card.isHidden},
					cache: true
				}).then(hideSuccessCallback, hideErrorCallback);
			};
			$scope.unlock_card_if_possible = function (card) {
				function unlockSuccessCallback(response) {
					//success code
					card.isUnlocked = !card.isUnlocked;
				}
				function unlockErrorCallback(error) {
					//error code
					Load_confirm_box("Looks like you do not have sufficient EduCash. Would you like to buy EduCash Now?");
				}
				$http({
					method: 'POST',
					url: '/wp-json/marketplace/v1/leads/setunlock',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'},
					transformRequest: function (obj) {
						var str = [];
						for (var p in obj)
							str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
						return str.join("&");
					},
					data: {lead_id: card.leadId, unlock_status: "true"},
					cache: true
				}).then(unlockSuccessCallback, unlockErrorCallback);
			};
			$scope.cards = [];
			$scope.topLocations = [];
			$scope.topCategories = [];
			$scope.reCalcCounts = function(){
				var locationArray = {};
				var categoryArray = {};
				$scope.topLocations.length = 0;
				$scope.topCategories.length = 0;
				var allCards = $scope.cards;
				for (var index = 0; index < allCards.length; ++index) {
					var card = allCards[index];
					var locationDetails = card.locationDetails;
					var categoryDetails = card.categoryDetails;
					for (var locIndex = 0; locIndex < locationDetails.length; ++locIndex) {
						var locationDetail = locationDetails[locIndex];
						var locationName = locationDetail.loc_name;
						var locationId = locationDetail.loc_id;

						var locationCount = ++locationArray[locationName];
						if (isNaN(locationCount)) {
							locationArray[locationName] = locationCount = 1;
						}
						if (!$scope.cardSelectionCriteria(card)) {
							locationCount = locationCount - 1;
						}
						var currentLocation = {
							Name: locationName,
							locId: locationId,
							Count: locationCount
						};
						var isExistingLocation = false;
						for (var topLocationIndex = 0; topLocationIndex < $scope.topLocations.length; topLocationIndex++) {
							if ($scope.topLocations[topLocationIndex].locId == currentLocation.locId) {
								isExistingLocation = true;
								$scope.topLocations[topLocationIndex].Count = locationCount;
							}
						}
						if (!isExistingLocation) {
							$scope.topLocations.push(currentLocation);
						}
					}
					for (var catIndex = 0; catIndex < categoryDetails.length; ++catIndex) {
						var categoryDetail = categoryDetails[catIndex];
						var categoryName = categoryDetail.cat_name;
						var categoryId = categoryDetail.cat_id;
						var catergoryCount = ++categoryArray[categoryName];
						if (isNaN(catergoryCount)) {
							categoryArray[categoryName] = catergoryCount = 1;
						}
						if (!$scope.cardSelectionCriteria(card)) {
							catergoryCount = catergoryCount - 1;
						}
						var currentCategory = {
							Name: categoryName,
							catId: categoryId,
							Count: catergoryCount
						};
						var isExistingCategory = false;
						for (var topCategoryIndex = 0; topCategoryIndex < $scope.topCategories.length; topCategoryIndex++) {
							if ($scope.topCategories[topCategoryIndex].catId == currentCategory.catId) {
								isExistingCategory = true;
								$scope.topCategories[topCategoryIndex].Count = catergoryCount;
							}
						}
						if (!isExistingCategory) {
							$scope.topCategories.push(currentCategory);
						}
					}
				}
			};
			function populateScopevariablesFromAPI(data) {
				for (var index = 0; index < data.length; ++index) {
					var card = data[index].lead_card;
					card.relativeTime = moment(card.date_time, "YYYY-MM-DD HH:mm:ss").fromNow();
					$scope.cards.push(card);
				}
				$scope.reCalcCounts();
			};
			function detailSuccessCallback(response) {
				//success code
				populateScopevariablesFromAPI(response.data);
			}

			function detailErrorCallback(error) {
				//error code
				customLoadDialog('unable to fetch api details');
			}
			$http({
				url: '/wp-json/marketplace/v1/leads/details',
				cache: true
			}).then(detailSuccessCallback, detailErrorCallback);
		}]);
})(window.angular);

/**
 * Generic function to remove an item from the given array.
 *
 * @param {Array} array the original array with all items
 * @param {any} item the time you want to remove
 * @returns {Array} a new Array without the item
 */
var removeItemFromArray = function (arr, item) {
	var i = arr.length;
	while (i--) if (arr[i] === item) arr.splice(i, 1);
};

var containsInArrayUtil = function (a, obj) {
	for (var i = 0; i < a.length; i++) {
		if (a[i] === obj) {
			return true;
		}
	}
	return false;
};

function addCategoryToURLParameter(category) {
	var domURL = new Url;
	var currentCategoryString = domURL.query.catgr;
	var currentCategories = [];
	if (currentCategoryString != undefined && currentCategoryString.trim().length > 0) {
		currentCategories = currentCategoryString.split(',');
	}
	if (containsInArrayUtil(currentCategories, category)) {
		currentCategories = currentCategories.filter(function (item) {
			return item !== category;
		});
	} else {
		currentCategories.push(category);
	}
	delete domURL.query.catgr;
	var categoryStr = currentCategories.toString();
	domURL.query.catgr = categoryStr;
	var urlAfterHostName = domURL.path + "?" + domURL.query.toString() + "#" + domURL.hash;
	window.history.pushState("String as Data", "Manage Leads", urlAfterHostName);
	//alert("Current categories are : "+categoryStr+" URL: "+domURL);
}

function addLocationToURLParameter(location) {
	var domURL = new Url;
	var currentLocationString = domURL.query.loctn;
	var currentLocations = [];
	if (currentLocationString != undefined && currentLocationString.trim().length > 0) {
		currentLocations = currentLocationString.split(',');
	}
	if (containsInArrayUtil(currentLocations, location)) {
		currentLocations = currentLocations.filter(function (item) {
			return item !== location;
		});
	} else {
		currentLocations.push(location);
	}
	delete domURL.query.loctn;
	var locationStr = currentLocations.toString();
	domURL.query.loctn = locationStr;
	var urlAfterHostName = domURL.path + "?" + domURL.query.toString() + "#" + domURL.hash;
	window.history.pushState("String as Data", "Manage Leads", urlAfterHostName);
	//alert("Current locations are : "+locationStr+" URL: "+domURL);
}

/**
 * Generic function to modify URL parameter.
 *
 * @param {uri} URL to be modified
 * @param {key} parameter's key
 * @returns {value} parameter's value
 */
function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i");
	if (value === undefined) {
		if (uri.match(re)) {
			return uri.replace(re, '$1$2');
		} else {
			return uri;
		}
	} else {
		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		} else {
			var hash = '';
			if (uri.indexOf('#') !== -1) {
				hash = uri.replace(/.*#/, '#');
				uri = uri.replace(/#.*/, '');
			}
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			return uri + separator + key + "=" + value + hash;
		}
	}
}
