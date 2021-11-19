app.filter('to_trusted', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]).filter('dateFormat', function ($window) {
    return function (input) {
        return moment(input).format($window.localStorage.getItem('global_dateFormat'));
        /*var d = new Date(input);
        return (d.getDate().toString().length > 1 ? d.getDate() : '0' + d.getDate()) + "."
                + ((d.getMonth() + 1).toString().length > 1 ? (d.getMonth() + 1) : '0' + (d.getMonth() + 1)) + "."
                + d.getFullYear() + " ";*/
        // + d.getHours() + ":"  
        // + d.getMinutes() + ":" 
        // + d.getSeconds();
    }
}).filter('cmdate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);
app.filter('globalDtFormat', function($window) {
    return function(input) {
       return moment(input).format($window.localStorage.getItem('global_dateFormat')+' '+'HH:mm A');
    }
});