        (function () {

        	var tbody = $('.result table tbody');
        	var loading = $('.loading');

            var inputElm = document.querySelector('input[name="q"]');

            var settings = {
                whitelist: [1111, 222, 333, 444],
                pattern: /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi
            }

            var tagify = new Tagify(inputElm, settings);

            $('#finder-songs').on('submit', function (e) {

                e.preventDefault();
                var $q = $(this).find('input').val();

	            getData.call($q);

            });
            // Service
            var getData = {
            	active: false,
            	call: function(data) {
            		
            		var self = this;
            		var render = '';

                    if (this.active != false) {
                        this.active.abort();
                    }
                    loading.fadeIn();
		            self.active = $.post('/', { form_data : data } ,function(res){
	                $.each(res, function(i, item){
		                	render += '<tr>';
		                	render += '<td>'+item.type+'</td>';
		                	render += '<td>'+item.host+'</td>';
		                	render += '<td>'+item.ip+'</td>';
		                	render += '<td>'+item.ttl+' sec</td>';
		                	render += '</tr>';
		                });
		                loading.fadeOut('fast', function(){
		                	tbody.prepend(render).find('tr:first-child').hide().show('slow');
		                	self.active = false;
		                });
		            }, 'json');

            	}
            }

        })();

        (function ($, wp) {
            return;
            // View
            var template = wp.template('my-template');

            var $tmpl = $('#tmpl-component-1');

            $tmpl.html(template({
                status: "Loading...",
                msg: "Default message",
                data: []
            }));

            // Service
            var getData = {
                active: false,
                call: function (data) {
                    if (this.active != false) {
                        this.active.abort();
                    }
                    var self = this;
                    self.active = wp.ajax.send('exposed_ajax_action_get', {
                        url: wp_headless_cms.admin_ajax,
                        type: 'get',
                        data: data,
                        success: function (res) {
                            console.log('success', res);
                            $tmpl.html(template({
                                status: res
                            }));
                        },
                        error: function (res) {
                            var err = (res.statusText) ? res.statusText : res;
                            console.log('error', err);
                            $tmpl.html(template({
                                status: err
                            }));
                        },
                        complete: function () {
                            self.active = false;
                        }
                    }).done(function (res) {
                        console.log('done', res)
                    }).fail(function (res) {
                        var err = (res.statusText) ? res.statusText : res;
                        console.log('fail', err)
                    });
                }
            }

            // Listener
            $("#get-data").on('click', function (e) {
                e.preventDefault();
                getData.call({
                    inputs: {
                        name: 'BrianBrain',
                        age: 30,
                        status: 'Working...',
                        activate_err: $("#activate-err").is(":checked")
                    }
                });
            });

            // utility

            // str.isBlank();
            String.prototype.isBlank = function () {
                return (this.length === 0 || !this.trim());
            };

            // Service 
            var FinderSongs = {
                active: false,
                call: function (settings, cb_done, cb_fail) {
                    // console.log('FinderSongs called', this);
                    var self = this;
                    if (self.active != false) {
                        self.active.abort();
                    }
                    self.active = $.ajax({
                        url: settings && settings.url || "/",
                        data: settings && settings.form_data || [],
                        type: settings && settings.type || "GET",
                        // done didn't work
                        success: function (res) {
                            if (cb_done) cb_done(res);
                        },
                        // fail didn't work
                        error: function (res) {
                            if (cb_done) cb_fail(res);
                        },
                        // always didn't work
                        complete: function () {
                            self.active = false;
                        },
                    });
                },
            };

            // View
            var template_item = wp.template('suggestions-item');

            // var $target_2 = $('#tmpl-component-2');

            function add_suggestion() {
                console.log(this);
            }

            // Listener
            $('#finder-songs').on('submit', function (e) {
                e.preventDefault();
                var $q = $(this).find('input').val();
                if ($q.isBlank() == false) {
                    // FinderSongs.call(null, null, null);
                    FinderSongs.call({
                        url: wp_headless_cms.admin_ajax,
                        type: "POST",
                        form_data: {
                            action: 'finder_songs',
                            wp_nonce_add_projects: wp_headless_cms.wp_nonce_add_projects,
                            inputs: {
                                q: $q,
                                name: "BrianBrain",
                                age: 30,
                                status: "Working...",
                                activate_err: $("#activate-err").is(":checked"),
                            },
                        },
                    },
                        function (res) {
                            // bind events to cta
                            // console.log('succ', res)
                            // $tmpl.html(template({
                            //     msg: res.data
                            // }));
                            var _list = '';
                            $.each(res.data, function (key, item) {
                                // console.log(key, item)
                                _list += '<li><a href="#' + item.id + '" data-option="key-' + item.id + '">' + item.name + '</li>';
                            });
                            $suggestions.html(_list);
                        },
                        function (err) {
                            // console.log('err', err)
                            $tmpl.html(template({
                                msg: err.data
                            }));
                        }
                    );
                }
            });

            var $input = $('#finder-songs').find('input');
            var $suggestions = $('#suggestions');

            $suggestions.on('click', '[data-option]', function (e) {
                e.preventDefault();
                // console.log($(this).data('option'))
                $input.val($(this).data('option'));
            });

            $("#form-reset").on('click', function () {
                // console.log('close')
                $input.val('');
                $suggestions.slideUp('fast');
            });

            var closeSuggestions = function () {
                console.log('is suggestions open');
                if ($suggestions.is(":hidden")) return;
                $suggestions.slideUp('fast', function () {
                    // console.log('slideUp fast')
                });
            };

            // $(document).on('click', closeSuggestions);

            $input
                .focusout(closeSuggestions)
                .blur(function () {
                    if ($suggestions.is(":visible")) return;
                    if ($(this).val().isBlank() == false) {
                        $suggestions.slideDown('fast', function () {
                            // console.log('slideDown fast')
                        });
                    }
                });

            function close(el) {
                console.log('close', el)
                el.parentNode.style.right = "-100%";
            }

            // subscribe
            $('#subscribe-cta').on('click', function () {
                var $input_email = $('#subscribe input[name=email]');
                $input_email.removeClass('error');
                var $btn = $(this);
                FinderSongs.call({
                    url: wp_headless_cms.admin_ajax,
                    type: "POST",
                    form_data: {
                        action: 'subscribe_email_songs',
                        wp_nonce_add_projects: wp_headless_cms.wp_nonce_add_projects,
                        input: {
                            email: $input_email.val(),
                        },
                    },
                },
                    function (res) {
                        if (res.success) {
                            $input_email.val(res.data);
                        } else {
                            $input_email.addClass('error');
                            $('<p class="error-mgs">' + res.data + ' <button onclick="close(this)">[close]</button></p>').insertAfter($btn);
                        }
                    },
                    function (err) {
                        $input_email.addClass('error');
                    }
                );
            });

            // })(jQuery, wp);
        })();