'use strict';
var app =   angular.module('app', ['ui.bootstrap.contextMenu','ngRoute','ngAnimate', 'ngAria', 'ngCookies', 'ngMessages',
                'ngResource', 'ngSanitize', 'ngTouch', 'ngStorage', 'ui.bootstrap', 'ui.utils', 'ui.load', 'ui.jq',
                'oc.lazyLoad', 'pascalprecht.translate', 'ngMaterial','datatables', 'ngTagsInput','ngRateIt','angularRangeSlider',
                'internationalPhoneNumber','polyglotLanguageSwitcher','textAngular','ngLoadingSpinner','ui.sortable'
            ]);

app.config(function($locationProvider, $routeProvider, $httpProvider, $compileProvider, $translateProvider) {
    var modulesPath = 'tpl/';   
    $routeProvider.when('/', {
            templateUrl: modulesPath + 'login.html',
            controller: 'loginController',
        }).when('/dashboard', {
            templateUrl: modulesPath + 'dashboard.html',
            controller: 'dashboardController',
            activetab: 'dashboard',
            role: ['1', '2']
        }).when('/dashboard1', {
            templateUrl: modulesPath + 'dashboard.html',
            controller: 'dashboardController',
            activetab: 'dashboard',
            role: ['1', '2']
        }).when('/dashboard-user/:id', {
            templateUrl: modulesPath + 'dashboardUser.html',
            controller: 'dashboardUserController',
            activetab: 'dashboard',
            role: ['1', '2']
        }).when('/resetpassword', {
            templateUrl: modulesPath + 'resetPassword.html',
            controller: 'loginController',
            activetab: 'dashboard',
            //role : ['1','2']
        }).when('/resetpassword/:id', {
            templateUrl: modulesPath + 'resetPassword1.html',
            controller: 'passwordResetController',
            activetab: 'dashboard',
            //role : ['1','2']
        }).when('/user/:id', {
            templateUrl: modulesPath + 'user.html',
            controller: 'userController',
            activetab: 'dashboard',
            role: '1'
        }).when('/resourcetype', {
            templateUrl: modulesPath + 'user-type.html',
            controller: 'usertypeController',
            activetab: 'dashboard',
            role: '1'
        })
        // ------------user profiles --------------------//
        .when('/user-profile', {
            templateUrl: modulesPath + 'communication.html',
            controller: 'communicationController',
            //activetab: 'dashboard',
            role: '1'
        }).when('/internal', {
            templateUrl: modulesPath + 'internal.html',
            controller: 'communicationController',
            activetab: 'internal',
            role: '1'
        }).when('/internal/:id', {
            templateUrl: modulesPath + 'internal.html',
            controller: 'communicationController',
            //activetab: 'dashboard',
            role: '1'
        }).when('/user-profile/:id', {
            templateUrl: modulesPath + 'communication.html',
            controller: 'communicationController',
            //activetab: 'dashboard',
            role: '1'
        }).when('/calender', {
            templateUrl: modulesPath + 'calender.html',
            controller: 'CalendarCtrl',
            //activetab: 'dashboard',
            role: '1'
        }).when('/properties', {
            templateUrl: modulesPath + 'property.html',
            controller: 'propController',
            //activetab: 'dashboard',
            role: '1'
        }).when('/payment', {
            templateUrl: modulesPath + 'payment.html',
            controller: 'paymentController',
            //activetab: 'dashboard',
            role: '1'
        }).when('/user-task', {
            templateUrl: modulesPath + 'userTask.html',
            controller: 'usertaskController',
            //activetab: 'dashboard',
            role: '1'
        })

    // ------------------- client profile ------------------------//
    .when('/client/:id', {
        templateUrl: modulesPath + 'client.html',
        controller: 'clientController',
        //activetab: 'dashboard',
        role: '1'
    }).when('/client', {
        templateUrl: modulesPath + 'client.html',
        controller: 'clientController',
        activetab: 'client',
        role: '1'
    }).when('/client-profile', {
        templateUrl: modulesPath + 'client-profile.html',
        controller: 'basicinfoController',
        activetab: 'client',
        role: '1'
    }).when('/edit-indirect/:id', {
        templateUrl: modulesPath + 'client-profile1.html',
        controller: 'indirectclientController',
        activetab: 'client',
        role: '1'
    }).when('/client-profile1', {
        templateUrl: modulesPath + 'client-profile1.html',
        controller: 'indirectclientController',
        activetab: 'dashboard',
        role: '1'
    }).when('/edit-client/:id', {
        templateUrl: modulesPath + 'client-profile.html',
        controller: 'basicinfoController',
        activetab: 'client',
        role: '1'
    }).when('/contact-person', {
        templateUrl: modulesPath + 'contact-person.html',
        controller: 'contactController',
        activetab: 'client',
        role: '1'
    }).when('/Resource_assesment', {
        templateUrl: modulesPath + 'resource_assesment.html',
        controller: 'resourceAssetsController',
        activetab: 'client',
        role: '1'
    }).when('/price-list', {
        templateUrl: modulesPath + 'price-list.html',
        controller: 'pricelistController',
        activetab: 'client',
        role: '1'
    }).when('/price-list1', {
        templateUrl: modulesPath + 'price-list1.html',
        controller: 'pricelistController',
        activetab: 'client',
        role: '1'
    }).when('/login-detail', {
        templateUrl: modulesPath + 'login-detail.html',
        controller: 'clientLoginController',
        activetab: 'client',
        role: '1'
    })

    // ----------------order section ---------------//

    .when('/project', {
            templateUrl: modulesPath + 'order.html',
            controller: 'orderController',
            activetab: 'order',
            role: '1'
        }).when('/order-add', {
            templateUrl: modulesPath + 'order_add.html',
            controller: 'selectController',
            activetab: 'order',
            role: '1'
        }).when('/project-team', {
            templateUrl: modulesPath + 'project_team.html',
            controller: 'ProTeamController',
            activetab: 'order',
            role: '1'
        }).when('/project-language', {
            templateUrl: modulesPath + 'project_language.html',
            controller: 'ProLangController',
            role: '1'
        }).when('/project-customer', {
            templateUrl: modulesPath + 'project_customer.html',
            controller: 'ProCustomerController',
            activetab: 'order-add',
            role: '1'
        }).when('/project-task', {
            templateUrl: modulesPath + 'order_task.html',
            controller: 'usertaskController',
            role: '1'
        }).when('/item-add', {
            templateUrl: modulesPath + 'item_add.html',
            controller: 'itemController',
            role: '1'
        }).when('/general', {
            templateUrl: modulesPath + 'general.html',
            controller: 'generalController',
            activetab: 'general',
            role: '1'
        })
        // --------------admin masters ----------------//
        .when('/to-do-lists', {
            templateUrl: modulesPath + 'task-type.html',
            controller: 'tasktypeController',
            activetab: 'to-do-lists',
            role: '1'
        }).when('/resourcestatus', {
            templateUrl: modulesPath + 'user-status.html',
            controller: 'userstatusController',
            activetab: 'resource',
            role: '1'
        }).when('/customer-status', {
            templateUrl: modulesPath + 'client-status.html',
            controller: 'clientstatusController',
            activetab: 'customer-status',
            role: '1'
        }).when('/currency', {
            templateUrl: modulesPath + 'currency.html',
            controller: 'currencyController',
            activetab: 'currency',
            role: '1'
        }).when('/property', {
            templateUrl: modulesPath + 'properties.html',
            controller: 'PropertyController',
            activetab: 'property',
            role: '1'
        }).when('/project-type', {
            templateUrl: modulesPath + 'project-type.html',
            controller: 'projectTypeController',
            role: '1'
        }).when('/project-status', {
            templateUrl: modulesPath + 'project-status.html',
            controller: 'projectStatusController',
            role: '1'
        }).when('/language', {
            templateUrl: modulesPath + 'language.html',
            controller: 'langController',
            activetab: 'language',
            role: '1'
        }).when('/languages', {
            templateUrl: modulesPath + 'languages.html',
            controller: 'languagesController',
            activetab: 'languages',
            role: '1'
        }).when('/tax-status', {
            templateUrl: modulesPath + 'tax-status.html',
            controller: 'taxstatusController',
            activetab: 'tax-status',
            role: '1'
        }).when('/item-status', {
            templateUrl: modulesPath + 'item-status.html',
            controller: 'itemstatusController',
            activetab: 'item-status',
            role: '1'
        }).when('/jobs', {
            templateUrl: modulesPath + 'jobs.html',
            controller: 'jobController',
            activetab: 'job-status',
            role: '1'
        }).when('/user-contact-person', {
            templateUrl: modulesPath + 'contact-external.html',
            controller: 'contactController',
            activetab: 'job',
            role: '1'
        }).when('/services', {
            templateUrl: modulesPath + 'services.html',
            controller: 'serviceController',
            activetab: 'dashboard',
            role: '1'
        }).when('/price1', {
            templateUrl: modulesPath + 'price1.html',
            controller: 'priceController',
            activetab: 'price1',
            role: '1'
        }).when('/price2', {
            templateUrl: modulesPath + 'price2.html',
            controller: 'price2Controller',
            activetab: 'price2',
            role: '1'
        }).when('/items', {
            templateUrl: modulesPath + 'items.html',
            controller: 'itemsController',
            activetab: 'items',
            role: '1'
        }).when('/master-price', {
            templateUrl: modulesPath + 'master-price.html',
            controller: 'masterpriceController',
            activetab: 'master-price',
            role: '1'
        }).when('/child-price', {
            templateUrl: modulesPath + 'child-price.html',
            controller: 'childpriceController',
            activetab: 'child-price',
            role: '1'
        }).when('/rounding-price', {
            templateUrl: modulesPath + 'rounding.html',
            controller: 'roundingPriceController',
            activetab: 'rounding-price',
            role: '1'
        }).when('/customer-types', {
            templateUrl: modulesPath + 'customer-group.html',
            controller: 'customerGroupController',
            activetab: 'customer-types',
            role: '1'
        }).when('/job-chain', {
            templateUrl: modulesPath + 'job-chain.html',
            controller: 'jobchaincontroller',
            activetab: 'job-chain',
            role: '1'
        }).when('/newjob', {
            templateUrl: modulesPath + 'select-job.html',
            controller: 'newjobchaincontroller',
            activetab: 'job-chain',
            role: '1'
        }).when('/chain', {
            templateUrl: modulesPath + 'chain.html',
            //controller : 'jobchaincontroller',
            activetab: 'job-chain',
            role: '1'
        }).when('/viewExternal/:id', {
            templateUrl: modulesPath + 'viewexternal.html',
            controller: 'viewExternaldetailController',
            activetab: 'job-chain',
            role: '1'
        }).when('/viewinternal/:id', {
            templateUrl: modulesPath + 'viewinternal.html',
            controller: 'viewInternaldetailController',
            activetab: 'job-chain',
            role: '1'
        }).when('/viewindirect/:id', {
            templateUrl: modulesPath + 'viewindirect.html',
            controller: 'viewIndirectdetailController',
            activetab: 'job-chain',
            role: '1'
        }).when('/viewdirect/:id', {
            templateUrl: modulesPath + 'viewdirect.html',
            controller: 'viewdirectdetailController',
            activetab: 'job-chain',
            role: '1'
        }).when('/business-units', {
            templateUrl: modulesPath + 'centers.html',
            controller: 'centerController',
            activetab: 'business-units',
            role: '1'
        }).when('/viewProject/:id', {
            templateUrl: modulesPath + 'viewproject.html',
            controller: 'viewProjectController',
            activetab: 'order-add',
            role: '1'
        }).when('/sign', {
            templateUrl: modulesPath + 'emailsign.html',
            controller: 'signController',
            activetab: 'order-add',
            role: '1'
        }).when('/jobs-detail/:id', {
            templateUrl: modulesPath + 'jobs-detail.html',
            controller: 'jobDetailController',
            activetab: 'order-add',
            role: '1'
        }).when('/jobs-summeryCalendar/:id', {
            templateUrl: modulesPath + 'jobs-summeryCalendar.html',
            controller: 'jobDetailController',
            activetab: 'order-add',
            role: '1'
        }).when('/project-output', {
            templateUrl: modulesPath + 'project-output.html',
            controller: 'ProOutputController',
            activetab: 'order-add',
            role: '1'
        }).when('/project-invoices', {
            templateUrl: modulesPath + 'project-invoices.html',
            controller: 'ProInvoicesController',
            activetab: 'order-add',
            role: '1'
        }).when('/job-summery-calendar', {
            templateUrl: modulesPath + 'jobs-summeryCalendar.html',
            controller: 'jobSummeryCalendarController',
            activetab: 'order-add',
            role: '1'
        }).when('/job-summery-jobchain', {
            templateUrl: modulesPath + 'jobs-summeryjobChain.html',
            controller: 'jobSummeryjobchainController',
            activetab: 'order-add',
            role: '1'
        }).when('/job-summery-details/:id', {
            templateUrl: modulesPath + 'project_summmery_detail.html',
            controller: 'jobSummeryDetailsController',
            activetab: 'order-add',
            role: '1'
        }).when('/filemanager/:id', {
            templateUrl: modulesPath + 'folder.html',
            controller: 'filemanagerController',
            //activetab: 'order-add'
            role: ['1', '2']
        }).when('/filemanager/:id/:jobId', {
            templateUrl: modulesPath + 'folder.html',
            controller: 'filemanagerController',
            //activetab: 'order-add'
            role: ['1', '2']
        }).when('/filemanage/:id', {
            templateUrl: modulesPath + 'filemanager_user.html',
            controller: 'filemanagerCtrl',
            //activetab: 'order-add'
            role: ['1', '2']
        }).when('/Order-status-report', {
            templateUrl: modulesPath + 'status_report.html',
            controller: 'orderstatusReportController',
            activetab: 'Order-status-report',
            role: '1'
        }).when('/Jobs-status-report', {
            templateUrl: modulesPath + 'status_job.html',
            controller: 'jobstatusReportController',
            activetab: 'Jobs-status-report',
            role: '1'
        }).when('/project-job-detail/:id', {
            templateUrl: modulesPath + 'project_job_detail.html',
            controller: 'projectjobDetailController',
            activetab: 'project-job-detail',
            role: '2'
        }).when('/project-detail/:id', {
            templateUrl: modulesPath + 'project_detail.html',
            controller: 'projectDetailController',
            activetab: 'project-job-detail',
            role: '2'
        }).when('/project-job-status', {
            templateUrl: modulesPath + 'project_status_job.html',
            controller: 'projectjobstatusController',
            activetab: 'project-job-satatus',
            role: '2'
        }).when('/payment_client', {
            templateUrl: modulesPath + 'payment_client.html',
            controller: 'paymentController',
            activetab: 'payment',
            role: '1'
        }).when('/discussion/:id', {
            templateUrl: modulesPath + 'comment.html',
            controller: 'commentController',
            activetab: 'payment',
            role: ['1', '2']
        }).when('/knowledge_category', {
            templateUrl: modulesPath + 'knowledge_category.html',
            controller: 'knowledgecategoryController',
            activetab: 'knowledge_category',
            role: '1'
        }).when('/knowledge_article', {
            templateUrl: modulesPath + 'knowledge_article.html',
            controller: 'knowledgeArticleController',
            activetab: 'knowledge_article',
            role: '1'
        }).when('/knowledge_news', {
            templateUrl: modulesPath + 'knowledge_news.html',
            controller: 'knowledgeNewsController',
            activetab: 'knowledge_news',
            role: '1'
        }).when('/customer-price-list', {
            templateUrl: modulesPath + 'customer-price-list.html',
            controller: 'customerpricelistController',
            activetab: 'customer-price-list',
            role: '1'
        }).when('/resource-price-list', {
            templateUrl: modulesPath + 'resource-price-list.html',
            controller: 'resourcepricelistController',
            activetab: 'resource-price-list',
            role: '1'
        }).when('/calendar-event-add', {
            templateUrl: modulesPath + 'knowledge_calender.html',
            controller: 'knowledgeCalendarController',
            activetab: 'calendar-event-add',
            role: '1'
        }).when('/project-invoice', {
            templateUrl: modulesPath + 'project-invoice.html',
            controller: 'projectInvoiceDetailController',
            activetab: 'project-invoice',
            role: '1'
        }).when('/funny-picture', {
            templateUrl: modulesPath + 'knowledge_funpic.html',
            controller: 'knowledgefunPicController',
            activetab: 'funny-picture',
            role: '1'
        }).when('/national-holiday', {
            templateUrl: modulesPath + 'national-holiday.html',
            controller: 'holidayController',
            activetab: 'national-holiday',
            role: '1'
        }).when('/invoice-detail', {
            templateUrl: modulesPath + 'freelancer_invoice_detail.html',
            controller: 'freelanceInvoiceController',
            activetab: 'invoice-detail',
            role: '2'
        }).when('/invoice-create', {
            templateUrl: modulesPath + 'invoice_create.html',
            controller: 'InvoiceCreateController',
            activetab: 'invoice-detail',
            role: '2'
        }).when('/invoice-show/:id', {
            templateUrl: modulesPath + 'invoice_show.html',
            controller: 'invoiceShowController',
            activetab: 'invoice-show',
            role: ['1', '2']
        }).when('/invoice-view/:id', { /* Freelancer Invoice View */
            templateUrl: modulesPath + 'freelancer_invoice_view.html',
            controller: 'freelancerInvoiceViewController',
            activetab: 'invoice-view',
            role: ['1', '2']
        }).when('/invoice-due-period', {
            templateUrl: modulesPath + 'invoice-due-period.html',
            controller: 'InvoiceDuePeriodController',
            activetab: 'invoice-due-period',
            role: '1'
        }).when('/invoice-data', {
            templateUrl: modulesPath + 'internal_invoice.html',
            controller: 'invoiceInternalController',
            activetab: 'invoice-data',
            role: '1'
        }).when('/statement', {
            templateUrl: modulesPath + 'statement.html',
            controller: 'statementController',
            activetab: 'statement',
            role: ['1','2']
        }).when('/reported-images', {
            templateUrl: modulesPath + 'reported-images.html',
            controller: 'reportedImagesController',
            activetab: 'reported-images',
            role: '1'
        }).when('/resource-advance-search', {
            templateUrl: modulesPath + 'resource-advance-search.html',
            controller: 'resourceAdvanceSearchController',
            activetab: 'resource-advance-search',
            role: '1'
        }).when('/resource-position', {
            templateUrl: modulesPath + 'resource-position.html',
            controller: 'resourcePositionController',
            activetab: 'resource-position',
            role: '1'
        }).when('/resources', {
            templateUrl: modulesPath + 'resources.html',
            controller: 'resourcesController',
            activetab: 'resources',
            role: '1'
        }).when('/wizard', {
            templateUrl: modulesPath + 'wizard.html',
            controller: 'wizardCtrl',
            activetab: 'wizard',
            role: '1'
        }).when('/dateformat', {
            templateUrl: modulesPath + 'dateFormat.html',
            controller: 'dateFormatController',
            activetab: 'dateformat',
            role: '1'
        }).when('/decimal-separator', {
            templateUrl: modulesPath + 'decimal-separator.html',
            controller: 'decimalSeparatorController',
            activetab: 'decimal-separator',
            role: '1'
        }).when('/activation/:id', {
            templateUrl: modulesPath + 'activation.html',
            controller: 'userActivationController',
            activetab: 'activation-separator'
        }).when('/work-instruction', {
            templateUrl: modulesPath + 'work-instruction.html',
            controller: 'workInstructionsController',
            activetab: 'work-instruction',
            role: '1'
        }).when('/activity', {
            templateUrl: modulesPath + 'activity-list.html',
            controller: 'activitydetailController',
            activetab: 'activity',
            role: '1'
        }).otherwise({
            redirectTo: '/'
        });
    /*$locationProvider.html5Mode(true);*/
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|file|skype):/);
    $httpProvider.interceptors.push('authInterceptor');
});

app.run(function($rootScope, $log, $location, $interval, $window, $cookieStore,$timeout) {
    
    $('body').on('change', '.select2-offscreen', function() {
        var select2Id = $(this).attr('id');
        console.log("select2Id", select2Id);
        if(select2Id){
            $('#'+select2Id+'-error').remove();//if error label contains id
            $('#'+select2Id).next().remove();//if not error label contains id
        }
    });

    //Blank table of title
    $rootScope.dataNotFound = "No data found";
    //cookies Expire and auto logout
    var lastDigestRun = Date.now();
    var idleCheck = $interval(function() {
        //console.log("lastDigestRun", lastDigestRun);
        var now = Date.now();
        //console.log("now", now);
        if (now - lastDigestRun > 30 * 60 * 1000) {
            //console.log('a');
            $window.sessionStorage.clear();
            $window.localStorage.clear();
            angular.forEach(["session_iUserId", "auth", "session_iFkUserTypeId", "session_vEmailAddress", "session_password", "internalUserEdit", "internalUserAdd", "jobRecentEdit", "jobRecentAdd", "session_holidayCountry","generalEdit","editInternalUser"], function(key) {
                $cookieStore.remove(key);
            });

            //All popup modal close after auto logout
            $timeout(function() {
                $('button.close').click();
            },100);

            closeWindows();
            $location.path('/');
        }
    }, 200);
    
    $rootScope.adminRole = $cookieStore.get('session_iFkUserTypeId');
    $rootScope.userRight = $window.localStorage.getItem("session_iFkUserTypeId");
    
    $rootScope.$on('$routeChangeStart', function(event, next) {
        $timeout(function() {
            $('.butterbar').removeClass('hide').addClass('active');
        },200);
        var userRole = jQuery.inArray($window.localStorage.getItem("session_iFkUserTypeId"), next.role);
        var roles = next.role != $window.localStorage.getItem("session_iFkUserTypeId");
        if(next.role){
            if (next.role[0] && !next.role[1]) {
                if (next.role != $cookieStore.get('session_iFkUserTypeId')) {
                    $location.path('/dashboard1');
                }
            }
        }
        if (roles == true && userRole == -1) {
            $location.path('/dashboard1');
        }
        
        lastDigestRun = Date.now();
    });
    
    $rootScope.$on('$routeChangeSuccess', function(event, next) {
        $timeout(function() {
            $('.butterbar').addClass('hide').removeClass('active');
        },1000);
    });

}).run(['$rootScope', '$location', function($rootScope, $location) {
    var path = function() {
        return $location.path();
    };

    $rootScope.$watch(path, function(newVal, oldVal) {
        $rootScope.activetab = newVal;
        $rootScope.Oldtab = oldVal;
    });
}]);