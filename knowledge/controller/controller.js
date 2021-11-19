function notification(msg, type) {
    var n = noty({
        layout: 'center',
        type: type,
        text: msg, // can be html or string
        timeout: 2000,
    });
}
function dateFormat(da) {
    return currentdateT = pad(da.getDate(), 2) + '.' + pad((da.getMonth() + 1), 2) + '.' + da.getFullYear();
}

function date_time_format(input) {
    var d = new Date(input);
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var date = pad(d.getDay(), 2) + "." + pad((d.getMonth() + 1), 2) + "." + d.getFullYear();
    var time = d.toLocaleTimeString().toLowerCase().replace(/([\d]+:[\d]+):[\d]+(\s\w+)/g, "$1$2");
    return (date + " " + time);
};

function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}

function weekdayName(num) {
    var weekday = new Array(7);
    weekday[0] = "Sunday";
    weekday[1] = "Monday";
    weekday[2] = "Tuesday";
    weekday[3] = "Wednesday";
    weekday[4] = "Thursday";
    weekday[5] = "Friday";
    weekday[6] = "Saturday";
    return weekday[num];
}

function toDate(date) {
    return new Date(date);
}

function chechImageType(type) {
    var imgTypes = ['image/jpeg', 'image/png','image/x-windows-bmp','image/bmp','image/gif'];
    var TypeExists = false;
    imgTypes.map(function(val,key){
        if(type == val){
            TypeExists = true;
        }
    })
    return TypeExists;
}

function isEmpty(obj) {
    for (var prop in obj) {
        if (obj.hasOwnProperty(prop))
            return false;
    }
    return true;
};

app.controller('dashboardController', function($window,$scope, rest, $log, $location, $timeout, $interval, fileUpload, $cookieStore, $route, $modal, $log, $document, fileReader,$rootScope) {
    $scope.loginUserId = $cookieStore.get('session_iUserId');
    //$window.localStorage.welUser = $window.localStorage.getItem("session_vUserName");
    $scope.welUser = $window.localStorage.welUser;
    rest.path = "userBirthDate";
    rest.get().success(function(data) {
        $scope.birthdateList = data;
        var date = new Date();
        $scope.YEAR = date.getFullYear();
        $scope.birthEmpty = isEmpty(data);
    });
    
    function getDate(offset) {
        var now = new Date();
        var hour = 60 * 60 * 1000;
        var min = 60 * 1000;
        return new Date(now.getTime() + (now.getTimezoneOffset() * min) + (offset * hour));
    }
    
    var dateCET = getDate(1); // Central European Time is GMT +1
    function getDate(offset) {
        var now = new Date();
        var hour = 60 * 60 * 1000;
        var min = 60 * 1000;
        return new Date(now.getTime() + (now.getTimezoneOffset() * min) + (offset * hour));
    }
    
    function display() {
        var offsetDate = [2, 1, -7];
        for (var i = 0; i < offsetDate.length; i++) {
            var today = getDate(offsetDate[i]);
            var month = today.getMonth() + 1;
            var day = today.getDate();
            var year = today.getFullYear();
            var daynum = today.getDay();
            var dayname = weekdayName(daynum);
            var hour = today.getHours() > 12 ? today.getHours() : today.getHours();
            var minute = today.getMinutes();
            var seconds = today.getSeconds();
            var milliseconds = today.getMilliseconds();
            //var timeOfDay = ( hour > 12 ) ? "AM" : "PM";
            //var timeOfDay = ( hour < 12 ) ? "AM" : "PM";
            var date = pad(day, 2) + '.' + pad(month, 2) + '.' + year;
            //var output =  + ' - ' +
            // pad(hour, 2) + ':' + pad(minute, 2) + ':' + pad(seconds, 2) + ' = ' +  dayname;
            var datePrint = dayname + ',  ' + date;
            var time = pad(hour, 2) + ':' + pad(minute, 2) + ':' + pad(seconds, 2);
            angular.element('.Timer' + i).text(time);
            angular.element('.date' + i).text(datePrint);
        }
    }
    
    $interval(display, 1000);
    rest.path = "newsGetLatest";
    rest.get().success(function(data) {
        angular.forEach(data, function(val, i) {
            data[i].created_date= new Date(val.created_date);
            
           $timeout(function() {
                //newsTitleLength
                if (val.k_news_title.length > 20) {
                    angular.element('.newsTitleLength').text('...');
                }
            }, 100);
        });
        $scope.newsList = data;
    });

    $scope.newsDetail = function(id) {
        $location.path('/news/' + id);
    }
    
    //get picture
    $scope.displayAllpic = function(limit) {
        rest.path = "pictureGet/" + $scope.loginUserId + '/' + limit;
        rest.get().success(function(data) {
            $scope.funnyPicList = data;
            /* Start continue check Comment Detail Check from child controller updation */
            var CommentCheck = function () {
                if($rootScope.commentListLength != undefined){
                    $rootScope.$watch('commentListLength', function(newValue, oldValue){
                        if(oldValue.Length != newValue.Length){
                            angular.forEach($scope.funnyPicList,function(val,i){
                                if(newValue.pickId == val.picID){
                                   val.NumberOfComment = newValue.Length;
                                }
                            })
                        }
                    });
                }
            }
            $interval(CommentCheck, 1000);
            /* End continue check Comment Detail Check from child controller updation */

            $scope.loadingData = false;
        });
    }
    
    $scope.displayAllpic(2);

    //funny picture like or unlike
    $scope.pictureLike = function(likeId, id, like, lId) {
        $scope.fpicLike = {};
        
        if (!likeId && lId != 0) {
            $scope.fpicLike.pic_flage_like = parseInt(like) + 1;
            $scope.fpicLike.pic_like = 1;
        } else {
            $scope.fpicLike.pic_flage_like = parseInt(like) + 1;
        }
        
        $scope.fpicLike.pic_id = id;
        $scope.fpicLike.userId = $scope.loginUserId;
        rest.path = "pictureLikeSave";
        rest.post($scope.fpicLike).success(function(data) {
            $scope.displayAllpic($scope.funnyPicList.length);
        })
    }

    //picture load more
    angular.element('.loadPicture').on('scroll', function() {
        if (parseInt($(this).scrollTop() + $(this).innerHeight()) >= $(this)[0].scrollHeight - 1) {
            $scope.displayAllpic($scope.funnyPicList.length + 2);
            $scope.loadingData = true;
        }
    });
    
    $scope.onFileSelect = function() {
        fileReader.readAsDataUrl($scope.file, $scope).then(function(result) {
            $scope.imageSrc = result;
        });
    };
    
    //upload picture modal
    $scope.showAdvanced = function() {
        var modalInstance = $modal.open({
            templateUrl: 'pages/picture-dialogue.html',
            controller: 'pictureDialogueController',
            resolve: {
                items: function() {
                    return $scope.items;
                }
            }
        });
        modalInstance.result.then(function(selectedItem) {
            $scope.selected = selectedItem;
        }, function() {
            //            $log.info('Modal dismissed at: ' + new Date());
        });
        $timeout(function() {
            angular.element('.modal, .fade,  .in').css('display', 'block');
        }, 100);
    }
    $scope.ReportImage = function(fpid,uploadedUserName,reportedImage) {
    	$scope.reportdata = {};
    	$scope.reportdata.fpid = fpid;
    	$scope.reportdata.uploadedUserName = uploadedUserName;
    	$scope.reportdata.reportedImage = reportedImage;
    	var modalInstance = $modal.open({
            templateUrl: 'pages/report-image.html',
            controller: 'reportImageController',
            resolve: {
                items: function() {
                    return $scope.reportdata;
                }
            }
        });
        modalInstance.result.then(function(selectedItem) {
            $scope.selected = selectedItem;
        }, function() {
            //            $log.info('Modal dismissed at: ' + new Date());
        });
        $timeout(function() {
            angular.element('.modal, .fade,  .in').css('display', 'block');
        }, 100);

    }
    //comment modal
    $scope.commentDialogue = function(commentId, pId, comment) {
        //          $log.log(cId + ' ' + pId + ' ' + comment);
        $scope.items = {};
        $scope.items.pId = pId;
        $scope.items.comment = comment;
        $scope.items.commentId = commentId;
        $scope.items.userId = $scope.loginUserId;
        var modalInstance = $modal.open({
            templateUrl: 'pages/comment-dialogue.html',
            controller: 'commentDialogueController',
            resolve: {
                items: function() {
                    return $scope.items;
                }
            }
        });
        modalInstance.result.then(function(selectedItem) {
            $scope.selected = selectedItem;
        }, function() {
            //            $log.info('Modal dismissed at: ' + new Date());
        });
        $timeout(function() {
            angular.element('.modal, .fade,  .in').css('display', 'block');
        }, 100);
    }

}).controller('pictureDialogueController', function($scope, rest, $log, $modalInstance, fileReader, $cookieStore, $route,$timeout) {

    //upload funny picture dialogue box
    $scope.getFile = function(destination, file) {
        fileReader.readAsDataUrl(file, $scope).then(function(result) {
            switch (destination) {
                case 'fileToUpload':
                    $scope.lt_logo_image = result;
                    break;
                case 'discountImageBox1':
                    $scope.discountImageBox[0] = result;
                    $scope.discountImageBox1 = file;
                    break;
            }
            var fileType = result.split(';')[0].split(':');
            if(chechImageType(fileType[1])){
                $scope.imageSrc = result;
                $scope.AllowFileType = false;    
            }else{
                $scope.AllowFileType = true;
                $scope.imageSrc = undefined;
            }
        });
    };
    
    //Picture save
    $scope.AllowFileType = false;
    $scope.showAdvanced = function(imageSrc) {
        $scope.pictureData = {};
        $scope.pictureData.funny_pic = imageSrc;
        var fileType = $scope.pictureData.funny_pic.split(';')[0].split(':');
        if(chechImageType(fileType[1])){
            $scope.AllowFileType = false;
            $scope.pictureData.user_id = $cookieStore.get('session_iUserId');
            var date = new Date();
            $scope.pictureData.created_date = date;
            if (imageSrc) {
                rest.path = "pictureSave";
                rest.post($scope.pictureData).success(function(data) {
                    $modalInstance.dismiss('cancel');
                    $route.reload();
                })
            }
        }else{
            $scope.imageSrc = undefined;
            $scope.AllowFileType = true;
        }
    }

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };

}).controller('commentDialogueController', function($window,$scope, items, rest, $log, $modalInstance, fileReader, $cookieStore, $route, $routeParams,$timeout,$rootScope) {
    items.checkComment = false;
    $scope.modal = {
        comment1: ""
    }


    $scope.AutoScrollModalBody = function(){
        $timeout(function() {
            var totalHeight=0;
            var padding = 15;
            $.each($("#myModal").find(".comment-list"),function(i,e){
                totalHeight += ($(e).height() + padding);
            });
            $('.modal-body').animate({ scrollTop:totalHeight}, 'slow');
        },200);
    }

    $scope.AutoScrollModalBody();
    
    $scope.getComment = function() {
        
        rest.path = "pictureComentEdit/" +items.pId ;
        rest.get().success(function(data) {

            items.checkComment = true;
            $scope.commentList = data;
            
            /* start Comment Detail Send to parent Controller for update comment Count in parent controller*/
            $scope.commentDetail = {
                Length :$scope.commentList.length,
                pickId :items.pId
            };
            $rootScope.commentListLength = $scope.commentDetail;
            /* End Comment Detail Send to parent Controller for update comment Count in parent controller*/

            $scope.AutoScrollModalBody();
            //$scope.glued = true;
        });
        /*}*/
    }
    
    $scope.getComment();
    $scope.commentSave = function(frmId,input) {
        if((input.trim().length==0)){
            alert('You can not add space only');
        }else{
            $scope.comments = {};
            $scope.comments.flag_comment = parseInt(items.comment) + 1;
            $scope.comments.pic_id = items.pId;
            $scope.comments.user_id = $cookieStore.get('session_iUserId');
            $scope.comments.user_name = $window.localStorage.getItem("session_vUserName");
            $scope.comments.comment = input;
            var date = new Date();
            $scope.comments.created_date = date;
            rest.path = "picCommentSave";
            rest.post($scope.comments).success(function(data) {
                $timeout(function() {
                    angular.element('#commentInput').val('');
                    $scope.resetTextArea();
                    $scope.getComment();
                },100);
            });
        }
    }

    $scope.resetTextArea= function() {
        $scope.modal.comment1 = '';
    };

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };

}).controller('mainController', function($scope, rest, $log) {

    rest.path = 'categoryGet';
    rest.get().success(function(data) {
        $scope.categoryList = data;
        var catId;
        angular.forEach(data, function(val) {
            if (val.category_name == 'Getting started') {
                catId = val.category_id;
            }
        });
        $scope.getCategory(catId);
    });

    $scope.getCategory = function(id) {
        rest.path = "categoryDetail/" + id;
        rest.get().success(function(data) {
            $scope.categoryDetailList = data.data;
        });
    }

}).controller('headerController', function($scope, rest, $location, $routeParams, $log, $rootScope, $mdToast, $timeout) {
    angular.element('.site-header').css('background-color', '#008DD2');
    angular.element('.hkb_widget_toc ol li.active>a').css('background-color', '#00a0e3 !important');

    // $timeout(function() {
    //     angular.element('.ui-corner-all')
    // }, 500);

    //Custom autocomplete highlight
    $.widget("app.autocomplete", $.ui.autocomplete, {
        // Which class get's applied to matched text in the menu items.
        options: {
            highlightClass: "ui-state-highlight"
        },
        _renderItem: function(ul, item) {
            // Replace the matched text with a custom span. This
            // span uses the class found in the "highlightClass" option.
            var re = new RegExp("(" + this.term + ")", "gi"),
                cls = this.options.highlightClass,
                template = "<span class='" + cls + "'>$1</span>",
                label = item.label.replace(re, template),
                $li = $("<li/>").appendTo(ul);

            // Create and return the custom menu item content.
            $("<a/>").attr("href", "#")
                .html(label)
                .appendTo($li);

            return $li;
        }
    });

    $scope.pagePermission = function() {
        $mdToast.show(
            $mdToast.simple()
            .textContent('you have no permission to open this page')
            .position('right')
            .hideDelay(3000)
        );
    };

    //search result
    var searchD = [];
    $scope.searchResult = function() {
        var id = angular.element('#autoCompleteID').val();
        if (id) {
            $location.path('/search/' + id);
            angular.element('#autoComplete').val('');
        }
    }

}).controller('articleController', function($scope, rest, $routeParams, $log, anchorSmoothScroll, $location, $sce, $timeout) {
    getarticleData($routeParams.id);

    $scope.catgoryId = $routeParams.catId;

    //get article data on category id
    function getarticleData(id) {
        rest.path = "categoryArticle/" + id;
        rest.get().success(function(data) {
            $scope.articleDetail = data;
            $scope.id = id;
            angular.element('.sidebar--sticky').css('top', '0');
            $scope.ScrollerBar();
            
            //article nav id add daynamically
            $timeout(function() {
                $scope.articleTitle = [];
                var count = 1;
                angular.element('div h1').each(function(i, val) {
                    if (i > 0) {

                        if (angular.element('div h1:eq(' + i + ')').text() && angular.element('div h1:eq(' + i + ')').nextAll('p').length >= 9 && angular.element('div h1').length > 2) {
                            $scope.articleTitle.push({
                                'id': angular.element('div h1:eq(' + i + ')').text()
                            });
                            angular.element('div h1:eq(' + i + ')').attr({
                                "id": angular.element('div h1:eq(' + i + ')').text(),
                                "class": "nav_" + count + " target",
                            });
                            count++;
                        }

                        if (angular.element('div h1:eq(' + i + ')').text() && angular.element('div h1').length < 3) {
                            $scope.articleTitle.push({
                                'id': angular.element('div h1:eq(' + i + ')').text()
                            });
                            angular.element('div h1:eq(' + i + ')').attr({
                                "id": angular.element('div h1:eq(' + i + ')').text(),
                                "class": "nav_" + count + " target",
                            });
                            count++;
                        }
                    }
                });
            }, 100);
        });
    }

    //get category detail
    if ($routeParams.catId) {
        rest.path = "categoryDetail/" + $routeParams.catId;
        rest.get().success(function(data) {
            $scope.catId = $routeParams.catId;
            $scope.categoryDetailList = data.data;
            $scope.headerTitle = data;
        });
    }

    //article onchange
    $scope.articleGet = function(id) {
        //getarticleData(id);
        anchorSmoothScroll.scrollTo(id);
    }

    //sidebar sticky
    $scope.ScrollerBar = function() {
        $(window).scroll(function() {
            if ($(window).scrollTop() > 290) {
                var windowTop = $(window).scrollTop() - 200;

                if (windowTop < 150) {
                    angular.element('.sidebar--sticky').css('top', 0);
                } else {
                    angular.element('.sidebar--sticky').css('top', windowTop);
                }

                angular.element('.target').each(function() {
                    if ($(window).scrollTop() >= $(this).position().top) {
                        var id = $(this).attr('class').split(' ')[0];
                        if (id == "target") {
                            angular.element('nav li').removeClass('active');
                            angular.element('nav #nav_1').addClass('active');
                        } else {
                            angular.element('nav li').removeClass('active');
                            angular.element('nav #' + id).addClass('active');
                        }
                    }
                })
            }
        });
    }

    $scope.trustedHtml = function(plainText) {
        return $sce.trustAsHtml(plainText);
    }

}).controller('getStartedController', function($scope, rest, $routeParams, $compile, $log, anchorSmoothScroll) {
    getCategory($routeParams.id);
    
    //category get
    $scope.categoryGet = function(id) {
        $scope.categoryDetailList = [];
        rest.path = "categoryDetail/" + id;
        rest.get().success(function(data) {
            $scope.catId = id;
            $scope.categoryDetailList = data.data;
            $scope.categoryTitle = data.info[0];
            $scope.Nodata = jQuery.isEmptyObject(data.data);
            anchorSmoothScroll.scrollTo('top');
        });

        rest.path = 'categoryGet';
        rest.get().success(function(data) {
            $scope.categoryList = data;
        });
    }
        
    function getCategory(id) {
        $scope.categoryDetailList = [];
        rest.path = "categoryDetail/" + id;
        rest.get().success(function(data) {
            $scope.catId = id;
            $scope.categoryDetailList = data.data;
            $scope.Nodata = jQuery.isEmptyObject(data.data);
            $scope.categoryTitle = data.info[0];
        });
        
        rest.path = 'categoryGet';
        rest.get().success(function(data) {
            $scope.categoryList = data;
        });
    }

}).controller('searchController', function($scope, rest, $routeParams, $compile, $log, $sce, $location) {
    //search data
    if ($routeParams.id) {
        rest.path = "categoryArticle/" + $routeParams.id;
        rest.get().success(function(data) {
            $scope.resultList = data[0];
        });
    }

    $scope.trustedHtml = function(plainText) {
        return $sce.trustAsHtml(plainText);
    }

}).controller('newsController', function($scope, rest, $log, $sce, $filter, $location, $timeout) {
    rest.path = "newsGetAll";
    rest.get().success(function(data) {
        $scope.newsList = data;
        /*angular.forEach(data, function(val, i) {
            $timeout(function() {
                angular.element('.newsDate' + i).text(date_time_format(val.created_date));
            }, 100);
        });*/
    });

    $scope.trustedHtml = function(plainText) {
        return $sce.trustAsHtml(plainText);
    }

    $scope.newsDetail = function(id) {
        $location.path('/news/' + id);
    }

}).controller('NewsDetailController', function($scope, rest, $log, $sce, $filter, $location, $routeParams, anchorSmoothScroll, $timeout) {
    rest.path = "newsGetAll";
    rest.get().success(function(data) {
        $scope.newsList = data;
    });

    if ($routeParams.id) {
        rest.path = "newsGetOne/" + $routeParams.id;
        rest.get().success(function(data) {
            $scope.newData = data;
            $scope.ScrollerBar();
        });
    }

    $scope.trustedHtml = function(plainText) {
        return $sce.trustAsHtml(plainText);
    }

    $scope.NewsGet = function(id) {
        rest.path = "newsGetOne/" + id;
        rest.get().success(function(data) {
            $scope.newData = data;
            $scope.ScrollerBar();
        });
        anchorSmoothScroll.scrollTo('top');
    }

    $scope.ScrollerBar = function() {
        $timeout(function() {
            $stick = angular.element('aside');
            $foot = angular.element('footer');
            margin = 20;
            offtop = $stick.offset().top - 20;
            offbtm = $foot.offset().top - (margin * 2 + $stick.height());
            $(window).scroll(function() {
                scrtop = $(window).scrollTop() - offtop;
                var footer = offbtm - offtop;
                //$log.log(footer + ' ' + scrtop);
                if (offtop < scrtop) {
                    if (scrtop < footer) {
                        angular.element('.natural').css('top', scrtop);
                    } else {
                        angular.element('.natural').css('top', footer - 60);
                    }
                } else {
                    angular.element('.natural').css('top', 0);
                }
            });
        }, 200);
    }

    //    if (scrtop > offtop && $stick.hasClass('natural')) {
    //      $stick.removeClass('natural').addClass('fixed').css('top', margin);
    //    }
    //
    //    if (offtop > scrtop && $stick.hasClass('fixed')) {
    //      $stick.removeClass('fixed').addClass('natural').css('top', 'auto');
    //    }
    //    if (scrtop > offbtm && $stick.hasClass('fixed')) {
    //      $stick.removeClass('fixed').addClass('bottom').css('top', offbtm+margin);
    //    }
    //    
    //    if (offbtm > scrtop && $stick.hasClass('bottom')) {
    //      $stick.removeClass('bottom').addClass('fixed').css('top', margin);
    //    }

}).controller('memberController', function($rootScope,$scope, rest, $log, $sce, $filter, $mdToast) {

    //member manage
    $scope.changeMember = function(id) {
        rest.path = 'searchMemberResult/' + id;
        rest.get().success(function(data) {
            $scope.memberList = data;
            console.log("$scope.memberList", $scope.memberList);
        });
    }
    if($rootScope.adminRule == 2){
        $scope.changeMember(2);
        angular.element('#external_list').addClass('memberActive');
    }else{
        $scope.changeMember(1);
    }
    // member tab active set
    angular.element('.memberLink').click(function() {
        $('.memberLink').removeClass('memberActive');
        $(this).addClass('memberActive');
    });

}).controller('calendarController', function($cookieStore,$window,$scope, rest, $log, $sce, $filter, $mdToast, uiCalendarConfig, $timeout) {
    //'use strict';
    function dateFormatChange(da) {
        return currentdateT = pad(da.getDate() - 1, 2) + '.' + pad((da.getMonth() + 1), 2) + '.' + da.getFullYear();
    }
    
    $scope.eventSource = [];
    rest.path = "knowledgeEventGetAll";
    rest.get().success(function(data) {

        angular.forEach(data, function(val, i) {
            $scope.eventSource.push({
                event_id: val.event_id,
                title: val.event_title,
                start: toDate(val.event_startdate),
                end: toDate(val.event_enddate),
                allDay: false
            });
        });
        /*$scope.CalendarRen($scope.eventSource);*/
    });
    $scope.countryHolidayGet = function() {
        //var uid = $window.sessionStorage.getItem("session_iUserId");
    	var uid = $cookieStore.get('session_iUserId');
        //National Holiay List current date to higher date get
        var currentYear = new Date().getFullYear();
        rest.path = "holidayGet/" + uid;
        rest.get().success(function(data) {
            angular.forEach(data, function(val, i) {
                $scope.eventSource.push({
                	title: val.holidayName,
                    start: toDate(val.holidayDate + ' 00:00:00'),
                    end: toDate(val.holidayDate + ' 23:59:59'),
                    backgroundColor:'#ffffff',
                    borderColor:'#bdbdbd',
                    allDay: false
                });
            });

            $scope.CalendarRen($scope.eventSource);
            
        });
    }
    $scope.countryHolidayGet();

    /* event source that calls a function on every view switch */
    $scope.CalendarRen = function(data) {
    	angular.element('#calendar').fullCalendar({
            header: {
                left: 'prev',
                center: 'title',
                right: 'today next'
            },
            height: 500,
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            eventMouseover: function(data, event, view) {
                if(data.event_id){
            		if (!data.end){
	                    var endDate = dateFormat(data.start._d);
	                }
	                else{
	                    var endDate = dateFormat(data.end._d);
	                }
					tooltip = '<div class="tooltiptopicevent fc-overlay left top"><div class="panel bg-white b-a pos-rlt">' +
	                    // '<span class="arrow pull-down left"></span>'+
	                    '<div class="h4 font-thin m-b-sm ng-binding">' + data.title + '</div>' +
	                    '<div class="line b-b b-light"></div>' +
	                    '<div class="ng-binding"> Start Date &nbsp;: &nbsp;<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;' + dateFormat(data.start._d) + '</div>' +
	                    '<div class="ng-binding"> End Date &nbsp;&nbsp;&nbsp;: &nbsp;<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;' + endDate + '</div>' +
	                    // '<div class="ng-binding ng-hide" ng-show="event.end" aria-hidden="true"><i class="icon-clock text-muted m-r-xs"></i> </div>'+
	                    // '<div class="ng-binding" ng-show="event.location" aria-hidden="false"><i class="icon-pointer text-muted m-r-xs"></i> Tokyo</div>'+
	                    // '<div class="m-t-sm ng-binding">Tokyo Game Racing</div>'+
	                    '<div class="m-t-sm ng-binding"></div>' +
	                    '</div></div>'
	                    // tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:#fff;position:absolute;z-index:10001;padding:10px 10px 10px 10px ;  line-height: 200%; box-shadow: 2px -3px 24px #CACACA;">' + 'Title: ' + ': ' + data.title +'</br> Start Date : ' + dateFormat(data.start._d) +'</br> End Date : ' + dateFormatChange(data.end._d) +'</div>';
	                $("body").append(tooltip);
            	}else{
            		tooltip = '<div class="tooltiptopicevent fc-overlay left top"><div class="panel bg-white b-a pos-rlt">' +
	                    // '<span class="arrow pull-down left"></span>'+
	                    '<div class="h4 font-thin m-b-sm ng-binding">' + data.title + '</div>' +
	                    '<div class="line b-b b-light"></div>' +
	                    '<div class="ng-binding"><b>Holiday</b>' 
	                    '</div></div>'
	                $("body").append(tooltip);
            	}
                $(this).mouseover(function(e) {
                    $(this).css('z-index', 10000);
                    $('.tooltiptopicevent').fadeIn('500');
                    $('.tooltiptopicevent').fadeTo('10', 1.9);
                }).mousemove(function(e) {
                    angular.element('.tooltiptopicevent').css('top', e.pageY + 10);
                    angular.element('.tooltiptopicevent').css('left', e.pageX + 20);
                });
            },
            eventMouseout: function(data, event, view) {
                $(this).css('z-index', 8);
                $('.tooltiptopicevent').remove();
            },
            dayClick: function() {
                tooltip.hide()
            },
            eventResizeStart: function() {
                tooltip.hide()
            },
            eventDragStart: function() {
                tooltip.hide()
            },
            viewDisplay: function() {
                tooltip.hide()
            },
            events: data
        });
    }

}).controller('reportImageController', function($window,$scope, items, rest, $log, $modalInstance, fileReader, $cookieStore, $route, $routeParams) {
	
	$scope.ReportImageSave = function(reason) {
        if((reason.trim().length==0)){
            alert('You can not add space only');
        }else{
            $scope.saveReport = {};
            $scope.saveReport.uploadedUserName = items.uploadedUserName;
            $scope.saveReport.reportedUserName = $window.localStorage.getItem("session_vUserName");
            $scope.saveReport.reportedUserId = $window.localStorage.getItem("session_iUserId");
            $scope.saveReport.reported_image = items.reportedImage;
            $scope.saveReport.reason = reason;
            console.log("$scope.saveReport", $scope.saveReport);
            rest.path = "reportimagesave";
            rest.post($scope.saveReport).success(function(data) {
                $modalInstance.dismiss('cancel');
                $route.reload();
            });
        }
		
	}	
	$scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
});