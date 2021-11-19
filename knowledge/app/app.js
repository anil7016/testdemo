// create the module and name it scotchApp
var app = angular.module('myApp', ['ngRoute', 'angular-sticky-box', 'ngAnimate', 'ngSanitize', 'ngCookies', 'ngMaterial', 'angular-sticky-box','ui.calendar', 'ui.bootstrap', 'ngLoadingSpinner','luegg.directives']);

// configure our routes
app.config(function ($locationProvider, $routeProvider, $httpProvider, $compileProvider, $sceProvider) {
    $routeProvider
    .when('/', {
        templateUrl: 'pages/dashboard.html',
        controller: 'dashboardController',
        role: '1'
    }).when('/home', {
        templateUrl: 'pages/home.html',
        controller: 'mainController',
        role: ['1', '2']
    }).when('/sourt-code', {
        templateUrl: 'pages/home.html',
        controller: 'mainController',
        role: '1'
    }).when('/article/:catId/:id', {
        templateUrl: 'pages/article.html',
        controller: 'articleController',
        role: ['1', '2']
    }).when('/getStarted/:id', {
        templateUrl: 'pages/getStart.html',
        controller: 'getStartedController',
        role: ['1', '2']
    }).when('/search/:id', {
        templateUrl: 'pages/search-result.html',
        controller: 'searchController',
        role: ['1', '2']
    }).when('/news', {
        templateUrl: 'pages/news.html',
        controller: 'newsController',
        role: '1'
    }).when('/member', {
        templateUrl: 'pages/member.html',
        controller: 'memberController',
        role: ['1', '2']
    }).when('/calendar', {
        templateUrl: 'pages/calendar.html',
        controller: 'calendarController',
        role: '1'
    }).when('/news/:id', {
        templateUrl: 'pages/news_detail.html',
        controller: 'NewsDetailController',
        role: '1'
    }).otherwise({
        resolve:{
        "check":function($location,$cookieStore){   
                if($cookieStore.get("session_iFkUserTypeId") == 2){ 
                    $location.path('/home');
                }else{
                    $location.path('/');
                }
            }
        }
    });    
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|file|skype):/);
}).run(function ($rootScope, $cookieStore, $location, $window, $log,$route) {

    //role base access to user if login with  role 2 (freelancer).
    $rootScope.$on('$routeChangeSuccess', function (currentRoute, previousRoute) {
        
        //Gettting Login  User Role
        var loginUserRole = $rootScope.adminRule;
        
        //Checking Access Only if User Role is 2
        if(loginUserRole == 2){

            //Getting Current role if route is available
            var currentRole = $route.current.$$route.role;
            if(currentRole != undefined){
                //Check if page has only one role assign
                if(currentRole.length == 1){
                    if(currentRole != loginUserRole){
                        $location.path('/home');
                    }
                }else{
                    //if page has only more than one role assign
                    var roleAssignOrNot = false;
                    angular.forEach(currentRole, function(val, i) {
                        if(loginUserRole == parseInt(val)){
                            roleAssignOrNot = true;
                        }
                    });
                    if(!roleAssignOrNot){
                        $location.path('/home');
                    }
                }
            }
        }
    });

    //get url
    var url = $location.absUrl();
    console.log("url", url);
    $rootScope.path = url.split('knowledge/#/')[0];
    console.log("$rootScope.path", $rootScope.path);
    $rootScope.$on('$routeChangeStart', function(event, next) {
        var userRole = jQuery.inArray($window.sessionStorage.getItem("session_iFkUserTypeId"), next.role);
        var roles = next.role != $window.sessionStorage.getItem("session_iFkUserTypeId");
    });

    //set ifkUserid
    $rootScope.adminRule = $cookieStore.get("session_iFkUserTypeId");
    if (!$cookieStore.get('session_iUserId')) {
        $window.location.href = $rootScope.path;
    }
    $rootScope.$on('$routeChangeStart', function (event, next) {
        $rootScope.role = next.role;
    });
});
	