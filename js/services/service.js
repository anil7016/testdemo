'use strict';
app.factory('authInterceptor', function ($q, $window, $location, $cookieStore, $rootScope,$timeout) {
    return {
        request: function (config) {
            $rootScope.adminRole = $cookieStore.get('session_iFkUserTypeId');
            if ($cookieStore.get('auth')) {
                config.headers['Authorization'] = $cookieStore.get('auth');
            }
            return config;
        },
        responseError: function (rejection) {
            (rejection);
            if (rejection.status === 401) {
                $window.sessionStorage.clear();
                $window.localStorage.clear();
                angular.forEach(["session_iUserId", "auth", "session_iFkUserTypeId", "session_vEmailAddress", "session_password"], function (key) {
                    $cookieStore.remove(key);
                });
                $timeout(function () {
                    $location.path('/');
                }, 1000);
            }
            return $q.reject(rejection);
        }
    };
});

// Need set url REST Api in controller!
app.service('rest', function ($http, $location,$routeParams) {
    return {
        baseUrl: 'api/v1/',
        path: undefined,
        // id:null,
        get: function () {
            return $http.get(this.baseUrl + this.path);
        },
        model: function () {
            return $http.get(this.baseUrl + this.path + "/" + $routeParams.id);
        },
        post: function (model) {
            return $http.post(this.baseUrl + this.path, model);
        },
        put: function (model) {
            return $http.put(this.baseUrl + this.path + "/" + $routeParams.id, model);
        },
        delete: function () {
            return $http.delete(this.baseUrl + this.path);
        }
    };
});
//file reader
app.factory('fileReader', function ($q, $log) {
    var onLoad = function (reader, deferred, scope) {
        return function () {
            scope.$apply(function () {
                deferred.resolve(reader.result);
            });
        };
    };

    var onError = function (reader, deferred, scope) {
        return function () {
            scope.$apply(function () {
                deferred.reject(reader.result);
            });
        };
    };

    var onProgress = function (reader, scope) {
        return function (event) {
            scope.$broadcast("fileProgress", {
                total: event.total,
                loaded: event.loaded
            });
        };
    };

    var getReader = function (deferred, scope) {
        var reader = new FileReader();
        reader.onload = onLoad(reader, deferred, scope);
        reader.onerror = onError(reader, deferred, scope);
        reader.onprogress = onProgress(reader, scope);
        return reader;
    };

    var readAsDataURL = function (file, scope) {
        var deferred = $q.defer();
        var reader = getReader(deferred, scope);
        reader.readAsDataURL(file);
        return deferred.promise;
    };

    return {
        readAsDataUrl: readAsDataURL
    };
});

//page scroll
app.service('anchorSmoothScroll', function() {

    this.scrollTo = function(eID) {

        // This scrolling function 
        // is from http://www.itnewb.com/tutorial/Creating-the-Smooth-Scroll-Effect-with-JavaScript

        var startY = currentYPosition();
        var stopY = elmYPosition(eID);
        var distance = stopY > startY ? stopY - startY : startY - stopY;
        if (distance < 100) {
            scrollTo(0, stopY);
            return;
        }
        var speed = Math.round(distance / 100);
        if (speed >= 20) speed = 20;
        var step = Math.round(distance / 25);
        var leapY = stopY > startY ? startY + step : startY - step;
        var timer = 0;
        if (stopY > startY) {
            for (var i = startY; i < stopY; i += step) {
                setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
                leapY += step;
                if (leapY > stopY) leapY = stopY;
                timer++;
            }
            return;
        }
        for (var i = startY; i > stopY; i -= step) {
            setTimeout("window.scrollTo(0, " + leapY + ")", timer * speed);
            leapY -= step;
            if (leapY < stopY) leapY = stopY;
            timer++;
        }

        function currentYPosition() {
            // Firefox, Chrome, Opera, Safari
            if (self.pageYOffset) return self.pageYOffset;
            // Internet Explorer 6 - standards mode
            if (document.documentElement && document.documentElement.scrollTop)
                return document.documentElement.scrollTop;
            // Internet Explorer 6, 7 and 8
            if (document.body.scrollTop) return document.body.scrollTop;
            return 0;
        }

        function elmYPosition(eID) {
            var elm = document.getElementById(eID);
            var y = elm.offsetTop;
            var node = elm;
            while (node.offsetParent && node.offsetParent != document.body) {
                node = node.offsetParent;
                y += node.offsetTop;
            }
            return y;
        }

    };

});
app.factory('allLanguages', function($http, $location, $routeParams,rest) {
    var allLanguages = [];
    rest.path = 'allLanguages';
    rest.get().success(function(data) {
        
        angular.forEach(data, function(val, i) {
            var langName = JSON.stringify('<span style="color:green;">'+val.name+'</span>');
            allLanguages.push({
                'id': val.id,
                //'title': val.name,
                'title': val.title,
                'name': val.title,
                'flagImg': 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/'+val.flagImg,
                'flagTitle': val.flagTitle,
                'is_favourite': val.is_favourite,
            });
        });
        console.log("allLanguages", allLanguages);
    
    }).error(function(data, error, status) {});

/*    var allLanguages = [
        { id: 'ca_ES', title: 'Catalan', name: ' Català', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/catalonia.png', flagTitle: 'Catalonia' },
        { id: 'cz_CZ', title: 'Czech', name: ' Čeština', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/cz.png', flagTitle: 'Czech Republic' },
        { id: 'dk_DK', title: 'Danish', name: ' Dansk', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/dk.png', flagTitle: 'Denmark' },
        { id: 'de_DE', title: 'German', name: ' Deutsch', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/de.png', flagTitle: 'Germany' },
        { id: 'ee_EE', title: 'Estonian', name: ' Eesti', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/ee.png', flagTitle: 'Estonia' },
        { id: 'en_UK', title: 'English (UK)', name: ' English (UK)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/gb.png', flagTitle: 'United Kingdom' },
        { id: 'en_US', title: 'English (US)', name: ' English (US)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/us.png', flagTitle: 'United States' },
        { id: 'es_ES', title: 'Spanish', name: ' Español', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/es.png', flagTitle: 'Spain' },
        { id: 'fr_CA', title: 'French (Canada)', name: ' Français (Canada)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/ca.png', flagTitle: 'Canada' },
        { id: 'fr_FR', title: 'French (France)', name: ' Français (France)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/fr.png', flagTitle: 'France' },
        { id: 'hr_HR', title: 'Croatian', name: ' Hrvatski', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/hr.png', flagTitle: 'Croatia' },
        { id: 'is_IS', title: 'Icelandic', name: ' ‏Íslenska', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/is.png', flagTitle: 'Iceland' },
        { id: 'it_IT', title: 'Italian', name: ' Italiano', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/it.png', flagTitle: 'Italy' },
        { id: 'lv_LV', title: 'Latvian', name: ' Latviešu', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/lv.png', flagTitle: 'Latvia' },
        { id: 'lt_LT', title: 'Lithuanian', name: ' Lietuvių', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/lt.png', flagTitle: 'Lithuania' },
        { id: 'hu_HU', title: 'Hungarian', name: ' Magyar', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/hu.png', flagTitle: 'Hungary' },
        { id: 'nl_NL', title: 'Dutch', name: ' Nederlands', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/nl.png', flagTitle: 'Netherlands' },
        { id: 'nl_BE', title: 'Dutch (Belgium)', name: ' Nederlands (België)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/be.png', flagTitle: 'Belgium' },
        { id: 'no_NO', title: 'Norwegian (bokmal)', name: ' Norsk (bokmål)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/no.png', flagTitle: 'Norway' },
        { id: 'ny_NO', title: 'Norwegian (nynorsk)', name: ' Norsk (nynorsk)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/no.png', flagTitle: 'Norway' },
        { id: 'pl_PL', title: 'Polish', name: ' Polski', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/pl.png', flagTitle: 'Poland' },
        { id: 'pt_BR', title: 'Portuguese (Brazil)', name: ' Português (Brasil)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/br.png', flagTitle: 'Brazil' },
        { id: 'pt_PT', title: 'Portuguese (Portugal)', name: ' Português (Portugal)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/pg.png', flagTitle: 'Portugal' },
        { id: 'ro_RO', title: 'Romanian', name: ' Română', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/ro.png', flagTitle: 'Romania' },
        { id: 'sk_SK', title: 'Slovak', name: '‏ Slovenčina', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/sk.png', flagTitle: 'Slovakia' },
        { id: 'si_SI', title: 'Slovenian', name: ' Slovenščina', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/si.png', flagTitle: 'Slovenia' },
        { id: 'fi_FI', title: 'Finnish', name: ' Suomi', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/fi.png', flagTitle: 'Finland' },
        { id: 'se_SE', title: 'Swedish', name: ' Svenska', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/se.png', flagTitle: 'Sweden' },
        { id: 'tr_TR', title: 'Turkish', name: ' Türkçe', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/tr.png', flagTitle: 'Turkey' },
        { id: 'gr_GR', title: 'Greek', name: ' Ελληνικά', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/gr.png', flagTitle: 'Greece' },
        { id: 'bg_BG', title: 'Bulgarian', name: ' Български', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/bg.png', flagTitle: 'Bulgaria' },
        { id: 'ru_RU', title: 'Russian', name: ' Русский', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/ru.png', flagTitle: 'Russia' },
        { id: 'ua_UA', title: 'Ukrainian', name: ' Українська', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/ua.png', flagTitle: 'Ukraine' },
        { id: 'hi_IN', title: 'Hindi', name: ' हिन्दी', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/in.png', flagTitle: 'India' },
        { id: 'kr_KR', title: 'Korean', name: ' 한국어', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/kr.png', flagTitle: 'South Korea' },
        { id: 'cn_TW', title: 'Traditional Chinese (Taiwan)', name: ' 中文(台灣)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/tw.png', flagTitle: 'Taiwan' },
        { id: 'cn_CN', title: 'Simplified Chinese (China)', name: ' 中文(简体)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/cn.png', flagTitle: 'China' },
        { id: 'cn_HK', title: 'Traditional Chinese (Hong Kong)', name: ' 中文(香港)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/hk.png', flagTitle: 'Hong Kong' },
        { id: 'jp_JP', title: 'Japanese', name: ' 日本語', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/jp.png', flagTitle: 'Japan' },
        { id: 'ka_JP', title: 'Japanese (Kansai)', name: ' 日本語(関西)', flagImg: 'assets/vendor/Polyglot-Language-Switcher-2-master/images/flags/jp.png', flagTitle: 'Japan' }
    ];*/
    //console.log('all lang', allLanguages);
    return allLanguages;
});