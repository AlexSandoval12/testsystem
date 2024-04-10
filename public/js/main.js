$(document).ready(function () {
    $('[magnific-popup]').magnificPopup({
        type: 'image'
    });

    // fix multiple modal z-index
    $(document).on('show.bs.modal', '.modal', function () {
        var zIndex = 2040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function () {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            $('[select2]').select2({
                language: 'es'
            });
        }, 0);
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        $('[select2]').select2({
            language: 'es'
        });
    });

    $("[select2]").select2({
        language: 'es',
        matcher: function (params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            keywords = (params.term).split(" ");

            for (var i = 0; i < keywords.length; i++) {
                if (((data.text).toUpperCase()).indexOf((keywords[i]).toUpperCase()) == -1)
                    return null;
            }
            return data;
        }
    });

    $("[select2-group]").select2({
        language: 'es',
        matcher: function (params, data) {
            // If there are no search terms, return all of the data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Skip if there is no 'children' property
            if (typeof data.children === 'undefined') {
                return null;
            }

            // `data.children` contains the actual options that we are matching against
            var filteredChildren = [];
            $.each(data.children, function (idx, child) {
                if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) != -1) {
                    filteredChildren.push(child);
                }
            });
            // If we matched any of the timezone group's children, then set the matched children on the group
            // and return the group object
            if (filteredChildren.length) {
                var modifiedData = $.extend({}, data, true);
                modifiedData.children = filteredChildren;

                // You can return modified objects from here
                // This includes matching the `children` how you want in nested data sets
                return modifiedData;
            }

            // Return `null` if the term should not be displayed
            return null;
        }
    });

    $("[enterprise-select2-placeholder]").select2({
        language: 'es',
        placeholder: 'Todas las Unidades'
    });

    $("[service-invoices-select2-placeholder]").select2({
        language: 'es',
        placeholder: 'Todas los servicios'
    });

    $("[status-select2-placeholder]").select2({
        language: 'es',
        placeholder: 'Seleccione estado'
    });

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $("[input-mask]").inputmask();

    $("[date-mask]").inputmask({
        alias: 'date'
    });

    $("[time-mask]").inputmask({ "mask": "99:99" });
    $("[month-year-mask]").inputmask({ "mask": "99/9999" });
    $("[date-range-mask]").inputmask({ "mask": "99/99/9999 - 99/99/9999" });
    $("[date-range-time-mask]").inputmask({ "mask": "99/99/9999 99:99 - 99/99/9999 99:99" });
    $("[datetime-mask]").inputmask({
        alias: 'datetime'
    });

    $('[percentage-data-mask]').inputmask({
        alias: 'percentage',
    });

    $('[numeric-data-mask]').inputmask({
        alias: 'numeric',
        rightAlign: false,
        digits: 0
    });

    $("[period-data-mask]").inputmask({
        alias: 'decimal',
        groupSeparator: '.',
        radixPoint: ',',
        autoGroup: true,
        allowMinus: false,
        rightAlign: false,
        digits: 0,
        removeMaskOnSubmit: true,
    });

    $("[period-data-mask-decimal]").inputmask({
        alias: 'decimal',
        groupSeparator: '.',
        radixPoint: ',',
        autoGroup: true,
        allowMinus: false,
        rightAlign: true,
        digits: 2,
        removeMaskOnSubmit: true,
    });

    $("[period-data-mask-4-decimal]").inputmask({
        alias: 'decimal',
        groupSeparator: '.',
        radixPoint: ',',
        autoGroup: true,
        allowMinus: false,
        rightAlign: true,
        digits: 4,
        removeMaskOnSubmit: true,
    });

    $("[linea-baja-py-mask]").inputmask({ "mask": "(999) 999-999" });
    $("[celular-py-mask]").inputmask({ "mask": "(9999) 999-999" });

    $("[invoice-purchase-mask]").inputmask({ "mask": "999-999-9999999" });

    // $("[datepicker]").datepicker({
    //     language: "es",
    //     autoclose: true,
    // });

    // $('form').preventDoubleSubmission();

    //$('.selectpicker').selectpicker();
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    // error: function (x, status, error) {
    // if (x.status == 550)
    //     alert("550 Error Message");
    // else if (x.status == "403")
    //     alert("403. Not Authorized");
    // else if (x.status == "500")
    //     alert("500. Internal Server Error");
    // else
    //     alert("Error...");

    //     if (x.status == 403) {
    //         alert("Sorry, your session has expired. Please login again to continue");
    //         window.location.href ="/Account/Login";
    //     }
    //     else {
    //         alert("An error occurred: " + status + "nError: " + error);
    //     }
    // }
});

// fix para que select2 se abra con TAB
$(document).on('focus', '.select2', function (e) {
    if (e.originalEvent) {
        $(this).siblings('select').select2('open');
    }
});

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

// jQuery.fn.bank_account_number = function() {
//     console.log(1);
// };

jQuery.fn.extend({
    bank_account_number: function (debitentity_id, card_number, client_fullname) {
        var value = '';

        if (debitentity_id == 10 && card_number.length >= 8) // REGIONAL
        {
            var value = card_number.substr(-8, 7);
        }
        else if (debitentity_id == 6) // BNF
        {
            var value = card_number.replace(/^0+/, '');
        }
        else if (debitentity_id == 16 && card_number.length >= 8) // VISION
        {
            var value = card_number.substr(-8, 7);
        }
        else if (debitentity_id == 3 && card_number.length >= 10) // ITAU
        {
            var value = card_number.substr(-10, 9);
            // var value = card_number.substr(-10, 9).replace(/^0*/, "");
        }
        else if (debitentity_id == 5 && card_number.length >= 8) // SUDAMERIS
        {
            var value = card_number.substr(-8, 7);
        }
        else if (debitentity_id == 19 && card_number.length >= 8) // ATLAS
        {
            var value = card_number.substr(-8, 7);
        }
        else if (debitentity_id == 31 && card_number.length >= 10) // BBVA
        {
            var value = card_number.substr(-10, 2) + '0' + card_number.substr(-8, 7);
        }
        else if (debitentity_id == 2 && card_number.length >= 11) // CONTINENTAL
        {
            var value = card_number.substr(-11, 2) + card_number.substr(-8, 6) + '0' + card_number.substr(-2, 1);
        }
        else if (debitentity_id == 15) // PANAL
        {
            var value = card_number;
        }
        else if (debitentity_id == 1) // BANCARD
        {
            var value = getInitials(client_fullname).toUpperCase();
        }
        else if (debitentity_id == 14) // CABAL
        {
            var value = card_number;
        }
        else if (debitentity_id == 13) // PROCARD
        {
            var value = card_number;
        }
        else if (debitentity_id == 33) // PROCARD
        {
            var value = card_number.substr(7, 8);
        }

        this.val(value);
    },
});

function getInitials(name) {
    var parts = name.split(' ')
    var initials = ''
    for (var i = 0; i < parts.length; i++) {
        if (parts[i].length > 0 && parts[i] !== '') {
            initials += parts[i][0]
        }
    }
    return initials;
}

function digito_verificador(ruc, baseMax = 11) {
    ruc = typeof ruc === 'string' ? ruc : ruc.toString()
    var resultado = 0
    var index = 0;
    for (var rucIndex = ruc.length - 1; rucIndex >= 0; rucIndex--) {
        resultado += parseInt(ruc[rucIndex]) * (index + 2);
        var r = resultado % baseMax
        index++;
    };
    var verificador = r > 1 ? baseMax - r : 0
    return verificador
}

function CleanLinkLocation(link) {
    link = typeof link === 'string' ? link : link.toString();
    from = link.search("q=");
    end = link.search("&z=");
    middle = link.search("%");
    if (from > 0) {
        toReplace = link.substr(middle, 3);
        link = link.substring(from + 2, end);
        link = link.replace(toReplace, ",");
    }
    return link;
}

String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
}

jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit', function (e) {
        var $form = $(this);

        if ($form.data('submitted') === true) {
            // Previously submitted - don't submit again
            e.preventDefault();
        } else {
            // Mark it so that the next submit can be ignored
            $form.data('submitted', true);
        }
    });

    // Keep chainability
    return this;
};

jQuery.fn.donetyping = function (callback) {
    var _this = $(this);
    var x_timer;
    _this.keyup(function () {
        clearTimeout(x_timer);
        x_timer = setTimeout(clear_timer, 1000);
    });

    function clear_timer() {
        clearTimeout(x_timer);
        callback.call(_this);
    }
};

function redirect(url) {
    document.location.href = url;
    return false;
}

function laravelErrorMessages(data) {
    // var errors = data.responseJSON;
    // console.log(errors);

    var response = JSON.parse(data.responseText);

    var printError = '';
    // var errorString = '<ul>';

    $.each(response.errors, function (key, value) {
        // errorString += '<li>' + value + '</li>';
        $.each(value, function (key1, value1) {
            printError += value1.capitalize() + "\n";
        });
    });
    // errorString += '</ul>';
    if (printError) {
        title = "SISTEMA";
        text = printError;
        icon = "info";
    }
    else {
        title = "PAGINA NO DISPONIBLE";
        text = "Favor contacte al área de Sistemas";
        icon = "warning";
    }
    swal({
        title: title,
        text: text,
        icon: icon,
        button: "OK",
    });
    // alert(printError);
}

function key_delay(callback, ms) {
    var timer = 0;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            callback.apply(context, args);
        }, ms || 0);
    };
}

// Dropzone.prototype.defaultOptions.dictDefaultMessage = "Arrastre los archivos aquí o haga click para cargar.";
// Dropzone.prototype.defaultOptions.dictFallbackMessage = "Su navegador no admite la carga de archivos arrastrados.";
// Dropzone.prototype.defaultOptions.dictFallbackText = "Utilice el siguiente formulario alternativo para cargar sus archivos como en los viejos tiempos.";
// Dropzone.prototype.defaultOptions.dictFileTooBig = "El archivo es muy grande ({{filesize}}MiB). Tamaño máximo: {{maxFilesize}}MiB.";
// Dropzone.prototype.defaultOptions.dictInvalidFileType = "No puede cargar archivos de este tipo.";
// Dropzone.prototype.defaultOptions.dictResponseError = "El servidor respondió con el código {{statusCode}}.";
// Dropzone.prototype.defaultOptions.dictCancelUpload = "Cancelar carga";
// Dropzone.prototype.defaultOptions.dictCancelUploadConfirmation = "¿Seguro que quieres cancelar esta carga?";
// Dropzone.prototype.defaultOptions.dictRemoveFile = "Remover archivo";
// Dropzone.prototype.defaultOptions.dictMaxFilesExceeded = "No puedes cargar mas archivos.";
