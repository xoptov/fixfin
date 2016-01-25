modulejs.define('dashboard', function() {
    return {
        start: function() {
            this.initTabs();
        },
        initTabs: function() {
            $('.nav-tabs a').on('click', function(e){
                e.preventDefault();
                $(this).tab('show');
            });
        }
    };
});

modulejs.define('profile', function() {
    return {
        start: function() {
            this.initAccountInput();
        },
        initAccountInput: function() {
            var $inputAccount = $('input#profile_account');
            var $errorAccount = $('#profile_account_error');
            $inputAccount.on('focusout', function(e){
                var ctx = this;
                var number = $(this).val();
                $.ajax('/api/accounts', {
                    context: ctx,
                    method: 'POST',
                    data: {'number': number},
                    success: function(data) {
                        $errorAccount.empty();
                    },
                    error: function(xhr) {
                        var list = $('<ul><li>');
                        $(list).find('li').append(xhr.responseJSON.error);
                        $errorAccount.html(list);
                    }
                });
            });
        }
    };
});
