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
				for (var i = 0; i < a.length; i++) {
					if (a[i] === obj) {
						return true;
					}
				}
				return false;
			};
			$scope.userSelectedLocations = [];
			$scope.userSelectedCategories = [];
			$scope.setSelectedCategories = function(prop){
				if (!($scope.containsInArray($scope.userSelectedCategories, prop.Name))) {
					$scope.userSelectedCategories.push(prop.Name);
				}else{
					removeItemFromArray($scope.userSelectedCategories, prop.Name);
				}
			};
			$scope.setSelectedLocations = function(prop){
				if (!($scope.containsInArray($scope.userSelectedLocations, prop.Name))) {
					$scope.userSelectedLocations.push(prop.Name);
				}else {
					removeItemFromArray($scope.userSelectedLocations, prop.Name);
				}
			};
			$scope.toggle_card_hidden = function (card) {
				function hideSuccessCallback(response) {
					//success code
					card.isHidden = !card.isHidden;
				}

				function hideErrorCallback(error) {
					//error code
					Load("Unable to set the hidden status.");
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
					var retVal = confirm("Looks like you do not have sufficient EduCash. Would you like to buy EduCash Now?");
					if( retVal == true ){
						Load("Redirecting to Payment page!");
						return true;
					}
					else{
						Load("Redirecting to home page!");
						return false;
					}
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
				Load('unable to fetch api details');
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
}
