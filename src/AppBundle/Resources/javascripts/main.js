modulejs.define('dashboard', function() {
    return {
        start: function() {
            this.initTabs();
        },
        initTabs: function() {
            $('.nav-tabs a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        }
    };
});

modulejs.define('profile', function() {
    return {
        fields: {
            $inputAccount: $('input#profile_account'),
            $errorAccount: $('#profile_account_error'),
            $inputAvatarFile: $('input#avatar_file'),
            $avatarImage: $('img#user_avatar'),
            $avatarStub: $('.avatar-empty'),
            $changeAvatarBtn: $('.js-change-avatar')
        },
        start: function() {
            this.initAccountInput();
            this.initAvatarUploader();
            this.initClipboard();
        },
        initAccountInput: function() {
            var module = this;
            this.fields.$inputAccount.on('focusout', function(e) {
                var number = $(this).val();
                $.ajax('/api/account', {
                    method: 'POST',
                    data: {'number': number},
                    success: function() {
                        module.fields.$errorAccount.empty();
                    },
                    error: function(xhr) {
                        var list = $('<ul><li>');
                        $(list).find('li').append(xhr.responseJSON.error);
                        module.fields.$errorAccount.html(list);
                    }
                });
            });
        },
        initAvatarUploader: function() {
            var module = this;
            this.fields.$inputAvatarFile.on('change', function() {
                var formData = new FormData();
                var file = this.files[0];
                formData.append('avatar', file, file.name);
                $.ajax({
                    url: '/api/avatar',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    context: module,
                    success: function(data) {
                        this.changeAvatarImage(data.path);
                    },
                    error: function(xhr) {
                        //TODO: Высвечивать ошибку пользователю о том что не получилось загрузить фоточку
                    }
                });
            });
            this.fields.$changeAvatarBtn.on('click', function(e) {
                e.preventDefault();
                module.fields.$inputAvatarFile.click();
            });
        },
        changeAvatarImage: function(path) {
            if(!this.fields.$avatarStub.hasClass('hidden')) this.fields.$avatarStub.addClass('hidden');
            if(this.fields.$avatarImage.hasClass('hidden')) this.fields.$avatarImage.removeClass('hidden');
            this.fields.$avatarImage.attr('src', path);
        },
        initClipboard: function() {
            var clipboard = new Clipboard('.js-ref-link');
        }
    };
});

modulejs.define('notifyPopover', function() {
    return {
        start: function (options) {
            var $counter = $(options.counter);
            var $popover = $(options.button);
            $popover.popover({html: true, placement: 'bottom'});
            $popover.on('inserted.bs.popover', function(){
                $('.popover-content li').on('click', function(){
                    $.ajax({
                        url: '/api/notification/' + $(this).data('target'),
                        method: 'GET',
                        dataType: 'json',
                        context: this,
                        success: function() {
                            $(this).addClass('viewed');
                            var unread = parseInt($counter.text()) - 1;
                            unread == 0 ? $counter.remove() : $counter.text(unread);
                            $(this).off('click');
                        }
                    });
                });
            });
        }
    };
});

modulejs.define('privacyModal', function() {
    return {
        start: function() {
            var $modal = $('#modal-privacy-policy');
            $('.js-privacy-policy').on('click', function(e) {
                e.preventDefault();
                $modal.modal('show');
            });
        }
    };
});
