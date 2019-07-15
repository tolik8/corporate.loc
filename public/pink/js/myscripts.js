jQuery(document).ready(function($){

    $('.commentlist li').each(function(i) {

        $(this).find('div.commentNumber').text('#' + (i + 1));

    });

    $('#commentform').on('click', '#submit', function() {

        e.preventDefault();

        var comParent = $(this);

        $('.wrap_result').css('color', 'green').text('Сохранение комментария');

    });

});