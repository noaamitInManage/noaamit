$(function(){

    $(".saveError").on('click',function(){
        var thisRow = $(this).closest('tr');
        var translation = {};

        translation.key_code = thisRow.data('keycode');
        var descripation=$('input[rel=descripation_' +translation.key_code+']').val();

        translation.values = [];
        thisRow.find('textarea').each(function(index,elem){
            var langid = $(elem).data('langid');
            var value = encodeURIComponent($(elem).val());
            if (langid){
                translation.values.push({'langid':langid,'text':value,'descripation':descripation});
            }
        });

        $.post('/salat2/_ajax/ajax.index.php',
            {'file':'cg_errors','act':'save','data':translation},
            function(response){

                if (response.status == 'ok'){
                    //				alert("נשמר בהצלחה");
                    iflameRefresh();
                }else{
                    //				alert('הפעולה נכשלה!');
                }
            },'json');
    });

    $(".deleteError").on('click',function(){
        if (confirm("לא ניתן לבטל את הפעולה. נא לאשר מחיקה.")){
            var thisRow = $(this).closest('tr');
            var key_code = encodeURIComponent(thisRow.data('keycode'));
            $.post('/salat2/_ajax/ajax.index.php',
                {'file':'cg_errors','act':'delete','key_code':key_code},
                function(response){
                    if (response.status == 'ok'){
                        alert("נמחק בהצלחה");
                        thisRow.remove();
                    }else{
                        alert('הפעולה נכשלה!');
                    }
                },'json');
        }
    });

    $("#saveNewError").on('click',function(){
        var thisRow = $(this).closest('tr');
        var translation = {};
        translation.key_code = $("#newErrorKey").val();
        if (translation.key_code != ''){
            translation.values = [];
            thisRow.find('textarea').each(function(index,elem){
                var langid = $(elem).data('langid');
                var value = encodeURIComponent($(elem).val());
                if (langid){
                    translation.values.push({'langid':langid,'text':value});
                }
            });

            $.post('/salat2/_ajax/ajax.index.php',
                {'file':'cg_errors','act':'save-new','data':translation},
                function(response){
                    if (response.status == 'ok'){
                        //				alert("נשמר בהצלחה");
                        iflameRefresh();
                    }else{
                        				alert(response.err);
                    }
                },'json');
        }else{
            //		alert('יש להגדיר מפתח באנגלית');
        }

    });

    $("#cancelNewError").on('click',function(){
        $("#newError textarea").val('');
    });

});
