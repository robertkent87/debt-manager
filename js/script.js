$(document).ready(function() {
    // Fix modals for mobile
    $('.modal').modalResponsiveFix({
        debug: true
    });
    //$('.modal').touchScroll();
    
    //------------------------------------------------------------------------------------------------------------------
    // Set up date picker in form
    $('#dp3').datepicker();
    
    //------------------------------------------------------------------------------------------------------------------
    // Set up payment form
    var btn_del = $('#btnDel'),
    btn_add = $('#btnAdd');
    btn_del.attr('disabled', 'disabled');
    
    //------------------------------------------------------------------------------------------------------------------
    // Duplicate form fields for additional people
    btn_add.click(function(e) {
        e.preventDefault();
        var num = $('.clonedInput').length,
        newNum = new Number(num + 1),
        newElem = $('#input' + num).clone().attr('id', 'input' + newNum);
            
        newElem.find('input :first').attr('id', 'user_id' + newNum);
        newElem.find('.drinks').attr('id', 'drinks_owed' + newNum).val('');
        newElem.find('.food').attr('id', 'food_owed' + newNum).val('');
        
        // insert the new element after the last "duplicatable" input field
        $('#input' + num).after(newElem);
        btn_del.removeAttr('disabled');
        
        if (newNum == 6) {
            btn_add.attr('disabled', 'disabled');
        }
    });
    
    //------------------------------------------------------------------------------------------------------------------
    // Remove last duplicated form fields                
    btn_del.click(function(e) {
        e.preventDefault();
        var num = $('.clonedInput').length; // how many "duplicatable" input fields we currently have
        
        $('#input' + num).remove(); // remove the last element
        btn_add.removeAttr('disabled');
        
        if (num - 1 == 1) {
            btn_del.attr('disabled', 'disabled');
        }
    });
    
    //------------------------------------------------------------------------------------------------------------------
    // Handle form submission
    $('#modal-form-submit').on('click', function(e) {
        e.preventDefault();
        
        var data = {
            'user_id[]': [],
            'drinks_owed[]': [],
            'food_owed[]': [],
            'date': ''
        },
        error = false;
            
        $(".user:selected").each(function() {
            if ($(this).val() < 1) {
                error = true;
            } else {
                data['user_id[]'].push($(this).val());
            }
        });
        
        $(".drinks").each(function() {
            if (!$(this).val()) {
                error = true;
            } else {
                data['drinks_owed[]'].push($(this).val());
            }
        });
        
        $(".food").each(function() {
            if (!$(this).val()) {
                error = true;
            } else {
                data['food_owed[]'].push($(this).val());
            }
        });
        
        data['date'] = $('input.date').val();
        
        if (error) {
            // Show error message if it isn't already
            if ($('#addModal .alert-error').length == 0) {
                error_string = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please correct any errors in the form.</div>";
                $('#addModal .modal-body').prepend(error_string);
            }
        } else {
            $.ajax({
                type: "POST",
                url: "form_process.php",
                data: data,
                success: function(data) {
                    $('#addModal').modal('hide');
                    $('#addModal').on('hidden', function() {
                        // Show notification
                        $('.notification').html(data).hide().fadeIn(1000);
                        // Update debts
                        $.get('debts.php', function(data) {
                            $('#debts_cont').html(data).hide().fadeIn(1000);
                        });
                        // Update cheevs
                        $.get('cheevos.php', function(data) {
                            $('#cheevos_cont').html(data).hide().fadeIn(1000);
                        });
                    });
                }
            });
        }
    });
});