var csrfToken = $('meta[name="csrf-token"]').attr('content');

$('input[id]:not([name])', '.wrapper').attr('name', function() {
    if (this.id != undefined) { return this.id; }
});
$('input[type=checkbox][id]:not([name]), input[type=radio][id]:not([name])', '.wrapper').attr('name', function() {
    if (this.id != undefined) { return this.id; }
});
$('select[id]:not([name])', '.wrapper').attr('name', function() {
    if (this.id != undefined) { return this.id; }
});
$('textarea[id]:not([name])', '.wrapper').attr('name', function() {
    if (this.id != undefined) { return this.id; }
});

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();

    $.each(a, function() {
        var value = this.value || '';

        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(value);
        } else {
            o[this.name] = value;
        }
    });
    return o;
};

function decimalNumber(number, before=3, after=3){
	var result="";
	number	= number.toString().replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g,'');
	number	= number.toString().replace('-', '');
	arrNumber = number.split('.');
	if(arrNumber[0] != undefined && arrNumber[0].length >before ){
		result += parseFloat(arrNumber[0].slice(0, -1)).toString();

	}else {
		var parseResult = parseFloat(arrNumber[0]).toString()
		result += isNaN(parseResult) ? 0: parseResult;
	}

	if(arrNumber[1] != undefined && arrNumber[1].length >after ){

		result += "."+arrNumber[1].slice(0, -1);

	}else if(arrNumber[1] != undefined) {

		result += "."+arrNumber[1];
	}
	if(result == "" || result==null){
		result = 0;
	}
	return result;
}

function blockUI(selectorObj, block, msg, width) {
    block = (block == undefined ? true : block);
    msg = '<div class="blockUI-ajax-loading"></div><div class="blockUI-text-loading">' + (msg == undefined ? 'Please wait' : msg) + ' ...</div>';
    width = (width == undefined ? '185px' : width);

    var obj = $(selectorObj);
    if (block) {
        obj.block({
            message: msg,
            css: {
                border: '3px solid #3C354E',
                padding: '10px',
                textAlign: 'left',
                color: '#3C354E',
                'border-radius': '3px',
                width: width,
                backgroundColor: '#EFF3F6',
                position: "fixed"
            },
        });

    } else {
        obj.unblock();
    }
}

function ajax(ajaxProperty) {
    /*
    	ajaxProperty
    		url				: required
    		postData 		: optional
    		dataType		: optional (default: json)
    		selectorBlock	: required
    		selectorAlert	: optional (default: mainAlert)
    		success			: required
    		beforeSend		: optional (additional function)
    		error			: optional (additional function)
    */
    ajaxProperty.dataType = (ajaxProperty.dataType == undefined ? 'json' : ajaxProperty.dataType);
    ajaxProperty.processData = (ajaxProperty.processData == undefined ? true : ajaxProperty.processData);
    ajaxProperty.selectorAlert = (ajaxProperty.selectorAlert == undefined ? '#mainAlert' : ajaxProperty.selectorAlert);
    ajaxProperty.selectorBlock = (ajaxProperty.selectorBlock == undefined ? 'body' : ajaxProperty.selectorBlock);
    ajaxProperty.type = (ajaxProperty.type == undefined ? 'post' : ajaxProperty.type);
    ajaxProperty.blocking = (ajaxProperty.blocking == undefined ? 'all' : ajaxProperty.blocking);
    ajaxProperty.loading = (ajaxProperty.loading == undefined) ? true : ajaxProperty.loading;
    ajaxProperty.alert = (ajaxProperty.alert == undefined) ? true : false;
    ajaxProperty.async = true;
    if (ajaxProperty.postData == undefined) { ajaxProperty.postData = new Object(); }

    obj = {};

    obj = {
        type: ajaxProperty.type,
        url: ajaxProperty.url,
        data: ajaxProperty.postData,
        dataType: ajaxProperty.dataType,
        processData: ajaxProperty.processData,
        contentType: ajaxProperty.contentType,
        async: ajaxProperty.async,
        cache: false,
        beforeSend: function(xhr) {
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
            if (ajaxProperty.loading) {
                blockUI(ajaxProperty.selectorBlock);
            }
            if (ajaxProperty.beforeSend != undefined) { ajaxProperty.beforeSend(xhr); }
        },
        success: function(ret) {
            if (ret.expired != undefined) {
                window.scrollTo(0, 0);
                var timeout = 5;
                setTimeout(function() {
                    clearInterval();
                    location.reload();
                }, 5000);
                swal("Error!", ret.msg + ' (5 second)', "error");

                $('#lblTimeout').html(timeout);
                setInterval(function() {
                    timeout -= 1;
                    $('#lblTimeout').html(timeout);
                }, 1000);
                return;
            }
            if (ret.result != undefined) {
                if (ret.result == false) {
                    window.scrollTo(0, 0);
                    if (ret.msg.includes('SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry')) {
                        ret.msg = 'Kode Sudah Terdaftar'
                    }

                    if (ret.msg.includes('SQLSTATE[23000]: Integrity constraint violation: 1451')) {
                        ret.msg = 'Kode Tidak Bisa Dirubah'
                    }

                    // swal("Alert!", ret.msg, "error");
                    if (ajaxProperty.error != undefined) {
                        ajaxProperty.error(ret);
                    }
                    return;
                }
            }

            if (ajaxProperty.alert) {
                loadAlert(ret.msg, false);
            }

            ajaxProperty.success(ret);
        },
        error: function(jqXHR, textStatus, errorThrown) {

            if (ajaxProperty.hasOwnProperty('error') && typeof ajaxProperty.error === 'function') {
                ret = jqXHR.hasOwnProperty('responseJSON')
                    ? jqXHR.responseJSON
                    : jqXHR.responseText ? JSON.parse(jqXHR.responseText) : {};

                if (jqXHR.status === 429) {
                    ret.message = "Too many login attempts. Please try again in one second."
                }

                return ajaxProperty.error(ret);
            }

            switch (jqXHR.status) {
                case 302:
                    location.reload();
                    break;
            }
            window.scrollTo(0, 0);

            if (ajaxProperty.alert) {
                loadAlert(jqXHR.responseJSON.msg)
            }
        },
        complete: function(jqXHR, textStatus) {
            if (ajaxProperty.loading) {
                blockUI(ajaxProperty.selectorBlock, false);
            }
        },
    };

    if (ajaxProperty.contentType == undefined) {
        delete obj.contentType;
    } else {
        obj.contentType = ajaxProperty.contentType;
    }

    return $.ajax(obj);
}

function processingform(form) {
    var elements = form.elements;
    for (var i = 0, len = elements.length; i < len; ++i) {
        elements[i].disabled = true;
    }
}
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

function unprocessingform(form) {
    var elements = form.elements;
    for (var i = 0, len = elements.length; i < len; ++i) {
        elements[i].disabled = false;
    }
}

const toTitleCase = (phrase) => {
    return phrase
      .toLowerCase()
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };

function showMoney(strNumber, minus) {
    minus = minus == undefined ? false : minus;

    //strNumber	= (isNaN(strNumber) || strNumber=="") ? 0 : strNumber;
    strNumber = strNumber == undefined ? 0 : strNumber;
    strNumber = strNumber == "" ? 0 : strNumber;
    strNumber = strNumber.toString().replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    if (!minus) strNumber = strNumber.toString().replace('-', '');

    arrKoma = strNumber.split('.');
    arrMinus = arrKoma[0].split('-');
    if (arrMinus[1] == undefined) {
        nilai = arrKoma[0].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    } else {
        nilai = arrMinus[1].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    }
    nilai = parseFloat(nilai).toString();
    nilai = isNaN(nilai) ? 0 : nilai;

    panjang = nilai.length;
    output = '';
    j = 0;
    for (i = panjang; i > 0; i--) {
        j = j + 1;
        if (((j % 3) == 1) && (j != 1)) {
            output = nilai.substr(i - 1, 1) + ',' + output;
        } else {
            output = nilai.substr(i - 1, 1) + output;
        }
    }
    if (arrKoma[1] == undefined) {
        //objText.value = output;
    } else {
        //objText.value = output + "." +arrvalue[1];
        arrKoma[1] = replaceAll('-', '', arrKoma[1]);
        output = output + '.' + arrKoma[1].substring(0, 4);
    }
    if (arrMinus[1] == undefined) {
        return output;
    } else {
        return '-' + output;
    }
}

function showMoneyDot(strNumber, minus) {
    minus = minus == undefined ? false : minus;

    //strNumber	= (isNaN(strNumber) || strNumber=="") ? 0 : strNumber;
    strNumber = strNumber == undefined ? 0 : strNumber;
    strNumber = strNumber == "" ? 0 : strNumber;
    strNumber = strNumber.toString().replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    if (!minus) strNumber = strNumber.toString().replace('-', '');

    arrKoma = strNumber.split(',');
    arrMinus = arrKoma[0].split('-');
    if (arrMinus[1] == undefined) {
        nilai = arrKoma[0].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    } else {
        nilai = arrMinus[1].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\:'/@#~,?\<>?|`?\]\[]/g, '');
    }
    nilai = parseFloat(nilai).toString();
    nilai = isNaN(nilai) ? 0 : nilai;

    panjang = nilai.length;
    output = '';
    j = 0;
    for (i = panjang; i > 0; i--) {
        j = j + 1;
        if (((j % 3) == 1) && (j != 1)) {
            output = nilai.substr(i - 1, 1) + '.' + output;
        } else {
            output = nilai.substr(i - 1, 1) + output;
        }
    }
    if (arrKoma[1] == undefined) {
        //objText.value = output;
    } else {
        //objText.value = output + "." +arrvalue[1];
        arrKoma[1] = replaceAll('-', '', arrKoma[1]);
        output = output + ',' + arrKoma[1].substring(0, 4);
    }
    if (arrMinus[1] == undefined) {
        return output;
    } else {
        return '-' + output;
    }
}

function replaceAll(find, replace, str) {
    if (str == null) {
        return '';
    } else {
        return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
    }
}

function xssDecode(val) {
    return $("<div/>").html(val).text();
}

function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
}

function moneyEntry(objInput) {
    objInput.value = showMoney(objInput.value);

    if (parseFloat(convertMoney(objInput.value)) > 99999999999999) { //99.999.999.999.999
        validation = convertMoney(objInput.value);
        var arrKoma = validation.split('.');
        validation = arrKoma[0].substring(0, validation.length - 1);
        if (arrKoma[1] != undefined) {
            validation += arrKoma[1];
        }
        objInput.value = showMoney(validation);
    }
}

function convertMoney(strNumber) {

    if (strNumber == "") {
        return ''
    }
    objReplace = replaceAll(',', '', strNumber);
    if (objReplace == "") {
        objReplace = 0;
    }

    return objReplace;
}

function formatNumber(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function loadAlert(msg, error = true) {
    $("#alertBox").removeClass("alert-danger")
        .removeClass("alert-success");

    $(".invalid-feedback").html("");
    $(".is-invalid").removeClass("is-invalid");
    window.scrollTo(0, 0);

    if (error) {
        if (typeof msg == 'object') {
            for (const field in msg) {
                elementId = field.replaceAll('.', '-'); // replace dot from validation message
                _this = $("#" + elementId)
                if (_this.attr("type") == "file") {
                    _this.parent(".label-input-file").addClass("is-invalid")


                    if(_this.parent(".label-input-file").siblings(".invalid-feedback").length == 0){
                        _this.parent(".label-input-file").after( `<div class="invalid-feedback">
                        </div>` );
                    }
                    _this.parent(".label-input-file").siblings(".invalid-feedback").html(msg[field])

                } else if (_this.attr("type") == "radio") {
                    _this.closest(".form-check").addClass("is-invalid")


                    if(_this.closest(".form-check").find('.invalid-feedback').length == 0){
                        _this.closest(".form-check").after( `<div class="invalid-feedback">
                        </div>`);
                    }
                    _this.closest(".form-check").find('.invalid-feedback').html(msg[field])
                } else {


                    if(_this.hasClass("select2")){
                        _this.siblings(".select2-container").find(".select2-selection--single").addClass("is-invalid")

                        if(_this.siblings(".invalid-feedback").length == 0){
                            _this.siblings(".select2-container").after( `<div class="invalid-feedback">

                            ${msg[field]}

                            </div>`);
                        }else{
                            _this.siblings(".select2-container").siblings(".invalid-feedback").html(msg[field])

                        }
                    }

                    else if(_this.parents("td").length != 0 ){
                        _this.parents("td").addClass("is-invalid")
                        _this.addClass("is-invalid")
                        if(_this.parents("td").find(".invalid-feedback").length == 0){
                            _this.parents("td").append( `<div class="invalid-feedback">

                            ${msg[field]}

                            </div>`);
                        }else{
                            _this.parents("td").find(".invalid-feedback").html(msg[field])

                        }
                    }

                    else if(_this.siblings(".invalid-feedback").length == 0 && ! _this.hasClass("select2")){
                        _this.after( `<div class="invalid-feedback">
                        ${msg[field]}
                        </div>`);
                        _this.addClass("is-invalid")

                    }

                    else{
                        _this.addClass("is-invalid")
                        _this.siblings(".invalid-feedback").html(msg[field])
                    }

                }
            }
            $("#alertBox").html("Error : Please Fix The Red Field")
            $("#alertBox").addClass("alert alert-danger");
            $("#alertBox").show()
        } else {
            $("#alertBox").html(msg)
            $("#alertBox").addClass("alert alert-danger");
            $("#alertBox").show()
        }
    } else {
        if(msg != ""){
            $("#alertBox").html(msg)
            $("#alertBox").addClass("alert alert-success")
            $("#alertBox").show()
        }

    }



}

function printDiv(divid, title, layout='portrait') {
    var contents = document.getElementById(divid).innerHTML;
    var frame1 = document.createElement('iframe');

    frame1.name = "frame1";
    frame1.style.position = "absolute";
    frame1.style.top = "-1000000px";
    document.body.appendChild(frame1);

    var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;

    frameDoc.document.open();
    frameDoc.document.write(`<html><head><title>${title}</title>`);
    frameDoc.document.write('</head><body>');
    frameDoc.document.write(contents);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();

    if (layout === 'landscape') {
        var css = '@page { size: landscape; }',
          head = document.head || document.getElementsByTagName('head')[0],
          style = document.createElement('style');
    }

    style.type = 'text/css';
    style.media = 'print';

    if (style.styleSheet){
        style.styleSheet.cssText = css;
    } else {
        style.appendChild(document.createTextNode(css));
    }

    head.appendChild(style);

    setTimeout(function () {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        document.body.removeChild(frame1);
    }, 500);

    return false;
}

function getTimeFromStringDate(date, format){
    format = format ? format : 'DD-MM-YYYY';
    return moment(date, format, 'id').valueOf()
}
