var $w4finish = $('#w4').find('ul.pager li.finish'),
        $w4validator = $("#w4 form").validate({
        highlight: function(element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).remove();
        },
        errorPlacement: function( error, element ) {
            element.parent().append( error );
        }
    });

    $w4finish.on('click', function( ev ) {
        ev.preventDefault();
        var validated = $('#w4 form').valid();
        if ( validated ) {
            new PNotify({
                title: 'Congratulations',
                text: 'You completed the wizard form.',
                type: 'custom',
                addclass: 'notification-success',
                icon: 'fa fa-check'
            });
        }
    });

    $('#w4').bootstrapWizard({
        tabClass: 'wizard-steps',
        nextSelector: 'ul.pager li.next',
        previousSelector: 'ul.pager li.previous',
        firstSelector: null,
        lastSelector: null,
        onNext: function( tab, navigation, index, newindex ) {
            var validated = $('#w4-tab'+index+' form').valid();
            if( !validated ) 
            {
                $w4validator.focusInvalid();
                return false;
            }
            else
            {

            	var $scope = angular.element(document.getElementById("userprofile-ctrl")).scope();
                $scope.$apply(function(){
                    $scope.saveUserProfile();
		        });
                return false;
            }
        },
        onTabClick: function( tab, navigation, index, newindex ) {

            if ( newindex == index + 1 ) {
                return this.onNext( tab, navigation, index, newindex);
            } else if ( newindex > index + 1 ) {
                return false;
            } else {
                return true;
            }
        },
        onTabChange: function( tab, navigation, index, newindex ) {
            var $total = navigation.find('li').size() - 1;
            $w4finish[ newindex != $total ? 'addClass' : 'removeClass' ]( 'hidden' );
            $('#w4').find(this.nextSelector)[ newindex == $total ? 'addClass' : 'removeClass' ]( 'hidden' );
        },
        onTabShow: function( tab, navigation, index ) {
            var $total = navigation.find('li').length - 1;
            var $current = index;
            var $percent = Math.floor(( $current / $total ) * 100);
            $('#w4').find('.progress-indicator').css({ 'width': $percent + '%' });
            tab.prevAll().addClass('completed');
            tab.nextAll().removeClass('completed');
        }
    });