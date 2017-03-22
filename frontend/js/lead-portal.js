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
				if ($scope.userSelectedLocations != 0 && !($scope.containsInArray($scope.userSelectedLocations, card.locationName))) {
					return false;
				}
				if ($scope.userSelectedCategories != 0 && !($scope.containsInArray($scope.userSelectedCategories, card.categoryName))) {
					return false;
				}
				$scope.cardsNotEmptyVariable = true;
				return true;
			};
			$scope.cardHiddenStatus = function (card) {
				return card.isHidden;
			};
			$scope.containsInArray = function(a, obj) {
				containsInArrayUtil(a, obj);
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
				var currentCategory = prop.Name;
				addCategoryToURLParameter(currentCategory);
				if (!($scope.containsInArray($scope.userSelectedCategories, currentCategory))) {
					$scope.userSelectedCategories.push(currentCategory);
				}else{
					removeItemFromArray($scope.userSelectedCategories, currentCategory);
				}
			};
			$scope.setSelectedLocations = function(prop){
				var currentLocation = prop.Name;
				addLocationToURLParameter(currentLocation);
				if (!($scope.containsInArray($scope.userSelectedLocations, currentLocation))) {
					$scope.userSelectedLocations.push(currentLocation);
				}else {
					removeItemFromArray($scope.userSelectedLocations, currentLocation);
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
					if(!$scope.cardSelectionCriteria(card)) {
						continue;
					}
					var locationCount = ++locationArray[card.locationName];
					var catergoryCount = ++categoryArray[card.categoryName];
					if (isNaN(locationCount)) {
						locationArray[card.locationName] = locationCount = 1;
					}
					if (isNaN(catergoryCount)) {
						categoryArray[card.categoryName] = catergoryCount = 1;
					}
					var currentLocation = {
						Name: card.locationName,
						locId: card.locationId,
						Count: locationCount
					};
					var currentCategory = {
						Name: card.categoryName,
						catId: card.categoryId,
						Count: catergoryCount
					};

					var isExistingLocation = false;
					var isExistingCategory = false;
					for (var i = 0; i < $scope.topLocations.length; i++) {
						if ($scope.topLocations[i].locId == currentLocation.locId) {
							isExistingLocation = true;
							$scope.topLocations[i].Count = locationCount;
						}
					}
					for (var i = 0; i < $scope.topCategories.length; i++) {
						if ($scope.topCategories[i].catId == currentCategory.catId) {
							isExistingCategory = true;
							$scope.topCategories[i].Count = catergoryCount;
						}
					}
					if (!isExistingLocation) {
						$scope.topLocations.push(currentLocation);
					}
					if (!isExistingCategory) {
						$scope.topCategories.push(currentCategory);
					}
				}
			};
			function populateScopevariablesFromAPI(data) {
				for (var index = 0; index < data.length; ++index) {
					var card = data[index].lead_card;
					if (card.locationId == -1) {
						card.locationName = "Unknown Location";
					}
					if (card.categoryId == -1) {
						card.categoryName = "Unknown Category";
					}
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
