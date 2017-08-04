


$(document).ready(function(){
    NProgress.configure({ showSpinner: true });
    NProgress.start();

    title_lock = 'disabled';

    var liveSearch = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/search/%QUERY.json',
            wildcard: '%QUERY'
        }
    });


    liveSearch.initialize();

    var liveSearch2 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/artists/%QUERY.json',
            wildcard: '%QUERY'
        }
    });

    liveSearch2.initialize();

    var liveSearch3 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/albums/%QUERY.json',
            wildcard: '%QUERY'
        }
    });

    liveSearch3.initialize();

    $('.typeahead6').typeahead(null, {
            name: 'liveSearch',
            displayKey: 'name',
            //header: '<h3 class="tt-tag-heading tt-tag-heading2">1</h3>',
            templates: {
                empty: [
                    '<div class="text-danger tt-error">',
                    'No Results',
                    '</div>'
                ].join('\n'),
                header: '<h4 class="tt-tag-heading">Tracks</h4>'
            },
            source: liveSearch.ttAdapter(),
        },
        {
            name: 'liveSearch3',
            displayKey: 'name',
            templates: {
                empty: [
                    '<div class="text-danger tt-error">',
                    'No Results',
                    '</div>'
                ].join('\n'),
                header: '<h4 class="tt-tag-heading">Albums</h4>'
            },
            source: liveSearch3.ttAdapter(),
        },
        {
            name: 'liveSearch2',
            displayKey: 'name',
            templates: {
                empty: [
                    '<div class="text-danger tt-error">',
                    'No Results',
                    '</div>'
                ].join('\n'),
                header: '<h4 class="tt-tag-heading">Artists</h4>'
            },
            source: liveSearch2.ttAdapter(),
        }
    ).on('typeahead:selected', function(ev, suggestion, data_set) {
            var api = '';
            var surl = '';
            if(data_set == 'liveSearch') {
                api = 'track';
                surl = rootURL+'/'+api+'/'+slugify(suggestion.name)+'-'+suggestion.slug;
            } else if(data_set == 'liveSearch2') {
                api = 'artist';
                surl = rootURL+'/'+api+'/'+suggestion.slug;
            } else if(data_set == 'liveSearch3') {
                api = 'album';
                surl = rootURL+'/'+api+'/'+slugify(suggestion.name)+'-'+suggestion.slug;
            }
            pjax.invoke({
                'url': surl,
                'container': 'wrapper',
                'beforeSend': function(e){ NProgress.start(); },
                'complete': function(e){ slider(); NProgress.done(); },
            });
        });

    $('.typeahead6').keypress(function (e) {
        if (e.which == 13) {
            var that = $(this);
            pjax.invoke({
                'url': rootURL+'/?q='+that.val().trim(),
                'container': 'wrapper',
                'beforeSend': function(e){ that.typeahead('close'); NProgress.start(); },
                'complete': function(e){ slider(); NProgress.done(); },
            });
        }
    });


    _token = $('meta[name="_token"]').attr('content');
    auth_download = $('meta[name="auth_download"]').attr('content');
    auth_download = parseInt(auth_download);
    is_login = $('meta[name="is_login"]').attr('content');
    is_login = parseInt(is_login);

    ias = jQuery.ias({
        container:  '.scroll-container',
        item:       '.scroll-items',
        pagination: '.pager',
        next:       'a[rel="next"]'
    });

    ias.on('load', function(event) {
        NProgress.start();
    });

    ias.on('loaded', function(items) {
        //alert($('a[rel="next"]').attr('href'));
        NProgress.done();
    });

    ias.on('rendered', function(){
        clipTransparentImage();
    });

    // Enter your ids or classes
    var toggler = '.navbar-toggle';
    var pagewrapper = '#page-wrapper';
    var menuwidth = '100%'; // the menu inside the slide menu itself
    var slidewidth = '70px';
    var menuneg = '-100%';
    var slideneg = '-70px';

    $(document).on("click", toggler, function (e) {
        var selected = $(this).hasClass('slide-active');
        menuDirection = $(this).attr('data-direction');
        var animeOpt = {};

        animeOpt[menuDirection] = selected ? menuneg : '0px';
        $($(this).attr('data-target')).stop().animate(animeOpt);

        animeOpt[menuDirection] = selected ? '0px' : slidewidth;
        $(pagewrapper).stop().animate(animeOpt);


        $(this).toggleClass('slide-active', !selected);
        $($(this).attr('data-target')).toggleClass('slide-active');

        $('#page-wrapper, .navbar, body, .navbar-header').toggleClass('slide-active');
        $($(this).attr('data-target')).css('height', $(window).height());
        $(pagewrapper).css({'left' : 'unset', 'right' : 'unset'});
    });

    var selected = '#slidemenu, #page-wrapper, body, .navbar';
    $(window).on("resize", function () {
        if ($('.navbar-toggle').is(':hidden')) {
            $(selected).removeClass('slide-active');
        }
    });

    $(".pjax").on("click", function(event){
        var that = $(this);
        pjax.invoke({
            'url': that.attr("href"),
            'container': 'wrapper',
            'beforeSend': function(e){
                NProgress.start();
            },
            'complete': function(e){
                //alert($('title').val());
                slider();
                ias.reinitialize();
                clipTransparentImage();
                typeaheadInit();
                NProgress.done();
            },
        });
        event.preventDefault();
    });

    $('.navbar-form').submit(function(e) {
        var q = $(this).find('.form-control').val();
        //q = slugify(q);
        pjax.invoke({
            'url': rootURL+'/?q='+q,
            'container': 'wrapper',
            'beforeSend': function(e){ NProgress.start(); },
            'complete': function(e){  NProgress.done(); },
        });
        e.preventDefault();
    });



    $(document).on('change', '#sl-audio', function(){
        var files = $(this)[0].files;
        $('#upload-mp3s').removeAttr('disabled');
        $(files).each(function(i, file){
            var fileName = file.name;
            fileName = fileName.replace(/\.[^/.]+$/, "");
            var clonedData = $('.track-info').children().clone();
            clonedData.find('.audio-title').val(fileName);
            clonedData.appendTo('#sel-tracks');
            var tagNames = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/artists/%QUERY.json',
                    wildcard: '%QUERY'
                }
            });
            tagNames.initialize();

            $('#sel-tracks').find('.artist-tags').tagsinput({
                typeaheadjs: {
                    name: 'tagNames',
                    displayKey: 'name',
                    valueKey: 'name',
                    maxTags: 1,
                    trimValue: true,
                    source: tagNames.ttAdapter()
                }
            });
            //$('#sel-tracks').find('.track-info').removeClass('hidden');
        });
    });


    $("#login-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            NProgress.start();
            $("#btn-login").attr("disabled", "true");
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == "success")
            {
                pjax.invoke({
                    'url': rootURL+"/admin",
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        NProgress.start();
                    },
                    'complete': function(e){
                        slider();
                        clipTransparentImage();
                        NProgress.done();
                    },
                });
                $("#login").modal('hide');
                window.location.href="";
            }
            $("#btn-login").removeAttr("disabled");
        }
    });

    $("#signup-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $("#btn-signup").attr("disabled", "true");
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });

            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/user',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        NProgress.start();
                    },
                    'complete': function(e){
                        //$('.typeahead').tagsinput('refresh');
                        typeaheadInit();
                        NProgress.done();
                    },
                });
                $("#signup").modal('hide');
            }
            $("#btn-signup").removeAttr("disabled");
        }
    });

    $("#create-album").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $("#btn-signup").attr("disabled", "true");
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/album/'+data.slug+'/add',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        NProgress.start();
                    },
                    'complete': function(e){
                        //$('.typeahead').tagsinput('refresh');
                        typeaheadInit();
                        NProgress.done();
                    },
                });
            }
            $("#btn-signup").removeAttr("disabled");
        }
    });

    $(document).on('submit', '#add-track', function(e){
        e.preventDefault();
        var postLink = $(this).attr('action');
        var files = $(this).find('#sl-audio')[0].files;
        $('#sl-audio, #upload-mp3s').attr('disabled', 'disabled');
        handleFile(files, 0, postLink);
    });

    function handleFile(files, index, postLink) {
        if(files.length > index) {
            var formData = new FormData();
            var request = new XMLHttpRequest();
            var progressBar = $('#add-track-pbar');
            progressBar.removeClass('hidden');

            formData.append('audio', files[index]);

            formData.append('title', $('#sel-tracks').find('.audio-title').eq(index).val());
            formData.append('artist', $('#sel-tracks').find('.artist-tags').eq(index).val());
            formData.append('_token', _token);

            request.open('POST', postLink, true);

            request.onloadstart = function(e) {
                progressBar.find('.progress-bar').css('width', '10%');
            };
            request.onloadend = function(e) {
                progressBar.find('.progress-bar').width(((index+1)/(files.length+1))*100+'%').html('Uploading '+ (index) + ' / ' + (files.length));
                var n = noty({
                    text        : $('#sel-tracks').find('.audio-title').eq(index-1).val()+' uploaded',
                    type        : 'success',
                    dismissQueue: true,
                    layout      : 'bottomRight',
                    closeWith   : ['click'],
                    theme       : 'relax',
                    maxVisible  : 10
                });
            };

            request.onload = function() {
                if(request.status === 200) {
                    console.log("Uploaded");
                    //uploadDiv.innerHTML += files[ index ].name + "<br>";
                    handleFile(files, ++index, postLink);
                    $.noty.closeAll();
                } else {
                    console.log("Error");
                }
            };
            request.send(formData);
        } else {
            pjax.invoke({
                'url': postLink,
                'container': 'wrapper',
                'beforeSend': function(e){
                    NProgress.start();
                },
                'complete': function(e){
                    typeaheadInit();
                    NProgress.done();
                },
            });
        }
    }

    function updateProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            console.log(percentComplete);
            var progressBar = $('#add-track-pbar');
            progressBar.removeClass('hidden');
            progressBar.find('.progress-bar').width(percentComplete+'%').html('Uploading progress ' + percentComplete);
        } else {
            // Unable to compute progress information since the total size is unknown
        }
    }

    /*$("#add-track").ajaxForm({
     type: "post",
     delegation: true,
     dataType: "json",
     beforeSubmit: function(){
     $("#add-track").attr("disabled", "true");
     $('#add-track-pbar').removeClass('hidden');
     },
     uploadProgress: function(event, position, total, percentComplete) {
     var percentVal = percentComplete + '%';
     $('#add-track-pbar > .progress-bar').width(percentVal).html('Uploading progress ' + percentVal);
     //percent.html(percentVal);
     },
     success: function(data){
     var n = noty({
     text        : data.message,
     type        : data.status,
     dismissQueue: true,
     layout      : 'bottomRight',
     closeWith   : ['click'],
     theme       : 'relax',
     maxVisible  : 10
     });
     if(data.status == 'success') {
     pjax.invoke({
     'url': rootURL+'/admin/album/'+data.slug+'/add',
     'container': 'wrapper',
     'beforeSend': function(e){
     NProgress.start();
     },
     'complete': function(e){
     typeaheadInit();
     NProgress.done();
     },
     });
     }
     $("#add-track").removeAttr("disabled");
     $('#add-track-pbar').addClass('hidden');
     }
     });*/

    $("#edit-album-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#edit-album-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/album',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $(".cat-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            NProgress.start();
        },
        success: function(data){
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/categories',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });
    $(".lang-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            NProgress.start();
        },
        success: function(data){
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/languages',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#edit-track-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#edit-track-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/album/'+data.slug+'/add',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        typeaheadInit();
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#edit-artist-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#edit-artist-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/artist',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        typeaheadInit();
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#create-playlist-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#create-playlist-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/playlist',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){

                    },
                });
            }
            NProgress.done();
        }
    });

    $("#track-playlist-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#track-playlist-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                NProgress.done();
            }
        }
    });

    $("#edit-playlist-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#edit-playlist-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/playlist/'+data.slug,
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#rm-track-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#rm-track-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/playlist/'+data.slug,
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#rm-playlist-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#rm-playlist-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/playlist',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#edit-user-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#edit-user-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    //'url': rootURL+'/'+data.url,
                    'url': rootURL+'/admin/user',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        NProgress.done();
                    },
                });
            }
        }
    });

    $("#user-mode-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#user-mode-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/user',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        //NProgress.done();
                    },
                });
            }
            NProgress.done();
        }
    });

    $("#user-status-form").ajaxForm({
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#user-status-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10,
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/user',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        //NProgress.done();
                    },
                });
            }
            NProgress.done();
        }
    });

    $("#subscribe-form").ajaxForm({             //Subscribtion From
        type: "post",
        delegation: true,
        dataType: "json",
        beforeSubmit: function(){
            $('#subscribe-modal').modal('hide');
            NProgress.start();
        },
        success: function(data){
            var n = noty({
                text        : data.message,
                type        : data.status,
                dismissQueue: true,
                layout      : 'bottomRight',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10
            });
            if(data.status == 'success') {
                pjax.invoke({
                    'url': rootURL+'/admin/user',
                    'container': 'wrapper',
                    'beforeSend': function(e){
                        //NProgress.start();
                    },
                    'complete': function(e){
                        //NProgress.done();
                    }
                });
            }
            NProgress.done();
        }
    });

    $(document).on('show.bs.modal', '#subscribe-modal', function (event) {      // For Subscription form
        var that = $(event.relatedTarget);

        var user            = that.data('user');
        var subscription    = that.data('subscription');
        var expire_date     = that.data('expire_date');
        var expire_month    = that.data('expire_month');
        var expire_year     = that.data('expire_year');
        var download_limit  = that.data('download_limit');

        var modal = $(this);

        modal.find('input[name="user"]').val(user);
        if(subscription){
            modal.find('input[name="subscription"]').attr("checked",true);
        }else{
            modal.find('input[name="subscription"]').attr("checked",false);
        }
        //modal.find('input[name="subscription"]').val(subscription);
        modal.find('select[name="expire_date"]').val(expire_date);
        modal.find('select[name="expire_month"]').val(expire_month);
        modal.find('select[name="expire_year"]').val(expire_year);
        modal.find('input[name="download_limit"]').val(download_limit);
    });

    $(document).on('shown.bs.modal', '#edit-album-modal', function (event) { // id of the modal with event
        var that = $(event.relatedTarget);
        var title = that.data('title');
        var artist = that.data('artist');
        var language = that.data('language');
        var category = that.data('category');
        var release = that.data('release');
        var slug = that.data('slug');
        // Update the modal's content.
        var modal = $(this);
        modal.find('input[name="slug"]').val(slug);
        modal.find('input[name="release"]').val(release);
        modal.find('input[name="title"]').val(title);

        modal.find('.typeahead').tagsinput('removeAll');
        modal.find('.typeahead2').tagsinput('removeAll');
        modal.find('.typeahead3').tagsinput('removeAll');

        modal.find('.typeahead').tagsinput('add', artist);
        modal.find('.typeahead2').tagsinput('add', category);
        modal.find('.typeahead3').tagsinput('add', language);
        modal.find('.datepicker').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
        //typeaheadInit();
    });

    $(document).on('show.bs.modal', '#add-album-modal', function (event) { // id of the modal with event
        var modal = $(this);
        modal.find('.datepicker').datepicker({
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    $(document).on('show.bs.modal', '#edit-track-modal', function (event) {
        var that = $(event.relatedTarget);
        var title = that.data('title');
        var artist = that.data('artist');
        var slug = that.data('slug');
        $('input[name="slug"]').val(slug);
        //typeaheadInit();
        // Update the modal's content.
        var modal = $(this);
        modal.find('input[name="title"]').val(title);
        modal.find('.typeahead').tagsinput('removeAll');
        modal.find('.typeahead').tagsinput('add', artist);
    });

    $(document).on('show.bs.modal', '#edit-artist-modal', function (event) {
        var that = $(event.relatedTarget);
        var name = that.data('name');
        var id = that.data('id');
        $('input[name="id"]').val(id);
        var modal = $(this);
        modal.find('input[name="title"]').val(name);
    });

    $(document).on('show.bs.modal', '#track-playlist-modal', function (event) {
        var that = $(event.relatedTarget);
        var track = that.parents('tr').data('trackid');
        var modal = $(this);
        modal.find('input[name="track"]').val(track);
    });

    $(document).on('show.bs.modal', '#rm-track-modal', function (event) {
        var that = $(event.relatedTarget);
        var track = that.parents('tr').data('trackid');
        var modal = $(this);
        modal.find('input[name="track"]').val(track);
    });

    $(document).on('show.bs.modal', '#edit-user-modal', function (event) {

        var that = $(event.relatedTarget);

        var user = that.data('user');
        var username = that.data('username');
        var email = that.data('email');

        var modal = $(this);

        modal.find('input[name="user"]').val(user);
        modal.find('input[name="username"]').val(username);
        modal.find('input[name="email"]').val(email);
    });

    $(document).on('show.bs.modal', '#user-status-modal', function (event) {
        var that = $(event.relatedTarget);
        var user = that.data('user');
        var modal = $(this);
        modal.find('input[name="user"]').val(user);
    });

    $(document).on('show.bs.modal', '#user-mode-modal', function (event) {
        var that = $(event.relatedTarget);
        var user = that.data('user');
        var modal = $(this);
        modal.find('input[name="user"]').val(user);
    });

    $(window).load(function(){
        NProgress.done();
        $('#wrapper').removeClass('hidden');
        slider();
        clipTransparentImage();
        typeaheadInit();
    });
    function loadMusic(url) {
        var req = new XMLHttpRequest();
        req.open( "GET", url, true );
        req.responseType = "arraybuffer";
        req.onreadystatechange = function (e) {
            if (req.readyState == 4) {
                if(req.status == 200)
                    audioContext.decodeAudioData(req.response,
                        function(buffer) {
                            currentBuffer = buffer;
                            displayBuffer(buffer);
                        }, onDecodeError);
                else
                    alert('error during the load.Wrong url or cross origin issue');
            }
        } ;
        req.send();
    }

    function onDecodeError() {  alert('error while decoding your file.');  }

// MUSIC DISPLAY
    function displayBuffer(buff /* is an AudioBuffer */) {

        var drawLines = 3000;
        var leftChannel = buff.getChannelData(0); // Float32Array describing left channel
        var lineOpacity = canvasWidth / leftChannel.length  ;
        context.save();
        context.fillStyle = '#222' ;
        context.fillRect(0,0,canvasWidth,canvasHeight );
        context.strokeStyle = '#8d8d8d';
        context.globalCompositeOperation = 'lighter';
        context.translate(0,canvasHeight / 2);
        //context.globalAlpha = 0.6 ; // lineOpacity ;
        context.lineWidth=1;
        var totallength = leftChannel.length;
        var eachBlock = Math.floor(totallength / drawLines);
        var lineGap = (canvasWidth/drawLines);

        context.beginPath();
        for(var i=0;i<=drawLines;i++){
            var audioBuffKey = Math.floor(eachBlock * i);
            var x = i*lineGap;
            var y = leftChannel[audioBuffKey] * canvasHeight / 2;
            context.moveTo( x, y );
            context.lineTo( x, (y*-1) );
        }
        context.stroke();
        context.restore();
    }

    $(document).on('click', '.track-play', function(){
        $(".sm2-progress-ball").removeClass("hidden");
        $(".songs-info").removeClass("hidden");
        $(".sm2-inline-element").removeClass("hidden");
        //document.getElementById('play-pause').click();
        var that = $(this);

        var _track_link = that.parents('tr').data('track');
        var _title = that.parents('tr').data('title');
        var _artist = that.parents('tr').data('artist');
        var _track_cover = that.parents('tr').data('cover');
        var _track_title = that.parents('tr').data('title');
        var _album_title = that.parents('tr').data('album_title');



        // Display Cover on player

        $('#player_cover').attr('src', _track_cover);
        $('.track-title').text(_track_title);
        $('.album-title').text(_album_title);


        if(!$(".sm2-playlist-wrapper > .sm2-playlist-bd li a span:contains("+_title+")").length ){
            $(".sm2-playlist-wrapper > .sm2-playlist-bd").prepend('<li><a href="'+_track_link+'"><span>'+_title+'</span> - <i>'+_artist+'</i></a></li>');
        }

        document.getElementById('play-pause').click();

        title_lock = 'enabled';
        document.title = _title;
        if(that.hasClass("active"))
        {
            that.removeClass("active");
            that.find("span.fa").removeClass("fa-pause").addClass("fa-play");
            soundManager.stopAll();
        }
        else
        {
            $(".track-play").removeClass("active");
            $(".track-play").find("span.fa").removeClass("fa-pause");
            soundManager.stopAll();
            that.addClass("active");
            that.find("span.fa").addClass("fa-pause");
            if(!$(".sm2-playlist-wrapper > .sm2-playlist-bd li a:contains("+_title+")").length ){
             $(".sm2-playlist-wrapper > .sm2-playlist-bd").prepend('<li><a href="'+_track_link+'">'+_title+'</a></li>');
             }




            //appendCanvas();
            //window.onload = appendCanvas;
            
           /* 
            var wavesurfer = WaveSurfer.create({
                container: '#waveforms',
                waveColor: 'red',
                progressColor: 'purple'
            });

            wavesurfer.load(_track_link);
*/


// MUSIC LOADER + DECODE


            loadMusic(_track_link);


            soundManager.load('sm-player', {url: _track_link});
            var smPlayer = soundManager.getSoundById('sm-player');
            smPlayer.play({
                onfinish: function() {

                    $(".btn-group .track-play").removeClass("active");
                    $(".btn-group .track-play").find("span.fa").removeClass("fa-pause");
                    soundManager.stopAll();
                }
            });

        }
    });

    $(document).on('click', '.sm2-playlist-bd > li', function(){

        var href = $(this).find("a").attr("href");
        loadMusic(href);

    });



   /* $(document).on('click', '.track-play', function(){
        //document.getElementById('play-pause').click();
        var that = $(this);

        var _track_link = that.parents('tr').data('track');
        var _title = that.parents('tr').data('title');
        var _artist = that.parents('tr').data('artist');

        if(!$(".sm2-playlist-wrapper > .sm2-playlist-bd li a span:contains("+_title+")").length ){
            $(".sm2-playlist-wrapper > .sm2-playlist-bd").prepend('<li><a href="'+_track_link+'"><span>'+_title+'</span> - <i>'+_artist+'</i></a></li>');
        }

        document.getElementById('play-pause').click();

        title_lock = 'enabled';
        document.title = _title;
        if(that.hasClass("active"))
        {
            that.removeClass("active");
            that.find("span.fa").removeClass("fa-pause").addClass("fa-play");
            soundManager.stopAll();
        }
        else
        {
            $(".track-play").removeClass("active");
            $(".track-play").find("span.fa").removeClass("fa-pause");
            soundManager.stopAll();
            that.addClass("active");
            that.find("span.fa").addClass("fa-pause");
            *//*if(!$(".sm2-playlist-wrapper > .sm2-playlist-bd li a:contains("+_title+")").length ){
             $(".sm2-playlist-wrapper > .sm2-playlist-bd").prepend('<li><a href="'+_track_link+'">'+_title+'</a></li>');
             }*//*
            loadMusic(_track_link);
            soundManager.load('sm-player', {url: _track_link});
            var smPlayer = soundManager.getSoundById('sm-player');
            smPlayer.play({
                onfinish: function() {
                    $(".btn-group .track-play").removeClass("active");
                    $(".btn-group .track-play").find("span.fa").removeClass("fa-pause");
                    soundManager.stopAll();
                }
            });
            smPlayer.whileplaying = function() {
                // Move 256 absolutely-positioned 1x1-pixel DIVs, for example (ugly, but works)
                var gPixels = document.getElementById('graphPixels').getElementsByTagName('div');
                var gScale = 32; // draw -32 to +32px from "zero" (i.e., center Y-axis point)
                for (var i=0; i<256; i++) {
                    graphPixels[i].style.top = (gScale+Math.ceil(this.waveformData.left[i]*-gScale))+'px';
                }
            }
        }
    });*/

    $(document).on('click', '#getImgUrl', function(){
        var that = $(this).parents('.modal-body')
        var title = that.find('input[name="title"]').val();
        //get related video link from youtube
        var youtube_key = $('meta[name="youtube_key"]').attr('content').trim();
        var youtubeData = 'https://www.googleapis.com/youtube/v3/search?part=id&type=video&key='+youtube_key+'&maxResults=1&q='+title;
        $.getJSON(youtubeData, function(data) {
            //data = jQuery.parseJSON(data);
            var youtubeID = data.items[0].id.videoId;
            var thumbnail = 'https://i1.ytimg.com/vi/'+ youtubeID +'/maxresdefault.jpg';
            that.find('input[name="imgurl"]').val(thumbnail);
            that.find('.al-art-opt').html('<div id="rm-ytimg" style="position: absolute; top: 0px; right: 0px;" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></div><img src="'+thumbnail+'" class="img-responsive"/>');
        });
    });

    $(document).on('click', '#rm-ytimg', function(){
        $('.al-art-opt').html('<span class="btn btn-file btn-primary"><input type="file" name="image">Select From PC</span>OR<input class="btn btn-info" id="getImgUrl" value="Get via Web" />');
    });

    //Checking track downloding
    $(document).on('click', '.track-download', function(){
        if(auth_download) {
            if(!is_login) {
                $("#signup").modal();
                return;
            }
        }
        var that = $(this);
        var slug = that.parents('tr').data('slug');

        $.get(rootURL+'/download/track/before/'+slug,
            function(data){
                data = jQuery.parseJSON(data);

                if(data.status == 'success') {

                    window.location.href = rootURL+'/download/track/'+slug;
                } else if(data.status = 'error') {
                    var n = noty({
                        text        : data.message,
                        type        : data.status,
                        dismissQueue: true,
                        layout      : 'bottomRight',
                        closeWith   : ['click'],
                        theme       : 'relax',
                        maxVisible  : 10
                    });
                }
            }
        );
    });

    $(document).on('click', '.album-download', function(){
        if(auth_download) {
            if(!is_login) {
                $("#signup").modal();
                return;
            }
        }
        var that = $(this);
        var slug = that.data('album');

        $.get(rootURL+'/download/album/before/'+slug,
            function(data) {
                data = jQuery.parseJSON(data);

                if(data.status == 'success') {

                    window.location.href = rootURL+'/download/album/'+slug;
                } else if(data.status = 'error') {
                    var n = noty({
                        text        : data.message,
                        type        : data.status,
                        dismissQueue: true,
                        layout      : 'bottomRight',
                        closeWith   : ['click'],
                        theme       : 'relax',
                        maxVisible  : 10
                    });
                }
            }

        );
        //window.location.href = rootURL+'/download/album/'+slug;
    });

    $(document).on('click', '.track-like', function(){
        if(!is_login) {
            $("#signup").modal();
            return;
        }
        that = $(this);

        trackID = that.parents('tr').data('trackid');

        $.post(rootURL+'/track/favorite',
            { track_id: trackID, _token: _token },
            function(data){
                data = jQuery.parseJSON(data);
                if(data.status == 'success') {
                    that.toggleClass('active');
                } else if(data.status = 'error') {
                    var n = noty({
                        text        : data.message,
                        type        : data.status,
                        dismissQueue: true,
                        layout      : 'bottomRight',
                        closeWith   : ['click'],
                        theme       : 'relax',
                        maxVisible  : 10
                    });
                }
            }
        );
    });

    $(document).on('click', '.album-like', function(){
        if(!is_login) {
            $("#signup").modal();
            return;
        }
        that = $(this);
        album = that.parent().data('album');

        $.post(rootURL+'/album/favorite',
            { album_id: album, _token: _token },
            function(data){
                data = jQuery.parseJSON(data);
                if(data.status == 'success') {
                    that.toggleClass('active');
                    $('#al-fav-count').text(data.count);
                }else if(data.status = 'error') {
                    var n = noty({
                        text        : data.message,
                        type        : data.status,
                        dismissQueue: true,
                        layout      : 'bottomRight',
                        closeWith   : ['click'],
                        theme       : 'relax',
                        maxVisible  : 10
                    });
                }
            }
        );
    });

    $(document).on('click', '.play-album ,.play-album-release', function(){
        $(".sm2-progress-ball").removeClass("hidden");
        $(".songs-info").removeClass("hidden");
        $(".sm2-inline-element").removeClass("hidden");

        that = $(this);
        album = that.data('album');
        album = $.trim(album);

        playlist = that.data('playlist');
        playlist = $.trim(playlist).trim();

        /****************Track title, Album Cover, Album Title displays on Player********************/

        album_cover_ = that.data('cover');
        album_title = that.data('album_title');
        //track_title = that.data('title');

        $('#player_cover').attr('src', album_cover_);
        $('.album-title').text(album_title);
        //$('.track-title').text(track_title);


        if(album != ''){
            url = rootURL+'/album/'+album+'/play';
        } else if(playlist != '') {
            url = rootURL+'/playlist/'+playlist+'/play';
        }

        $.post(url,
            { _token: _token },
            function(data){
                if(data != '') {
                    $(".sm2-playlist-wrapper > .sm2-playlist-bd").html(data);
                    document.getElementById('play-pause').click();
                    title_lock = 'enabled';
                    soundManager.stopAll();
                    soundManager.load('sm-player', {url: $('.sm2-playlist-bd li a:first').attr('href')});
                    loadMusic($('.sm2-playlist-bd li a:first').attr('href'));
                    var smPlayer = soundManager.getSoundById('sm-player');
                    smPlayer.play();
                    document.title = $('.sm2-playlist-bd li:first a > span').text();   // Get the Album's 1st track

                    track_title = $('.sm2-playlist-bd li a:first span').html();     // Set the Album's 1st track on player

                    $('.track-title').text(track_title);
                    $('.play-album').removeClass('hidden');
                    if(!that.hasClass("play-album-release"))
                        that.addClass('hidden');
                }
            }
        );
    });

});

clipTransparentImage = function() {
    var that = $('.bg-transparent');
    $('.transparent-img').css({'clip': 'rect(0,'+that.width()+'px,'+that.outerHeight()+'px,0)'});

}

slider = function() {
    $('.slider1').bxSlider({
        slideWidth: 150,
        minSlides: 3,
        maxSlides: 12,
        slideMargin: 15,
        pager: false,
        moveSlides: 1,
        hideControlOnEnd: true,
        infiniteLoop: false,
    });
}


typeaheadInit = function() {
    var tagNames = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/artists/%QUERY.json',
            wildcard: '%QUERY'
        }
    });
    tagNames.initialize();

    $('.typeahead').tagsinput({
        typeaheadjs: {
            name: 'tagNames',
            displayKey: 'name',
            valueKey: 'name',
            maxTags: 1,
            trimValue: true,
            source: tagNames.ttAdapter()
        }
    });

    var tagNames2 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/categories/%QUERY.json',
            wildcard: '%QUERY'
        }
    });
    tagNames2.initialize();

    $('.typeahead2').tagsinput({
        typeaheadjs: {
            name: 'tagNames2',
            displayKey: 'name',
            valueKey: 'name',
            maxTags: 1,
            trimValue: true,
            source: tagNames2.ttAdapter()
        }
    });

    var tagNames3 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: $('meta[name="rootURL"]').attr('content').trim()+'/admin/api/languages/%QUERY.json',
            wildcard: '%QUERY'
        }
    });
    tagNames3.initialize();

    $('.typeahead3').tagsinput({
        typeaheadjs: {
            name: 'tagNames3',
            displayKey: 'name',
            valueKey: 'name',
            maxTags: 1,
            trimValue: true,
            source: tagNames3.ttAdapter()
        }
    });
}

function slugify(word) {
    return word.replace(/[^a-z0-9-]/gi, '-').replace(/-+/g, '-').replace(/^-|-$/g, '').toLowerCase();
}
/*

 $(document).on('click', '.startbulkupload', function(){
 pjax.invoke({
 'url': rootURL+'/admin/startbulkupload',
 'container': 'wrapper',
 'beforeSend': function(e){ NProgress.start(); },
 'complete': function(e){  NProgress.done(); }
 });
 e.preventDefault();
 });*/

$("#bulkupload-form").ajaxForm({
    type: "post",
    delegation: true,
    dataType: "json",
    beforeSubmit: function(){
        NProgress.start();
    },
    success: function(data){
        var n = noty({
            text        : data.message,
            type        : data.status,
            dismissQueue: true,
            layout      : 'bottomRight',
            closeWith   : ['click'],
            theme       : 'relax',
            maxVisible  : 10
        });
        if(data.status == 'success') {
            pjax.invoke({
                'url': rootURL+'/admin/album',
                'container': 'wrapper',
                'beforeSend': function(e){
                    //NProgress.start();
                },
                'complete': function(e){
                    typeaheadInit();
                    NProgress.done();
                },
            });
        }
    }
});

/*

 $(document).on('click','.pagination a', function(e) {
 e.preventDefault();

 //to get what page, when you click paging
 var page = $(this).attr('href').split('page=')[1];
 //console.log(page);

 getTransaction(page);
 location.hash = page;
 });
 function getTransaction(page) {

 //console.log('getting sort for page = '+page);
 $.ajax({
 type: 'GET',
 url: rootURL+"/?page="+page
 }).done(function(data) {
 console.log(data);
 $('#container').html(data);
 });
 }

 $( document ).ready(function() {
 $("").each(function(i, ele){
 $(this).addClass("pjax");
 console.log([i,ele]);
 })

 });
 */
$( document ).ready(function() {
    $(".pagination-point li a").on("click", function(event){
        var that = $(this);
        pjax.invoke({
            'url': that.attr("href"),
            'container': 'wrapper',
            'beforeSend': function(e){
                NProgress.start();
            },
            'complete': function(e){
                //alert($('title').val());
                slider();
                ias.reinitialize();
                clipTransparentImage();
                typeaheadInit();
                NProgress.done();
            },
        });
        event.preventDefault();
    });
})
