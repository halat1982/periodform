<?php
$activForm = \COption::GetOptionString("extra_setting","pform_enable_form_showing");
$titleForm = \COption::GetOptionString("extra_setting","pform_title_form", "Свяжитесь с нами");
$firstTime = \COption::GetOptionString("extra_setting","pform_time_1", "60");
$secondTime = \COption::GetOptionString("extra_setting","pform_time_2", "2000");
//\Extra\Dump::p($_COOKIE);
?>
<?php
if($activForm == "Y" && !isset($_COOKIE["FORM_SENDED"])){ ?>
    <div id="period_form">
        <form name="periodform" method="post" action="/local/ajax/periodform.php">
            <div class="form-result-new-fields intec-ui-form-fields" data-role="fields">
                <label class="form-result-new-field intec-ui-form-field intec-ui-form-field-required" data-focused="false"
                       data-role="field">                                                            <span
                        class="form-result-new-field-title intec-ui-form-field-title" data-role="field.title">
                                    Ваше имя                                </span> <sup>*</sup>
                    <span class="form-result-new-field-content intec-ui-form-field-content" data-role="field.content">

                                <input type="text"
                                       class="inputtext intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-3 intec-ui-size-4"
                                       name="pf_name" value="">
                    <span class="pf_name error">Ошибка</span>
                    </span>
                </label><br>
                <label class="form-result-new-field intec-ui-form-field intec-ui-form-field-required"
                       data-focused="false" data-role="field">                                                            <span
                        class="form-result-new-field-title intec-ui-form-field-title" data-role="field.title">
                                    Ваш email                                </span><sup>*</sup>
                    <span class="form-result-new-field-content intec-ui-form-field-content" data-role="field.content">

                                <input type="text"
                                       class="inputtext intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-3 intec-ui-size-4"
                                       name="pf_email" value="">
                    <span class="pf_email error">Ошибка</span>
                    </span>
                </label>

            </div>
            <div class="form-result-new-consent">
                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                    <input type="checkbox" name="pf_licenses" value="Y" checked="checked"> <span class="intec-ui-part-selector"></span>
                    <span class="form-result-new-consent-text intec-ui-part-content">
                            Я согласен(а) на <a href="/company/consent/"
                                                target="_blank">обработку персональных данных</a>
                    <div class="pf_licenses error">Необходимо согласие</div></span>
                </label>
            </div>
        </form>
    </div>
    <div id="success_period_form">

    </div>


    <style>
        #popup-window-content-period_form_id {
            flex: initial;
        }

        form .error {
            display:none;
            color: red;
        }
    </style>
    <script>
        var successPeriod = new BX.PopupWindow(
            "success_period",
            null,
            {
                content: BX( 'success_period_form'),
                closeIcon: {right: "20px", top: "10px" },
                titleBar: {content: BX.create("span", {html: '<b>Данные отправлены, с вами свяжется менеджер</b>', 'props': {'className': 'access-title-bar'}})},
                zIndex: 0,
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: false}
            });





        function checkFieldsPeriodForm(form){
            // console.log(form.serializeArray());
            let notErrors = true;
            let licensesChecked = false;
            let fields = form.serializeArray();
            form.find('.error').text('').css('display', 'none');
            for(var fieldData in fields){

                if(fields[fieldData].name == "pf_name"){
                    if(fields[fieldData].value == ""){
                        form.find('.error.'+fields[fieldData].name).text('Заполните поле').show();
                        notErrors = false;
                    } else {
                        form.find('.error.'+fields[fieldData].name).hide();
                    }
                } else if(fields[fieldData].name == "pf_email"){
                    if(fields[fieldData].value == ""){

                        form.find('.error.'+fields[fieldData].name).text('Заполните поле').show();
                        notErrors = false;
                    } else {
                        form.find('.error.'+fields[fieldData].name).hide();
                    }
                } else if(fields[fieldData].name == "pf_licenses" && fields[fieldData].value == 'Y'){
                    licensesChecked = true;
                }
            }

            if(!licensesChecked){
                form.find('.error.pf_licenses').text('Обязательно согласие').show();
                notErrors = false;
            }

            return notErrors;
        }

        var periodForm = new BX.PopupWindow(
            "period_form_id",
            null,
            {
                content: BX('period_form'),
                closeIcon: {right: "20px", top: "10px"},
                titleBar: {
                    content: BX.create("span", {
                        html: '<?=$titleForm?>',
                        'props': {'className': 'access-title-bar'}
                    })
                },
                zIndex: 0,
                offsetLeft: 0,
                offsetTop: 0,
                draggable: {restrict: false},
                buttons: [
                    new BX.PopupWindowButton({
                        text: "Отправить",
                        className: "form-result-new-submit-button intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current",
                        events: {
                            click: function () {
                                let form = $('#period_form form');
                                let notErrors = checkFieldsPeriodForm(form);
                                if(notErrors){
                                    $.ajax({
                                        type: 'post',
                                        url: '/local/ajax/periodform.php',
                                        data: form.serialize(),
                                        success: function(jsonResponse) {
                                            data = JSON.parse(jsonResponse);
                                            if(data.type == "success"){
                                                periodForm.close();
                                                successPeriod.show();
                                            }
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                }
                                //
                            }
                        }
                    }),
                ]
            });
        //document.cookie = "FORM_SENDED=TRUE; max-age=31556926";
        function getCookie(name) {
            var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
            //console.log("MATCHES",matches);
            return matches ? decodeURIComponent(matches[1]) : undefined;
        }


        function getTimeRemaining(endtime) {
            var t = Date.parse(endtime) - Date.parse(new Date());
            return t;
        }

        function initializeClock(endtime) {
            function updateClock() {
                var t = getTimeRemaining(endtime);
                console.log(t);
                if (t <= 0) {
                    clearInterval(timeinterval);
                    periodForm.show();
                    let secondTime = new Date(Date.parse(new Date()) + <?=$secondTime?> * 1000); //(new Date())*1000
                    document.cookie = "SECOND_TIME_SHOW_FORM="+secondTime+"; max-age=31556926; path=/";
                }
            }
            updateClock();
            var timeinterval = setInterval(updateClock, 1000);
        }
        if(getCookie("SECOND_TIME_SHOW_FORM")){
            console.log("SECOND_TIME_SHOW_FORM");
            let secondTime = getCookie("SECOND_TIME_SHOW_FORM");
            var deadline = new Date(Date.parse(secondTime));
        } else {
            var deadline = new Date(Date.parse(new Date()) + <?=$firstTime?> * 1000);
        }
        // for endless timer


        initializeClock(deadline);
    </script>


<?php }?>
