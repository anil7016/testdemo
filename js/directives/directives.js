app.directive('login', ['$http', function($http) {
    return {
        transclude: true,
        link: function(scope, element, attrs) {
            scope.isGuest = window.localStorage._auth == undefined;
        },
        template: '<a href="" ng-if="!isGuest" ng-click="logout()">LogOut({{user}})</a>'
    }
}]);

// ---------------page scroll event for top header------------------//
app.directive("ngScroll", function($window) {
    return {
        restrict: 'EA',
        link: function($scope, element) {
            document.body.scrollTop = 0;
            $window.onscroll = function() {
                if (document.body.scrollTop > 0) {
                    //element.removeClass('affix-top').addClass('left-menu');
                    angular.element('.left-menu').css('width', angular.element('#aside').width());
                } else {
                    //element.removeClass('left-menu').addClass('affix-top');
                    angular.element('.affix-top').css('width', angular.element('#aside').width());
                }
            };
        }
    };
});

// ------------------file upload --------------------//
app.directive("ngFileSelect", function() {
    return {
        link: function(scope, el) {
            el.bind("change", function(e) {
                scope.file = (e.srcElement || e.target).files[0];
                scope.getFile( scope.file);
            });
        }
    }
});

app.directive("ngDatepicker", function($window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                format:globalDateFormat
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat));
                element.blur();
            });
        }
    };
});
app.directive("ngUpcommingDate", function($http,rest,$timeout,$window,$rootScope) {
    return {
        //ng-upcomming-date
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            var nowDate = new Date();
            var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                minDate: today,
                format:globalDateFormat
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat));
                element.blur();
                $timeout(function() {
                    $('#endDate').data("DateTimePicker").minDate(ev.date);
                    $('#endDate').val('');
                }, 100);
            });
        }
    };
});
app.directive("ngEventEndDate", function($http,rest,$timeout,$window) {
    return {
        //ng-upcomming-date
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            var nowDate = new Date();
            var today = new Date(nowDate.getFullYear(), nowDate.getMonth(), nowDate.getDate(), 0, 0, 0, 0);
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                useCurrent: false,
                minDate: today,
                format:globalDateFormat
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat));
                element.blur();
            });
        }
    };
});
app.directive("ngOutgoing", function($window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                // /minDate: moment().subtract(1,'d'),
                maxDate: moment(new Date()),
                format:globalDateFormat
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat));
                element.blur();
            });
        }
    };
});
// ------------------ datetimepicker ------------------------//
app.directive("ngDatetimepicker", function() {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                minDate: moment().subtract(1,'d'),
                format:'DD.MM.YYYY HH:mm:ss'
            }).on('dp.change', function(ev) {
                //ngModelCtrl.$setViewValue(moment(ev.date).format("DD.MM.YYYY HH:mm:ss"));
                /*element.blur();*/
            });
        }
    };
});
app.directive("ngDatetimepicker1", function($window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                // minDate: moment().subtract(1,'d'),
                minDate:new Date(),
                format:globalDateFormat+' - '+'HH:mm'
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat+' '+'HH:mm'));
                /*element.blur();*/
            });
        }
    };
});
app.directive("ngDatepicker2", function($window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            var globalDateFormat = $window.localStorage.getItem("global_dateFormat");
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                // minDate: moment().subtract(1,'d'),
                //minDate:new Date(),
                //useCurrent:false,
                format:globalDateFormat
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format(globalDateFormat));
                element.blur();
            });
        }
    };
});
app.directive("timepicker1", function($window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                // minDate: moment().subtract(1,'d'),
                //minDate:new Date(),
                format:'HH:mm'
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(ev.date);
                scope.$apply();
            });
        }
    };
});
app.directive("ngDateTime", function($parse) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker().on("dp.change", function(e) {
                changeDate();
            });
            scope.$watch('ngModel', function() {
                element.val(ngModelCtrl.$viewValue);
            });

            function changeDate() {
                ngModelCtrl.$setViewValue(element.val());
            }
            changeDate();
        }
    };
});
app.directive("ngEventFirst", function() {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker().on("dp.change", function(e) {
                changeDate();
                $('.end-date').data("DateTimePicker").minDate(e.date);
            });
            scope.$watch('ngModel', function() {
                element.val(ngModelCtrl.$viewValue);
            });
            function changeDate() {
                ngModelCtrl.$setViewValue(element.val());
            }
            // changeDate();
        }
    };
});
app.directive("ngEventLast", function($log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datepicker({
                useCurrent: false
            // Important! See issue #1075
            }).on("dp.change", function(e) {
                changeDate();
                $('.start-date').data("DateTimePicker").maxDate(e.date);
            });
            scope.$watch('ngModel', function() {
                element.val(ngModelCtrl.$viewValue);
            });

            function changeDate() {
                ngModelCtrl.$setViewValue(element.val());
            }
            // changeDate();
        }
    };
});
app.directive("ngFromTime", function() {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                format: 'LT'
            }).on("dp.change",
                function(e) {
                    element.parent('div').find('.to-time').data('DateTimePicker').minDate(e.date);
                    changeDate();
                });
            scope.$watch('ngModel', function() {
                element.val(ngModelCtrl.$viewValue);
            });

            function changeDate() {
                ngModelCtrl.$setViewValue(element.val());
            }
            // changeDate();
        }
    };
});
app.directive("ngToTime", function() {
    return {
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                format: 'HH:mm',
                //format: 'LT',
                //useCurrent: false
            }).on("dp.change", function(e) {
                element.parent('div').find('.from-time').data('DateTimePicker');
                changeDate();
            });
            scope.$watch('ngModel', function() {
                element.val(ngModelCtrl.$viewValue);
            });

            function changeDate() {
                ngModelCtrl.$setViewValue(element.val());
            }
            // changeDate();
        }
    };
});

//----------------------select2-------------------------//
app.directive('select2User', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'users';
            rest.get().success(function(data) {
                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vFirstName + ' ' + value.vLastName
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});

app.directive('select2Manager', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        priority: 1,
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'userManager/2';
            rest.get().success(function(data) {
                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vFirstName + ' ' + value.vLastName
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Coordinator', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        priority: 2,
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'userCoordinator/3';
            rest.get().success(function(data) {

                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vFirstName + ' ' + value.vLastName
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});

// ----------------------------Customer Group-------------------------//

app.directive('select2CustomerGroup', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'customergroupGetdata';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.group_id,
                        'text': value.group_name
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
// -----------------------------------------------------------------//
app.directive('select2Qa', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'user/3';
            rest.get().success(function(data) {
                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vFirstName + ' ' + value.vLastName
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2InvoiceNumber', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'getInvoiceNumber';
            rest.get().success(function(data) {
                var invoiceNumber = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.invoice_number,
                        'text': value.invoice_number
                    };
                    invoiceNumber.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: invoiceNumber,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});

app.directive('select2InvoiceStatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var invoiceStatuses = [
                {
                    'id': 'Complete',
                    'text': 'Complete' 
                },{
                    'id': 'Part Paid',
                    'text': 'Part Paid'
                }
            ];
            
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: invoiceStatuses,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 200);
            
        }
    }
});
app.directive('select2Qaspa', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'userQaSpecialist/4';
            rest.get().success(function(data) {
                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vFirstName + ' ' + value.vLastName
                    };
                    users.push(obj);
                });

                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
// --------------select2 for displaying taxation list-------------------------//
app.directive('select2Taxation', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'taxList';
            rest.get().success(function(data) {
                var taxList = [];
                angular.forEach(data, function(key, value) {
                    var obj = {
                        'id': key.tax_id,
                        'text': key.tax_name
                    };
                    taxList.push(obj);
                });

                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: taxList,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
// --------------select2-------------------------//
app.directive('select2Property', function($http, rest, $timeout, $window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'propertyactive/' + $window.localStorage.priority;
            rest.get().success(function(data) {
                var property = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.property_id,
                        'text': value.property_name
                    };
                    property.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: property,
                        multiple:true,
                        maximumSelectionSize:1

                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Gender', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            $http({
                dataType: "dataType",
                url: "gender.json",
                async: false
            }).success(function(data) {
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: data,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            });
        }
    }
});
// --------------select2 direct sectionn-------------------------//
app.directive('select2Client', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'clients';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.iClientId,
                        'text': value.vUserName
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Indirect', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'clientlistindirect_show';
            rest.get().success(function(data) {
                var indirect = [];
                $.each(data, function(key, value) {
                    //console.log(value);
                    var obj = {
                        'id': value.iClientId,
                        'text': value.vUserName
                    };
                    indirect.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: indirect,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Itemstatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'ItemStatusget';
            rest.get().success(function(data) {
                var indirect = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.item_status_name,
                        'text': value.item_status_name
                    };
                    indirect.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: indirect,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
// --------- select2 project Type---------------------//
app.directive('select2ProjType', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'prtypeactive';
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.pr_type_id,
                        text: value.project_name
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
// --------- project Progression -------------------//
app.directive('select2ProjJobs', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'proStatusget';
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.pr_status_id,
                        text: value.project_status_name
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2JobCode', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'select2JobCodeGet';
            rest.get().success(function(data) {
                var prType = [];
                angular.forEach(data, function(value, key) {
                    var obj = {
                        id: value.job_code,
                        text: value.job_code
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
//------------------ select2 job -----------------------//
app.directive('select2Jobs', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'select2Jobdata';
            rest.get().success(function(data) {
                var prType = [];
                angular.forEach(data, function(value, key) {
                    var obj = {
                        id: value.job_summmeryId,
                        text: value.po_number
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// --------- select2 currency---------------------//
app.directive('select2Currency', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'currencyCodeGet';
            rest.get().success(function(data) {
                var currency = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: key,
                        text: key
                    };
                    currency.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: currency,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive("defaultCurrencyData", function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        //require: 'ngModel',
        link: function(scope, element, attrs) {
            rest.path = 'CurrencyDatadefault';
            rest.get().success(function(data) {
                $('#currencyDef').text(data.country_name);
                $('#currency').text(data.country_name);
                scope.item = {};
                scope.item.currency = data.country_name;
            })
        }
    }
});
app.directive('select2Userstatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'userstatusactive';
            rest.get().success(function(data) {
                var status = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.status_id,
                        text: value.status_name
                    };
                    status.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: status,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2ClientStatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'clientstatusactive';
            rest.get().success(function(data) {
                var status = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.status_id,
                        text: value.status_name
                    };
                    status.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        dropdownCssClass : 'select2ClientStatus',
                        allowClear: true,
                        data: status,
                        multiple:true,
                        maximumSelectionSize:1

                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});

app.directive('select2Tasktype', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'tasktypeactive';
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.task_id,
                        text: value.task_type
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Sourcetype', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            $http({
                dataType: "dataType",
                url: "resource.json",
                async: false
            }).success(function(data) {
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: data,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200)

            }).error(function(data) {

            });
        }
    }
});
// --------- user type select2---------------------//
app.directive('ngSelect2Usertype', function(rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {

            rest.path = 'defaultUser/' + attrs.typeid;
            rest.get().success(function(data) {

                var type = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.iTypeId,
                        'text': value.vType
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }

});
// --------- User Position select2---------------------//
app.directive('ngSelect2Userposition', function(rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {

            rest.path = 'select2GetAll/' + attrs.typeid;
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.position_id,
                        'text': value.position_name
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }

});
// --------------------Currrency----------------------//
app.directive('ngSelect2Currency', function(rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'currencyGet';
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.currency_id,
                        'text': value.country_name
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }

});
// --------- price1 select2---------------------//
app.directive('select2masterPrice', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'masterPriceitemget';
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.master_price_id,
                        text: value.name

                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2childPrice', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'childPriceitemget/' + scope.childunit;
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.child_price_id,
                        text: value.name
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    $("#childprice").select2({
                        allowClear: true,
                        data: prType
                    });
                }, 200);
            })
        }
    }
});

app.directive('select2Tax', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'taxStatusget';
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.tax_id,
                        text: value.tax_name
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// ------------------- language ----------------------------//
app.directive('select2Language', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'activelanguage';
            rest.get().success(function(data) {

                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.lang_id,
                        'text': value.lang_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple: true
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});

// ------------------- MemoQ Language ----------------------------//
app.directive('select2MomoqLanguage', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'getMemoQLanguage';
            rest.get().success(function(data) {
                
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id'    : key,
                        'text'  : value[0],
                        'code'  : value[1],
                    };
                    lang.push(obj);
                });
                
                $timeout(function() {
                    element.select2({
                        allowClear  : true,
                        data        : lang,
                        multiple    : true
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2LanguagesData', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'activelanguageGet';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.lang_name,
                        'text': value.lang_name
                    };
                    users.push(obj);
                });
                element.select2({
                    allowClear: true,
                    data: users,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }).error(function(data, error, status) {});
        }
    }

});
app.directive('select2MultipleLanguagesData', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            //rest.path = 'activelanguageGet';
            rest.path = 'languagesGet';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.title,
                        'text': value.title
                    };
                    users.push(obj);
                });
                element.select2({
                    allowClear: true,
                    data: users,
                    multiple: true
                });
            }).error(function(data, error, status) {});
        }
    }

});
//-------------------language select for order search-----------------//
app.directive('select2OrderLanguagesData', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'activelanguageGet';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.lang_name,
                        'text': value.lang_name
                    };
                    users.push(obj);
                });
                element.select2({
                    allowClear: true,
                    data: users,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }).error(function(data, error, status) {});
        }
    }

});

// ------------------------- Child unit ------------------------------//
app.directive('select2ChildUnit', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'mastergetPrice';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value,
                        'text': value
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// -----------------------Job Chain status------------------------------//
app.directive('select2JobChainStatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'jobchainstatusGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': key,
                        'text': value
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// --------------------job chain customer----------------------------//
app.directive('select2JobChainCustomer', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'customerGroupGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.group_id,
                        'text': value.group_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple: true
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// --------------------job chain for jobs-----------------------//
app.directive('select2JobChainJobs', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'Jobsget';
            rest.get().success(
                function(data) {
                    var lang = [];
                    $.each(data, function(key, value) {
                        var obj = {
                            'id': value.job_id,
                            'text': value.service_name + ' (' +
                                value.job_code + ')'
                        };
                        lang.push(obj);
                    });
                    $timeout(function() {
                        element.select2({
                            allowClear: true,
                            data: lang,
                            multiple:true,
                            maximumSelectionSize:1
                        });
                    }, 500);

                }).error(function(data, error, status) {});
        }
    }
});
// ----------------------------------------------------------//
app.directive('select2MasterUnit', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'masterPriceGetdata';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.master_price_id,
                        'text': value.name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});

// -----------------------service master----------------------------//
app.directive('select2JobPresentation', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'serviceJobrepresentationGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': key,
                        'text': value
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// ----------------------properties master--------------------------//
app.directive('select2Properties', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'property';
            rest.get().success(function(data) {

                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.property_id,
                        'text': value.property_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// ----------------------price list data----------------------------//
app.directive('select2ChildPrice', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'childpriceDataget';
            rest.get().success(function(data) {

                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.child_price_id,
                        'text': value.name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
// ----------------------job chain name-----------------------------//
app.directive('select2JobchainName', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'jobchainnameGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.job_chain_id,
                        'text': value.job_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});

//---------------- center client field ----------------------------//
app.directive('select2CenterClient', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'centerClientGet';
            rest.get().success(function(data) {
                var lang = [];

                angular.forEach(data, function(value, key) {

                    var orLen = JSON.parse(value.order_number)[0].value.length;
                    var curntYear = JSON.parse(value.order_number)[0].value.substr((orLen-5), 2);
                    var obj = {
                        'id': value.abbrivation + curntYear,
                        'text': value.name
                    };
                    lang.push(obj);
                });

                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {

            });
        }
    }
});

app.directive('select2CompanyCode', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'centerClientGet';
            rest.get().success(function(data) {
                var companyCodeArray = [];

                angular.forEach(data, function(value, key) {

                    var orLen = JSON.parse(value.order_number)[0].value.length;
                    var curntYear = JSON.parse(value.order_number)[0].value.substr((orLen-5), 2);
                    var obj = {
                        'id': value.abbrivation,
                        'text': value.abbrivation
                    };
                    companyCodeArray.push(obj);
                });
                
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: companyCodeArray,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {

            });
        }
    }
});

// -------------- autocomplete location ---------------------------//
app.directive('googleplace', function($log,rest) {
    return {
        require: 'ngModel',
        scope: {
            ngModel: '=',
            details: '=?'
        },
        link: function(scope, element, attrs, model) {
            var options = {
                types: ['geocode'],
                /*componentRestrictions: {}*/
            };

            var addr_json = [];
            var forMatchArray = [];

            var componentForm = {
                street_number: 'short_name',
                //route: 'short_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };

            scope.gPlace = new google.maps.places.Autocomplete(element[0], options);
            google.maps.event.addListener(scope.gPlace, 'place_changed', function() {
                var geoComponents = scope.gPlace.getPlace();
                var latitude = geoComponents.geometry.location.lat();
                var longitude = geoComponents.geometry.location.lng();
                var addressComponents = geoComponents.address_components;
                addressComponents = addressComponents.filter(function(component) {
                
                    switch (component.types[0]) {
                        case "administrative_area_level_1":
                            return true;
                        case "locality":
                            return true;
                        case "route":
                            return true;
                        case "country":
                            return true;
                        case "postal_code":
                            return true;
                        default:
                            return false;
                    }
                }).map(function(obj) {
                    return obj.long_name;
                });

                addressComponents.push(latitude, longitude);
                fillInAddress();

                function fillInAddress() {
                    addr_json = [];
                    forMatchArray = [];
                    var addr = element.attr('id');
                    for (var component in componentForm) {
                        document.getElementById(addr + '_' + component).value = '';
                        document.getElementById(addr + '_' + component).disabled = false;
                    }
                    for (var i = 0; i < geoComponents.address_components.length; i++) {
                        var addressType = geoComponents.address_components[i].types[0];
                        if (componentForm[addressType]) {
                            var val = geoComponents.address_components[i][componentForm[addressType]];
                            var addr = element.attr('id');
                            
                            var ele = $('#'+addr + '_' + addressType);
                            
                            var test = document.getElementById(addr + '_' + addressType).value = val;
                            
                            //update element  ng-model variable value
                            ele.trigger('input');
                            //update element  ng-model variable value

                            forMatchArray.push(addressType);

                            addr_json.push({
                                id: addressType,
                                value: val
                            });
                        }
                    }
                }
                

                addDetailArry = ['locality','administrative_area_level_1','country','postal_code']
                
                function arr_diff (a1, a2) {
                    var a = [], diff = [];

                    for (var i = 0; i < a1.length; i++) {
                        a[a1[i]] = true;
                    }

                    for (var i = 0; i < a2.length; i++) {
                        if (a[a2[i]]) {
                            delete a[a2[i]];
                        } else {
                            a[a2[i]] = true;
                        }
                    }

                    for (var k in a) {
                        diff.push(k);
                    }

                    return diff;
                }
                
                var modalUpdateValue = arr_diff(addDetailArry,forMatchArray);

                if(modalUpdateValue.length > 0){
                    angular.forEach(modalUpdateValue, function(val, i) {
                        var addrEle = $('#address1_'+val).val('');
                        addrEle.trigger('input');
                    });
                }   
                
                var addressForTimezone = $('#address1').val(); 
               
                var obj ={
                    "lat"  : latitude,
                    "long"   : longitude
                };
                 
                rest.path = "getTimeZoneByLatLong";
                rest.post(obj).success(function(res) {
                    if(!res){
                        notification('Unable to get timezone.please try again.','warning');
                        var storeAddress = $('#address1_vTimezone').val(''); 
                    }else{
                        var storeAddress = $('#address1_vTimezone').val(res.timeZone); 
                    }
                });

                scope.$apply(function() {
                    scope.details = addr_json;
                    model.$setViewValue(element.val());
                });

                
            });
        }
    };
});

app.directive('googleCity', function($log,rest) {
    return {
        require: 'ngModel',
        scope: {
            ngModel: '=',
            details: '=?'
        },
        link: function(scope, element, attrs, model) {
            var options = {
                types: ['(cities)']/*,
                componentRestrictions: {}*/
            };
            var addr_json = [];
            scope.gPlace = new google.maps.places.Autocomplete(element[0], options);
            google.maps.event.addListener(scope.gPlace, 'place_changed', function() {
                var geoComponents = scope.gPlace.getPlace();
                var latitude = geoComponents.geometry.location.lat();
                var longitude = geoComponents.geometry.location.lng();
                var addressComponents = geoComponents.address_components;
                

                var obj ={
                    "lat"  : latitude,
                    "long"   : longitude
                };
                 
                rest.path = "getTimeZoneByLatLong";
                rest.post(obj).success(function(res) {
                    scope.$parent.userprofiledata.vTimeZone = res.timeZone;
                });
                    
                scope.$apply(function() {
                    scope.details = addr_json;
                    model.$setViewValue(element.val());
                });
            });
        }
    };
});
app.directive('ngRow', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            element.bind('click', function() {
                if(angular.element('#value_name_' + (scope.counter-1)).val() == '' || angular.element('#description_' + (scope.counter-1)).val() == ''){
                    notification('Please fill value name and description','error');
                    return false;
                }else{
                        var input = angular.element('<tr class="put_row" id="item_' +
                        scope.counter +
                        '">' +
                        '<td><input ng-model="value_name[' +
                        scope.counter +
                        ']" type="text" class="form-control" name="value_name[]" id="value_name_'+scope.counter+'" placeholder="Value name"/> </td>' +
                        '<td><input ng-model="description[' +
                        scope.counter +
                        ']" type="text" class="form-control name="description[]" id="description_'+scope.counter+'" placeholder="Description" /></td>' +
                        '<td><a href="javascript:void(0)" ng-click="deleteValue(' +
                        scope.counter +
                        ')" title="delete"><i class="fa fa-times"></i></a></td>' +
                        '</tr>');
                        var compile = $compile(input)(scope);
                        var len = $('.put_row').length;
                        var totalClone = len - 1;
                        if (len > 1) {
                            $('#item_' + totalClone).after(input);
                        } else {
                            $('#item_0').after(input);
                        }
                        scope.counter++;
                }
                
            });
        }
    }
}]);
app.directive('ngRow2', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counterr = 0;
            element.bind('click', function() {
                if(scope.counterr == 0){
                    var totalClone = 0;
                    var len = $('.put_row2').length;
                    var input = angular.element('<tr class="put_row2" id="' + len + '">' +
                        '<td><input ng-model="new_value[' + scope.counterr + ']" type="text" class="form-control" id="new_value_'+scope.counterr+'" name="new_value[]" placeholder="Value name"/> </td>' +
                        '<td><input ng-model="new_description[' + scope.counterr + ']" type="text" id="new_description_'+scope.counterr+'" class="form-control name="new_description[]" placeholder="Description" /></td>' +
                        '<td><a href="javascript:void(0)" ng-click="deleteValue2(' + scope.counterr + ', ' + len + ')" title="delete"><i class="iconDelete glyph-icon fa-times"></i></a></td>' +
                        '</tr>');
                    var compile = $compile(input)(scope);
                    if (len > 0) {
                        totalClone = len - 1;
                        $('#' + totalClone).after(input);
                    } else {
                        totalClone = len;
                        $('#additional_row').html(input);
                    }
                    scope.counterr++;
                }else if(angular.element('#new_value_' +(scope.counterr-1)).val() == '' || angular.element('#new_description_' +(scope.counterr-1)).val() == ''){
                    notification('Please fill value name and description','error');
                    return false;
                }else{
                    var totalClone = 0;
                    var len = $('.put_row2').length;
                    var input = angular.element('<tr class="put_row2" id="' + len + '">' +
                        '<td><input ng-model="new_value[' + scope.counterr + ']" type="text" class="form-control" id="new_value_'+scope.counterr+'" name="new_value[]" placeholder="Value name"/> </td>' +
                        '<td><input ng-model="new_description[' + scope.counterr + ']" type="text" id="new_description_'+scope.counterr+'" class="form-control name="new_description[]" placeholder="Description" /></td>' +
                        '<td><a href="javascript:void(0)" ng-click="deleteValue2(' + scope.counterr + ', ' + len + ')" title="delete"><i class="fa fa-times"></i></a></td>' +
                        '</tr>');
                    var compile = $compile(input)(scope);
                    if (len > 0) {
                        totalClone = len - 1;
                        $('#' + totalClone).after(input);
                    } else {
                        totalClone = len;
                        $('#additional_row').html(input);
                    }
                    scope.counterr++;
                }
                
            });
        }
    }
}]);

app.directive('contenteditable', function() {
    return {
        require: 'ngModel',
        link: function(scope, elm, attrs, ctrl) {
            //debugger;
            elm.bind('blur', function() {
                scope.$apply(function() {
                    ctrl.$setViewValue(elm.html());
                });
            });
            // model -> view
            ctrl.render = function(value) {
                elm.html(value);
            };
        }
    };
});
//-------------------- form for stepswizard -------------------------//
// app.directive('ngStepform', createDirective('ngStepform'));

// function createDirective(name) {
//     return function() {
//         return {
//             restrict: 'EA',
//             compile: function(element, attr) {
//                 //-----------form wizards -------------------//
//                 $w4validator = element.find("form");
//                 $w4validator.validate({
//                     highlight: function(element) {
//                         $(element).closest('.form-group').removeClass(
//                             'has-success').addClass('has-error');
//                     },
//                     success: function(element) {
//                         $(element).closest('.form-group').removeClass(
//                             'has-error');
//                         $(element).remove();
//                     },
//                     errorPlacement: function(error, element) {
//                         element.parent().append(error);
//                     }
//                 });
//                 element.bootstrapWizard({
//                     tabClass: 'wizard-steps',
//                     firstSelector: null,
//                     lastSelector: null,
//                     onNext: function(tab, navigation, index, newindex) {
//                         return true;
//                     }
//                 });
//             }
//         }
//     }
// }
// ---------------only number input ---------------------//
app.directive('ngCapitalize', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                //              var transformedInput = text.substring(0, 1).toUpperCase()
                //                      + text.substring(1);

                var transformedInput = text.toUpperCase();
                ngModelCtrl.$setViewValue(transformedInput);
                ngModelCtrl.$render();
                element.html(transformedInput);
                return transformedInput;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
app.directive('capitalizeFirst', function() {
    return {
        require: 'ngModel',
        scope: {
            ngModel: "="
        },
        link: function(scope, element, attrs, modelCtrl) {
            scope.$watch("ngModel", function() {
                if (scope.ngModel) {
                    scope.ngModel = scope.ngModel.replace(/^(.)|\s(.)/g,
                        function(v) {
                            return v.toUpperCase();
                        });
                }
            });
        }
    };
});
app.directive('ngProperties', ['$compile', 'rest', function($compile, rest) { // inject $compile service
    // as dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.properties = {};
            rest.path = 'propertybypriority/project';
            rest.get().success(function(data) {
                scope.allProp = data;
                $.each(data, function(i, v) {
                    var text = angular.element('<div class="col-sm-12">' +
                        '<div class="form-group">' +
                        '<label class="control-label">' +
                        v.property_name +
                        '</label>' +
                        '<input type="text" class="form-control" id="properties-' +
                        v.property_name +
                        '"  name="properties[]" ng-model="properties.' +
                        v.property_name +
                        '"  required/>' +
                        '</div>' +
                        '</div>');
                    var compiled = $compile(text)(scope);
                    $('#property').append(text);
                });

                $.each(data, function(i, v) {
                    var valueData = [];
                    $.each(v.value, function(key, value) {
                        var obj = {
                            'id': value.value_id,
                            'text': value.value_name
                        };
                        valueData.push(obj);
                    });
                    $('#properties-' + v.property_name).select2({
                        allowClear: true,
                        data: valueData,
                        multiple: true
                    });
                });
            }).error(function(data, error, status) {

            });
        }
    }
}]);
app.directive('modal', function() {
    return {
        template: '<div class="modal fade">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title">{{ title }}</h4>' +
            '</div>' +
            '<div class="modal-body" ng-transclude></div>' +
            '</div>' + '</div>' + '</div>',
        restrict: 'E',
        transclude: true,
        replace: true,
        scope: true,
        link: function postLink(scope, element, attrs) {
            scope.title = attrs.title;

            scope.$watch(attrs.visible, function(value) {
                if (value == true)
                    $(element).modal('show');
                else
                    $(element).modal('hide');
            });

            $(element).on('shown.bs.modal', function() {
                scope.$apply(function() {
                    scope.$parent[attrs.visible] = true;
                });
            });

            $(element).on('hidden.bs.modal', function() {
                scope.$apply(function() {
                    scope.$parent[attrs.visible] = false;
                });
            });
        }
    };
});
app.directive('translatedelegate', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                var input = angular.element('<tr class="translationPrice' + scope.traninputCounter + '">' +
                    '<td><input type="text" select2-child-price  id="translationc_master' + scope.traninputCounter + '" class="form-control" name="translationMaster[]" ng-model="translationMaster[' + scope.traninputCounter + ']"/></td>' +
                    '<td><input type="number" class="form-control" id="translationc_percent' + scope.traninputCounter + '" name="translationPercent[]" ng-model="translationPercent[' + scope.traninputCounter + ']" ></td>' +
                    '<td><input type="number" class="form-control" id="translationc_total' + scope.traninputCounter + '" value="{{(price.translation_no_match*translationPercent[' + scope.traninputCounter + '])/100}}" name="translationTotal[]" ng-modal="translationTotal[' + scope.traninputCounter + ']" disabled></td>' +
                    '<td><a href="javascript:void(0);" ng-click="remove_delegate(' + scope.traninputCounter + ')"><i class="fa fa-times trashdata"></i></a></td>' +
                    '</tr>');
                // Compile the HTML and assign to scope
                var compile = $compile(input)(scope);
                // Append input to div
                element.parent().find('table').append(input);
                scope.traninputCounter++;
            });
        }
    }
}]);
app.directive('proofreadingdelegate', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                var input = angular.element('<tr class="proofreadingPrice' + scope.proofinputCounter + '">' +
                    '<td><input type="text" select2-child-price  id="proofreadingc_master' + scope.proofinputCounter + '" class="form-control" name="proofreadingMaster[]" ng-model="proofreadingMaster[' + scope.proofinputCounter + ']"/></td>' +
                    '<td><input type="number" class="form-control" id="proofreadingc_percent' + scope.proofinputCounter + '" name="proofreadingPercent[]" ng-model="proofreadingPercent[' + scope.proofinputCounter + ']" ></td>' +
                    '<td><input type="number" class="form-control" id="proofreadingc_total' + scope.proofinputCounter + '" value="{{(price.proofreading_no_match*proofreadingPercent[' + scope.proofinputCounter + '])/100}}" name="proofreadingTotal[]" ng-modal="proofreadingTotal[' + scope.proofinputCounter + ']" disabled></td>' +
                    '<td><a href="javascript:void(0);" ng-click="remove_proofreadingdelegate(' + scope.proofinputCounter + ')"><i class="fa fa-times trashdata"></i></a></td>' +
                    '</tr>');
                // Compile the HTML and assign to scope
                var compile = $compile(input)(scope);
                // Append input to div
                element.parent().find('table').append(input);
                scope.proofinputCounter++;
            });
        }
    }
}]);
app.directive('tpdelegate', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                var input = angular.element('<tr class="tpPrice' + scope.tpinputCounter + '">' +
                    '<td><input type="text" select2-child-price id="tpc_master' + scope.tpinputCounter + '" class="form-control" name="tpMaster[]" ng-model="tpMaster[' + scope.tpinputCounter + ']"/></td>' +
                    '<td><input type="number" class="form-control" id="tpc_percent' + scope.tpinputCounter + '" name="tpPercent[]" ng-model="tpPercent[' + scope.tpinputCounter + ']" ></td>' +
                    '<td><input type="number" class="form-control" id="tpc_total' + scope.tpinputCounter + '" value="{{(price.tp_no_match*tpPercent[' + scope.tpinputCounter + '])/100}}" name="tpTotal[]" ng-modal="tpTotal[' + scope.tpinputCounter + ']" disabled></td>' +
                    '<td><a href="javascript:void(0);" ng-click="remove_tpdelegate(' + scope.tpinputCounter + ')"><i class="fa fa-times trashdata"></i></a></td>' +
                    '</tr>');
                // Compile the HTML and assign to scope
                var compile = $compile(input)(scope);
                // Append input to div
                element.parent().find('table').append(input);
                scope.tpinputCounter++;
            });
        }
    }
}]);
//----------------------------address type----------------------------//
app.directive('ngSelect2Addresstype', function(rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'AddressTypeget';
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value,
                        'text': value
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {

            });
        }
    }
});
//------------------------More invoices----------------------------------//
app.directive('moreinvoice', ['$compile', '$log', function($compile, $log) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                var y = ['invoice email', 'contact email', 'vendor email'];
                var removeItem = 0;
                $("[id^=selectEmail_opetion_]").each(function() {
                    removeItem = $(this).val();
                    y = jQuery.grep(y, function(value) {
                        return value != removeItem;
                    });
                });

                var str = '';

                for (var i = 0; i < y.length; i++) {
                    str += "<option value='" + y[i] + "'>" + y[i] + "</option>";
                }

                if (removeItem != 0) {
                    var input = angular.element('<div class="invoiceCount" id="invoiceCou' + scope.inputCounter + '">' +
                        '<div class="col-sm-5">' +
                        '<div class="form-group">' +
                        '<select class="form-control m-b selectEmail"  name="eUserStatus" id="selectEmail_opetion_' + scope.inputCounter + '" onchange="changeOption(this.id)">' +
                        '<option value="">Select Invoice</option>' + str +
                        // '<option value="invoice email" class="sub-status val-1">invoice email</option>' +
                        // '<option value="contact email" class="sub-status val-2">contact email</option>' +
                        // '<option value="vendor email" class="sub-status val-3">vendor email </option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-5">' +
                        '<div class="form-group">' +
                        '<input type="email"  name="test" autocomplete="off" class="form-control m-b invoice" id="selectEmail_invoice_' + scope.inputCounter + '">' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-2">' +
                        '<a href="javascript:void(0);" ng-click="removeinvoice(' + scope.inputCounter + ');"><i class="fa fa-times fa-lg invoicedelete"></i></a>' +
                        '</div>' +
                        '</div>' +
                        '</tr>');
                    // Compile the HTML and assign to scope
                    var compile = $compile(input)(scope);
                    // Append input to div
                    element.parent().find('.invocedata1').append(input);
                    scope.inputCounter++;
                }
            });
        }
    }
}]);

app.directive('moreinvoiceedit', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                var y = ['invoice email', 'contact email', 'vendor email'];
                var removeItem = 0;
                $("[id^=selectEmail_opetion_]").each(function() {
                    removeItem = $(this).val();
                    y = jQuery.grep(y, function(value) {
                        return value != removeItem;
                    });
                });
                1
                var str = '';
                for (var i = 0; i < y.length; i++) {
                    str += "<option value='" + y[i] + "'>" + y[i] + "</option>";
                }

                if (removeItem != 0) {
                    var input = angular.element('<div class="invoiceCount1" id="invoiceCou' + scope.inputCounter + '" ng-if="' + scope.inputCounter + '< 3">' +
                        '<div class="col-sm-5">' +
                        '<div class="form-group">' +
                        '<select class="form-control m-b selectEmail"  name="eUserStatus" id="selectEmail_opetion_' + scope.inputCounter + '" onchange="changeOption(this.id)">' +
                        '<option value="">Select Invoice</option>' + str +
                        // '<option value="invoice email" class="sub-status val-1">invoice email</option>' +
                        // '<option value="contact email" class="sub-status val-2">contact email</option>' +
                        // '<option value="vendor email" class="sub-status val-3">vendor email </option>' +
                        '</select>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-5">' +
                        '<div class="form-group">' +
                        '<input type="email"  name="test" autocomplete="off" class="form-control m-b invoice" id="selectEmail_invoice_' + scope.inputCounter + '">' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-sm-2">' +
                        '<a href="javascript:void(0)" ng-click="removeinvoice(' + scope.inputCounter + ');"><i class="fa fa-times fa-lg invoicedelete"></i></a>' +
                        '</div>' +
                        '</div>' +
                        '</tr>');
                    // Compile the HTML and assign to scope
                    var compile = $compile(input)(scope);
                    element.parent().find('.invocedata').append(input);
                    scope.inputCounter++;
                }
            });
        }
    }
}]);

app.directive('select2Emailsign', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'emailSigngetdata';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.sign_id,
                        'text': value.sign_picture
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
//------------------job-item-------------------//
app.directive('select2JobItem', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'jobitemsGet/' + scope.item;
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.item_number,
                        'text': value.item_number.toString()
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});

app.directive('select2Jobsummery', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'Jobsummeryget';
            rest.get().success(function(data) {
                var lang = [];

                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.job_id,
                        'text': value.service_name + ' (' +
                            value.job_code + ')'
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
//--------------------project job chain-----------------------//
app.directive('select2ProJobChainJobs', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'Jobsget';
            rest.get().success(
                function(data) {
                    var lang = [];
                    $.each(data, function(key, value) {
                        var obj = {
                            'id': value.job_id,
                            'text': value.service_name + ' (' +
                                value.job_code + ')'
                        };
                        lang.push(obj);
                    });
                    $timeout(function() {
                        element.select2({
                            allowClear: true,
                            data: lang,
                            multiple:true,
                            maximumSelectionSize:1
                        });
                    }, 500);
                }).error(function(data, error, status) {});
        }
    }
});
//--------------------job resource for project----------------//
app.directive('select2Resourcesummery', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'JobsummeryResourceGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.status_name,
                        'text': value.status_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2Contactsummery', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'JobsummerycontactGet';
            rest.get().success(function(data) {
                var lang = [];
                angular.forEach(data, function(value, key) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vUserName
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2ItemStatussummery', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'JobsummeryitemstatusGet';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.item_status_name,
                        'text': value.item_status_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2EmailsignData', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'emailSigngetdata';
            rest.get().success(function(data) {

                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.sign_id,
                        'text': value.sign_name
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple: true
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2JobDetailitmStatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'jobdetailItemStatusGet';
            rest.get().success(function(data) {
                var indirect = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value,
                        'text': value
                    };
                    indirect.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: indirect,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});
// Scoop item statuses
app.directive('select2ScoopDetailitmStatus', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'scoopdetailItemStatusGet';
            rest.get().success(function(data) {
                var indirect = [];
                $.each(data, function(key, value) {
                    var obj = {
                        //'id': value.item_status_id,
                        'id': value.item_status_name,
                        'text': value.item_status_name
                    };
                    indirect.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: indirect,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});

//------------------jobdetail-itemGet--------------------//
app.directive('select2JobdetailItem', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'jobitemsGet/' + scope.DetailId;
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.item_number,
                        'text': value.item_number.toString()
                    };
                    lang.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
//-----------------------jobdetail resource----------------------------//
app.directive('select2JobDetailResource', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'jobdetailresourceGet';
            rest.get().success(function(data) {
                var indirect = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vUserName
                    };
                    indirect.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: indirect,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});

//-------------------------Items Coordinator------------------------------//
app.directive('select2ItemsCoordinator', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            rest.path = 'contactPerson/' + scope.order_id;
            rest.get().success(function(data) {
                var cont = [];
                $.each(data, function(i, val) {
                    var c = {
                        'id': val.iUserId,
                        'text': val.vUserName
                    };
                    cont.push(c);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: cont,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(function(data, error, status) {});
        }
    }
});


//----------------------User Email---------------------------//

app.directive('select2UserEmail', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'users';
            rest.get().success(function(data) {
                var users = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.vEmailAddress,
                        'text': value.vEmailAddress
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple: true
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});

//--------------service child price-----------------//
app.directive('select2Service', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'servicegetdata';
            rest.get().success(function(data) {
                var users = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.sId,
                        'text': value.service_name
                    };
                    users.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: users,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});

// ------------------job chain compile html --------------------//
app.directive('chainElemup', ['$compile', 'rest', function($compile, rest) { // inject $compile service as dependency
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            var test = $('.testChiin').text();
            rest.path = 'jobChainsummerychain/' + scope.DetailId;
            rest.get().success(function(data) {
                $.each(data.data, function(i, val) {
                    if (!val.vUserName) {
                        var username = "Not Assign";
                    } else {
                        username = val.vUserName.substring(0,10)+'..';
                    }
                    // if (!val.due_date) {
                    //     var due_date = " ";
                    // } else {
                    //     var d1 = val.due_date.split(' ');
                    //     var due_date = d1[0];
                    // }
                    var statusClass;
                    var noDate;
                    if (val.due_date == '0000-00-00 00:00:00') {
                        due_date = 'Not Assign';
                    }else{
                        due_date = val.due_date.split(' ')[0].split("-").reverse().join(".");
                    }
                    if (val.item_status == 'In preparation') {
                        statusClass = 'inpreprartionColor';
                    } else if (val.item_status == 'Requested') {
                        statusClass = 'requestedColor';
                    } else if (val.item_status == 'Assigned-waiting') {
                        statusClass = 'assignedwaitongColor';
                    } else if (val.item_status == 'In-progress') {
                        statusClass = 'inprogressColor';
                    } else if (val.item_status == 'Overdue') {
                        statusClass = 'overdueColor';
                    } else if (val.item_status == 'Delivered') {
                        statusClass = 'deliveredColor';
                    } else if (val.item_status == 'Approved') {
                        statusClass = 'approvedColor';
                    } else if (val.item_status == 'Invoice Accepted') {
                        statusClass = 'invoiceacceptedColor';
                    } else if (val.item_status == 'Canceled') {
                        statusClass = 'canceledColor';
                    } else if (val.item_status == 'Without invoice') {
                        statusClass = 'withoutInvoiceColor';
                    }else if (val.item_status == 'New') {
                        statusClass = 'newJob';
                    }else if (val.item_status == 'Ready to be Delivered') {
                        statusClass = 'rdyToDeliverColor';
                    }else if (val.item_status == 'Completed') {
                        statusClass = 'completedColor';
                    }
                    if (val.per_id == '0') {
                        var input = angular.element('<button class="btn m-b-xs w-xs btn-default wpl-jobChainwidth btnch">' +
                            '<div>&nbsp;&nbsp;&nbsp;<i class="fa fa-stop ' + statusClass +
                            '" style="margin-right:150px;"></i>' +
                            '</div><p>' + val.job_code + '<span  ng-hide="showJobno">' + pad(val.job_no, 3) +
                            '</span><i class="fa fa-cog" ng-click="jobdatachain(' + val.job_summmeryId + ',jobitem.item_number)"></i>' +
                            '</p>' +
                            '<p ng-hide="showresource">' + username + '</p>' +
                            ' <p  ng-hide="showreduedate">' +
                            due_date +
                            '</p></button>');
                        if (test == 2) {
                            var compile = $compile(input)(scope);
                            $('.jc' + val.job_summmeryId).append(input);
                        }
                    } else if (val.per_id != '0') {
                        //console.log(noDate);
                        var input = angular.element('<button class="btn m-b-xs w-xs btn-default wpl-jobChainwidth btnch">' +
                            '<div>&nbsp;&nbsp;&nbsp;<i class="fa fa-stop ' + statusClass +
                            '" style="margin-right:150px;"></i>' +
                            '</div><p>' + val.job_code + '<span  ng-hide="showJobno">' + pad(val.job_no, 3) +
                            '</span><i class="fa fa-cog" ng-click="jobdatachain(' + val.per_id + ',jobitem.item_number)"></i>' +
                            '</p>' +
                            '<p ng-hide="showresource">' + username + '</p>' +
                            ' <p  ng-hide="showreduedate">' +
                            due_date +
                            '</p></button>');
                        var compile = $compile(input)(scope);
                        element.parent().find('.jc' + val.per_id).append(input);
                    }
                })
            })
        }
    }
}]);
//------------------only digits allow-------------------//
app.directive('onlyDigits', function() {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function(scope, element, attr, ctrl) {
            function inputValue(val) {
                if (val) {
                    var digits = val.replace(/[^0-9.]/g, '');
                    if (digits.split('.').length > 2) {
                        digits = digits.substring(0, digits.length - 1);
                    }

                    if (digits !== val) {
                        ctrl.$setViewValue(digits);
                        ctrl.$render();
                    }
                    return parseFloat(digits);
                }
                return undefined;
            }
            ctrl.$parsers.push(inputValue);
        }
    };
});

//------------------ only digits copy2 allow | Decimal point using Comma -------------------//
app.directive('onlyDigits2', function() {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function(scope, element, attr, ctrl) {
            function inputValue(val) {
                if (val) {
                    //var digits = val.replace(/[^0-9.]/g, '');
                    var digits = val.replace(/[^0-9,]/g,'').replace(/(,.*?),(.*,)?/, "$1");
                    
                    // if (digits.split(',').length > 2) {
                    //     digits = digits.substring(0, digits.length - 1);
                    // }
                    if (digits !== val) {
                        ctrl.$setViewValue(digits);
                        ctrl.$render();
                    }
                    return digits;
                }
                return undefined;
            }
            ctrl.$parsers.push(inputValue);
        }
    };
});

//--------------item language------------------//
app.directive('select2ItemLanguage', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'prolanguage/' + scope.orderID;
            rest.get().success(function(data) {
                var prType = [];
                $.each(data, function(key, value) {
                    var obj = {
                        id: value.pl_id,
                        text: value.source_lang + ' > ' + value.target_lang
                    };
                    prType.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: prType,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 200);
            }).error(errorCallback);
        }
    }
});

//-------------job chain group and customer group---------//
app.directive('customerGroupClone', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs) {
            element.find('button').bind('click', function() {
                scope.jobcustomerCount = $('[id^=Cusid]').length;
                for (var i = 0; i < $('[id^=Cusid]').length; i++) {
                    var cusNAM = $('#Cusname' + i).text();
                    if (scope.customName == cusNAM) {
                        var match = true;
                    }
                }

                if (scope.customName != undefined && match != true) {
                    var input = angular.element('<div class="cus' + scope.jobcustomerCount + '"><a href="javascript:void(0)" ng-click="removeCustomer(' + scope.jobcustomerCount + ')"><i class="iconDelete glyph-icon fa-times" aria-hidden="true"></i></a>' +
                        '&nbsp;&nbsp;<span class="none" id="Cuscode' + scope.jobcustomerCount + '">' + scope.customCode + '</span>' +
                        '<span class="none" id="Cusid' + scope.jobcustomerCount + '">' + scope.customId + '</span>' +
                        '<span id="Cusname' + scope.jobcustomerCount + '">' + scope.customName + '</span>' +
                        '</div>');
                    // Compile the HTML and assign to scope
                    var compile = $compile(input)(scope);
                    // Append input to div
                    $('.newChain').append(input);
                    scope.jobcustomerCount++;
                } else {
                    notification('Duplicate & blank name not allowed', 'warning');
                }
            });
        }
    }
}]);
//------------------------job work instruction-----------------------------//
app.directive('workInstruction', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            element.bind('click', function() {
                var pu = [];
                for (var i = 0; i <= $('[id^=item_]').length; i++) {
                    pu.push({
                        id: i
                    });
                }
                scope.options = pu;
                var input = angular.element('<tr id="item_' + scope.counter + '" class="put_row">' +
                    '<td><input type="text" id="source' + scope.counter + '" name="source" class="form-control" ng-model="source' + scope.counter + '"/></td>' +
                    '<td><input type="text" id="target' + scope.counter + '" name="target" class="form-control" ng-model="source' + scope.counter + '"/></td>' +
                    '<td><div class="checkbox"><label class="i-checks i-checks-sm">' +
                    '<input type="checkbox" id="bdefault' + scope.counter + '" name="bdefault" ng-true-value="1" ng-false-value="0" ng-model="bdefault' + scope.counter + '">' +
                    '<i></i></label></div></td>'
                    // + '<td><select name="sequence" class="form-control m-b sequence" ng-model="seque[0]">'
                    // + '<option ng-repeat="op in options">{{op.id}}</option>'
                    // + '</select></td>'
                    +
                    '<td><div class="checkbox"><label class="i-checks i-checks-sm">' +
                    '<input type="checkbox" id="display' + scope.counter + '" name="display" ng-true-value="1" ng-false-value="0" ng-model="display' + scope.counter + '">' +
                    '<i></i></label></div></td>' +
                    '<td><a href="javascript:void(0)" ng-click="deleteValue(' + scope.counter + ')" title="delete"><i class="fa fa-times"></i></a></td></tr>'
                );

                var compile = $compile(input)(scope);
                $('.addWInstruct').append(input);
                scope.counter++;
            });
        }
    }
}]);
app.directive('workInstruction1', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            element.bind('click', function() {
                var length = $('.put_row1').length;
                if (length) {
                    scope.counter = length;
                } else {
                    scope.counter = 0;
                }
                var input = angular.element('<tr id="item1_' + scope.counter + '" class="put_row1">' +
                    '<td><input type="text" id="source_' + scope.counter + '" name="source" class="form-control" ng-model="source' + scope.counter + '"/></td>' +
                    '<td><input type="text" id="target_' + scope.counter + '" name="target" class="form-control" ng-model="source' + scope.counter + '"/></td>' +
                    '<td><div class="checkbox"><label class="i-checks i-checks-sm">' +
                    '<input type="checkbox" id="bdefault_' + scope.counter + '" name="bdefault" ng-true-value="1" ng-false-value="0" ng-model="bdefault' + scope.counter + '">' +
                    '<i></i></label></div></td>'
                    // + '<td><select name="sequence" class="form-control m-b sequence" ng-model="seque[0]">'
                    // + '<option ng-repeat="op in options">{{op.id}}</option>'
                    // + '</select></td>'
                    +
                    '<td><div class="checkbox"><label class="i-checks i-checks-sm">' +
                    '<input type="checkbox" id="display_' + scope.counter + '" name="display" ng-true-value="1" ng-false-value="0" ng-model="display' + scope.counter + '">' +
                    '<i></i></label></div></td>' +
                    '<td><a href="javascript:void(0)" ng-click="deleteValue(' + scope.counter + ')" title="delete"><i class="iconDelete glyph-icon fa-times"></i></a></td></tr>'
                );

                var compile = $compile(input)(scope);
                var test = $('.editWInstruct').append(input);

                // element.parent().find('table').append(input);
                scope.counter++;
            });
        }
    }
}]);

//---------------------job detail work instruction----------------------//
app.directive('jobWorkInstruction', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            element.bind('click', function() {
                var len = $('.put_row').length;
                if (len != undefined) {
                    scope.counter = len;
                } else {
                    scope.counter = 0;
                }
                var test = $('#work_instruction').val();
                if (!test) {
                    notification('Please select option', 'warning');
                } else {
                    var strD = test.split(',');
                    var i;
                    var allow = true;
                    for(i=0;i<=scope.counter-1;i++){
                        if(strD[1] == angular.element('#work_name'+ i).text()){
                            notification('Work instructions already exists.', 'error');
                            allow = false;
                        }
                    }
                    if(allow){
                        var input = angular
                        .element('<tr id="work1_' + scope.counter + '" class="put_row">' +
                            '<td class="none"><span id="work_id' + scope.counter + '">' + strD[0] + '<span></td>' +
                            '<td><span id="work_name' + scope.counter + '">' + strD[1] + '<span></td>' +
                            '<td><span class="checkList"></span></td>' +
                            '<td><a href="javascript:void(0)" ng-click="removeWorkIns(' + scope.counter + ')" title="delete"><i class="iconDelete glyph-icon fa-times"></i></a></td>'
                        );
                        var compile = $compile(input)(scope);
                        var test = $('.workIn').before(input);
                        scope.counter++;
                    }
                }
            });
        }
    }
}]);
//currenncy converter
app.directive('select2CurrencyConvert', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var users = [];
            rest.path = 'currencyCodeGet';
            rest.get().success(function(data) {
                angular.forEach(data, function(value, key) {
                    var obj = {
                        'id': key + ',' + value.symbol,
                        'text': key,
                        'name':value.name
                    };
                    users.push(obj);
                });
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: users,
                    // multiple:true,
                    maximumSelectionSize:1
                });
            }, 200);
        }
    }
});
app.directive('starRating', function() {
    return {
        restrict: 'A',
        template: '<ul class="rating">' +
            '<li ng-repeat="star in stars" ng-class="star" ng-click="toggle($index)">' +
            '\u2605' +
            '</li>' +
            '</ul>',
        scope: {
            ratingValue: '=',
            max: '=',
            onRatingSelected: '&'
        },
        link: function(scope, elem, attrs) {

            var updateStars = function() {
                scope.stars = [];
                for (var i = 0; i < scope.max; i++) {
                    scope.stars.push({
                        filled: i < scope.ratingValue
                    });
                }
            };

            scope.toggle = function(index) {
                scope.ratingValue = index + 1;
                scope.onRatingSelected({
                    rating: index + 1
                });
            };

            scope.$watch('ratingValue', function(oldVal, newVal) {
                if (newVal) {
                    updateStars();
                }
            });
        }
    }
});
app.directive('select2InternalResource', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'user/2';
            rest.get().success(function(data) {
                var type = [];
                $.each(data.data, function(key, value) {
                    var obj = {
                        'id': value.iUserId,
                        'text': value.vUserName
                    };
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
/* Specalization for Price List*/
app.directive('select2Specialization', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var data = ['Finance', 'General', 'General-Agricalture', 'General Art and Culture', 'General-Beauty-Fashion-Make-up',
                'General Bussiness', 'General Casino & Poker', 'General Entertainment', 'IT-Search Engine Optimization (SEO)', 'IT-Software', 'IT-Software (UI)', 'Legal', 'Legal Patents', 'Medical', 'Medical CLinical Trials', 'Medical Dentisty', 'Medical Health Care',
                'Technical', 'Technical Automotive', 'Technical Chemistry', 'Technical Electronics', 'Technical Engineering'
            ];
            var type = [];
            $.each(data, function(i, val) {
                var obj = {
                    'id': i,
                    'text': val
                }
                type.push(obj)
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: type,
                    //multiple: true,
                    closeOnSelect:false
                });
            }, 500);
        }
    }
});
/* Specalization for Project*/
app.directive('select2SpecializationProject', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var data = ['Finance', 'General', 'General-Agricalture', 'General Art and Culture', 'General-Beauty-Fashion-Make-up',
                'General Bussiness', 'General Casino & Poker', 'General Entertainment', 'IT-Search Engine Optimization (SEO)', 'IT-Software', 'IT-Software (UI)', 'Legal', 'Legal Patents', 'Medical', 'Medical CLinical Trials', 'Medical Dentisty', 'Medical Health Care',
                'Technical', 'Technical Automotive', 'Technical Chemistry', 'Technical Electronics', 'Technical Engineering'
            ];
            var type = [];
            $.each(data, function(i, val) {
                var obj = {
                    'id': i,
                    'text': val
                }
                type.push(obj)
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: type,
                    multiple: true,
                    closeOnSelect:false
                });
            }, 500);
        }
    }
});
app.directive('select2ItemTax', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'itemTaxget';
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(i, val) {
                    var obj = {
                        'id': val.tax_id,
                        'text': val.tax_name
                    }
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('itemsAdd', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs, rootScope) {
            scope.counter = 0;
            element.bind('click', function() {
                if(angular.element('[id^=totalItem_'+scope.it.itemId+']').length == 0){
                    scope.counter = 0;

                }else if (angular.element('[id^=totalItem_'+scope.it.itemId+']').length > 0) {
                    var len = angular.element('[id^=totalItem_'+scope.it.itemId+']').length;
                    scope.counter = len + 1;
                }
                
                var totaPrice = scope.it.total_price;
                
                var quantity = angular.element('#Quantity'+scope.it.itemId).val();

                if (!quantity) {
                    quantity = 1;
                }

                var price = angular.element('#priceUnit'+scope.it.itemId).val();
                if(price == 0){
                    notification('please select price','warning');
                    return false;
                }
                var temp = price.split(',');
                var Price_unit = temp[0];

                //check if priceUitt already exists
                var exists = true;
                 if(scope.itemPriceUni[scope.it.itemId]){
                    if(scope.itemPriceUni[scope.it.itemId].length > 0){
                        angular.forEach(scope.itemPriceUni[scope.it.itemId], function(val,i){
                            if(exists){
                                if(val.pricelist == Price_unit){
                                  exists = false;  
                                }
                            }
                        })
                    }
                }else{
                    scope.itemPriceUni[scope.it.itemId] = [];
                }

                if(exists){
                    var amount = temp[1];
                    var total = amount * quantity;

                    if(scope.it == undefined) {
                        scope.it = {};
                    }

                    if(scope.it.quantity==undefined || scope.it.itemPrice==undefined) {
                        scope.it.quantity = [];
                        scope.it.itemPrice = [];
                    }
                    
                    scope.it.quantity[scope.counter] = quantity;
                    scope.it.itemPrice[scope.counter] = amount;


                    if (quantity && price != 0) {
                        var totalItemPrice = parseFloat(total) + parseFloat(totaPrice);
                        scope.it.total_price = totalItemPrice;
                        $('#priceUnit'+scope.it.itemId).val(0);
                        $('#Quantity').val('');
                        scope.counter++;
                        scope.itemPriceUni[scope.it.itemId].push({
                            'quantity': quantity,
                            'pricelist':Price_unit,
                            'itemPrice':amount,
                            'itemTotal':quantity*amount
                        });
                        angular.element('#Quantity'+scope.it.itemId).val('');
                    } else {
                        notification('Please select item', 'warning');
                    }
                }else{
                    notification('Price list already exists.', 'warning');
                }
            });
        }
    }
}]);
app.directive('itemsAdd2', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs, rootScope) {
            scope.counter = 0;
            element.bind('change', function() {
                if(angular.element('[id^=totalItem_'+scope.it.itemId+']').length == 0){
                    scope.counter = 0;

                }else if (angular.element('[id^=totalItem_'+scope.it.itemId+']').length > 0) {
                    var len = angular.element('[id^=totalItem_'+scope.it.itemId+']').length;
                    scope.counter = len + 1;
                }
                
                var totaPrice = scope.it.total_price;
                
                var quantity = angular.element('#Quantity'+scope.it.itemId).val();

                if (!quantity) {
                    quantity = 1;
                }

                var price = angular.element('#priceUnit'+scope.it.itemId).val();
                console.log('price', price)
                if(price == 0){
                    notification('please select price','warning');
                    return false;
                }
                var temp = price.split(',');
                var Price_unit = temp[0];

                //check if priceUitt already exists
                var exists = true;
                 if(scope.itemPriceUni[scope.it.itemId]){
                    if(scope.itemPriceUni[scope.it.itemId].length > 0){
                        angular.forEach(scope.itemPriceUni[scope.it.itemId], function(val,i){
                            if(exists){
                                if(val.pricelist == Price_unit){
                                  exists = false;  
                                }
                            }
                        })
                    }
                }else{
                    scope.itemPriceUni[scope.it.itemId] = [];
                }

                if(exists){
                    var amount = temp[1];
                    var total = amount * quantity;

                    if(scope.it == undefined) {
                        scope.it = {};
                    }

                    if(scope.it.quantity==undefined || scope.it.itemPrice==undefined) {
                        scope.it.quantity = [];
                        scope.it.itemPrice = [];
                    }
                    
                    scope.it.quantity[scope.counter] = quantity;
                    scope.it.itemPrice[scope.counter] = amount;


                    if (quantity && price != 0) {
                        var totalItemPrice = parseFloat(total) + parseFloat(totaPrice);
                        scope.it.total_price = totalItemPrice;
                        $('#priceUnit'+scope.it.itemId).val(0);
                        $('#Quantity').val('');
                        scope.counter++;
                        var itemTotal1 = quantity*amount;
                        var itemTotal = itemTotal1.toString().replace('.', ',');  
                        var amount = amount.toString().replace('.', ',');  
                        scope.itemPriceUni[scope.it.itemId].push({
                            'quantity': quantity,
                            'pricelist':Price_unit,
                            'itemPrice':amount,
                            'itemTotal':itemTotal
                        });
                        angular.element('#Quantity'+scope.it.itemId).val('');
                    } else {
                        notification('Please select item', 'warning');
                    }
                }else{
                    notification('Price list already exists.', 'warning');
                }
            });
        }
    }
}]);

app.directive('select2ItemTax', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            rest.path = 'itemTaxget';
            rest.get().success(function(data) {
                var type = [];
                $.each(data, function(i, val) {
                    var obj = {
                        'id': val.tax_id,
                        'text': val.tax_name
                    }
                    type.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: type,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            }).error(function(data, error, status) {});
        }
    }
});
app.directive('select2KnowledgeCategory', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            rest.path = 'KcategorygetAll';
            rest.get().success(function(data) {
                var lang = [];
                $.each(data, function(key, value) {
                    var obj = {
                        'id': value.category_id,
                        'text': value.category_name
                    };
                    lang.push(obj);
                });

                $timeout(function() {
                    element.select2({
                        placeholder: "Select category",
                        allowClear: true,
                        data: lang,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);

            }).error(function(data, error, status) {});
        }
    }
});

app.directive('select2CalculationBasis', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            var basis = [];
            var obj = ['Words'];
            $.each(obj, function(i, val) {
                basis.push({
                    'id': val,
                    'text': val
                });
            });
            element.select2({
                allowClear: true,
                data: basis,
                multiple:true,
                maximumSelectionSize:1
            });
        }
    }
});
app.directive('select2Rounding', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, ngModel) {
            var obj = [];
            rest.path = "roundingPriceGetAll";
            rest.get().success(function(data) {
                $.each(data, function(i, val) {
                    obj.push({
                        'id': val.rounding_id,
                        'text': val.rounding_desc
                    });
                });
            });
            element.select2({
                allowClear: true,
                data: obj,
                multiple:true,
                maximumSelectionSize:1
            });
        }
    }
});

app.directive('languagePriceList', function($http, rest, $timeout, $compile, $log) {
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            element.bind('click', function() {
                if (jQuery.inArray(scope.item.source_lang)) {
                    var source = scope.item.source_lang.split(',');
                }

                if (jQuery.inArray(scope.item.source_lang)) {
                    var target = scope.item.target_lang.split(',');
                }

                var sourceLength = source.length;
                var targetLength = target.length;

                var j = 0;
                var obj = [];

                if (angular.element('[id^=priceLanguageID]').length != 0) {
                    var count = angular.element('[id^=priceLanguageID]').length;
                } else {
                    var count = 0;
                }

                for (var i = 0; i < targetLength; i++) {
                    if (i % sourceLength == 0) {
                        j = 0;
                    }
                    // var data = '<tr class="priceLPrice"><td>'+source[j]+' > '+target[i]+'</td></tr>';
                    // obj.push(data);<td><a><i class="fa fa-caret-right" aria-hidden="true" ng-click="sendPriceLanguage(' + count + ')"></i></a></td>
                    var wrap = angular.element('<tr class="priceLPrice" style="cursor:pointer" id="priceLanguageID' + count + '" ng-click="sendPriceLanguage(' + count + ')"><td class="priceLanguage' + count + '">' + source[j] + ' > ' + target[i] + '</td><td><a><i class="iconDelete glyph-icon fa-times" ng-click="removePriceLanguage(' + count + ')"></i></a></td>/tr>');
                    var wrapc = $compile(wrap)(scope)
                    $('.itemList').before(wrapc);
                    j++;
                    count++;
                }

                $('#source_lang').select2('val', '');
                $('#targetLanguage').select2('val', '');
                scope.item = {};
            });
        }
    }
});

app.directive('basePriceAdd', function($http, rest, $timeout, $compile, $log) {
    return {
        restrict: 'EA',
        link: function(scope, element, attr) {
            var count = 0;
            element.bind('click', function() {
                var priceCheck = true;
                for (var i = 0; i < angular.element('[id^=basePriceUnit]').length; i++) {
                    //alert();
                    if (scope.priceUnit.split(',')[0] == angular.element('#basePriceUnit' + i).text().trim()) {
                        var priceCheck = false;
                    }

                };
                if (priceCheck == true) {
                    if (angular.element('[id^=priceLanguageID]').length > 0) {
                        if (angular.element('[class^=basePriceMain]').length) {
                            count = angular.element('[class^=basePriceMain]').length;
                        }

                        var basePriceUnit = scope.priceUnit;
                        
                        if (scope.price == null || scope.price == undefined || scope.price == '') {
                            scope.price = {};
                        }

                        if (scope.baseQuentity == undefined || scope.baseQuentity == null || scope.baseQuentity == '') {
                            scope.baseQuentity = [];
                        }

                        var firstInt = 0;
                        secondint = 1;

                        if (basePriceUnit != 0) {
                            var quantity = 0;
                            for (var i = 0; i < angular.element('[class^=plannedQuentity]').length; i++) {
                                quantity += parseInt(angular.element('.plannedQuentity' + i).text());
                            }
                            var standard = 0;
                            for (var i = 0; i < angular.element('[class^=plannedStandardTime]').length; i++) {
                                standard += parseInt(angular.element('.plannedStandardTime' + i).text());
                            }

                            var standardTime = scope.priceUnit.split(',')[1];
                            scope.planedHourTotal = parseInt(standard) + parseInt(standardTime);
                            scope.planedQuaTotal = quantity + 1;
                            scope.baseQuentity[count] = 1;
                            var index = count + 1;
                            var input = [];

                            var arrayOfRow = [];
                            rest.path = 'getAllChildPricesByMasterPrice/' +basePriceUnit;
                            rest.get().success(function(data) {
                                //console.log("data", data);
                                if(data.length == 0){
                                    notification('No child price available','warning');
                                    return false;
                                }
                                else{
                                    angular.forEach(angular.element('[id^=basePriceUnit]'), function(value, key){
                                        var alreadyAddedChildPrice = angular.element('#basePriceUnit'+key);
                                        data = $.grep(data, function(e){ 
                                                return e.name != alreadyAddedChildPrice.text().trim(); 
                                                });
                                    });
                                    
                                    if(data.length == 0){
                                        notification('All child price already exists.','warning');
                                    }else{
                                        angular.forEach(data,function(val,i){
                                            input += '<tr class="basePriceMain' + count + '"><td><div class="col-sm-12">' +
                                            '<div class="col-sm-2"><input type="text" id="basepriceQuantity' + count + '" numbers-only class="form-control" name="basepriceQuantity" ng-model="baseQuentity[' + count + ']" ng-change="basePriceQuantityChnage(' + count + ', baseQuentity[' + count + '])" required></div>' +
                                            '<div class="col-sm-1"><a ng-click="basePriceCheck(' + count + ')"><i class="fa fa-check" id="basePriceCheck' + count + '" aria-hidden="true"></i></a></div>' +
                                            '<div class="col-sm-3" id="basePriceUnit' + count + '">' + val.name + '</div>' +
                                            '<div class="col-sm-2"><input type="text" class="form-control" id="basePrice' + count + '" only-digits name="basePrice" ng-model="basePrice[' + count + ']" required ng-change="basePriceChnage(' + count + ', basePrice[' + count + '])"></div>' +
                                            '<div class="col-sm-2" id="baseWaiting' + count + '"></div>' +
                                            '<div class="col-sm-2"><a><i class="iconDelete glyph-icon fa-times" ng-click="removeBasePrice(' + count + ')"></i></a></div>' +
                                            
                                            '</td></tr>';
                                            count++; 
                                        })
                                    }
                                }
                                
                                input = angular.element(input);
                                $compile(input)(scope);
                                $('body').find('.basePriceAdd').before(input);
                                scope.priceUnit = '0';
                            });
                            
                        } else {
                            notification('Please select base price', 'warning');
                        }
                    } else {
                        notification('Please first add language', 'warning');
                    }
                } else {
                    notification('Duplicate base price not allowed', 'warning');
                }
            });
        }
    }
});

// Old directive basePriceAdd
/*app.directive('basePriceAdd', function($http, rest, $timeout, $compile, $log) {
    return {
        restrict: 'EA',
        link: function(scope, element, attr) {
            var count = 0;
            element.bind('click', function() {
                var priceCheck = true;
                for (var i = 0; i < angular.element('[id^=basePriceUnit]').length; i++) {
                    //alert();
                    if (scope.priceUnit.split(',')[0] == angular.element('#basePriceUnit' + i).text().trim()) {
                        var priceCheck = false;
                    }

                };
                if (priceCheck == true) {
                    if (angular.element('[id^=priceLanguageID]').length > 0) {
                        if (angular.element('[class^=basePriceMain]').length) {
                            count = angular.element('[class^=basePriceMain]').length;
                        }

                        var basePriceUnit = scope.priceUnit.split(',')[0];
                        if (scope.price == null || scope.price == undefined || scope.price == '') {
                            scope.price = {};
                        }

                        if (scope.baseQuentity == undefined || scope.baseQuentity == null || scope.baseQuentity == '') {
                            scope.baseQuentity = [];
                        }

                        var firstInt = 0;
                        secondint = 1;

                        if (basePriceUnit != 0) {
                            var quantity = 0;
                            for (var i = 0; i < angular.element('[class^=plannedQuentity]').length; i++) {
                                quantity += parseInt(angular.element('.plannedQuentity' + i).text());
                            }
                            var standard = 0;
                            for (var i = 0; i < angular.element('[class^=plannedStandardTime]').length; i++) {
                                standard += parseInt(angular.element('.plannedStandardTime' + i).text());
                            }

                            var standardTime = scope.priceUnit.split(',')[1];
                            scope.planedHourTotal = parseInt(standard) + parseInt(standardTime);
                            scope.planedQuaTotal = quantity + 1;
                            scope.baseQuentity[count] = 1;
                            var index = count + 1;

                            var input = angular.element('<tr class="basePriceMain' + count + '"><td><div class="col-sm-12">' +
                                '<div class="col-sm-2"><input type="text" id="basepriceQuantity' + count + '" only-digits class="form-control" name="basepriceQuantity" ng-model="baseQuentity[' + count + ']" ng-change="basePriceQuantityChnage(' + count + ', baseQuentity[' + count + '])" required></div>' +
                                '<div class="col-sm-1"><a ng-click="basePriceCheck(' + count + ')"><i class="fa fa-check" id="basePriceCheck' + count + '" aria-hidden="true"></i></a></div>' +
                                '<div class="col-sm-3" id="basePriceUnit' + count + '">' + basePriceUnit + '</div>' +
                                '<div class="col-sm-2"><input type="text" class="form-control" id="basePrice' + count + '" only-digits name="basePrice" ng-model="basePrice[' + count + ']" required ng-change="basePriceChnage(' + count + ', basePrice[' + count + '])"></div>' +
                                '<div class="col-sm-2" id="baseWaiting' + count + '"></div>' +
                                '<div class="col-sm-2"><a><i class="fa fa-times" ng-click="removeBasePrice(' + count + ')"></i></a></div>' +
                                '<div class="col-sm-2 none standardTime' + count + '">' + standardTime + '</div>' +
                                '</div>' +
                                '</td></tr>');
                            var input1 = angular.element('<tr class="plannedData' + count + '"><td>' + index + '</td><td class="plannedQuentity' + count + '" ng-bind="baseQuentity[' + count + ']"></td><td>' + basePriceUnit + '</td><td class="plannedStandardTime' + count + '" ng-bind="' + standardTime + '"></td></tr>');
                            $compile(input)(scope);
                            $compile(input1)(scope);
                            $('body').find('.plannTime').before(input1);
                            $('body').find('.basePriceAdd').before(input);
                            scope.priceUnit = '0';
                            count++;
                        } else {
                            notification('Please select base price', 'warning');
                        }
                    } else {
                        notification('Please first add language', 'warning');
                    }
                } else {
                    notification('Duplicate base price not allowed', 'warning');
                }
            });
        }
    }
});*/

app.directive('select2PriceList', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        link: function(scope, element, attr) {
            var obj = [];
            rest.path = 'customerpriceAll/' + scope.pricePageId;
            rest.get().success(function(data) {
                var newdata = data;
                if(scope.pricePageId == 1){
                    console.log('pricelist = UserId',scope.UserId);
                    newdata = data.filter( function (data) {
                        return data.resource_id == scope.UserId;  
                    });
                }
                if(scope.pricePageId == 2){
                    newdata = data.filter( function (data) {
                        return data.resource_id == scope.ExternalPricelistId;  
                    });
                }
                angular.forEach(newdata, function(val, i) {
                    obj.push({
                        'id': val.price_list_id,
                        'text': val.price_name
                    });
                });
            });
            element.select2({
                allowClear: true,
                data: obj,
                multiple:true,
                maximumSelectionSize:1
            });
        }
    }
});

//country for  national holiday
app.directive('select2HolidayCountry', function(rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var country = ['Argentina', 'Angola', 'Australia', 'Aruba', 'Belgium', 'Bulgaria', 'Brazil', 'Canada', 'China', 'Colombia', 'Croatia', 'Denmark', 'France', 'Germany', 'Guatemala', 'Hungary', 'Indonesia', 'Ireland', 'India', 'Italy', 'Lesotho', 'Luxembourg', 'Madagascar', 'Martinique', 'Mexico', 'Netherlands', 'Norway', 'Poland', 'Russia', 'Slovenia', 'Slovakia', 'Switzerland', 'Spain', 'Ukraine'];
            var type = [];
            angular.forEach(country, function(value, key) {
                var obj = {
                    'id': value,
                    'text': value
                };
                type.push(obj);
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: type,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
        }
    }
});
//get country
app.directive('select2Country', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            var country = [{
                id: 'AT',
                text: 'Austria'
            }, {
                id: 'BE',
                text: 'Belgium'
            }, {
                id: 'BG',
                text: 'Bulgaria'
            }, {
                id: 'CY',
                text: 'Cyprus'
            }, {
                id: 'CZ',
                text: 'Czech Republic'
            }, {
                id: 'DE',
                text: 'Germany'
            }, {
                id: 'DK',
                text: 'Denmark'
            }, {
                id: 'EE',
                text: 'Estonia'
            }, {
                id: 'ES',
                text: 'Spain'
            }, {
                id: 'FI',
                text: 'Finland'
            }, {
                id: 'FR',
                text: 'France'
            }, {
                id: 'GB',
                text: 'United Kingdom'
            }, {
                id: 'HR',
                text: 'Croatia'
            }, {
                id: 'HU',
                text: 'Hungary'
            }, {
                id: 'IE',
                text: 'Ireland'
            }, {
                id: 'IT',
                text: 'Italy'
            }, {
                id: 'LT',
                text: 'Lithuania'
            }, {
                id: 'LU',
                text: 'Luxembourg'
            }, {
                id: 'LV',
                text: 'Ltvia'
            }, {
                id: 'MT',
                text: 'Malta'
            }, {
                id: 'NL',
                text: 'The Netherlands'
            },{
                id: 'NO',
                text: 'Norway'
            }, {
                id: 'PL',
                text: 'Poland'
            }, {
                id: 'PT',
                text: 'Potugal'
            }, {
                id: 'RO',
                text: 'Romania'
            }, {
                id: 'SE',
                text: 'Sweden'
            }, {
                id: 'SI',
                text: 'Siovenia'
            }, {
                id: 'SK',
                text: 'Slovakia'
            }];

            // rest.path = 'getCountry';
            // rest.get().success(function (data) {
            //     var status = [];
            //     $.each(data, function (key, value) {
            //         var obj = {
            //             id: value.iso,
            //             text: value.name
            //         };
            //         status.push(obj);
            //     });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: country,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
            // }).error(function (data, error, status) {
            // });
        }
    }
});
app.directive('select2Projects', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = 'projectGet';
            rest.get().success(function(data) {
                var status = [];
                angular.forEach(data, function(value, key) {
                    var obj = {
                        id: value.order_id,
                        text: value.order_no
                    };
                    status.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: status,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            })
        }
    }
});

//External and internal user group select2
app.directive('select2AllUser', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            rest.path = "getAlluserGroup";
            rest.get().success(function(data) {
                var obj = [];
                var group = ["Internal"];
                children = [];

                for (var j = 0; j < group.length; j++) {
                    obj.push({
                        'text': group[j],
                        'children': [],
                    });
                            
                    angular.forEach(data, function(val, i) {
                        if ((val.iFkUserTypeId - 1) == j) {
                            obj[(val.iFkUserTypeId - 1)]['children'].push({
                                'id': val.iUserId,
                                'text': val.vUserName
                            })
                        }
                    });
                }

                element.select2({
                    allowClear: true,
                    data: obj,
                    multiple:true,
                    maximumSelectionSize:1
                });
            });
        }
    }
});

app.directive('clickOutside', function($document, $timeout) {
    return {
        restrict: 'A',
        scope: {
            clickOutside: '&'
        },
        link: function(scope, el, attr) {
            $document.on('click', function(e) {
                if (el !== e.target && !el[0].contains(e.target)) {
                    angular.element('.iconResults').hide();
                }
            });
        }
    }

});
//----------------invoice status----------------------//
app.directive('select2Invoice', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            
            var arr = ['Open', 'Pending', 'Overdue', 'Cancelled'];
            var users = [];
            angular.forEach(arr, function(value, key) {
                var obj = {
                    'id': value,
                    'text': value
                };
                users.push(obj);
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: users,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
        }
    }
});
app.directive('select2InvoicePayment', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var arr = ['Complete', 'Part'];
            var users = [];
            angular.forEach(arr, function(value, key) {
                var obj = {
                    'id': value,
                    'text': value
                };
                users.push(obj);
            });
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: users,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
        }
    }
});
app.directive('moDateInput', function ($window) {
    return {
        require:'^ngModel',
        restrict:'A',
        link:function (scope, elm, attrs, ctrl) {
            var moment = $window.moment;
            var dateFormat = attrs.moDateInput;
            attrs.$observe('moDateInput', function (newValue) {
                if (dateFormat == newValue || !ctrl.$modelValue) return;
                dateFormat = newValue;
                ctrl.$modelValue = new Date(ctrl.$setViewValue);
            });

            ctrl.$formatters.unshift(function (modelValue) {
                if (!dateFormat || !modelValue) return "";
                var retVal = moment(modelValue).format(dateFormat);
                return retVal;
            });

            ctrl.$parsers.unshift(function (viewValue) {
                var date = moment(viewValue, dateFormat);
                return (date && date.isValid() && date.year() > 1950 ) ? date.toDate() : "";
            });
        }
    };
});

//freelance user list
app.directive('select2FreelanceUser', function($http, rest, $timeout, $log) {
   return {
       restrict: 'EA',
       require: 'ngModel',
       link: function(scope, element, attrs) {
           rest.path="userExternalGet/2";
           rest.get().success(function(data) {
                var users = [];
               angular.forEach(data.data, function(value, key) {
                   var obj = {
                       'id': value.iUserId,
                       'text': value.vUserName
                   };
                   users.push(obj);
               });
               $timeout(function() {
                   element.select2({
                       allowClear: true,
                       data: users,
                       multiple:true,
                       maximumSelectionSize:1
                   });
               }, 500);
           });
       }
   }
});

app.directive('isNumber', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attrs) {    
            scope.$watch('customerPrice.min_price_item', function(newValue,oldValue) {
                if(oldValue != undefined){
                    var arr = String(newValue).split("");
                    if (arr.length === 0) return;
                    if (arr.length === 1 && (arr[0] == '-' || arr[0] === '.' )) return;
                    if (arr.length === 2 && newValue === '-.') return;
                    if (isNaN(newValue)) {
                        scope.customerPrice.min_price_item = oldValue;
                    }        
                }
            });
        }
    };
});
app.directive('numbersOnly', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                if (text) {
                    var transformedInput = text.toString().replace(/[^0-9]/g, '');

                    if (transformedInput !== text) {
                        ngModelCtrl.$setViewValue(transformedInput);
                        ngModelCtrl.$render();
                    }
                    return transformedInput;
                }
                return undefined;
            }            
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});
app.directive("ngDate", function() {
    return {
        //ng-upcomming-date
        restrict: 'EA',
        require: 'ngModel',
        scope: {
            ngModel: '='
        },
        link: function(scope, element, attrs, ngModelCtrl) {
            element.datetimepicker({
                widgetPositioning:{
                    horizontal: 'auto',
                    vertical: 'bottom'
                },
                format:'YYYY/MM/DD'
            }).on('dp.change', function(ev) {
                ngModelCtrl.$setViewValue(moment(ev.date).format("YYYY/MM/DD"));
                element.blur();
            });
        }
    };
});
app.directive('select2ContactPerson', function($http, rest, $timeout, $log,$window,$routeParams) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            $routeParams.id = $window.localStorage.orderID;//getting clientContact from order id
            rest.path = 'checkContactClientId';
            rest.model().success(function(data) {
                var status = [];
                angular.forEach(data, function(value, key) {
                    var obj = {
                        id: value.iContactId,
                        text: value.vFirstName+' '+value.vLastName
                    };
                    status.push(obj);
                });
                $timeout(function() {
                    element.select2({
                        allowClear: true,
                        data: status,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                }, 500);
            })
        }
    }
});
app.directive('numbersOnly', function($filter) {
   return {
       require: 'ngModel',
       restrict: 'A',
       link: function(scope, element, attr, ctrl) {
           function inputValue(val) {
               if (val) {
                   var digits = val.replace(/[^0-9.]/g, '');
                   

                   if (digits.split('.').length > 2) {
                       digits = digits.substring(0, digits.length - 1);
                   }

                   $filter('number',2)(digits);

                   if (digits !== val) {
                       ctrl.$setViewValue(digits);
                       ctrl.$render();
                   }
                   return parseFloat(digits);
               }
               return undefined;
           }
           ctrl.$parsers.push(inputValue);
       }
   };
});

app.directive('select2EmailTplCat', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            var tplCat = [
                {
                    id: '1',
                    text: 'Accepted job by resource'
                }, {
                    id: '2',
                    text: 'Job rejected by resource'
                }, {
                    id: '3',
                    text: 'Reset Password'
                }, {
                    id: '4',
                    text: 'Registration email'
                }, {
                    id: '5',
                    text: 'Overdue job'
                }, {
                    id: '6',
                    text: 'Job Delivery'
                },{
                    id: '7',
                    text: 'Password to resource'
                }
            ];
            /*var tplCat = [{
                id: '1',
                text: 'Purchase Order'
            }, {
                id: '2',
                text: 'Invoice'
            }, {
                id: '3',
                text: 'Reset Password'
            }];*/

            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: tplCat,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
            
        }
    }
});
app.directive('select2EmailTplType', function($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
            var tplCat = [{
                id: '1',
                text: 'Type 1'
            }, {
                id: '2',
                text: 'Type 2'
            }];

            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: tplCat,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 500);
            
        }
    }
});

app.directive('select2DateFormat', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var DateFormats = [{
                id: '0',
                text: 'YYYY/MM/DD'
            },{
                id: '1',
                text: 'DD/MM/YYYY'
            },{
                id: '2',
                text: 'MMM/DD/YYYY'
            },{
                id: '3',
                text: 'DD/MMM/YYYY'
            }];

            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: DateFormats,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 200);
        }
    }
});
app.directive('select2DateSeparator', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var dateSeparator = [{
                id: '/',
                text: '/'
            },{
                id: ',',
                text: ','
            },{
                id: '.',
                text: '.'
            }];

            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: dateSeparator,
                    multiple:true,
                    //separators: ';',
                    maximumSelectionSize:1
                });
            }, 200);
        }
    }
});
app.directive('select2DecimalSeparator', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var Separator = [{
                id: ',',
                text: ','
            },{
                id: '.',
                text: '.'
            }];

            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: Separator,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 200);
        }
    }
});
app.directive('loading',   ['$http' ,function ($http)
    {
        return {
            restrict: 'A',
            link: function (scope, elm, attrs)
            {
                scope.isLoading = function () {
                    return $http.pendingRequests.length > 0;
                };

                scope.$watch(scope.isLoading, function (v)
                {
                    if(v){
                        if(elm){
                            var url = elm[0].baseURI;
                            if(url){
                                if(url.includes("#/discussion/")){
                                    elm.hide();
                                }
                            }else{
                                elm.show();        
                            }
                        }
                        //elm.show();
                    }else{
                        elm.hide();
                    }
                });
            }
        };

    }]);

app.directive('allowDecimalNumbers', function () {  
    return {  
        restrict: 'A',  
        link: function (scope, elm, attrs, ctrl) {  
            elm.on('keydown', function (event) {  
                var $input = $(this);  
                var value = $input.val();  
                value = value.replace(/[^0-9\.]/g, '')  
                var findsDot = new RegExp(/\./g)  
                var containsDot = value.match(findsDot)  
                if (containsDot != null && ([46, 110, 190].indexOf(event.which) > -1)) {  
                    event.preventDefault();  
                    return false;  
                }  
                $input.val(value);  
                if (event.which == 64 || event.which == 16) {  
                    // numbers  
                    return false;  
                } if ([8, 13, 27, 37, 38, 39, 40, 110].indexOf(event.which) > -1) {  
                    // backspace, enter, escape, arrows  
                    return true;  
                } else if (event.which >= 48 && event.which <= 57) {  
                    // numbers  
                    return true;  
                } else if (event.which >= 96 && event.which <= 105) {  
                    // numpad number  
                    return true;  
                } else if ([46, 110, 190].indexOf(event.which) > -1) {  
                    // dot and numpad dot  
                    return true;  
                } else {  
                    event.preventDefault();
                    return false;  
                }  
            });  
        }  
    }  
});

app.directive('allowDecimalCommaNumber', function () {  
    return {  
        restrict: 'A',  
        link: function (scope, elm, attrs, ctrl) {  
            elm.on('keydown', function (event) {  
                var $input = $(this);  
                var value = $input.val();  
                //value1 = value.replace(/[a-z]/g, '')  
                value = value.replace(/[^0-9\,\.]/g, '')  
                var findsComma = new RegExp(/\,/g)  
                var findsComma = value.match(findsComma)  
                if (findsComma != null && ([188].indexOf(event.which) > -1)) {  
                    event.preventDefault();  
                    return false;  
                }  
                $input.val(value);  
                //$input.val(value1);  
                var containsDot = new RegExp(/\./g)  
                var containsDot = $input.val().match(containsDot)  
                if (containsDot != null) {  
                    return true;  
                }
                if (event.which == 64 || event.which == 16) {  
                    // numbers  
                    return false;  
                } if ([8, 13, 27, 37, 38, 39, 40, 110].indexOf(event.which) > -1) {  
                    // backspace, enter, escape, arrows  
                    return true;  
                } else if (event.which >= 48 && event.which <= 57) {  
                    // numbers  
                    return true;  
                } else if (event.which >= 96 && event.which <= 105) {  
                    // numpad number  
                    return true;  
                } else if ([46, 110, 190, 188].indexOf(event.which) > -1) {  
                    // dot and numpad dot comma 
                    return true;  
                } else {  
                    event.preventDefault();
                    return false;  
                }  
            });  
        }  
    }  
}); 

app.directive('allowtimefrmt', function () {  
    return {  
        restrict: 'A',  
        link: function (scope, elm, attrs, ctrl) {  
            elm.on('keydown', function (event) {  
                if ([37, 38, 39, 40, 110].indexOf(event.which) > -1) {  
                    // backspace, enter, escape, arrows  
                    return true;  
                }  
            });  
        }  
    }  
});

app.directive('allowtimefrmtTest', function () {  
    return {  
        restrict: 'A',  
        link: function (scope, elm, attrs, ctrl) {  
            elm.on('keyup', function (event) {  
                var $input = $(this);  
                var value = $input.val();
                
                value = value.replace(/[^0-9\:]/g, '')  
                var findsColon = new RegExp(/\:/g)  
                var containsColon = value.match(findsColon)  
                if (containsColon != null && ([59,186].indexOf(event.which) > -1)) {  
                    event.preventDefault();  
                    return false;  
                }  
                $input.val(value);  
                var timeStr = $input.val();
                console.log('value',timeStr.toString())  
                var valid = (timeStr.search(/^\d{2}:\d{2}$/) != -1) &&
            (timeStr.substr(0,2) >= 0 && timeStr.substr(0,2) <= 24) &&
            (timeStr.substr(3,2) >= 0 && timeStr.substr(3,2) <= 59) &&
            (timeStr.substr(6,2) >= 0 && timeStr.substr(6,2) <= 59);
                var t1= timeStr.substr(0,2);
                /*if(t1 <=0 || t1>=24){
                    console.log('t1',t1);
                    console.log('yes',timeStr.substr(0,2));
                    console.log('yes',timeStr.substr(3,2));
                    event.preventDefault();  
                    return false;
                }*/
                if(valid){ 
                    return true; 
                }else if (event.which == 64 || event.which == 16) {  
                    // numbers  
                    return false;  
                }else if ([8, 13, 27, 37, 38, 39, 40, 110].indexOf(event.which) > -1) {  
                    // backspace, enter, escape, arrows  
                    return true;  
                } else if (event.which >= 48 && event.which <= 57) {  
                    // numbers  
                    return true;  
                } else if (event.which >= 96 && event.which <= 105) {  
                    // numpad number  
                    return true;  
                } else if ([59,186].indexOf(event.which) > -1) {  
                    // dot and numpad dot  
                    return true;  
                } else {  
                    event.preventDefault();
                    return false;  
                }  
            });  
        }  
    }  
}); 
// work instruction
app.directive('jobWorkInstruction2', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            var num = scope.counter;
            element.bind('click', function() {
                var len = $('.put_row').length;
                if (len != undefined) {
                    scope.counter = len;
                } else {
                    scope.counter = 1;
                }
                var test = $('#work_instructs').val();
                if (!test) {
                    notification('Please select option', 'warning');
                } else {
                    var CommaIndex = test.indexOf(",");
                    var strD1 = test.substring(0, CommaIndex); 
                    var strD2 = test.substring(CommaIndex + 1); 
                    
                    var strD = test.split(',');
                    var i;
                    var allow = true;
                    for(i=0;i<=scope.counter-1;i++){
                        if(strD2 == angular.element('#work_name'+ i).text()){
                            notification('Work instructions already exists.', 'error');
                            allow = false;
                        }
                    }
                    if(allow){
                        var input = angular
                        .element('<tr id="work1_' + scope.counter + '" class="put_row">' +
                            '<td class="none"><span id="work_id' + scope.counter + '">' + strD1 + '<span></td>' +
                            '<td><span style="color:green;"><i class="fa fa-check" aria-hidden="true"></i> </span> <span id="work_name' + scope.counter + '">' + strD2 + '<span></td>' +
                            '<td><a href="javascript:void(0)" ng-click="removeWorkIns(' + scope.counter + ')" title="delete"><i class="iconDelete glyph-icon fa-times"></i></a></td>'
                        );
                        var compile = $compile(input)(scope);
                        var test = $('.workIn').before(input);
                        scope.counter++;
                    }
                }
            });
        }
    }
}]);
// work instruction - job
app.directive('jobWorkInstruction3', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'EA',
        link: function(scope, element, attrs) {
            scope.counter = 1;
            var num = scope.counter;
            element.bind('click', function() {
                var len = $('.put_row').length;
                if (len != undefined) {
                    scope.counter = len;
                } else {
                    scope.counter = 1;
                }
                var test = $('#work_instructs').val();
                if (!test) {
                    notification('Please select option', 'warning');
                } else {
                    var CommaIndex = test.indexOf(",");
                    //var strD1 = test.substring(0, CommaIndex); 
                    var strD1 = 1; 
                    //var strD2 = test.substring(CommaIndex + 1); 
                    var strD2 = test;
                    console.log('test',test);
                    var strD = test.split(',');
                    var i;
                    
                    var allow = true;
                    for(i=0;i<=scope.counter-1;i++){
                        if(strD2 == angular.element('#work_name'+ i).text()){
                            notification('Work instructions already exists.', 'error');
                            allow = false;
                        }
                    }
                    if(allow){
                        var input = angular
                        .element('<tr id="work1_' + scope.counter + '" class="put_row putinstruct" style="background-color:#FFF;">' +
                            '<td class="none"><span id="work_id' + scope.counter + '">' + scope.counter + '<span></td>' +
                            '<td><span style="color:green;"><i class="fa fa-check" aria-hidden="true"></i> </span> <span id="work_name' + scope.counter + '">' + strD2 + '<span></td>' +
                            '<td><a href="javascript:void(0)" ng-click="removeWorkIns(' + scope.counter + ')" title="delete" class="deletebtn"><i class="iconDelete glyph-icon fa-times"></i></a></td>'
                        );
                        var compile = $compile(input)(scope);
                        if(len==1){
                            var test = $('.workIn').before(input);
                        }else{
                            var test = $(".putinstruct:first").before(input);
                        }
                        //var test = $('.workIn').before(input);
                        scope.counter++;
                        //var test = $('#work_instructs').val('');
                    }
                }
            });
        }
    }
}]);
// nested files
app.directive('angularTreeview', ['$compile', function($compile) { // inject $compile service as
    // dependency
    return {
        restrict: 'A',
        link: function ( scope, element, attrs) {
                //tree id
                var treeId = attrs.treeId;
            
                //tree model
                var treeModel = attrs.treeModel;

                //node id
                var nodeId = attrs.nodeId || 'id';

                //node label
                var nodeLabel = attrs.nodeLabel || 'label';
                console.log('nodeLabel',nodeLabel);
                //children
                var nodeChildren = attrs.nodeChildren || 'children';
                console.log('nodeChildren',nodeChildren);
                
                //tree template
                var template =
                    '<ul>' +
                        '<li data-ng-repeat="node in ' + treeModel + '">' +
                            '<i class="collapsed" data-ng-show="node.' + nodeChildren + '.length && node.collapsed" data-ng-click="' + treeId + '.selectNodeHead(node)"></i>' +
                            '<i class="expanded" data-ng-show="node.' + nodeChildren + '.length && !node.collapsed" data-ng-click="' + treeId + '.selectNodeHead(node)"></i>' +
                            '<i class="normal" data-ng-hide="node.' + nodeChildren + '.length"></i> ' +
                            '<span data-ng-class="node.selected" data-ng-click="' + treeId + '.selectNodeLabel(node)">{{node.' + nodeLabel + '}}</span>' +
                            '<div data-ng-hide="node.collapsed" data-tree-id="' + treeId + '" data-tree-model="node.' + nodeChildren + '" data-node-id=' + nodeId + ' data-node-label=' + nodeLabel + ' data-node-children=' + nodeChildren + '></div>' +
                        '</li>' +
                    '</ul>';


                //check tree id, tree model
                if( treeId && treeModel ) {

                    //root node
                    if( attrs.angularTreeview ) {
                    
                        //create tree object if not exists
                        scope[treeId] = scope[treeId] || {};

                        //if node head clicks,
                        scope[treeId].selectNodeHead = scope[treeId].selectNodeHead || function( selectedNode ){

                            //Collapse or Expand
                            selectedNode.collapsed = !selectedNode.collapsed;
                        };

                        //if node label clicks,
                        scope[treeId].selectNodeLabel = scope[treeId].selectNodeLabel || function( selectedNode ){

                            //remove highlight from previous node
                            if( scope[treeId].currentNode && scope[treeId].currentNode.selected ) {
                                scope[treeId].currentNode.selected = undefined;
                            }

                            //set highlight to selected node
                            selectedNode.selected = 'selected';

                            //set currentNode
                            scope[treeId].currentNode = selectedNode;
                        };
                    }

                    //Rendering template.
                    element.html('').append( $compile( template )( scope ) );
                }
            }
    }
}]);
app.directive('jobitemsAdd2', ['$compile', function($compile) { // inject $compile service as dependency
    return {
        restrict: 'E',
        link: function(scope, element, attrs, rootScope) {
            scope.counter = 0;
            element.bind('change', function() {
                if(angular.element('[id^=totalItem_'+scope.jobdetail.job_summmeryId+']').length == 0){
                    scope.counter = 0;

                }else if (angular.element('[id^=totalItem_'+scope.jobdetail.job_summmeryId+']').length > 0) {
                    var len = angular.element('[id^=totalItem_'+scope.jobdetail.job_summmeryId+']').length;
                    scope.counter = len + 1;
                }
                
                var totaPrice = scope.jobdetail.total_price;
                
                var quantity = angular.element('#Quantity'+scope.jobdetail.job_summmeryId).val();

                if (!quantity) {
                    quantity = 1;
                }

                var price = angular.element('#priceUnit'+scope.jobdetail.job_summmeryId).val();
                if(price == 0){
                    notification('please select price','warning');
                    return false;
                }
                var temp = price.split(',');
                var Price_unit = temp[0];

                //check if priceUitt already exists
                var exists = true;
                 if(scope.itemPriceUni[scope.jobdetail.job_summmeryId]){
                    if(scope.itemPriceUni[scope.jobdetail.job_summmeryId].length > 0){
                        angular.forEach(scope.itemPriceUni[scope.jobdetail.job_summmeryId], function(val,i){
                            if(exists){
                                if(val.pricelist == Price_unit){
                                  exists = false;  
                                }
                            }
                        })
                    }
                }else{
                    scope.itemPriceUni[scope.jobdetail.job_summmeryId] = [];
                }

                if(exists){
                    var amount = temp[1];
                    var total = amount * quantity;

                    if(scope.it == undefined) {
                        scope.it = {};
                    }

                    if(scope.jobdetail.quantity==undefined || scope.jobdetail.itemPrice==undefined) {
                        scope.jobdetail.quantity = [];
                        scope.jobdetail.itemPrice = [];
                    }
                    
                    scope.jobdetail.quantity[scope.counter] = quantity;
                    scope.jobdetail.itemPrice[scope.counter] = amount;


                    if (quantity && price != 0) {
                        var totalItemPrice = parseFloat(total) + parseFloat(totaPrice);
                        scope.jobdetail.total_price = totalItemPrice;
                        $('#priceUnit'+scope.jobdetail.job_summmeryId).val(0);
                        $('#Quantity').val('');
                        scope.counter++;
                        var itemTotal1 = quantity*amount;
                        var itemTotal = itemTotal1.toString().replace('.', ',');  
                        var amount = amount.toString().replace('.', ',');  
                        scope.itemPriceUni[scope.jobdetail.job_summmeryId].push({
                            'quantity': quantity,
                            'pricelist':Price_unit,
                            'itemPrice':amount,
                            'itemTotal':itemTotal
                        });
                        angular.element('#Quantity'+scope.jobdetail.job_summmeryId).val('');
                    } else {
                        notification('Please select item', 'warning');
                    }
                }else{
                    notification('Price list already exists.', 'warning');
                }
            });
        }
    }
}]);
// --------- Create scoop ---------------------//
//app.directive('ngSelect2Createscoop', function(rest, $timeout) {
app.directive('ngSelect2CreateScooptest', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
            var scoopData = [
                {
                    'id': '1',
                    'text': '1' 
                },{
                    'id': '2',
                    'text': '2'
                }
            ];
            
            $timeout(function() {
                element.select2({
                    allowClear: true,
                    data: scoopData,
                    multiple:true,
                    maximumSelectionSize:1
                });
            }, 200);
            
        }
    }
});

// --------- select2 project Type---------------------//
app.directive('select2CreateScoop', function($http, rest, $timeout) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element, attrs) {
            //rest.path = 'prtypeactive';
            //rest.get().success(function(data) {
                var scoopData = [
                    {
                        'id': '1',
                        'text': '1' 
                    },{
                        'id': '2',
                        'text': '2'
                    },{
                        'id': '3',
                        'text': '3'
                    },{
                        'id': '4',
                        'text': '4'
                    },{
                        'id': '5',
                        'text': '5'
                    }
                ];
                $timeout(function() {
                    element.select2({
                        placeholder:'Create Scoop',
                        allowClear: true,
                        data: scoopData,
                        multiple:true,
                        maximumSelectionSize:1
                    });
                    $('.scoopText input').attr("placeholder", "Create Scoop");
                }, 200);

            //}).error(function(data, error, status) {});
        }
    }
});
// --------- select2 Work Flow ---------------------//
app.directive('select2CommonDropdwn', function($http, rest, $timeout,$window) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function(scope, element) {
                $timeout(function() {
                    element.select2({
                        //placeholder: 'Please select scoop',
                        allowClear: true,
                        maximumSelectionSize:1,
                    });
                    //$('.scoopText input').attr("placeholder", "Create Scoop");
                    $("#jobDropDown").select2({
                        placeholder: "Please select scoop",
                        maximumSelectionSize:1
                    });
                    $("#jobchainName").select2({
                        placeholder: "-Please select-",
                        maximumSelectionSize:1
                    });
                    
                }, 200);
        }
    }
});
