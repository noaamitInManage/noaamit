$(function() {
    var trrig1= 0,numUpload=0;
    $('.bar').closest('td').prev().attr('class', '').attr({
        'colspan': '2',
        'align': 'center'
    }).parent().attr('class', 'dottTbl').children().last().remove();

    /*  auto media load */
    var mediaSelects = $('.media-select');

    var mediaItemId,MediaExt,mediaCategoryId,MediaRes;
    mediaSelects.bind('change', function() {
        $('input#tmp-medium').val($(this).children(':selected').val());
        showImage();
    });

    $(document).on('click', ".add-image",function(event){
        var Scope=$(this);
        var flag=0;
        if(Scope.hasClass('pic_r')){
            //get and show only gallery of THIS picture...
            var img_close=Scope.closest('.media_items_sel_box').siblings(".image_con");
            if($(img_close).hasClass('selected_item')){
                Scope.parent().find(".main_media").val($(img_close).attr("rel"));
                Scope.parent().find(".main_media").addClass('done');
                flag=1;
            }else{
                alert('תבחר קודם תמונה הראשית');
            }
        }else{
            flag=1;
        }
        if(flag){
            $(this).parent().find('.media_category_sel').show();
            $(this).parent().find('.media_category_sel').select2();
            $(this).parent().find('#uploadAlbum_Uploader').show();
            $(this).parent().find('#uploadAlbum_Uploader').css('display','block');
        }

    });

    $(document).on('click', ".remove-image",function(event){
        //if it is resolution, delete from tb_media_resolutions
        if($(this).prev().hasClass('pic_r')){
            var box=$(this).closest('.media_items_sel_box');
            var img_src=$($(this).parent().find('.image_con')).attr('src');
            $.get('/salat2/_ajax/ajax.index.php', {
                'file': 'auto_complete/media_category',
                'src': img_src,
                'action': 'deleteResolution'
            }, function (response) {
                $(box).siblings(".image_con").off("click",".image_con");
                $(box).siblings(".image_con").trigger('click');
                $(box).siblings(".image_con").addClass('selected_item');
            });
        }else{
            $.each($(this).parent().find('input[type="hidden"]'),function(key,val){
                if(!$(val).hasClass('typeArr_size')){
                    $(val).val('');
                }
            });
            trrig1=1;
            $(this).parent().find('.media_category_sel').trigger('change');
            var href=$($(this).parent().find('.cancel a')).attr('href');
            //window.location.href = href;
        }
    });

    $(document).on('change', ".media_category_sel", function(event){
        var Scope=$(this);
        Scope.parent().find (".media_items_sel_box").html("");
        var media_id="";
        var res="";
        var res_id=Scope.parent().attr('rel');
        var global_category_sel=Scope.closest('.media_items_sel_box').parent().find('.media_category_sel');
        //if it is resolution box add class resolution

        if(Scope.siblings(".add-image").hasClass('pic_r')){
            $(Scope).addClass('resolution');
        }
        if(Scope.prev().hasClass('done')){
            media_id=Scope.prev().val();
        }else if($(global_category_sel).hasClass('resolution')&&(!media_id)){
            media_id=$(global_category_sel).prev().val();
        }
        if(Scope.siblings(".add-image").hasClass('pic_r')){
            res=1;
        }
        if(!Scope.hasClass('resolution')){
            $.each(Scope.parent().find(".media_category_sel .resolution"), function( index, value ) {
                $(value).trigger('change');
            });
        }

        if(!trrig1){
            numUpload++;
            $($(this).parent().find('.upload_div')).html('<br /><input type="button" gallery_id="0" class="buttons orange uploadAlbum" value="העלאה מרובה" id="uploadAlbum_'+numUpload+'" rel="1"  /> &nbsp;');
            var href=$($(this).parent().find('.cancel a')).attr('href');
            var valscope=Scope.val();
            //setTimeout(function(){var k= 2,t=1; var s=0; s=k+t; },1000);
            uploadImg(numUpload,Scope,valscope,res_id,media_id);
        }
        trrig1=0;
        //for resolution picture (draw just the selecting and image)
        if($(Scope.parent().find('.add-image')).hasClass('pic_r')){
            $.get('/salat2/_ajax/ajax.index.php', {
                'file': 'auto_complete/media_category',
                'category': Scope.val(),
                'action': 'getSelcetItemsOnly'
            }, function (response) {
                if(response!==undefined&&response!=""){
                    Scope.parent().find(".media_items_sel").html(response);
                    Scope.parent().find(".media_items_sel").show();
                }
            });
        }else{
            //for Main picture
            var field_name=Scope.attr('field_name');
            //var box=$(Scope).closest(".media_items_sel_box");
            var box=$(Scope).parent().find(".media_items_sel_box");
            var typeArr=$(box.next()).val();

            $.get('/salat2/_ajax/ajax.index.php', {
                'file': 'auto_complete/media_category',
                'field_name': field_name,
                'category': Scope.val(),
                'typeArr': typeArr,
                'action': 'getSelcetItems'
            }, function (response) {
                if(response!==undefined&&response!=""){

                    var obj = jQuery.parseJSON(response);
                    if(obj.main_html!==undefined&&obj.main_html!=""){
                        Scope.parent().find(".media_items_sel").html(obj.main_html);
                        Scope.parent().find(".media_items_sel").show();
                    }
                    if(obj.res_html!==undefined&&obj.res_html!=""){
                        Scope.parent().find(".media_items_sel_box").html(obj.res_html);
                        Scope.parent().find(".media_items_sel_box").show();
                    }
                }
            });
        }


        Scope.parent().find(".gallery_id").val(Scope.val());
        Scope.parent().find(".image_con").attr("src","");
        Scope.parent().find(".image_con").attr("rel","");
        Scope.parent().find(".image_con").removeClass("selected_item");
        Scope.parent().find(".image_con").hide();

        $gallery_images = Scope.parent().find(".media_items_sel").select2({placeholder: 'בחר תמונה', width: '192px'});
        $gallery_images.on('select2:select', function (e) {
            Scope.parent().find(".media_items_sel").trigger('change');
        });
    });

    $(document).on('change', ".media_items_sel", function(event){
        var Scope=$(this);
        var splitStr = $(this).val().split('_');
        mediaCategoryId=splitStr[0];
        mediaItemId=splitStr[1];
        MediaExt=splitStr[2];
        MediaRes=splitStr[3];
        var resol_id=Scope.parent().attr('rel');
        var src_result="";
        var src=$(Scope.siblings('.image_con')).attr('src');

        var mainMedia = Scope.siblings('.image_con');
        var realMainMedia = Scope.closest(".dottTblS").find(".add-image").not('.pic_r').siblings('.image_con');
        var main_media_src = $(Scope.siblings('input.main_media')).val();

        //new resolution picture
        //if(main_media_src==''||!main_media_src){
        main_media_src=$(realMainMedia).attr('src');
        if(main_media_src!=''&&main_media_src){
            main_media_src=main_media_src.split('/');
            main_media_src=main_media_src[4].split('.');
            main_media_src=main_media_src[0];
        }
        //}

        //26.11-14 - Update by David - change the image id for save
        $(Scope).siblings('input.main_media').val(mediaItemId);

        //this is change of resolution photo, make ajax update
        if(Scope.siblings('.media_category_sel').hasClass('resolution')&&main_media_src!=''){
            var box=$(this).closest('.media_items_sel_box');
            var gallery_main=$(Scope.closest('.media_items_sel_box').siblings('.image_con')).attr('src');
            if(gallery_main){
                gallery_main=gallery_main.split('/');
                gallery_main=gallery_main[3];
            }
            $.get('/salat2/_ajax/ajax.index.php', {
                'file': 'auto_complete/media_category',
                'main_gallery': gallery_main,
                'category': Scope.val(),
                'action': 'saveResolution',
                'src': src,
                'resolution': resol_id,
                'main_pic': main_media_src
            }, function (response) {
                $($(box).siblings('.image_con')).off("click",".image_con");
                $($(box).siblings('.image_con')).trigger('click');
                $($(box).siblings('.image_con')).addClass('selected_item');
            });
        }

        if(mainMedia.attr('height')=="0"){
            mainMedia.attr('height',"100");
        }

        if($(this).val()){
            if(!MediaRes || MediaRes==undefined || MediaRes==""){
                mainMedia.attr("src","/_media/media/" + mediaCategoryId + '/' + mediaItemId + '.' + MediaExt);
            }else{
                mainMedia.attr("src","/_media/media/" + mediaCategoryId + '/' + mediaItemId + '_'+MediaRes+'.' + MediaExt);
            }
            mainMedia.attr("rel",mediaItemId);
            mainMedia.show();
        }else{
            mainMedia.hide();
            mainMedia.attr("src","");
            mainMedia.attr("rel","");
        }
    });

    $(document).on('click', ".image_con", function(event){
        var Scope=$(this);
        $(this).toggleClass('selected_item');
        if($(this).hasClass('selected_item')){
            if(!Scope.siblings('.main_media').hasClass('done')){
                Scope.siblings('.main_media').val(Scope.attr("rel"));
            }
        }else{
            if(!Scope.siblings('.main_media').hasClass('done')){  //main_media has class done..
                $.each($(".pic_r"), function( index, value ) {
                    if($($(value).next().next().next().next()).attr('style')=="display: inline-block;"){
                        $(value).next().trigger('click');
                    }
                });
                // Scope.parent().find(".main_media").val(Scope.attr("rel"));
            }
        }
        //if this is main picture, draw all the resolutions attached to it
        if(!Scope.siblings('.add-image').hasClass('pic_r')){
            var field_name = Scope.siblings('.media_category_sel').attr('field_name');
            var box = $(Scope).parent().find(".media_items_sel_box");
            var typeArr = $(box.next()).val() ? $(box.next()).val() : [];
            var media_id=$(Scope).attr('rel');
            if(!media_id||media_id==''){
                media_id=$(Scope).attr('src');
                media_id=media_id.split('/');
                media_id=media_id[4].split('.');
                media_id=media_id[0];
            }

            $.get('/salat2/_ajax/ajax.index.php', {
                'file': 'auto_complete/media_category',
                'field_name': field_name,
                //'typeArr': $.parseJSON(typeArr),
                'action': 'drawResolutionBoxes',
                'main_pic': media_id
            }, function (response) {
                if(response!==undefined&&response!=""){
                    var obj = jQuery.parseJSON(response);
                    if(obj.res_html!==undefined&&obj.res_html!=""){
                        $(Scope.parent().find(".media_items_sel_box")).html(obj.res_html);
                    }else{
                        $.get('/salat2/_ajax/ajax.index.php', {
                            'file': 'auto_complete/media_category',
                            'field_name': field_name,
                            'category': Scope.siblings(".media_category_sel").val(),
                            'typeArr': $.parseJSON(typeArr),
                            'action': 'getSelcetItems'
                        }, function (response) {
                            var obj = jQuery.parseJSON(response);
                            Scope.parent().find (".media_items_sel_box").html(obj.res_html);
                            Scope.parent().find (".media_items_sel_box").show();
                        });
                    }

                    Scope.parent().find('.media_category_sel').select2();
                }
            });
        }
    });

    /* end of  auto media load */

    /* start choose gallery and show all her items */
    $(document).on('change', ".gallery_media", function(event){
        console.log('Gallery change');
        //var div_id=$(this).attr('id')+'_items';
        var Scope=$(this).parent();
        var div_preview = $(Scope).parent().next().find('.gallery_media_items');

        $.get('/salat2/_ajax/ajax.index.php', {
            'file': 'getGalleryItems',
            'gal_id': $(this).val()
        }, function (response) {
            var gallery_id = $(event.target).val();

            // create the upload image button
            $(div_preview).html(response);
            $(div_preview).prepend('<input type="button" class="buttons orange uploadAlbum" value="העלאה" id="uploadAlbum_' + gallery_id + '" rel="' + gallery_id + '" width="120" style="display: none;" height="27">');


            var direction = document.getElementsByTagName("html")[0].getAttribute("dir");
            var dir;
            direction === "rtl" ? dir = true : dir = false;
            div_preview.find('.genric_gallery').slick({
                dots: true,
                infinite:false,
                rtl : dir,
                arrows: true,
                slidesToShow: 6,
                slidesToScroll: 3,
                centerMode: false
            });

            $('#uploadAlbum_'+ gallery_id).uploadifive({
                //'uploader'  : '/resource/uploadify/uploadify.swf',
                //'script'    : '/resource/uploadify/media.upload.php',
                'fileObjName'      : 'Filedata',
                'uploadScript'    : '/resource/uploadify/media.upload.php',
                'cancelImg' : '/resource/uploadify/cancel.png',
                'folder'    : '/_media/temp/<?=$user_hash;?>',
                'buttonText'  : 'בחר קובץ',
                'removeCompleted' : true,
                'hash'      : '',
                'album_id'      : gallery_id,
                'buttonImg' : '../_public/upload.png',
                'width'	    : 125,
                'height'    : 24,
                //'fileType'		: ['*.jpg;', '*.png;', '*.bmp;', '*.jpeg;', '*.gif;', '*.mov;', '*.avi;', '*.mp4;', '*.wmv;', '*.mpg'],
                'fileType'		: ['image/*', 'video/vnd.sealedmedia.softseal-mov', 'application/vnd.avistar+xml', 'video/mp4' ],
                'fileDesc'		: 'קבצי מדיה',
                'wmode'		: 'transparent',
                'formData': {'album_id':gallery_id},
                'auto'      : true,
                'multi'	    : true,
                'hideButton'  : false,
                'onUploadComplete'  : function( response, data) {
                    //console.log("startt");
                    //console.log(response);

                    var responseObj=$.parseJSON( data );
                    //console.log("rum");

                    if(responseObj.err){
                        //console.log("error1");

                        alert(responseObj.err);
                        remove_loader();
                    }
                    else{
                        if($('option[value='+responseObj.album_id+']').length == 0){
                            // change the selected options to the added value
                            $('#gallery_id').children().removeAttr('selected');
                            $('#gallery_id').append('<option value="'+responseObj.album_id+'" selected="selected">'+title+'</option>');
                            $('#gallery_id_items').html('');
                        }
                        //console.log("EFE");

                        // add the images that uploaded to new gallery into the page
                        div_preview.find('.genric_gallery').slick('slickAdd','<img src="/_media/media/'+responseObj.album_id+'/'+responseObj.item_id+'.'+responseObj.ext+'" width="300" height="150" >');
                        // $('#gallery_id_items').append('<img src="/_media/media/'+responseObj.album_id+'/'+responseObj.item_id+fileObj.type+'" width="300" height="150" >');
                        //console.log("EFqwfE");
                    }
                }
            });
        });
    });
    /* end of choose gallery and show all her items */
    //
    var upload_file_limit =35840000;

    function uploadImg(numUpload,Scope,gallery_id,resol_id,media_id){
        //   $(".uploadAlbum").each(function(a,item){
        $container = $('#uploadAlbum_'+ numUpload).closest('.upload_div').parent();

        $('#uploadAlbum_'+ numUpload).uploadifive({
            //'uploader'  : '/resource/uploadify/uploadify.swf',
            //'script'    : '/resource/uploadify/media.upload.php',
            'fileObjName'      : 'Filedata',
            'uploadScript'    : '/resource/uploadify/media.upload.php',
            'cancelImg' : '/resource/uploadify/cancel.png',
            'folder'    : '/_media/temp/',
            'buttonText'  : 'בחר קובץ',
            'removeCompleted' : true,
            'hash'      : '',
            'album_id'      : gallery_id,
            'buttonImg' : '../_public/upload.png',
            'width'	    : 125,
            'height'    : 24,
            //'fileType'		: ['*.jpg;', '*.png;', '*.bmp;', '*.jpeg;', '*.gif;', '*.mov;', '*.avi;', '*.mp4;', '*.wmv;', '*.mpg'],
            'fileType'		: ['image/*', 'video/vnd.sealedmedia.softseal-mov', 'application/vnd.avistar+xml', 'video/mp4' ],
            'fileDesc'		: 'קבצי מדיה',
            'wmode'		: 'transparent',
            'formData': {
                'album_id': gallery_id,
                'resolution_id': resol_id,
                'media_id': media_id,
                'minWidth': $container.data('min-width'),
                'minHeight': $container.data('min-height')
            },
            'auto'      : true,
            'multi'	    : true,
            'hideButton'  : false,
            'onUploadFile' : function(fileObj) {
                if(fileObj.size > upload_file_limit) {
                    alert('גודל קובץ וידיאו מקסימלי  הינו :30 MB');
                    total_video=0;
                    remove_loader();
                    return false;
                }
                if(parseInt(gallery_id)==0){
                    alert('תבחר את הגלריה קודם!');
                    remove_loader();
                    return false;
                }
            },
            'onUploadComplete'  : function( response, data) {
                trrig1=1;
                Scope.trigger('change');
                var responseObj=$.parseJSON( data );
                if(responseObj.err){
                    alert(responseObj.err);
                }
            }
        });

    }


    // ------------------- add new gallery (Itamar fahn 16.12.2014) ----------------------- //

    $('body').on('click','#add_gallery',function(e){
        e.preventDefault();
        $(this).after('<br><div id="create_gallery_wrap">שם:<input type="text" name="gallery_name"/>' +
            '&nbsp;גרסת מובייל:<input type="radio" name="mobile" id="RADIO_mobile_0" value="0" checked="checked">לא</input>' +
            '&nbsp;<input type="radio" name="mobile" id="RADIO_mobile_1" value="1" checked="checked">כן</input>&nbsp;&nbsp;' +
            '<button id="create_gallery" style="display: none;" >צור גלריה</button></div>');
        //$(this).prop('disabled',true);
        $(this).css('display','none');
    });

    $('body').on('keydown mousedown','input[name=gallery_name]',function(){
        $('#create_gallery').css('display', '');
    });

    // create new gallery via ajax
    $('body').on('click','#create_gallery',function(e){
        e.preventDefault();
        var title = $('input[name=gallery_name]').val();
        var mobile = $('input[name=mobile]:checked').val();

        if(title != "")
        {
            $('#create_gallery_wrap').children().prop('disabled',true);

            $.post("/salat2/site/media_category.php",'act=after&from_ajax=&title='+title+'&mobile='+mobile+'&send=הוסף',function(result){

                // create the upload image button
                $('#create_gallery_wrap').after('<input type="button" class="buttons orange uploadAlbum" value="העלאה מרובה" id="uploadAlbum_'+result.html+'" rel="'+result.html+'" width="120" style="display: none;" height="27">');

                $('#uploadAlbum_'+ result.html).uploadifive({
                    //'uploader'  : '/resource/uploadify/uploadify.swf',
                    //'script'    : '/resource/uploadify/media.upload.php',
                    'fileObjName'      : 'Filedata',
                    'uploadScript'    : '/resource/uploadify/media.upload.php',
                    'cancelImg' : '/resource/uploadify/cancel.png',
                    'folder'    : '/_media/temp/<?=$user_hash;?>',
                    'buttonText'  : 'בחר קובץ',
                    'removeCompleted' : true,
                    'hash'      : '',
                    'album_id'      : result.html,
                    'buttonImg' : '../_public/upload.png',
                    'width'	    : 125,
                    'height'    : 24,
                    //'fileType'		: ['*.jpg;', '*.png;', '*.bmp;', '*.jpeg;', '*.gif;', '*.mov;', '*.avi;', '*.mp4;', '*.wmv;', '*.mpg'],
                    'fileType'		: ['image/*', 'video/vnd.sealedmedia.softseal-mov', 'application/vnd.avistar+xml', 'video/mp4' ],
                    'fileDesc'		: 'קבצי מדיה',
                    'wmode'		: 'transparent',
                    'formData': {'album_id':result.html},
                    'auto'      : true,
                    'multi'	    : true,
                    'hideButton'  : false,
                    'onUploadFile' : function(fileObj) {
                        if(fileObj.size > 35840000) {
                            alert('גודל קובץ וידיאו מקסימלי  הינו :30 MB');
                            total_video=0;
                            remove_loader();
                            return false;
                        }
                    },

                    'onUploadComplete'  : function( response, data) {
                        var responseObj=$.parseJSON( data );
                        //console.log(responseObj);

                        if(responseObj.err){
                            alert(responseObj.err);
                            remove_loader();
                        }
                        else{
                            if($('option[value='+responseObj.album_id+']').length == 0){
                                // change the selected options to the added value
                                $('#gallery_id').children().removeAttr('selected');
                                $('#gallery_id').append('<option value="'+responseObj.album_id+'" selected="selected">'+title+'</option>');
                                $('#constraction_images').html('');
                            }

                            // add the images that uploaded to new gallery into the page
                            $('#constraction_images').append('<img src="/_media/media/'+responseObj.album_id+'/'+responseObj.item_id+'.'+responseObj.ext+'" width="300" height="150" >');
                        }
                    }
                });

            },"json");
        }
        else{
            alert("אין אפשרות ליצור גלריה ללא שם");
        }
    });

});