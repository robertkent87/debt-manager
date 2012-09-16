$(document).ready(function(){
    // Set up date picker in form
    $('#dp3').datepicker();

    // Set up payment form
    $('#btnDel').attr('disabled','disabled');

    // Duplicate form fields for additional people
    $('#btnAdd').click(function(e) { 
        e.preventDefault();
        var num     = $('.clonedInput').length; 
        var newNum  = new Number(num + 1);      

        var newElem = $('#input' + num).clone().attr('id', 'input' + newNum);

        newElem.find('input :first').attr('id', 'user_id' + newNum);
        newElem.find('.drinks').attr('id', 'drinks_owed' + newNum).val('');
        newElem.find('.food').attr('id', 'food_owed' + newNum).val('');

        // insert the new element after the last "duplicatable" input field
        $('#input' + num).after(newElem);

        $('#btnDel').removeAttr('disabled');

        if (newNum == 6)
            $('#btnAdd').attr('disabled','disabled');
    });

    // Remove last duplicated form fields                
    $('#btnDel').click(function(e) {
        e.preventDefault();
        var num = $('.clonedInput').length; // how many "duplicatable" input fields we currently have
        $('#input' + num).remove();     // remove the last element

        $('#btnAdd').removeAttr('disabled');

        if (num-1 == 1)
            $('#btnDel').attr('disabled','disabled');
    });

    // Handle form submission
    $('#modal-form-submit').on('click', function(e){
        e.preventDefault();
        
        var data = {
            'user_id[]' : [],
            'drinks_owed[]' : [],
            'food_owed[]' : [],
            'date' : ''
        };

        $(".user:selected").each(function() {
            data['user_id[]'].push($(this).val());
        });

        $(".drinks").each(function() {
            data['drinks_owed[]'].push($(this).val());
        });

        $(".food").each(function() {
            data['food_owed[]'].push($(this).val());
        });
        
        data['date'] = $('input.date').val();
        
        console.log(data);
        
        $.ajax({  
            type: "POST",  
            url: "form_process.php",  
            data: data,
            success: function(data) {  
                $('#addModal').modal('hide');
                $('#addModal').on('hidden', function(){
                    // Show notification
                    $('.notification')
                    .html(data)
                    .hide()
                    .fadeIn(500);   
                });
            }  
        });  

    });
});