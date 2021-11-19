// 'use strict';

// /**
//  * Config for the router
//  */
// angular.module('app')
//     .run(
//         ['$rootScope', '$state', '$stateParams',
//             function($rootScope, $state, $stateParams) {
//                 $rootScope.$state = $state;
//                 $rootScope.$stateParams = $stateParams;
//             }
//         ]
//     )
//     .config(
//         ['$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 'MODULE_CONFIG','$httpProvider','$compileProvider',
//             function($stateProvider, $urlRouterProvider, JQ_CONFIG, MODULE_CONFIG,$httpProvider,$compileProvider) {
//                 var htmlPath = 'tpl/';
//                 $urlRouterProvider.otherwise('/');
//                 $stateProvider
//                     .state('/', {
//                         url: '/',
//                         templateUrl: htmlPath+'login.html',
//                         controller: 'loginController'
//                     })
//                     .state('dashboard', {
//                         url: '/dashboard',
//                         templateUrl: htmlPath+'dashboard.html',
//                         controller: 'dashboardController',
//                         role: ['1', '2']
//                     })
//                     .state('resourcetype', {
//                         url: '/resourcetype',
//                         templateUrl: htmlPath+'user-type.html',
//                         controller: 'usertypeController',
//                         role: ['1', '2']
//                     }).state('calendar-event-add', {
//                         url: '/calendar-event-add',
//                         templateUrl: htmlPath+'knowledge_calender.html',
//                         controller: 'knowledgeCalendarController',
//                         role: '1'
//                     }).state('filemanager', {
//                         url: '/filemanager/:id',
//                         templateUrl: htmlPath+'folder.html',
//                         controller: 'filemanagerController',
//                         role: ['1', '2']
//                     }).state('filemanager1', {
//                         url: '/filemanager/:id/:jobId',
//                         templateUrl: htmlPath+'folder.html',
//                         controller: 'filemanagerController',
//                         role: ['1', '2']
//                     }).state('filemanager2', {
//                         url: '/filemanage/:id',
//                         templateUrl: htmlPath+'filemanager_user.html',
//                         controller: 'filemanagerCtrl',
//                         role: ['1', '2']
//                     })
//                 $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|file|skype):/);
//                 $httpProvider.interceptors.push('authInterceptor');
//                 function load(srcs, callback) {
//                     return {
//                         deps: ['$ocLazyLoad', '$q',
//                             function($ocLazyLoad, $q) {
//                                 var deferred = $q.defer();
//                                 var promise = false;
//                                 srcs = angular.isArray(srcs) ? srcs : srcs.split(/\s+/);
//                                 if (!promise) {
//                                     promise = deferred.promise;
//                                 }
//                                 angular.forEach(srcs, function(src) {
//                                     promise = promise.then(function() {
//                                         if (JQ_CONFIG[src]) {
//                                             return $ocLazyLoad.load(JQ_CONFIG[src]);
//                                         }
//                                         angular.forEach(MODULE_CONFIG, function(module) {
//                                             if (module.name == src) {
//                                                 name = module.name;
//                                             } else {
//                                                 name = src;
//                                             }
//                                         });
//                                         return $ocLazyLoad.load(name);
//                                     });
//                                 });
//                                 deferred.resolve();
//                                 return callback ? promise.then(function() { return callback(); }) : promise;
//                             }
//                         ]
//                     }
//                 }


//             }
//         ]
//     );

// app.run(function($rootScope, $log, $location, $interval, $window, $cookieStore) {
//     //Blank table of title
//     $rootScope.dataNotFound = "No data found";
    
//     //cookies Expire and auto logout
//     var lastDigestRun = Date.now();
//     var idleCheck = $interval(function() {
//         var now = Date.now();
//         if (now - lastDigestRun > 30 * 60 * 1000) {
//             $window.sessionStorage.clear();
//             $window.localStorage.clear();
//             angular.forEach(["session_iUserId", "auth", "session_iFkUserTypeId", "session_vEmailAddress", "session_password", "internalUserEdit", "internalUserAdd", "jobRecentEdit", "jobRecentAdd", "session_holidayCountry", "invoiceJobId"], function(key) {
//                 $cookieStore.remove(key);
//             });
//             //closeWindows();
//             $location.path('/');
//         }
//     }, 200);
    
//     $rootScope.adminRole = $cookieStore.get('session_iFkUserTypeId');
//     $rootScope.userRight = $window.localStorage.getItem("session_iFkUserTypeId");
    
//     $rootScope.$on('$routeChangeStart', function(event, next) {
//         var userRole = jQuery.inArray($window.localStorage.getItem("session_iFkUserTypeId"), next.role);
//         var roles = next.role != $window.localStorage.getItem("session_iFkUserTypeId");
//         if(next.role){
//             if (next.role[0] && !next.role[1]) {
//                 if (next.role != $cookieStore.get('session_iFkUserTypeId')) {
//                     $location.path('/dashboard');
//                 }
//             }
//         }
//         if (roles == true && userRole == -1) {
//         }
        
//         lastDigestRun = Date.now();
//     });

// }).run(['$rootScope', '$location', function($rootScope, $location) {
//     var path = function() {
//         return $location.path();
//     };

//     $rootScope.$watch(path, function(newVal, oldVal) {
//         $rootScope.activetab = newVal;
//         $rootScope.Oldtab = oldVal;
//     });
// }]);