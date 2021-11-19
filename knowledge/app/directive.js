app.directive('autoComplete', function ($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
        	obj = [];
            rest.path = 'searchResult';
            rest.get().success(function (data) {
                angular.forEach(data, function(val, i) {
                        obj.push({value : val.title, label : val.title, id: val.article_id});
                });
            });
            element.autocomplete({
                source : obj,
                minLength: 3,
                select: function (event, ui) {
                    angular.element('#autoCompleteID').val(ui.item.id);
                    angular.element('.ui-corner-all').removeAttr('href');
                }
            });
        }
    }
}).directive('autoCompleteMember', function ($http, rest, $timeout, $log) {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
        	obj = [];
            rest.path = 'searchMemberResult';
            rest.get().success(function (data) {
                angular.forEach(data, function(val, i) {
                        obj.push({value : val.vUserName, label : val.vUserName, id: val.iUserId});
                });
            });

            element.autocomplete({
                source : obj,
                select: function (event, ui) {
                    angular.element('#autoCompleteID').val(ui.item.id);
                    angular.element('.ui-corner-all').removeAttr('href');
                }
            });
        }
    }
});
app.directive('calendar', function($http, rest, $timeout, $log){
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            
        }
    }
});
app.directive('fileModel', ['$parse', function ($parse) {
    return {
       restrict: 'A',
       link: function(scope, element, attrs) {
          var model = $parse(attrs.fileModel);
          var modelSetter = model.assign;

          element.bind('change', function(){
             scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);
             });
          });
       }
    };
 }]);
 
//file reade
 app.directive("ngFileSelect", function() {
return {
    link: function($scope, el) {
        el.on('click',function(){
            this.value = '';
        });
        el.bind("change", function(e) {
            $scope.file = '';
            $scope.file = (e.srcElement || e.target).files[0]; 
            if($scope.file){
                if($scope.file.size < 6015220) {
                    $scope.getFile(el.attr('destination'), $scope.file);
                } else {
                    alert('File too large. File must be less than 6 MB.');
                    document.getElementById('file-input').value = null;
                }    
            }
        })
    }
}})
app.directive('ngClockGmt', function() {
  return {
    restrict: 'EA',
    replace: true,
    scope: {
      radius: '@',
      zone: '@?',
      lightFill: '@?',
      darkFill: '@?'
    },
    template: '<div class=\'bloc-clock\' ng-style=\'divStyle()\'>\n  <svg xmlns="http://www.w3.org/2000/svg"\n       width="25%"\n       height="100%"\n       viewBox="0 0 200 200">\n    <g class=\'face\' style=\'stroke: black; stroke-width: 5px;\'>\n      <g>\n        <circle style="stroke: white; fill: #f2f2f2; stroke-width: 1px;"\n                cx="100"\n                cy="100"\n                r="100"/>\n        <line x1="100"\n              x2="100"\n              y1="10"\n              y2="0"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="187"\n              x2="178"\n              y1="50"\n              y2="55"\n              />\n        <line x1="190"\n              x2="200"\n              y1="100"\n              y2="100"\n              />\n        <line x1="187"\n              x2="178"\n              y1="150"\n              y2="145"\n              />\n        <line x1="150"\n              x2="145"\n              y1="187"\n              y2="178"\n              />\n        <line x1="100"\n              x2="100"\n              y1="190"\n              y2="200"\n              />\n        <line x1="50"\n              x2="55"\n              y1="187"\n              y2="178"\n              />\n        <line x1="13"\n              x2="22"\n              y1="150"\n              y2="145"\n              />\n        <line x1="0"\n              x2="10"\n              y1="100"\n              y2="100"\n              />\n        <line x1="13"\n              x2="22"\n              y1="50"\n              y2="55"\n              />\n        <line x1="50"\n              x2="55"\n              y1="13"\n              y2="22"\n              />\n      </g>\n      <g>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="45"\n                style="stroke-width: 12px;"\n                class="hourhand"/>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="15"\n                style="stroke-width: 8px;"\n                class="minutehand" />\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="5"\n                style="stroke-width: 4px; stroke: red;"\n                class="secondhand"/>\n      </g>\n    </g>\n  </svg>\n</div>',
    link: function(scope, el, attrs) {
      var drawClock, drawHands, getRad, getX, getY;
      if (scope.lightFill == null) {
        scope.lightFill = '#f2f2f2';
      }
      if (scope.darkFill == null) {
        scope.darkFill = '#008dd2';
      }
      scope.divStyle = function() {
        return {
          width: scope.w(),
          height: scope.w()
        };
      };
      scope.w = function() {
        return scope.radius * 2;
      };
      getX = function(degrees, r, adjust, x) {
        var adj;
        x = x || r;
        adj = adjust || 1;
        return x + r * adj * Math.cos(getRad(degrees));
      };
      getY = function(degrees, r, adjust, y) {
        var adj;
        y = y || r;
        adj = adjust || 1;
        return y + r * adj * Math.sin(getRad(degrees));
      };
      getRad = function(degrees) {
        var adjust;
        adjust = Math.PI / 2;
        return (degrees * Math.PI / 180) - adjust;
      };
      drawClock = function() {
        return drawHands();
      };
      drawHands = function() {
        var $circle, H_HAND_SIZE, M_HAND_SIZE, S_HAND_SIZE, dark, drawHand, fillColor, hour, hour24, r, strokeColor, t;
        S_HAND_SIZE = 0.95;
        M_HAND_SIZE = 0.85;
        H_HAND_SIZE = 0.55;
        t = scope.zone ? moment.tz(new Date(), scope.zone) : moment();
        r = 100;
        $circle = el.find('circle');
        hour24 = Number(t.format('H'));
        dark = hour24 >= 18 || hour24 < 6;
        fillColor = dark ? scope.darkFill : scope.lightFill;
        strokeColor = dark ? 'white' : 'black';
        $circle.css('fill', fillColor);
        el.find('line').not('.secondhand').css('stroke', strokeColor);
        drawHand = function(hand, value, size, degrees) {
          var deg, x2, y2;
          deg = degrees * value;
          x2 = getX(deg, r, size, r);
          y2 = getY(deg, r, size, r);
          hand.attr("x1", r);
          hand.attr("y1", r);
          hand.attr("x2", x2);
          return hand.attr("y2", y2);
        };
        hour = t.hour() + t.minute() / 60;
        drawHand($(el).find(".secondhand"), t.second(), S_HAND_SIZE, 6);
        drawHand($(el).find(".minutehand"), t.minute(), M_HAND_SIZE, 6);
        return drawHand($(el).find(".hourhand"), hour, H_HAND_SIZE, 30);
      };
      drawClock();
      return setInterval(drawHands, 1000);
    }
  };
});
app.directive('ngClockPacificTime', function() {
  return {
    restrict: 'EA',
    replace: true,
    scope: {
      radius: '@',
      zone: '@?',
      lightFill: '@?',
      darkFill: '@?'
    },
    template: '<div class=\'bloc-clock\' ng-style=\'divStyle()\'>\n  <svg xmlns="http://www.w3.org/2000/svg"\n       width="75%"\n       height="100%"\n       viewBox="0 0 200 200">\n    <g class=\'face\' style=\'stroke: black; stroke-width: 5px;\'>\n      <g>\n        <circle style="stroke: white; fill: #f2f2f2; stroke-width: 1px;"\n                cx="100"\n                cy="100"\n                r="100"/>\n        <line x1="100"\n              x2="100"\n              y1="10"\n              y2="0"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="150"\n              x2="145"\n              y1="13"\n              y2="22"\n              />\n        <line x1="187"\n              x2="178"\n              y1="50"\n              y2="55"\n              />\n        <line x1="190"\n              x2="200"\n              y1="100"\n              y2="100"\n              />\n        <line x1="187"\n              x2="178"\n              y1="150"\n              y2="145"\n              />\n        <line x1="150"\n              x2="145"\n              y1="187"\n              y2="178"\n              />\n        <line x1="100"\n              x2="100"\n              y1="190"\n              y2="200"\n              />\n        <line x1="50"\n              x2="55"\n              y1="187"\n              y2="178"\n              />\n        <line x1="13"\n              x2="22"\n              y1="150"\n              y2="145"\n              />\n        <line x1="0"\n              x2="10"\n              y1="100"\n              y2="100"\n              />\n        <line x1="13"\n              x2="22"\n              y1="50"\n              y2="55"\n              />\n        <line x1="50"\n              x2="55"\n              y1="13"\n              y2="22"\n              />\n      </g>\n      <g>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="45"\n                style="stroke-width: 12px;"\n                class="hourhand"/>\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="15"\n                style="stroke-width: 8px;"\n                class="minutehand" />\n          <line x1="100"\n                y1="100"\n                x2="100"\n                y2="5"\n                style="stroke-width: 4px; stroke: red;"\n                class="secondhand"/>\n      </g>\n    </g>\n  </svg>\n</div>',
    link: function(scope, el, attrs) {
      var drawClock, drawHands, getRad, getX, getY;
      if (scope.lightFill == null) {
        scope.lightFill = '#008dd2';
      }
      if (scope.darkFill == null) {
        scope.darkFill = '#008dd2';
      }
      scope.divStyle = function() {
        return {
          width: scope.w(),
          height: scope.w()
        };
      };
      scope.w = function() {
        return scope.radius * 2;
      };
      getX = function(degrees, r, adjust, x) {
        var adj;
        x = x || r;
        adj = adjust || 1;
        return x + r * adj * Math.cos(getRad(degrees));
      };
      getY = function(degrees, r, adjust, y) {
        var adj;
        y = y || r;
        adj = adjust || 1;
        return y + r * adj * Math.sin(getRad(degrees));
      };
      getRad = function(degrees) {
        var adjust;
        adjust = Math.PI / 2;
        return (degrees * Math.PI / 180) - adjust;
      };
      drawClock = function() {
        return drawHands();
      };
      drawHands = function() {
        var $circle, H_HAND_SIZE, M_HAND_SIZE, S_HAND_SIZE, dark, drawHand, fillColor, hour, hour24, r, strokeColor, t;
        S_HAND_SIZE = 0.95;
        M_HAND_SIZE = 0.85;
        H_HAND_SIZE = 0.55;
        t = scope.zone ? moment.tz(new Date(), scope.zone) : moment();
        r = 100;
        $circle = el.find('circle');
        hour24 = Number(t.format('H'));
        dark = hour24 >= 18 || hour24 < 6;
        fillColor = '#008dd2';
        strokeColor = 'white';
        $circle.css('fill', fillColor);
        el.find('line').not('.secondhand').css('stroke', strokeColor);
        drawHand = function(hand, value, size, degrees) {
          var deg, x2, y2;
          deg = degrees * value;
          x2 = getX(deg, r, size, r);
          y2 = getY(deg, r, size, r);
          hand.attr("x1", r);
          hand.attr("y1", r);
          hand.attr("x2", x2);
          return hand.attr("y2", y2);
        };
        hour = t.hour() + t.minute() / 60;
        drawHand($(el).find(".secondhand"), t.second(), S_HAND_SIZE, 6);
        drawHand($(el).find(".minutehand"), t.minute(), M_HAND_SIZE, 6);
        return drawHand($(el).find(".hourhand"), hour, H_HAND_SIZE, 30);
      };
      drawClock();
      return setInterval(drawHands, 1000);
    }
  };
});
