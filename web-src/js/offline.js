var innSearch = $('#inn-search'),
    result = $('#inn-search-result'),
    phone = $('#phone');

innSearch.on("input", checkInn);

phone.on("input", validatePhone);
if (innSearch.val().length === 10) {
    checkInn();
}

function isNumberKey(e){
    var charCode = (e.which) ? e.which : event.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }

    return innSearch.val().length < 10;
}

function checkInn() {
    var routeWithInn = Routing.generate('offline_dashboard', { inn: innSearch.val()}, true);

    if (innSearch.val().length !== 10) {
        return;
    }

    if (location.href !== routeWithInn) {
        location.href = routeWithInn;
    }

    result.show();

    if (phone.val().length > 0) {
        validatePhone();
    }
}

function validatePhone() {
    $.post(Routing.generate('offline_phone_checker'), {'phone': phone.val()})
        .done(function (data) {
            if (data === '0') {
                $('#form_submit').attr('disabled', false);

                if (phone.parent().hasClass('has-error')) {
                    phone.parent().removeClass('has-error');
                }
            } else if (false === phone.parent().hasClass('has-error')) {
                phone.parent().addClass('has-error');
                $('#form_submit').attr('disabled', true);
            }
        });
}
