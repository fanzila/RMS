/*global jQuery*/

jQuery(function ($) {
    var position;

    //Check if the local storage has the information that the fullscreen button was pressed
    if (CacheLibrary.getLocalStorageItem('gcrud_fullscreen') === 'true') {
        $('.gc-full-width').find('i.fa:first').toggleClass('fa-expand el-resize-full');
        $('.gc-full-width').find('i.fa:first').toggleClass('fa-compress el-resize-small');
        $('.gc-container').addClass('container-full no-transition');
        $('.gc-container').removeClass('container');

        //Get enough time so the transition will not be triggered
        setTimeout(function (){
            $('.gc-container').removeClass('no-transition');
        }, 400);
    }

    $('.gc-full-width').click(function () {

        $(this).find('i.fa:first').toggleClass('fa-expand el-resize-full');
        $(this).find('i.fa:first').toggleClass('fa-compress el-resize-small');

        if ($(this).closest('.gc-container').hasClass('container-full')) {
            $(this).closest('.gc-container').removeClass('container-full');
            $(this).closest('.gc-container').addClass('container');
            var scroll_top = $(this).closest('.gc-container').offset().top - 10;
            $('html,body').animate({scrollTop: scroll_top}, 750);

            CacheLibrary.setLocalStorageItem('gcrud_fullscreen', 'false');

            return true;
        }

        position = $(this).closest('.gc-container').offset();

        $(this).closest('.gc-container')
            .css('left', position.left + 'px')
            .css('top', position.top + 'px')
            .addClass('container-before-resize');

        $('html,body').animate({scrollTop: '0'}, 750);

        $(this).closest('.gc-container').removeClass('container-before-resize')
            .removeAttr('style')
            .addClass('container-full')
            .removeClass('container');

        CacheLibrary.setLocalStorageItem('gcrud_fullscreen', 'true');

    });

    $('.minimize-maximize').click(function () {
        $(this).find('i').toggleClass('fa-caret-down').toggleClass('el-caret-down');
        $(this).find('i').toggleClass('fa-caret-up').toggleClass('el-caret-up');

        $(this).closest('.gc-container').find('.table-container:first').slideToggle('slow');
    });

    $('.gc-full-width').hover(
        function () {
            $(this).find('i.fa:first').addClass('fa-lg');
        },
        function () {
            $(this).find('i.fa:first').removeClass('fa-lg');
        }
    );
});

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}