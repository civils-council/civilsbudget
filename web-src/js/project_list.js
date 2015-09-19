$(function(){

    $('.cards').on('click',function(e){
        var target = $("#"+e.target.getAttribute('data-value'));
        var card = target.find('.card-reveal');

        card.show('slow');

        target.find('.card-reveal > button.close').on('click',function(e){
            card.hide('slow');
        });

        console.log(card);
    });


});