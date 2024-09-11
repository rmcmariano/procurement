$(document).ready(function(){

    $(this).find($('.glyphicon-eye-open')).removeClass('.glyphicon-eye-open').addClass('fas fa-eye');
    $(this).find($('.glyphicon-pencil')).removeClass('.glyphicon-pencil').addClass('fas fa-edit');
    $(this).find($('.glyphicon-trash')).removeClass('.glyphicon-trash').addClass('fas fa-trash');
    
});