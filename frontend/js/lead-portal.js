/**
 * Created by ananth on 7/1/17.
 */
(function (angular) {
	'use strict';
	angular.module('leadPortalModule', ['ngAnimate'])
		.controller('leadsFromAPI', ['$scope', '$http', function ($scope, $http) {
			$scope.cardSelectionCriteria = function(card){
				if(!$scope.user_hidden && card.isHidden) {
					return false;
				}
				if ($scope.user_purchased && !card.isUnlocked) {
					return false;
				}
				if ($scope.userSelectedLocations != 0 && !($scope.containsInArray($scope.userSelectedLocations, card.location))) {
					return false;
				}
				if ($scope.userSelectedCategories != 0 && !($scope.containsInArray($scope.userSelectedCategories, card.category))) {
					return false;
				}
				return true;
			};
			$scope.containsInArray = function(a, obj) {
				for (var i = 0; i < a.length; i++) {
					if (a[i] === obj) {
						return true;
					}
				}
				return false;
			}
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
				card.isHidden = !card.isHidden;
				//setCardHiddenStatusInDb(card.isHidden);
			};
			$scope.unlock_card_if_possible = function (card) {
				//var eduCashBalance = getCurrentEduCashBalance();
				//var costForUnlock = 1;
				//if(eduCashBalance>=costForUnlock) {
				//setEduCashBalance(eduCashBalance-costForUnlock);
				card.isUnlocked = true;
				//setCardUnlockedStatusInDb(card.isUnlocked);
				//}
			};
			$scope.cards = [];
			$scope.topLocations = [];
			$scope.topCategories = [];
			function populateScopevariablesFromAPI(data) {
				var locationCount = {};
				var categoryCount = {};
				for (var index = 0; index < data.length; ++index) {
					var card = data[index].lead_card;
					var locationInt = ++locationCount[card.location];
					var catrgoryInt = ++categoryCount[card.category];
					if (isNaN(locationInt)) {
						locationCount[card.location] = locationInt = 1;
					}
					if (isNaN(catrgoryInt)) {
						categoryCount[card.category] = catrgoryInt = 1;
					}
					var currentLocation = {
						Name: card.location,
						Count: locationInt
					};
					var currentCategory = {
						Name: card.category,
						Count: catrgoryInt
					};
					$scope.cards.push(card);
					var isExistingLocation = false;
					var isExistingCategory = false;
					for (var i = 0; i < $scope.topLocations.length; i++) {
						if ($scope.topLocations[i].Name == currentLocation.Name) {
							isExistingLocation = true;
							$scope.topLocations[i].Count = locationInt;
						}
					}
					for (var i = 0; i < $scope.topCategories.length; i++) {
						if ($scope.topCategories[i].Name == currentCategory.Name) {
							isExistingCategory = true;
							$scope.topCategories[i].Count = catrgoryInt;
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
			$http({
				url: '/wp-json/marketplace/v1/leads/details',
				cache: true
			})
				.success(function (data, status, headers, config) {
					// this callback will be called asynchronously
					// when the response is available
					populateScopevariablesFromAPI(data);
				})
				.error(function (data, status, header, config) {
					// called asynchronously if an error occurs
					// or server returns response with an error status.
					alert("Unable to fetch the lead details from the API.");
				});
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