$(function(){

    $('#show').on('click',function(){
        $('.card-reveal').slideToggle('slow');
    });

    $('.card-reveal .close').on('click',function(){
        $('.card-reveal').slideToggle('slow');
    });
});