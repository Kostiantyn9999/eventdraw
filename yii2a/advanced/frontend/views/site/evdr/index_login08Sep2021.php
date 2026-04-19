<?php

/* @var $this yii\web\View */

use common\models\Stencil;
use common\models\UserTemplates;
use yii\helpers\Html;
use yii\web\View;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

$this->title = 'Event Draw';



$userStencilXML = '';
$stencils = \common\models\Stencil::getUserStencils(Yii::$app->user->identity->id);

$arrlength = count($stencils);
for($x = 0; $x < $arrlength; $x++) {
 if ($stencils[$x]->isMasterStencil) {
        $userStencilXML =$userStencilXML .';M' . $stencils[$x]->stencilXML;
    }
    else {
        $userStencilXML =$userStencilXML .';U' . $stencils[$x]->stencilXML;
    }
}


// set last login name and increase nRelease
Yii::$app->user->identity->setLastLogin();

//add new record to UserSession
\common\models\UserSessions::addUserSession(Yii::$app->user->identity->id);

//get latest release HTML code
$lastReleaseHTML = \common\models\Apprelease::getLastReleaseHTML(Yii::$app->user->identity->id);


$tmplInfo = \common\models\Template::getUserTemplates(Yii::$app->user->identity->id);

$tmplNames = $tmplInfo["tmplNames"];
$userClientName = $tmplInfo["clientName"];

?>
<script type="text/javascript">
window.variableUserName = "<?php echo Yii::$app->user->identity->userfullname; ?>" ;
window.variableUserID = "<?php echo Yii::$app->user->identity->id; ?>" ;
window.variableUserNRelease = "<?php echo Yii::$app->user->identity->nrelease; ?>" ;

window.variableIsPayment = "<?php echo Yii::$app->user->identity->getPaymentStatus(); ?>" ;


window.variableUserStencil = "<?php echo $userStencilXML; ?>" ;
window.variableUserIsAdmin = "<?php echo Yii::$app->user->identity->userIsAdmin ?>" ;
window.variableUserIsSupport = "<?php echo Yii::$app->user->identity->userIsSupport ?>" ;
window.variableUserShowMaxCapPlans = "<?php echo Yii::$app->user->identity->getClientShowMaxCapPlans() ?>" ;
window.variableUserAllowSaveCloud = "<?php echo Yii::$app->user->identity->getClientAllowSaveCloud() ?>" ;
window.variableUserAllowFavStencils = "<?php echo Yii::$app->user->identity->getClientAllowFavStencils() ?>" ;
window.variableUserIsCompanyAdmin = "<?php echo Yii::$app->user->identity->company_admin ?>" ;

window.variableUserAllow3D = "<?php echo Yii::$app->user->identity->getClientAllow3D() ?>" ;

window.variableUserAllowImportPDF = "<?php echo Yii::$app->user->identity->getClientAllowImportPDF() ?>" ;
window.variableUserType = "<?php echo Yii::$app->user->identity->userType ?>" ;
window.variableUserTotalSessions = "<?php echo Yii::$app->user->identity->totSession ?>" ;


</script>

<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=5,IE=9"/><![endif]-->

<script type="text/javascript">
    window.tableNumArray = [];
    window.guestIdLast = 0;
    window.table_design_array = [];
</script>

<!DOCTYPE html>
<html>
<head>
    <title>Eventdraw - Event Floor Plan & Diagramming Software</title>

    <div
            class='hidden'
            data-templates='<?= $tmplNames ?>'
            data-splash='<?= $lastReleaseHTML ?>'
            data-clientname='<?= $userClientName ?>'

    ></div>

    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta
        name="Description"
        content=""
    />
    <meta
        name="Keywords"
        content="diagram, online, flow chart, flowchart maker, uml, erd"
    />
    <meta
        itemprop="name"
        content="Eventdraw - Event Floor Plan & Diagramming Software"
    />
    <meta
        itemprop="description"
        content=""
    />
    <meta
        itemprop="image"
        content="https://lh4.googleusercontent.com/-cLKEldMbT_E/Tx8qXDuw6eI/AAAAAAAAAAs/Ke0pnlk8Gpg/w500-h344-k/BPMN%2Bdiagram%2Brc2f.png"
    />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
    />
    <meta name="msapplication-config" content="images/browserconfig.xml" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="theme-color" content="#d89000" />


    <script type="text/javascript">
        console.log("Load Shapes Integration Loaded");

        var newurl = window.location.href;
        var reload = false;

        if (reload) {
            window.location = newurl;
        }
    </script>
    <script type="text/javascript">
        console.log("Init Load shapes");

        function loadCustomButton(url) {
            var addCustomLibraryButton = document.createElement("button");
            addCustomLibraryButton.className = "addCustomLibraryButton";
            var addCustomLibraryButtonText = document.createTextNode("Load Shapes");
            addCustomLibraryButton.appendChild(addCustomLibraryButtonText);
            addCustomLibraryButton.addEventListener("click", function() {
                var currentUrl = window.location.href;
                currentUrl += "&clibs=U" + encodeURI(url);
                window.location = currentUrl;
            });
            document.body.appendChild(addCustomLibraryButton);
        }

        function removeOverlay() {
            if (document.getElementById("customOverlay")) {
                setTimeout(document.getElementById("customOverlay").remove(), 2000);
            }
        }

    </script>
    <style type="text/css">
        .addCustomLibraryButton {
            position: absolute;
            top: 4%;
            right: 1%;
            z-index: 10000;
            background: #ddd;
            padding: 5px;
            border: none;
            border-radius: 5px;
        }

        .addCustomLibraryButton:hover {
            cursor: pointer;
        }

        #customOverlay {
            background: #fff;
            z-index: 1000000;
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
        }

    </style>

    <script type="text/javascript">

        var urlParams = (function() {
            var result = new Object();
            var params = window.location.search.slice(1).split("&");

            for (var i = 0; i < params.length; i++) {
                idx = params[i].indexOf("=");

                if (idx > 0) {
                    result[params[i].substring(0, idx)] = params[i].substring(idx + 1);
                }
            }

            return result;
        })();

        // Forces CDN caches by passing URL parameters via URL hash
        if (
            window.location.hash != null &&
            window.location.hash.substring(0, 2) == "#P"
        ) {
            try {
                urlParams = JSON.parse(
                    decodeURIComponent(window.location.hash.substring(2))
                );

                if (urlParams.hash != null) {
                    window.location.hash = urlParams.hash;
                }
            } catch (e) {
                // ignore
            }
        }


        /**
         * Adds meta tags with application name (depends on offline URL parameter)
         */
        (function() {
            function addMeta(name, content) {
                try {
                    var s = document.createElement("meta");
                    s.setAttribute("name", name);
                    s.setAttribute("content", content);

                    var t = document.getElementsByTagName("meta")[0];
                    t.parentNode.insertBefore(s, t);
                } catch (e) {
                    // ignore
                }
            }

            var name = "Eventdraw";

            if (urlParams["offline"] === "1") {
                name += " app";
            }

            addMeta("apple-mobile-web-app-title", name);
            addMeta("application-name", name);
        })();
    </script>
    <link
        rel="chrome-webstore-item"
        href="https://chrome.google.com/webstore/detail/plgmlhohecdddhbmmkncjdmlhcmaachm"
    />
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="images/apple-touch-icon.png"
    />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="images/favicon-32x32.png"
    />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="images/favicon-16x16.png"
    />
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#d89000" />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Roboto:400,700"
    />
    <link rel="stylesheet" type="text/css" href="styles/grapheditor.css" />

    <link rel="canonical" href="https://www.eventdraw.com" />
    <link rel="manifest" href="images/manifest.json" />
    <link rel="shortcut icon" href="favicon.ico" />
    <link href = "style.css" rel ="stylesheet">
    <style type="text/css">
        body {
            overflow: hidden;
        }
        div.picker {
            z-index: 10007;
        }
        .geSidebarContainer .geTitle input {
            font-size: 8pt;
            color: #606060;
        }
        .geBlock {
            z-index: -3;
            margin: 100px;
            margin-top: 40px;
            margin-bottom: 30px;
            padding: 20px;
        }
        .geBlock h1,
        .geBlock h2 {
            margin-top: 0px;
            padding-top: 0px;
        }
        .geEditor ::-webkit-scrollbar {
            width: 14px;
            height: 14px;
        }
        .geEditor ::-webkit-scrollbar-track {
            background-clip: padding-box;
            border: solid transparent;
            border-width: 1px;
        }
        .geEditor ::-webkit-scrollbar-corner {
            background-color: transparent;
        }
        .geEditor ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.1);
            background-clip: padding-box;
            border: solid transparent;
            border-radius: 10px;
        }
        .geEditor ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.4);
        }
        .geTemplate {
            border: 1px solid transparent;
            display: inline-block;
            _display: inline;
            vertical-align: top;
            border-radius: 3px;
            overflow: hidden;
            font-size: 14pt;
            cursor: pointer;
            margin: 5px;
        }
        .geFooterContainer div.geSocialFooter a {
            display: inline;
            padding: 0px;
        }
        .geFooterContainer div.geSocialFooter a img {
            margin-top: 10px;
            opacity: 0.5;
        }
        .geFooterContainer div.geSocialFooter a img:hover {
            opacity: 1;
        }
        .geFooterContainer > div#geFooter > img {
            opacity: 0.5;
            border: 1px solid transparent;
            cusor: pointer;
            margin-top: 3px;
            margin-right: 6px;
            position: absolute;
            right: 4px;
            top: 12px;
            padding: 1px;
            cursor: pointer;
        }
        .geFooterContainer > div#geFooter > img:hover {
            opacity: 1;
        }
    </style>
    <!-- Workaround for binary XHR in IE 9/10, see App.loadUrl -->
    <!--[if (IE 9)|(IE 10)]><!-->
    <script type="text/vbscript">
      Function mxUtilsBinaryToArray(Binary)
      	Dim i
      	ReDim byteArray(LenB(Binary))
      	For i = 1 To LenB(Binary)
      		byteArray(i-1) = AscB(MidB(Binary, i, 1))
      	Next
      	mxUtilsBinaryToArray = byteArray
      End Function
    </script>
    <!--<![endif]-->
    <script type="text/javascript">
        /**
         * Synchronously adds scripts to the page.
         */
        function mxscript(src, onLoad, id, dataAppKey, noWrite) {
            if (onLoad != null || noWrite) {
                var s = document.createElement("script");
                s.setAttribute("type", "text/javascript");
                s.setAttribute("src", src);
                var r = false;

                if (id != null) {
                    s.setAttribute("id", id);
                }

                if (dataAppKey != null) {
                    s.setAttribute("data-app-key", dataAppKey);
                }

                if (onLoad != null) {
                    s.onload = s.onreadystatechange = function() {
                        if (!r && (!this.readyState || this.readyState == "complete")) {
                            r = true;
                            onLoad();
                        }
                    };
                }

                var t = document.getElementsByTagName("script")[0];
                t.parentNode.insertBefore(s, t);
            } else {
                document.write(
                    '<script src="' +
                    src +
                    '"' +
                    (id != null ? ' id="' + id + '" ' : "") +
                    (dataAppKey != null
                        ? ' data-app-key="' + dataAppKey + '" '
                        : "") +
                    "></scr" +
                    "ipt>"
                );
            }
        }

        /**
         * Asynchronously adds scripts to the page.
         */
        function mxinclude(src) {
            var g = document.createElement("script");
            g.type = "text/javascript";
            g.async = true;
            g.src = src;
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(g, s);
        }

        // Checks for local storage
        var isLocalStorage = false;

        try {
            isLocalStorage =
                urlParams["local"] != "1" && typeof localStorage != "undefined";
        } catch (e) {
            // ignored
        }

        var t0 = new Date();

            mxscript("js/app_594m.min.js");

        // Adds basic error handling
        window.onerror = function() {
            var status = document.getElementById("geStatus");

            if (status != null) {
                status.innerHTML = "Page could not be loaded. Please try refreshing.";
            }
        };
    </script>
</head>
<body class="geEditor">

<div class="main">
    <div id="geInfo">
        <div class="geBlock" style="text-align:center;min-width:50%;">
            <h1>Eventdraw Software</h1>
            <p>
            </p>
            <h2 id="geStatus">Loading...</h2>
            <p>
                Please ensure JavaScript is enabled.
            </p>
        </div>
    </div>
    <?php
    Yii::$app->assetManager->bundles = [
        'yii\bootstrap\BootstrapPluginAsset' => false,
        'yii\bootstrap\BootstrapAsset' => false,

    ];
    ?>
    <script type="text/javascript">
        /**
         * Main
         */
        App.main();
    </script>
    <?php

    ?>
</div>
<!-- close main div -->


<script type="text/javascript">

    function toolBarFunction() {
        console.log('Tool Bar CallBack');
        jQuery(document).on('click', '#modify_table_guest_allocation', function(){
            //jQuery('#guest_allocation_first').show();
        });

        jQuery(document).on('click', '#guest_allocation_first_cancel', function(){
            jQuery('#guest_allocation_first').hide();
            jQuery('#modify_table_guest_allocation').removeClass('active')
            // jQuery('#modify_table_guest_allocation').trigger('click');
        });


        jQuery(document).on('click', '#guest_allocation_second_cancel', function(){
            jQuery('#guest_allocation_second').hide();
            jQuery('#modify_table_guest_allocation').removeClass('active')
            // jQuery('#modify_table_guest_allocation').trigger('click');
        });
        jQuery(document).on('click', '#guest_allocation_first_yes', function(){
            jQuery('#guest_allocation_second').show();
            jQuery('#guest_allocation_first').hide();
        });


        jQuery(document).on('click', '#modify_table_guest_allocation', function(){
            //jQuery('#guest_allocation_first').show();
        });

        jQuery(document).on('click', '.sidebar_right.short_client_view span.toggle_sidebar',function(){
            jQuery('.sidebar_right.short_client_view').toggleClass('closed_sidebar');
        });



        jQuery('#toolbar_rotate_option').on('change', function(){


        });

        jQuery(document).on('click','a.input_button', function(){
            //   jQuery(this).toggleClass('active');

        });//on click

        jQuery(document).on('click','.right-sec.direction_sec a.input_button', function(e){
            jQuery(this).addClass('active');
            setTimeout(function(){
                jQuery('.right-sec.direction_sec a.input_button').removeClass('active');
            }, 500);

        });//on click

        jQuery('.dropdown_field_button').each(function(){

            var selectId = jQuery(this).attr('id');
            var selectTitle = jQuery(this).attr('data-title');
            var output_li = '';
            var in_value = '';
            var dropParent = [];
            jQuery(this).children('option').each(function(i){

                var b_g = "design/icons/"+ jQuery(this).data('icon');
                var class_this = jQuery(this).attr('class');
                if(class_this === '' || class_this === undefined){
                    class_this = '';
                }

                var drop_p_id = jQuery(this).attr('drop_p_id');
                if(drop_p_id === '' || drop_p_id === undefined){
                    drop_p_id = '';
                }

                var p_id = jQuery(this).attr('p_id');

                if(p_id === '' || p_id === undefined){
                    output_li += '<li id="'+ jQuery(this).val() + '_cust" drop_p_id="'+drop_p_id+'"  class="'+class_this+'" data_val="'+ jQuery(this).val() + '" data_icon="' + jQuery(this).data('icon') + '" ><span style="background:url('+ b_g  +')" ></span>'+ jQuery(this).text() +'</li>';
                }else{

                }


                if (jQuery(this).hasClass('has_child_items')) {
                    dropParent.push(drop_p_id);
                }else{
                    //output_li += 'no';

                }



                if(i == 0){
                    in_value = '<span style="background:url('+ b_g  +')"></span>'+ jQuery(this).text();
                }


            });



            var output = '<div id="'+ selectId+'_cust" data_id="'+ selectId+'" class="cust_select dropdown_field_wrap" data-title="'+ selectTitle +'"><div class="select_val">'+ in_value +'</div><svg xmlns="http://www.w3.org/2000/svg" class="dropdown_f" viewBox="4.5 6 7 4"><path id="dropdown_f" d="M 8 10 C 7.867364406585693 10.00015640258789 7.74013614654541 9.947440147399902 7.646480560302734 9.853520393371582 L 4.646480083465576 6.853520393371582 C 4.451164245605469 6.65831184387207 4.451164245605469 6.341729164123535 4.646426677703857 6.146467208862305 C 4.841689109802246 5.951204776763916 5.158271312713623 5.951205253601074 5.353533744812012 6.146467208862305 L 8 8.793000221252441 L 10.64648056030273 6.146510124206543 C 10.84168910980225 5.951194286346436 11.15827178955078 5.951194286346436 11.35353469848633 6.146456718444824 C 11.54879665374756 6.341718673706055 11.54879665374756 6.65830135345459 11.35353374481201 6.853563785552979 L 8.353480339050293 9.853509902954102 C 8.259836196899414 9.947423934936523 8.132623672485352 10.00014400482178 8 10.00000095367432 Z"></path></svg><ul>' + output_li + '</ul></div>';
            jQuery(this).before(output);

            if(dropParent.length > 0){
                console.log(dropParent);
                var dropParent_le = dropParent.length;
                var out_list ='';
                var i = 0;
                for (i = 0; i < dropParent_le; i++) {
                    var p_id = dropParent[i];
                    out_list = "<ul class='drop_down'>";
                    jQuery(this).find('option[p_id="'+p_id+'"]').each(function(){
                        var b_g = "design/icons/"+ jQuery(this).data('icon');
                        var class_this = jQuery(this).attr('class');
                        if(class_this === '' || class_this === undefined){
                            class_this = '';
                        }
                        out_list += '<li  class="'+class_this+'" data_val="'+ jQuery(this).val() + '" data_icon="' + jQuery(this).data('icon') + '" ><span style="background:url('+ b_g  +')" ></span>'+ jQuery(this).text() +'</li>';
                    });
                    out_list += "</ul>";
                    //   console.log(out_list);

                    jQuery(document).find("div[data_id='"+selectId+"']").find('ul li[drop_p_id="'+ p_id +'"]').append(out_list);
                }// for


            }//dropParent



            jQuery(this).hide();
            jQuery(this).addClass('view_done');

        });

        jQuery(document).on('click', '.dropdown_field_wrap div.select_val', function(){
            jQuery('.cust_select').removeClass('open_drop');
            jQuery(this).parent('.cust_select').addClass('open_drop');
        });

        jQuery(document).on('click', '.dropdown_field_wrap.cust_select li', function(){
            var text = jQuery(this).html();
            jQuery(text).find("ul.drop_down").remove();
            var value = jQuery(this).attr('data_val');
            var selectId = jQuery(this).closest('.cust_select').attr('data_id');

            console.log(selectId);


            if(selectId == "toolbar_rotate_option" || selectId ==  "toolbar_alignment" || selectId == "toolbar_bring_front_back_drop" || selectId == "toolbar_group_ungroup_drop" || selectId == "toolbar_numbering" || selectId == "toolbar_obj_shap" || selectId == "toolbar_export_pdf_btn"){

            }else{
                jQuery(this).siblings().removeClass('selected_li');
                jQuery(this).addClass('selected_li');
                jQuery(this).closest('div.select_val').html(text);
                jQuery(this).closest('.cust_select').find('.select_val').html(text);
            }


            jQuery(this).closest('.cust_select').removeClass('open_drop');


            jQuery('#'+selectId).val(value).change();
        });


        //custom
        jQuery('.custom_dropdown_field').each(function(){

            var selectId = jQuery(this).attr('id');
            var selectTitle = jQuery(this).attr('data-title');
            var output_li = '';
            var in_value = '';
            jQuery(this).children('option').each(function(i){
                var b_g = "design/icons/"+ jQuery(this).data('icon');
                var class_this = jQuery(this).attr('class');
                if(class_this === '' || class_this === undefined){
                    class_this = '';
                }

                var text_right = jQuery(this).attr('data-right-text');
                var selected = jQuery(this).attr('selected');

                output_li += '<li  class="'+class_this+'" data_val="'+ jQuery(this).val() + '" data_icon="' + jQuery(this).data('icon') + '" ><span style="" ></span><span class="text_lable">'+ jQuery(this).text() + '</span>';

                if(text_right === '' || text_right === undefined){

                }else{
                    output_li +=  '<span class="right_text" >'+ text_right +'</span>';
                }

                output_li += '</li>';
                if(selected === 'selected'){
                    in_value = jQuery(this).text();
                }

            });
            var output = '<div id="'+ selectId+'_cust" data_id="'+ selectId+'" class="custom_dropdown_field_wrap cust_select" data-title="'+ selectTitle +'"><div class="select_val">'+ in_value +'</div><ul>' + output_li + '</ul></div>';
            jQuery(this).before(output);
            jQuery(this).hide();
        });//custom

        jQuery(document).on('click', '.custom_dropdown_field_wrap div.select_val', function(){
            jQuery('.cust_select').removeClass('open_drop');
            jQuery(this).parent('.cust_select').addClass('open_drop');
        });

        jQuery(document).on('click', '.custom_dropdown_field_wrap.cust_select li', function(){
            var text = jQuery(this).find('span.text_lable').text();
            var value = jQuery(this).attr('data_val');
            jQuery(this).siblings().removeClass('selected_li');
            jQuery(this).addClass('selected_li');
            jQuery(this).closest('div.select_val').html(text);
            jQuery(this).closest('.cust_select').find('.select_val').html(text);
            jQuery(this).closest('.cust_select').removeClass('open_drop');
            var selectId = jQuery(this).closest('.cust_select').attr('data_id');
            jQuery('#'+selectId).val(value).change();
        });

        jQuery(document).click(function(event){
            if(event.target.classList.contains('select_val')){

            }else{
                jQuery('.cust_select').removeClass('open_drop');

            }
        });


        jQuery('.nav_bar .menu li.dropdown ul.dropdown-content li').click(function(){
            jQuery(this).parent().addClass('open');
        });

        jQuery('.nav_bar .menu li.dropdown').hover(function(){
            jQuery('ul.dropdown-content').removeClass('open');
        });

        // Remove Menu Items start here
        var menu_items = [ "toolbar_extras_chngmeas", "toolbar_arrange_group"];
        jQuery.each( menu_items, function( i, val ) {
            // jQuery( "#" + val ).remove();
        });
        // Remove Menu Items end here


        // Remove Menu Items start here
        // jQuery(document).on('click', '#toolbar_modify_table_btn', function(){
        //         if(window.variableUserIsAdmin == 0){
        //             var table_info_items = [ "circle_table_admin_max_chair_count", "circle_table_admin_max_chair_edge_x" ,"circle_table_admin_max_chair_edge_y", "circle_table_admin_title_font_size", "circle_table_admin_chair_font_size"];
        //             jQuery.each( table_info_items, function( i, val ) {
        //                 var item = "#" + val;
        //                 jQuery(item).parent('span').parent('div').remove();
        //                 console.log(item + "Removed");
        //              });
        //         }
        // });
        // Remove Menu Items end here


        //color start
        jQuery(document).on('click', '#color_action_select_color', function(){
            jQuery('.color_select').show();
        });

        jQuery(document).on('click', '#color_action_buttons_cancel', function(){
            jQuery('.color_select').hide();
        });

        jQuery(document).on('click', '.color_select > span', function(){
            jQuery('.color_select > span').removeClass('active_plate');
            jQuery(this).addClass('active_plate');
            var selected_color =  jQuery(this).css('background-color');
            // console.log(selected_color);
        });

        jQuery(document).on('click', '#color_action_buttons_save', function(){
            jQuery('.color_select').hide();
            var selected_color =  jQuery('.color_select > span.active_plate').data('color');
            // console.log(selected_color);
        });

        // console.log(window.variableUserIsAdmin);
        jQuery('#Toolbar').addClass('view_done');


    } //toolBarFunction

    function passwordVisible() {
        var x = document.getElementById("guest_allocation_table_share_passowrd");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function modifyTableCust(){
        console.log('modifyTableCust');

        jQuery(document).on("click", "#guest_allocation_table_share_tab" ,function(){
            jQuery(".remove_class_selected").removeClass('selected');
            jQuery("#guest_allocation_table_share").toggleClass('selected');
            jQuery("div#list_tab ul button").removeClass('selected');
            jQuery(this).toggleClass('selected');
        });

        jQuery(document).on("click", "#guest_allocation_table_guest_allocation_tab" ,function(){
            jQuery(".remove_class_selected").removeClass('selected');
            jQuery("#guest_allocation_table_guest_allocation").toggleClass('selected');
            jQuery("div#list_tab ul button").removeClass('selected');
            jQuery(this).toggleClass('selected');
        });


        jQuery(document).on("click", "#guest_allocation_table_summary_tab" ,function(){
            GenerateSummary();
            jQuery(".remove_class_selected").removeClass('selected');
            jQuery("#guest_allocation_table_summary").toggleClass('selected');
            jQuery("div#list_tab ul button").removeClass('selected');
            jQuery(this).toggleClass('selected');
        });


        jQuery(document).on("click", "#guest_allocation_table_add_guest" ,function(){
            jQuery('div#new_guest_table').toggleClass('active');
            jQuery('button#guest_allocation_table_import_list').toggleClass('disabled');
            App.updateGuestTablePreview();
            console.log('previ function');

        });

        jQuery(document).on("click", ".filter_guest_row" ,function(){
            jQuery('.filter_guest_row').removeClass('active')
            jQuery(this).toggleClass('active')
        });

        jQuery(document).on("click", "button#guest_allocation_table_filter" ,function(){
            jQuery('.filter_btn_table').toggleClass('show')
        });


        jQuery(document).on("click", "#guest_allocation_new_guest_table_cancel_btn" ,function(){
            jQuery('#new_guest_table').removeClass('active');
            jQuery('#new_guest_table #guest_allocation_new_guest_table_firstname').val('');
            jQuery('#new_guest_table #guest_allocation_new_guest_table_lastname').val('');
            // jQuery('#new_guest_table #guest_allocation_new_guest_table_number').val('');
            jQuery('#new_guest_table #guest_allocation_new_guest_table_seat_type').val('adult');
            // jQuery('#new_guest_table #guest_allocation_new_guest_table_seat_number').val('');

            jQuery('#new_guest_table #guest_allocation_new_guest_table_item_id').val('');
            jQuery('#new_guest_table  #guest_allocation_new_guest_table_add_dietary_option').html('');
            jQuery('input[name="dietary_requirements"]').prop("checked",false)
            jQuery('#new_guest_table_view h4').html('New Guest');

            jQuery('#guest_allocation_table_import_list').removeClass('disabled')
        });

        jQuery(document).on("click", "button#guest_allocation_table_guest_allocation_filter_cancel" ,function(){
            jQuery('.filter_btn_table').removeClass('show');
            if(jQuery('.new_geust_all_list .new_guest_entry:visible').length == 0){
                jQuery('.filter_note').show();
            }else{
                jQuery('.filter_note').hide();
            }
        });

        jQuery(document).on('change', 'select#guest_allocation_table_share_password_protect' , function(){
            var drop_valu = jQuery(this).val();
            if (drop_valu == 'no') {
                jQuery('.password_user').css('display', 'none');
            }
            else {
                jQuery('.password_user').css('display', 'block');
            }
        });

        jQuery(document).on("click", ".guest_allocation_table_summary_row" ,function(){
            // jQuery('.guest_allocation_table_summary_row').removeClass('active')
            // jQuery(this).toggleClass('active')

        });

        jQuery(document).on("click", ".guest_allocation_table_summary_row.un_active h3" ,function(){
            jQuery('.guest_allocation_table_summary_row').removeClass('active');
            jQuery('.guest_allocation_table_summary_row').addClass('un_active');
            jQuery(this).parent('div').removeClass('un_active');
            jQuery(this).parent('div').addClass('active');
        });

        jQuery(document).on("click", ".guest_allocation_table_summary_row.active h3" ,function(){
            jQuery(this).parent('div').removeClass('active')
            jQuery(this).parent('div').addClass('un_active')
        });


        jQuery(document).on("click", ".guest_allocation_table_summary_row_die" ,function(){
            jQuery('.guest_allocation_table_summary_row_die').removeClass('select')
            jQuery(this).toggleClass('select')
        });

        jQuery(document).on("click", "#guest_allocation_new_guest_table_add_dietary" ,function(){
            jQuery('.die_req_list').toggleClass('active')
        });

        jQuery(document).on("click", "button#guest_allocation_new_guest_table_add_dietary_done" ,function(){
            jQuery('.die_req_list ').removeClass('active')
        });

        jQuery(document).on("click", "#guest_allocation_new_guest_table_create_new" ,function(){
            jQuery('.create_new_dietary').toggleClass('active');
            jQuery('.die_req_list ').removeClass('active')
        });

        jQuery(document).on("click", "#guest_allocation_create_new_dietary_cancel_btn" ,function(){
            jQuery('.create_new_dietary ').removeClass('active')
        });

        jQuery(document).on("click", "button#guest_allocation_table_import_list" ,function(){
            jQuery('.import_list_table').toggleClass('active');
            jQuery('button#guest_allocation_table_add_guest').toggleClass('disabled');
        });

        jQuery(document).on("click", "button#guest_allocation_import_list_cancel_btn" ,function(){
            jQuery('.import_list_table').removeClass('active');
            jQuery('#guest_allocation_table_add_guest').removeClass('disabled');
        });

        jQuery(document).on("click", "div#button-10" ,function(){
            jQuery('.sharable_url_table').toggleClass('active');
            jQuery(this).toggleClass('active');
        });

        jQuery(document).on("click", ".colour_sele_box span" ,function(){
            jQuery(".colour_sele_box span").removeClass('active_plate');
            jQuery(this).toggleClass('active_plate');
        });


        jQuery(document).on("click", "svg#Capa_1" ,function(){
            jQuery(this).toggleClass('active');
        });

        jQuery(document).on("click", "button#guest_allocation_new_guest_table_add_dietary_done" ,function(){

            var dietary_requirements_html = '';
            jQuery("input[type='checkbox'][name='dietary_requirements']:checked").each(function() {
                var radioValue = jQuery(this).val();
                var svg_icon_radio = jQuery( this).parent('label').find('.svg_icon_radio').html();
                var svg_icon_radio_color = jQuery( this).attr('data_color');
                var attr_title_text = jQuery( this).attr('data_title_text');
                dietary_requirements_html +=  "<p class='select_dietary' data_color='"+svg_icon_radio_color+"' data_title-text='"+radioValue+"'><span> <span>" + svg_icon_radio + "</span> "+ attr_title_text + " </span> <span class='select_dietary_close'> x  </span> </p>";

            });
            jQuery( "#guest_allocation_new_guest_table_add_dietary_option" ).html(dietary_requirements_html);
        });
        jQuery(document).on("click", "button#guest_allocation_create_new_dietary_save_btn" ,function(){
            var New_die_req = jQuery("input#guest_allocation_create_new_dietary_dietary_req").val();
            var arrow_Color = jQuery('.color_select > span.active_plate').data('color');
            var new_diet_value = New_die_req.replace(/ /g,"_");

            var dietary_id_hidden = jQuery('#dietary_id_hidden').val();

            var new_dietary_html = ("<div><label class='title_label'><span class='svg_icon_radio'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='12' fill='#" + arrow_Color + "' class='' viewBox='0 0 16 16' stroke='#" + arrow_Color + "' stroke-width='2px'><path fill-rule='evenodd' d='M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z'></path></svg></span>" + New_die_req + "<input type='checkbox' id='"+new_diet_value+"' name='dietary_requirements' value='" + new_diet_value.toLowerCase() + "' data_title_text='"+ New_die_req +"' data_color='#"+arrow_Color+"'><span class='checkmark'></span></label> <img src='./images/icons8-edit.png' class='edit_pencil'></div>");


            if(dietary_id_hidden == ''){
                if(New_die_req){
                    jQuery(".die_req_list").prepend(new_dietary_html);
                    resetDietaryForm();
                    console.log(dietary_id_hidden);
                }
            }else{
                updateDietary(New_die_req,arrow_Color,new_diet_value);
            }
            updateDietaryDataAll(New_die_req,arrow_Color,new_diet_value,dietary_id_hidden);
        });

        //update Dietary Data All user and filter
        function updateDietaryDataAll(new_die_req_title,new_die_req_color,new_die_req_value,old_dietary_id_hidden){
            new_dietary_html = '<div><span><input type="checkbox" name="guest_allocation_table_guest_allocation_filter_die_requi" id="diet_'+new_die_req_value+'" value="'+new_die_req_value.toLowerCase()+'"><label for="diet_'+new_die_req_value+'"> <span class="svg_icon_radio"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="#'+new_die_req_color+'" class="" viewBox="0 0 16 16" stroke="#'+new_die_req_color+'" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>'+new_die_req_title+'</label></span></div>';
            if(old_dietary_id_hidden == '' || old_dietary_id_hidden == null){
                // console.log(new_die_req_title);
                jQuery("#guest_allocation_filter_dietary").prepend(new_dietary_html);
            }else{
                // console.log(old_dietary_id_hidden);
                jQuery("#guest_allocation_filter_dietary input[id='diet_"+ old_dietary_id_hidden +"'").parent('span').parent('div').replaceWith(new_dietary_html);
                updateUserData(new_die_req_title,new_die_req_color,new_die_req_value,old_dietary_id_hidden);
            }
        }

        function  updateUserData(new_die_req_title,new_die_req_color,new_die_req_value,old_dietary_id_hidden){

            console.log(new_die_req_color+new_die_req_value+old_dietary_id_hidden);

            jQuery('.new_geust_all_list .new_guest_entry').each(function(){
                var ex_diet = jQuery(this).attr('data_dietary');
                var ex_diet_all = jQuery(this).attr('data_dietary_all');
                var ex_diet_allArray = ex_diet_all.replace("[","").replace("]","").split(',');
                if(ex_diet == old_dietary_id_hidden){
                    jQuery(this).attr('data_dietary',new_die_req_value);
                    jQuery(this).find('svg').attr('fill','#'+new_die_req_color).attr('stroke','#'+new_die_req_color);
                }
                ex_diet_all = ex_diet_all.replace(old_dietary_id_hidden,new_die_req_value);
                jQuery(this).attr('data_dietary_all',ex_diet_all);
            });

            regenerateDataArray();

        }

        //update Dietary Data All user and filter


        //reset dietary form
        function resetDietaryForm(){

            jQuery('.die_req_list').toggleClass('active');
            jQuery('.create_new_dietary ').removeClass('active');
            jQuery('input#guest_allocation_create_new_dietary_dietary_req').val('');
            jQuery('#dietary_id_hidden').val('');

        }

        //reset dietary form

        //edit dietary

        jQuery(document).on("click", ".edit_pencil" ,function(){
            jQuery('.die_req_list').toggleClass('active');
            var diet_details, diet_title, diet_color,diet_id;
            diet_details = jQuery(this).parent('div').find('input[name="dietary_requirements"]');
            diet_title = diet_details.attr('data_title_text');
            diet_color = diet_details.attr('data_color').replace("#","");
            diet_id = diet_details.attr('id');

            jQuery('#guest_allocation_create_new_dietary_dietary_req').val(diet_title);
            jQuery('#dietary_id_hidden').val(diet_id);
            jQuery('#color_select span').removeClass('active_plate');
            jQuery('#color_select span[data-color="'+ diet_color +'"]').val(diet_color).addClass('active_plate');

            jQuery(".create_new_dietary" ).addClass('active');
            jQuery('.die_req_list').toggleClass('active');
            //editDietary(diet_title,diet_color,diet_id);
        });

        function updateDietary(diet_title,diet_color,diet_id){

            var dietary_html = ("<div><label class='title_label'><span class='svg_icon_radio'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='12' fill='#" + diet_color + "' class='' viewBox='0 0 16 16' stroke='#" + diet_color + "' stroke-width='2px'><path fill-rule='evenodd' d='M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z'></path></svg></span>" + diet_title + "<input type='checkbox' id='"+diet_id+"' name='dietary_requirements' value='" + diet_id + "' data_title_text='"+ diet_title +"' data_color='#"+diet_color+"'><span class='checkmark'></span></label> <img src='./images/icons8-edit.png' class='edit_pencil'></div>");
            console.log(diet_id);
            var dietary_id_hidden = jQuery('#dietary_id_hidden').val();
            if( dietary_id_hidden == ''){
                jQuery("#guest_allocation_new_guest_add_user_dietary input[id='"+ diet_id +"'").parent('label').parent('div').replaceWith(dietary_html);
            }else{
                jQuery("#guest_allocation_new_guest_add_user_dietary input[id='"+ dietary_id_hidden +"'").parent('label').parent('div').replaceWith(dietary_html);
            }
            resetDietaryForm()

        }
        //update

        //close dietary
        jQuery(document).on("click", "button#guest_allocation_new_guest_table_add_dietary_cancel" , function(){
            // jQuery(".die_req_list").removeClass('active');
            resetDietaryForm();

        });
        //close dietary

        jQuery(document).on("click", ".select_dietary_close" ,function(){
            jQuery(this).parent('.select_dietary').remove();
        });

        jQuery(document).on("click", ".new_guest_entry input[type='checkbox']" ,function(){
            // jQuery('.arrows').toggleClass('select');
            // jQuery('.select_options').toggleClass('select');
            // jQuery('.new_guest_entry').toggleClass('select');
        });

        jQuery(document).on("click", "button#guest_allocation_table_add_to_table_btn" ,function(){
            jQuery('.add_to_table_popupform').toggleClass('popup');
        });

        jQuery(document).on("click", "#guest_allocation_table_add_to_table_cancel_btn" ,function(){
            jQuery('.add_to_table_popupform').removeClass('popup');
        });

        jQuery(document).on("click", "button#guest_allocation_table_guest_allocation_clr_btn" ,function(){
            jQuery('.filter_btn_table input[type="checkbox"]').prop("checked", false);
            jQuery('.new_geust_all_list .new_guest_entry').show();
        });


        jQuery(document).on("click" , "button#guest_allocation_new_guest_table_save_btn" , function(){
            var submit = false;
            var first_Name = jQuery('input#guest_allocation_new_guest_table_firstname').val();
            var last_Name = jQuery('input#guest_allocation_new_guest_table_lastname').val();
            var table_number = jQuery('input#guest_allocation_new_guest_table_number').val();
            var seat_number = jQuery('input#guest_allocation_new_guest_table_seat_number').val();

            var seat_type = jQuery( "#guest_allocation_new_guest_table_seat_type" ).val();
            var item_id = jQuery("#guest_allocation_new_guest_table_item_id").val()

            var dietary_text = jQuery('#guest_allocation_new_guest_table_add_dietary_option p.select_dietary:nth-child(1)').attr('data_title-text');
            var dietary_color = jQuery('#guest_allocation_new_guest_table_add_dietary_option p.select_dietary:nth-child(1)').attr('data_color');
            // console.log(first_Name+last_Name+table_number+seat_number+seat_type+dietary);

            var dietary_all = [];
            jQuery('#guest_allocation_new_guest_table_add_dietary_option p.select_dietary').each(function(){
                var data_dietary_name = jQuery(this).attr('data_title-text').toLowerCase().replace(/ /g, '_');;

                if(jQuery.inArray(data_dietary_name, dietary_all) === -1){
                    dietary_all.push(data_dietary_name);
                }
            });

            console.log(dietary_all);

            if(first_Name == '' || last_Name == '' || table_number == '' || seat_number == ''){

                jQuery('#new_guest_table_view :input[required]').each(function(){
                    var valu = jQuery(this).val();
                    if(valu == ""){
                        jQuery(this).css('border-color','red');
                    }else{
                        console.log(valu);
                        jQuery(this).css('border-color','#f7f8f8');
                    }
                });

            }else{
                if(item_id === '' || item_id === null){
                    window.guestIdLast =  parseInt(window.guestIdLast) + 1;
                    item_id = window.guestIdLast;
                    // window.guestIdLast =  parseInt(window.guestIdLast) + 1;

                    add_guest_allocation_new_guest_table(first_Name,last_Name,table_number,seat_number,seat_type,dietary_text,dietary_color,item_id,dietary_all);
                }else{

                    guest_allocation_update_guest(first_Name,last_Name,table_number,seat_number,seat_type,dietary_text,dietary_color,item_id,dietary_all);
                }

                submit = true;
            }

            if(submit == true){
                jQuery("#new_guest_table").removeClass('active');
                jQuery('#guest_allocation_table_import_list').removeClass('disabled');
                resetAddNewGuest();
                updateTableNumFilter(table_number);
                regenerateDataArray();
                GenerateSummary();
            }
        });

        function resetAddNewGuest(){
            jQuery('#new_guest_table_view :input[required]').css('border-color','#f7f8f8');
            jQuery('div#new_guest_table input[type="text"]').val('');
            jQuery('div#new_guest_table input[type="hidden"]').val('');
            jQuery('#guest_allocation_new_guest_table_add_dietary_option').html('');
            jQuery('div#new_guest_table input[type="checkbox"][name="dietary_requirements"] ').prop('checked',false);
            jQuery('#new_guest_table_view h4').html('New Guest');

        }

        window.updateTableNumFilter =    function updateTableNumFilter(table_number){
            // guest_filter_table_number_html

            console.log(window.tableNumArray);
            console.log(table_number);
            table_number = parseInt(table_number);
            window.tableNumArray = window.tableNumArray.map( Number );
            if(jQuery.inArray(table_number, window.tableNumArray) === -1){
                var html_filt = '<div class="table_num_'+table_number+'"><span><input type="checkbox" name="guest_filter_table_number" id="table_'+table_number+'" value="'+table_number+'"><label for="table_'+table_number+'">'+table_number+'</label></span></div>';
                jQuery('#guest_filter_table_number_html').append(html_filt);
                window.tableNumArray.push(parseInt(table_number));
            }else{
            }
        }//updateTableNumFilter

        function add_guest_allocation_new_guest_table(first_Name,last_Name,table_number,seat_number, seat_type,dietary_text,dietary_color, item_id, dietary_all){

            var seat_type = seat_type.replace("'", "_");
            if(dietary_color == null){
                dietary_color = '#fff';
            }
            if(dietary_text == null){
                dietary_text = 'none';
            }
            if(item_id == null || item_id === ''){
                item_id = first_Name;
            }
            var new_guest = jQuery("<div class='new_guest_entry' data_tableno='"+ table_number +"' data_seattype='"+ seat_type +"'  data_dietary='"+ dietary_text +"' data_first='"+ first_Name +"' data_lastname='"+last_Name+"' data_seatnumber='"+ seat_number +"' data_search='"+first_Name +' '+ last_Name +"' data_item_id='"+ item_id +"' data_dietary_all='"+ dietary_all +"'><input type='checkbox' name='guest' id='"+item_id+"' ><label for='"+item_id+"'></label> <span  class='custom_width'> <span class='svg_icon_radio'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='12' fill='"+dietary_color+"' class='' viewBox='0 0 16 16' stroke='"+dietary_color+"' stroke-width='2px'><path fill-rule='evenodd' d='M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z'></path></svg></span>"+first_Name+" " +last_Name +"</span><span style='margin-left: 4px;'  class='custom_width_1 table_number'>"+table_number+"</span><span style='margin-left: 3px;'  class='custom_width_1 seat_number' >"+seat_number+"</span></div>");

            jQuery(".new_geust_all_list").prepend(new_guest);

        }


        function guest_allocation_update_guest(first_Name,last_Name,table_number,seat_number, seat_type,dietary_text,dietary_color, item_id,dietary_all){

            var seat_type = seat_type.replace("'", "_");
            if(dietary_color == null){
                dietary_color = '#fff';
            }
            if(dietary_text == null){
                dietary_text = 'none';
            }
            // if(item_id == null || item_id === ''){
            //  item_id = first_Name;
            // }
            var update_guest = jQuery("<div class='new_guest_entry' data_tableno='"+ table_number +"' data_seattype='"+ seat_type +"'  data_dietary='"+ dietary_text +"' data_first='"+ first_Name +"' data_lastname='"+last_Name+"' data_seatnumber='"+ seat_number +"' data_search='"+first_Name +' '+ last_Name +"' data_item_id='"+ item_id +"' data_dietary_all='"+ dietary_all +"'><input type='checkbox' name='guest' id='"+item_id+"'><label for='"+item_id+"'></label> <span  class='custom_width'> <span class='svg_icon_radio'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='12' fill='"+dietary_color+"' class='' viewBox='0 0 16 16' stroke='"+dietary_color+"' stroke-width='2px'><path fill-rule='evenodd' d='M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z'></path></svg></span>"+first_Name+" " +last_Name +"</span><span style='margin-left: 4px;'  class='custom_width_1 table_number'>"+table_number+"</span><span style='margin-left: 3px;'  class='custom_width_1 seat_number' >"+seat_number+"</span></div>");

            console.log(update_guest);
            jQuery('.new_guest_entry[data_item_id="'+item_id+'"').replaceWith(update_guest);
            resetAddNewGuest();
            updateTableNumFilter(table_number);

        }

        function guest_allocation_table_guest_allocation_filter(table_number,seat_type,dietary_type){

            // console.log(table_number);
            // console.log(seat_type);
            // console.log(dietary_type);

            if(table_number.length !== 0 &&  seat_type.length !== 0 && dietary_type.length !== 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if(jQuery.inArray(data_tableno, table_number) !== -1 && jQuery.inArray(data_seattype, seat_type) !== -1){
                        //dietary
                        jQuery.each(dietary_type, function(i, item) {
                            if(jQuery.inArray(item, dietary_allArray) !== -1){
                                show_this = true;
                            }
                        });
                        //dietary
                        if(show_this == true){
                            jQuery(this).show();
                            show_this = false;
                        }else{
                            jQuery(this).hide();
                        }
                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length === 0 &&  seat_type.length !== 0 && dietary_type.length !== 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if(jQuery.inArray(data_seattype, seat_type) !== -1){

                        //dietary
                        jQuery.each(dietary_type, function(i, item) {
                            if(jQuery.inArray(item, dietary_allArray) !== -1){
                                show_this = true;
                            }
                        });
                        if(show_this == true){
                            jQuery(this).show();
                            show_this = false;
                        }else{
                            jQuery(this).hide();
                        }
                        //dietary



                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length === 0 &&  seat_type.length === 0 && dietary_type.length !== 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    //dietary
                    jQuery.each(dietary_type, function(i, item) {
                        if(jQuery.inArray(item, dietary_allArray) !== -1){
                            show_this = true;
                        }
                    });
                    if(show_this == true){
                        jQuery(this).show();
                        show_this = false;
                    }else{
                        jQuery(this).hide();
                    }
                    //dietary



                });

            }else if(table_number.length === 0 &&  seat_type.length !== 0 && dietary_type.length === 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if(jQuery.inArray(data_seattype, seat_type) !== -1){
                        jQuery(this).show();
                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length === 0 &&  seat_type.length !== 0 && dietary_type.length !== 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if( jQuery.inArray(data_seattype, seat_type) !== -1){

                        //dietary
                        jQuery.each(dietary_type, function(i, item) {
                            if(jQuery.inArray(item, dietary_allArray) !== -1){
                                show_this = true;
                            }
                        });
                        if(show_this == true){
                            jQuery(this).show();
                            show_this = false;
                        }else{
                            jQuery(this).hide();
                        }
                        //dietary



                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length !== 0 &&  seat_type.length !== 0 && dietary_type.length === 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data


                    if(jQuery.inArray(data_tableno, table_number) !== -1 && jQuery.inArray(data_seattype, seat_type) !== -1){
                        jQuery(this).show();
                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length !== 0 &&  seat_type.length === 0 && dietary_type.length !== 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if(jQuery.inArray(data_tableno, table_number) !== -1 ){


                        //dietary
                        jQuery.each(dietary_type, function(i, item) {
                            if(jQuery.inArray(item, dietary_allArray) !== -1){
                                show_this = true;
                            }
                        });
                        if(show_this == true){
                            jQuery(this).show();
                            show_this = false;
                        }else{
                            jQuery(this).hide();
                        }
                        //dietary



                    }else{
                        jQuery(this).hide();
                    }
                });

            }else if(table_number.length !== 0 &&  seat_type.length === 0 && dietary_type.length === 0){
                jQuery('.new_geust_all_list .new_guest_entry').each(function(){

                    //data
                    var show_this = false;
                    var data_tableno = jQuery(this).attr('data_tableno');
                    var data_seattype = jQuery(this).attr('data_seattype');
                    var data_dietary = jQuery(this).attr('data_dietary_all');
                    var dietary_allArray = data_dietary.replace("[","").replace("]","").split(',');
                    //data

                    if(jQuery.inArray(data_tableno, table_number) !== -1){
                        jQuery(this).show();
                    }else{
                        jQuery(this).hide();
                    }
                });

            }else{

                jQuery('.new_geust_all_list .new_guest_entry').show();

            }



        }// filter guest_allocation_table_guest_allocation_filter

        // All checkbox
        jQuery(document).on('change', '#guest_allocation_table_search_checkbox', function(){
            var status = $(this).prop("checked");
            // jQuery('.new_geust_all_list input[name="guest"]').prop('checked', $(this).prop("checked"));
            jQuery('.new_geust_all_list .new_guest_entry').each(function(){
                var data_search = jQuery(this).attr('data_search');
                if($(this).css('display') == 'none' || $(this).css("visibility") == "hidden"){
                    // console.log(data_search);
                }else{
                    jQuery(this).find('input[name="guest"]').prop('checked', status);
                }
            });
            showGuestListAction();

        });

        jQuery(document).on('click', '.new_geust_all_list input[name="guest"]', function(){
            var in_val =  jQuery('#guest_allocation_table_search_checkbox').prop('checked');
            var out_val = jQuery(this).val();
            if(in_val == true && out_val !== 'all'){
                jQuery('#guest_allocation_table_search_checkbox').prop('checked', false)
            }

            showGuestListAction();
        });
        // All checkbox

        function  showGuestListAction() {

            if(jQuery('.new_geust_all_list input[name="guest"]:checked').length > 0){
                jQuery('.select_options').css('display','inline-block');
                jQuery('#guest_list_table_head #guest_allocation_table_search_name,#guest_list_table_head #guest_allocation_table_search_table,#guest_list_table_head #guest_allocation_table_search_seat').hide();

            }else{
                jQuery('.select_options').css('display','none');
                jQuery('#guest_list_table_head #guest_allocation_table_search_name,#guest_list_table_head #guest_allocation_table_search_table,#guest_list_table_head #guest_allocation_table_search_seat').show();
            }


        }






        function resetFilter(){
            jQuery('.new_geust_all_list .new_guest_entry').show();

        }

        jQuery(document).on('click', '#guest_allocation_table_guest_allocation_filter_apply', function(){

            var guest_filter_table_number = [];
            jQuery("input[type='checkbox'][name='guest_filter_table_number']:checked").each(function() {
                guest_filter_table_number.push(jQuery(this).val());
            });

            var guest_filter_seat_type = [];
            jQuery("input[type='checkbox'][name='guest_allocation_table_guest_allocation_filter_seat_type']:checked").each(function() {
                guest_filter_seat_type.push(jQuery(this).val());
            });

            var guest_filter_diet = [];
            jQuery("input[type='checkbox'][name='guest_allocation_table_guest_allocation_filter_die_requi']:checked").each(function() {
                guest_filter_diet.push(jQuery(this).val());
            });

            guest_allocation_table_guest_allocation_filter(guest_filter_table_number,guest_filter_seat_type,guest_filter_diet);
            jQuery('.filter_btn_table').removeClass('show');

            if(jQuery('.new_geust_all_list .new_guest_entry:visible').length == 0){
                jQuery('.filter_note').show();
            }else{
                jQuery('.filter_note').hide();
            }


        });

    } // modifyTableCust


    // Regenerate DATA Array start
    function regenerateDataArray(){
        console.log('regenerateDataArray')
        window.NewGuestArray = [];
        window.NewDietaryArray = [];
        window.shareDataArray = [];

        // Share Tab Data start
        var share_arr = JSON.parse('{"guest_allocation_table_share_event_name": "'+jQuery('#guest_allocation_table_share_event_name').val()+'", "guest_allocation_table_share_submission_dealine": "'+jQuery('#guest_allocation_table_share_submission_dealine').val()+'", "guest_allocation_table_share_password_protect": "'+jQuery('#guest_allocation_table_share_password_protect').val()+'", "guest_allocation_table_share_geust_list_url_status_drop": "'+ jQuery('#guest_allocation_table_share_geust_list_url_status_drop').val() +'", "guest_allocation_table_share_passowrd": "'+jQuery('#guest_allocation_table_share_passowrd').val()+'", "guest_allocation_table_share_guest_list_link_button": "'+jQuery('#guest_allocation_table_share_guest_list_link_button:checked').length+'", "guest_allocation_table_share_geust_list_url_sharable_url": "'+jQuery('#guest_allocation_table_share_geust_list_url_sharable_url').val()+'" }');

        window.shareDataArray = share_arr;
        console.log( window.shareDataArray);
        // Share Tab Data end



        //diet array
        var DietType = App.GuestDietTypes;
        var DietTypeNew = [];
        jQuery('#guest_allocation_new_guest_add_user_dietary input[name="dietary_requirements"]').each(function(){
            var diet_arr = JSON.parse('{"id": "'+jQuery(this).attr("id")+'", "name": "'+jQuery(this).attr("data_title_text")+'", "color": "'+jQuery(this).attr("data_color")+'"}');
            // var diet_arr = [];
            // diet_arr['id'] = jQuery(this).attr('id');
            // diet_arr['name'] = jQuery(this).attr('data_title_text');
            // diet_arr['color'] = jQuery(this).attr('data_color');
            DietTypeNew.push(diet_arr);
        });
        window.NewDietaryArray = DietTypeNew;
        //diet array

        //guest array
        var GuestListNew = [];
        jQuery('.new_geust_all_list .new_guest_entry').each(function(){
            // var guest_arr = [];
            var guest_arr = JSON.parse('{"dietary": "'+jQuery(this).attr("data_dietary_all")+'", "firstName": "'+jQuery(this).attr("data_first")+'", "id": "'+jQuery(this).attr("data_item_id")+'", "lastName": "'+jQuery(this).attr("data_lastname")+'", "seatNumber": "'+jQuery(this).attr("data_seatnumber")+'", "seatType": "'+jQuery(this).attr("data_seattype")+'", "tableNumber": "'+jQuery(this).attr("data_tableno")+'"}');

            // guest_arr['dietary'] = jQuery(this).attr('data_dietary_all');
            // guest_arr['firstName'] = jQuery(this).attr('data_first');
            // guest_arr['id'] = jQuery(this).attr('data_item_id');
            // guest_arr['lastName'] = jQuery(this).attr('data_lastname');
            // guest_arr['seatNumber'] = jQuery(this).attr('data_seatnumber');
            // guest_arr['seatType'] = jQuery(this).attr('data_seattype');
            // guest_arr['tableNumber'] = jQuery(this).attr('data_tableno');
            GuestListNew.push(guest_arr);
        });
        window.NewGuestArray = GuestListNew;
        //guest array

        // console.log(window.NewDietaryArray);
        // console.log(window.NewGuestArray);
        validateData();
        // if(jQuery('.new_geust_all_list .new_guest_entry:visible').length == 0){
        //                     jQuery('.list_note').show();
        //                 }else{
        //                    jQuery('.list_note').hide();
        //                    jQuery('.filter_note').hide();
        //                 }



    }
    // Regenerate DATA Array end
    function pushNewData(){
        console.log('Data Refeed');
        // if(window.NewDietaryArray && window.NewDietaryArray.length > 0){
        //     addSeatType(App.GuestSeatTypes);
        //     addDietary(window.NewDietaryArray);
        //     addGuestsList(window.NewGuestArray);
        //     addshareDataArray(window.shareDataArray);
        // }else{
        //     addSeatType(App.GuestSeatTypes);
        //     addDietary(App.GuestDietTypes);
        //     addGuestsList(App.GuestAllocUsers);
        //     addshareDataArray(App.shareDataArray);
        // }

        if(window.NewDietaryArray && window.NewDietaryArray.length > 0){
            addDietary(window.NewDietaryArray);
        }else{
            addDietary(App.GuestDietTypes);
        }

        addSeatType(App.GuestSeatTypes);

        if(window.shareDataArray){
            addshareDataArray(window.shareDataArray);
        }else{
            addshareDataArray(App.shareDataArray);
        }

        if(window.NewGuestArray && window.NewGuestArray.length > 0){
            addGuestsList(window.NewGuestArray);
        }else{
            addGuestsList(App.GuestAllocUsers);
        }
        GenerateSummary();
        validateData();
        // $('input').attr('autocomplete', 'off');
    }

    //validate duplicate entries
    window.validateData = function validateData(){
        console.log('Validating Data');
        jQuery('.new_guest_entry').css('border-color','#ccc');
        jQuery('.new_guest_entry').each(function(){
            var table = jQuery(this).attr('data_tableno');
            var seat = jQuery(this).attr('data_seatnumber');
            var data = jQuery('.new_guest_entry[data_seatnumber="'+seat+'"][data_tableno="'+table+'"]').length;
            if(data > 1){
                jQuery('.new_guest_entry[data_seatnumber="'+seat+'"][data_tableno="'+table+'"]').css('border-color','red');
            }else{
                //jQuery('.new_guest_entry[data_seatnumber="'+seat+'"][data_tableno="'+table+'"]').css('border','#ccc');
            }

        })

    }
    //validate duplicate entries





    function toolBarExtra(){

        //Tables Numbering code
        function tableNumb(){

            //text field validation
            jQuery('input#zigzag_table_skip_number, input#theatre_style_table_skip_number').on('change, keyup', function() {
                var currentInput = jQuery(this).val();
                var fixedInput = currentInput.replace(/[A-Za-z!.>=+\?<@#/\/$%^&*[{}\]~`_()|"\\\\']/g, '');
                var fixedInput_n = fixedInput.replace(/[/-]/g, '');
                jQuery(this).val(fixedInput_n);
            });

            jQuery(document).on("mouseenter", "div#zigzag_table .table_icon img.hover_element", function(){
                var text = jQuery(this).data('hover_text');
                jQuery('p.layout_text').show();
                jQuery('p.layout_text').text(text);
            });

            jQuery('div#zigzag_table .table_icon img').mouseout( function(){
                if (jQuery('div#zigzag_table .table_icon img').hasClass('active_layout')){
                    var text = jQuery('div#zigzag_table .table_icon img.active_layout').data('hover_text');
                    jQuery('p.layout_text').text(text);
                } else {
                    jQuery('p.layout_text').hide().text('');
                    jQuery('p.note.layout_text').removeClass('active_note');
                }

            });

            jQuery(document).on('click','div#zigzag_table .table_icon img.hover_element', function(){
                jQuery('div#zigzag_table .table_icon img.hover_element').removeClass('active_layout');
                jQuery(this).addClass('active_layout');
                jQuery('p.note.layout_text').addClass('active_note');
            });


        }//tableNumb

        jQuery(document).on('click','li#table_number_zigzag_cust', function(){
            console.log('Extra');
            tableNumb();
        });

        //Tables Numbering code

        function tableNumbTheater(){

            //layout_text_num
            jQuery(document).on("mouseenter", "#theatre_style_table .theatre_arrow", function(){
                var text = jQuery(this).data('hover_text');
                jQuery('p.layout_text_num').text(text);
            });

            jQuery('#theatre_style_table .theatre_arrow').mouseout( function(){
                jQuery('p.layout_text_num').text('');
            });

        }
        jQuery(document).on('click','li#threatre_style_numbering_cust', function(){
            console.log('tableNumbTheater');
            tableNumbTheater();
        });


    }//toolBarExtra

    function initGuestAllocationData(){
        var DietType = App.GuestDietTypes;
        var seatType = App.GuestSeatTypes;
        var GuestList = App.newGuest;

        window.addSeatType =  function addSeatType(GuestSeatData){

            var seatType = GuestSeatData;

            if(seatType !== null){
                var html_add_guest = '';
                var html_filter_guest = '';
                jQuery.each(seatType, function(i, item) {
                    html_add_guest += '<option value="'+ item.id +'">'+item.name+'</option>';
                    html_filter_guest += '<div><span><input type="checkbox" name="guest_allocation_table_guest_allocation_filter_seat_type" id="'+ item.id +'" value="'+ item.id +'"><label for="'+ item.id +'">'+item.name+'</label></span></div>';
                    //console.log(item.name);
                });
                jQuery(document).find('#guest_allocation_new_guest_table_seat_type').html(html_add_guest);
                jQuery(document).find('#guest_filter_seat_type').html(html_filter_guest);

            }else{
                console.log('No data for Seat Type')
            }
        }//addSeatType

        window.addshareDataArray =    function addshareDataArray(shareData){
            // var data = window.shareDataArray;
            // console.log(shareData);
            if(shareData){
                jQuery.each(shareData, function(i, item) {
                    var id = i;
                    var value = item;
                    if(i == "guest_allocation_table_share_guest_list_link_button" && value == 1){
                        // jQuery('#guest_allocation_table_share_guest_list_link_button').prop('checked', true).trigger('change');
                        jQuery('#guest_allocation_table_share_guest_list_link_button').trigger('click');

                    }else if(i == "guest_allocation_table_share_password_protect"){
                        jQuery('#guest_allocation_table_share_password_protect').val(value).trigger('change')
                    }else{
                        jQuery('#'+i).val(value);
                    }

                });
            }else{
                console.log('no share tab data');
            }

        }

        window.addDietary =    function addDietary(DietaryData){

            var DietType = DietaryData;

            if( DietType !== null){
                var html_add_guest_diet = '';
                var html_filter_diet = '';

                jQuery.each(DietType, function(i, item) {
                    var color = item.color;
                    if(color === null){
                        color = '#fff';
                    }
                    html_add_guest_diet += '<div><label class="title_label"><span class="svg_icon_radio"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="'+color+'" class="" viewBox="0 0 16 16" stroke="'+color+'" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>'+item.name+'<input type="checkbox" id="'+ item.id +'" name="dietary_requirements" value="'+ item.id +'" data_title_text="'+item.name+'" data_color="'+color+'"><span class="checkmark"></span></label><img src="./images/icons8-edit.png" class="edit_pencil"></div>';
                    html_filter_diet += '<div><span><input type="checkbox" name="guest_allocation_table_guest_allocation_filter_die_requi" id="diet_'+item.id+'" value="'+item.id+'"><label for="diet_'+item.id+'"> <span class="svg_icon_radio"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="'+color+'" class="" viewBox="0 0 16 16" stroke="'+color+'" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>'+item.name+'</label></span></div>';
                });
                jQuery(document).find('#guest_allocation_new_guest_add_user_dietary #guest_allocation_new_guest_table_create_new').before(html_add_guest_diet);
                jQuery(document).find('#guest_allocation_filter_dietary ').html(html_filter_diet);


            }else{
                console.log('No data for dietary Requirements')
            }

        }//addDietary

        window.addGuestsList = function addGuestsList(GuestsListData){
            // console.log('new check');
            var GuestsListD = GuestsListData;

            if( GuestsListD !== null){
                var html_guest_list = '';
                var html_filter_diet = '';
                var tableNum = [];

                function dynamicSort(property) {
                    var sortOrder = 1;
                    if(property[0] === "-") {
                        sortOrder = -1;
                        property = property.substr(1);
                    }
                    return function (a,b) {
                        /* next line works with strings and numbers,
                         * and you may want to customize it to your needs
                         */
                        var result = (parseInt(a[property]) < parseInt(b[property])) ? -1 : (parseInt(a[property]) > parseInt(b[property])) ? 1 : 0;
                        return result * sortOrder;
                    }
                }

                GuestsListD.sort(dynamicSort("id"));


                jQuery.each(GuestsListD, function(i, item) {
                    // console.log(item);
                    var Dietary = item.dietary;
                    var firstName = item.firstName;
                    var id = item.id;
                    var lastName = item.lastName;
                    var seatNumber = item.seatNumber;
                    var seatType = item.seatType;
                    var tableNumber = item.tableNumber;
                    var dietary_color = '#fff';
                    var dietary_text = 'none';
                    var dietary_id = 'none';
                    window.guestIdLast = item.id;

                    var dietary_allArray = Dietary.replace("[","").replace("]","").split(',');

                    var diet_arr = [];
                    if(window.NewDietaryArray && window.NewDietaryArray.length > 0){
                        diet_arr = window.NewDietaryArray;
                    }else{
                        diet_arr = App.GuestDietTypes;
                    }

                    // console.log(dietary_allArray[0]);
                    jQuery.each(diet_arr, function(i,item){
                        if(item.id == dietary_allArray[0]){
                            dietary_text = item.name;
                            dietary_color = item.color;
                            dietary_id = item.id;
                        }
                    });

                    if(dietary_allArray[0] == null || dietary_allArray[0] ==''){
                        dietary_allArray = 'none';
                    }

                    html_guest_list += "<div class='new_guest_entry' data_tableno='"+ item.tableNumber +"' data_seattype='"+ seatType +"'  data_dietary='"+ dietary_id +"' data_first='"+ firstName +"' data_lastname='"+lastName+"' data_seatnumber='"+ seatNumber +"' data_search='"+firstName +' '+ lastName +"' data_item_id='"+ item.id +"' data_dietary_all='"+ dietary_allArray +"'><input type='checkbox' name='guest' id='"+item.id+"'><label for='"+item.id+"'></label> <span class='custom_width'> <span class='svg_icon_radio'><svg xmlns='http://www.w3.org/2000/svg' width='20' height='12' fill='"+dietary_color+"' class='' viewBox='0 0 16 16' stroke='"+dietary_color+"' stroke-width='2px'><path fill-rule='evenodd' d='M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z'></path></svg></span>"+firstName +" " +lastName +"</span><span style='margin-left: 4px;'  class='custom_width_1 table_number'>"+tableNumber+"</span><span style='margin-left: 3px;'  class='custom_width_1 seat_number' >"+seatNumber+"</span></div>";


                    if(jQuery.inArray(tableNumber, tableNum) === -1){
                        tableNum.push(tableNumber);
                    }




                });
                jQuery(document).find('.new_geust_all_list').html(html_guest_list);

                addTableNumFilter(tableNum);
                // console.log('new check 3');


            }
            if(jQuery('.new_geust_all_list .new_guest_entry').length == 0){
                // console.log(jQuery('.new_geust_all_list .new_guest_entry:visible').length);
                // console.log(jQuery('.new_geust_all_list .new_guest_entry').length);
                // console.log('check 4')
                jQuery('.list_note').show();
            }else{
                jQuery('.list_note').hide();
                jQuery('.filter_note').hide();
            }


        }//addGuestsList

        function addTableNumFilter(tableNum){
            if(tableNum !== null){
                tableNum.sort(function(a, b){return a-b});
                var tableNumFilter = '';
                window.tableNumArray = tableNum;
                jQuery.each(tableNum, function(i, item) {
                    tableNumFilter += '<div class="table_num_'+item+'"><span><input type="checkbox" name="guest_filter_table_number" id="table_'+item+'" value="'+item+'"><label for="table_'+item+'">'+item+'</label></span></div>';
                });
                jQuery(document).find('#guest_filter_table_number_html').html(tableNumFilter);
            }
        }

        //sorting

        jQuery(document).on("click", ".arrows span" ,function(){

            var sortType = jQuery(this).attr('data-sort');

            var sorted_items,has_class;
            has_class = jQuery(this).hasClass('active');
            getSorted = function(selector, attrName) {
                return $(
                    $(selector).toArray().sort(function(a, b){
                        if(attrName === 'data_first'){
                            var aVal = a.getAttribute(attrName).toLowerCase(),
                                bVal = b.getAttribute(attrName).toLowerCase();
                            if(has_class){
                                //return bVal - aVal;
                                return (bVal <  aVal) ? 1 : -1;
                            }else{
                                return (aVal <  bVal) ? 1 : -1;
                            }

                        }else{

                            var aVal = parseInt(a.getAttribute(attrName)),
                                bVal = parseInt(b.getAttribute(attrName));
                            if(has_class){
                                return bVal - aVal;
                            }else{
                                return aVal - bVal;
                            }
                        }//else
                    })
                );
            };

            sorted_items = getSorted('.new_geust_all_list .new_guest_entry', sortType).clone();

            jQuery('.new_geust_all_list').html(sorted_items);
            jQuery(".arrows span").removeClass('active');
            if(has_class){
                jQuery(this).removeClass('active');
            }else{
                jQuery(this).addClass('active');
            }
        });

        //sorting

        //edit guest

        jQuery(document).on('dblclick', 'div#guest_allocation_table_summary_diet_requriments tr[data_id]', function(){
            var id = jQuery(this).attr('data_id');
            console.log(id);
            jQuery('div.new_guest_entry[data_item_id="'+ id +'"]').trigger('dblclick');

        });


        $(document).on('dblclick', '.new_guest_entry', function () {
            var data_tableno, data_seattype, data_dietary, data_first, data_lastname, data_seatnumber, data_search, data_id, dietary_all;
            data_tableno = jQuery(this).attr('data_tableno');
            data_seattype = jQuery(this).attr('data_seattype');
            data_dietary = jQuery(this).attr('data_dietary');
            data_first = jQuery(this).attr('data_first');
            data_lastname = jQuery(this).attr('data_lastname');
            data_seatnumber = jQuery(this).attr('data_seatnumber');
            data_id = jQuery(this).attr('data_item_id');
            dietary_all = jQuery(this).attr('data_dietary_all');

            //DietaryValue
            console.log(data_dietary);
            if(dietary_all === 'none' || data_dietary === ''|| data_dietary === 'none'){



                console.log('No data for dietary')
            }else{

                var dietary_allArray = dietary_all.replace("[","").replace("]","").split(',');
                var dietary_html_data = '';
                jQuery.each(dietary_allArray, function(i,item){
                    var diet_detail = getDietaryValue(item);
                    dietary_html_data += '<p class="select_dietary" data_color="'+diet_detail["dietary_color"] +'" data_title-text="'+diet_detail["dietary_text"] +'"><span> <span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="'+diet_detail["dietary_color"] +'" class="" viewBox="0 0 16 16" stroke="'+diet_detail["dietary_color"] +'" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>'+diet_detail["dietary_text"] +'</span> <span class="select_dietary_close"> x  </span> </p>';
                    jQuery('input[name="dietary_requirements"][id="'+diet_detail["id"]+'"]').prop("checked",true);
                });

            }
            //DietaryValue

            //feed data
            jQuery('#new_guest_table #guest_allocation_new_guest_table_add_dietary_option').html(dietary_html_data);
            jQuery('#new_guest_table #guest_allocation_new_guest_table_firstname').val(data_first);
            jQuery('#new_guest_table #guest_allocation_new_guest_table_lastname').val(data_lastname);
            jQuery('#new_guest_table #guest_allocation_new_guest_table_number').val(data_tableno);
            jQuery('#new_guest_table #guest_allocation_new_guest_table_seat_type').val(data_seattype);
            jQuery('#new_guest_table #guest_allocation_new_guest_table_seat_number').val(data_seatnumber);

            jQuery('#new_guest_table #guest_allocation_new_guest_table_item_id').val(data_id);

            jQuery('#new_guest_table_view h4').html('Edit Guest');
            jQuery('#new_guest_table').addClass('active');
            App.updateGuestTablePreview();
            console.log('previ function 2');

            //feed data

        });

        //edit guest

        //edit dietary

        jQuery(document).on("click", ".edit_pencil" ,function(){
            var diet_details, diet_title, diet_color,diet_id;
            jQuery(".create_new_dietary" ).addClass('active');
            jQuery('.die_req_list').toggleClass('active');
            diet_details = jQuery(this).parent('div').find('input[name="dietary_requirements"]');
            diet_title = diet_details.attr('data_title_text');
            diet_color = diet_details.attr('data_color');
            diet_id = diet_details.attr('id');
            editDietary(diet_title,diet_color,diet_id);
        });

        function editDietary(diet_title,diet_color,diet_id){

        }

        //edit dietary

        window.getDietaryValue = function getDietaryValue(dietName){
            var dietaryDetails = [];
            var   dietDataAll = App.GuestDietTypes;
            if(window.NewDietaryArray && window.NewDietaryArray.length > 0){
                dietDataAll = window.NewDietaryArray;
            }


            jQuery.each(dietDataAll, function(i,item){
                if(item.id == dietName){
                    dietaryDetails['dietary_text'] = item.name;
                    dietaryDetails['dietary_color'] = item.color;
                    dietaryDetails['id'] = item.id;
                }
            });
            return  dietaryDetails;
        }

        jQuery(document).on('click', '#modify_table_guest_allocation', function(){

            // addSeatType(App.GuestSeatTypes);
            // addDietary(App.GuestDietTypes);
            // addGuestsList(App.GuestAllocUsers);

        }); //

        // addSeatType(App.GuestSeatTypes);
        // console.log('check this');
        // addDietary(App.GuestDietTypes);
        // addGuestsList(App.GuestAllocUsers);
//search function
        jQuery(document).on('input', '#guest_allocation_table_search_guests', function(){
            var valu = jQuery(this).val();
            jQuery('.new_geust_all_list .new_guest_entry').each(function(){
                var search = jQuery(this).attr('data_search').toLowerCase();
                if(search.indexOf(valu) != -1){
                    jQuery(this).show();
                }else{
                    jQuery(this).hide();
                }
            });
            if(jQuery('.new_geust_all_list .new_guest_entry:visible').length == 0){
                jQuery('.filter_note').show();
            }else{
                jQuery('.filter_note').hide();
                jQuery('.list_note').hide();
            }
        });//search function


//Allocation tab, Delete and assign table
        jQuery(document).on('click','#allcation_user_delete_confirmation_yes', function(){
            jQuery('.new_geust_all_list input[name="guest"]:checked').parent('div.new_guest_entry').remove();
            jQuery('#guest_allocation_table_search_checkbox').prop('checked',false).trigger('change');
            jQuery('#allcation_user_delete_confirmation').hide();
// jQuery('#allcation_user_delete_confirmation').hide();

            regenerateDataArray();
        });

        jQuery(document).on('click','#guest_allocation_table_delete_btn', function(){
            jQuery('#allcation_user_delete_confirmation').show();
        });

        jQuery(document).on('click','#allcation_user_delete_confirmation_no', function(){
            jQuery('#allcation_user_delete_confirmation').hide();
        });

        jQuery(document).on('click', 'div#guest_allocation_table #list_tab ul button', function(){

// regenerateDataArray();

        });

        jQuery(document).on('change', '#guest_allocation_table_share input, #guest_allocation_table_share select', function(){

            //    setTimeout(function() {
            //        if(window.shareData == false){
            //            regenerateDataArray_share_tab();
            //        }else{
            // // console.log('nope');
            //        }
            //          }, 2000);
        });

        jQuery(document).on('click', '#guest_allocation_table_share_geust_list_url_copy', function(){

            var copyText = document.getElementById("guest_allocation_table_share_geust_list_url_sharable_url");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            console.log("Copied the url: " + copyText.value);

        });

        jQuery(document).on('click', '#guest_allocation_table_share_geust_list_url_open', function(){

            var url = 'https://'+jQuery("#guest_allocation_table_share_geust_list_url_sharable_url").val();

            window.open(url, '_blank');

        });


        function regenerateDataArray_share_tab(){
            console.log('share tab change');

            // var data_array = window.shareDataArray;
            //      if(!window.shareDataArray){
            // Share Tab Data start
            var share_arr = JSON.parse('{"guest_allocation_table_share_event_name": "'+jQuery('#guest_allocation_table_share_event_name').val()+'", "guest_allocation_table_share_submission_dealine": "'+jQuery('#guest_allocation_table_share_submission_dealine').val()+'", "guest_allocation_table_share_password_protect": "'+jQuery('#guest_allocation_table_share_password_protect').val()+'", "guest_allocation_table_share_geust_list_url_status_drop": "'+ jQuery('#guest_allocation_table_share_geust_list_url_status_drop').val() +'", "guest_allocation_table_share_passowrd": "'+jQuery('#guest_allocation_table_share_passowrd').val()+'", "guest_allocation_table_share_guest_list_link_button": "'+jQuery('#guest_allocation_table_share_guest_list_link_button:checked').length+'", "guest_allocation_table_share_geust_list_url_sharable_url": "'+jQuery('#guest_allocation_table_share_geust_list_url_sharable_url').val()+'" }');

            window.shareDataArray = share_arr;
            // console.log( 'only share data');

            // console.log(  window.shareDataArray);
            //     window.shareData = true;
            //     setTimeout(function() {
            // window.shareData = false
            //   }, 3000);

            // }else{
            //     console.log( 'only share data');

            // }

            // Share Tab Data end
        }

// jQuery(document).on('click','#guest_allocation_table_add_to_table_save_btn', function(){
// var table_new = jQuery('#guest_allocation_add_to_table_table_number').val();
// jQuery('.new_geust_all_list input[name="guest"]:checked').parent('div.new_guest_entry').attr('data_tableno',table_new);
// jQuery('.new_geust_all_list input[name="guest"]:checked').parent('div.new_guest_entry').find('span.custom_width_1.table_number').html(table_new);
//  jQuery('.add_to_table_popupform').removeClass('popup');
// regenerateDataArray();
// updateTableNumFilter(table_new);
// });

        jQuery(document).on('click','#guest_allocation_table_add_to_table_save_btn', function(){
            var table_new = jQuery('#guest_allocation_add_to_table_table_number').val();
            var table_count = App.getTableChairsCount(table_new);

            var items_len = jQuery('.new_geust_all_list input[name="guest"]:checked').length;

            if(items_len > table_count ){
                console.log('Table not suitable for selected guests');
            }



            jQuery('.new_geust_all_list input[name="guest"]:checked').each(function(){
                var ex_table = jQuery(this).parent('div.new_guest_entry').attr('data_tableno');
                var ex_seat = jQuery(this).parent('div.new_guest_entry').attr('data_seatnumber');

                if(ex_table === table_new){
                    //check duplicate

                    var seat_stat = jQuery('.new_guest_entry[data_seatnumber="'+ex_seat+'"][data_tableno="'+table_new+'"]').length;
                    //console.log('ttt' + seat_stat)
                    if(seat_stat > 1){
                        var next_tem = jQuery(this).next().parent('div');
                        console.log(' newsds ' + table_new);

                        for(i = 1; i < table_count; i++) {
                            //console.log(i);
                            var seat_stat = jQuery('.new_guest_entry[data_seatnumber="'+i+'"][data_tableno="'+table_new+'"]').length;
                            if(seat_stat === 0){

                                console.log(next_tem);
                                jQuery(next_tem).attr('data_tableno',table_new);
                                jQuery(next_tem).attr('data_seatnumber',i);
                                jQuery(next_tem).find('span.custom_width_1.table_number').html(table_new);
                                jQuery(next_tem).find('span.custom_width_1.seat_number').html(i);

                                break;
                            }
                        }//for loop


                    }
                    //check duplicate

                }else{
                    for(i = 1; i < table_count; i++) {
                        //console.log(i);
                        var seat_stat = jQuery('.new_guest_entry[data_seatnumber="'+i+'"][data_tableno="'+table_new+'"]').length;
                        if(seat_stat === 0){

                            console.log(i+ ' new ' + table_new);
                            jQuery(this).parent('div.new_guest_entry').attr('data_tableno',table_new);
                            jQuery(this).parent('div.new_guest_entry').attr('data_seatnumber',i);
                            jQuery(this).parent('div.new_guest_entry').find('span.custom_width_1.table_number').html(table_new);
                            jQuery(this).parent('div.new_guest_entry').find('span.custom_width_1.seat_number').html(i);
                            break;
                        }
                    }//for loop
                }
            });

            jQuery('.add_to_table_popupform').removeClass('popup');
            regenerateDataArray();
            updateTableNumFilter(table_new);

        });


//Allocation tab, Delete and assign table


    } //initData


    function SummaryDetails(){

        window.GenerateSummary = function GenerateSummary(){

            console.log('generating Summary');

            var dietArray = App.GuestDietTypes;
            var guestArray = App.GuestAllocUsers;
            var seatArray = App.GuestSeatTypes;

            if(window.NewDietaryArray && window.NewDietaryArray.length > 0 || window.NewGuestArray && window.NewGuestArray.length > 0){
                var dietArray = window.NewDietaryArray;
                var guestArray = window.NewGuestArray;
            }
            //sort array function
            window.dynamicSort =     function dynamicSort(property) {
                var sortOrder = 1;
                if(property[0] === "-") {
                    sortOrder = -1;
                    property = property.substr(1);
                }
                return function (a,b) {
                    /* next line works with strings and numbers,
                     * and you may want to customize it to your needs
                     */
                    var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                    return result * sortOrder;
                }
            }
            //sort array function


            //tables
            var tableData = [];
            var tableData_seat = [];
            var all_seats = 0;

            jQuery.each( guestArray, function( i, item ) {
                var tableNum = item.tableNumber;
                if(jQuery.inArray(tableNum, tableData) === -1){
                    var seat = 1;
                    tableData.push(tableNum);
                    tableData_seat.push({tableNum,seat});
                }else{
                    var objIndex = tableData_seat.findIndex((obj => obj.tableNum == item.tableNumber));
                    //Update tableData_seat property.
                    tableData_seat[objIndex].seat = tableData_seat[objIndex].seat + 1;
                }
            });

            tableData_seat.sort(dynamicSort("tableNum"));
            var table_html = '<tr><th data-short="data_table">Table #<svg class="arrow_down" viewBox="4 2 9 11.999" width="9" height="11">            <path id="arrow_down" d="M 8.309080123901367 13.9616003036499 C 8.496039390563965 14.03855991363525 8.71092700958252 13.99572086334229 8.854080200195313 13.85295009613037 L 12.85346984863281 9.853549957275391 C 13.04878520965576 9.658340454101563 13.04878520965576 9.341758728027344 12.85352325439453 9.146495819091797 C 12.65826034545898 8.951233863830566 12.34167861938477 8.951233863830566 12.14641571044922 9.146495819091797 L 9 12.29300022125244 L 9 2.5 C 9 2.223857641220093 8.776142120361328 2 8.5 2 C 8.223857879638672 2 8 2.223857641220093 8 2.5 L 8 12.29300022125244 L 4.853519916534424 9.146510124206543 C 4.658311367034912 8.951193809509277 4.341729164123535 8.951193809509277 4.146466732025146 9.146455764770508 C 3.951204538345337 9.341717720031738 3.951204299926758 9.658300399780273 4.146466732025146 9.85356330871582 L 8.145870208740234 13.85299968719482 C 8.192574501037598 13.89961528778076 8.248043060302734 13.93652534484863 8.309080123901367 13.96160125732422 Z"></path></svg></th><th data-short="data_chairs">Chairs<svg class="arrow_down" viewBox="4 2 9 11.999" width="9" height="11"><path id="arrow_down" d="M 8.309080123901367 13.9616003036499 C 8.496039390563965 14.03855991363525 8.71092700958252 13.99572086334229 8.854080200195313 13.85295009613037 L 12.85346984863281 9.853549957275391 C 13.04878520965576 9.658340454101563 13.04878520965576 9.341758728027344 12.85352325439453 9.146495819091797 C 12.65826034545898 8.951233863830566 12.34167861938477 8.951233863830566 12.14641571044922 9.146495819091797 L 9 12.29300022125244 L 9 2.5 C 9 2.223857641220093 8.776142120361328 2 8.5 2 C 8.223857879638672 2 8 2.223857641220093 8 2.5 L 8 12.29300022125244 L 4.853519916534424 9.146510124206543 C 4.658311367034912 8.951193809509277 4.341729164123535 8.951193809509277 4.146466732025146 9.146455764770508 C 3.951204538345337 9.341717720031738 3.951204299926758 9.658300399780273 4.146466732025146 9.85356330871582 L 8.145870208740234 13.85299968719482 C 8.192574501037598 13.89961528778076 8.248043060302734 13.93652534484863 8.309080123901367 13.96160125732422 Z"></path>          </svg></th></tr><tr><td colspan="2"><table class="inner_table_cust" style="margin:0;">';
            var total_seat = 0;



            jQuery.each( tableData_seat, function( i, item ) {
                var tab_n = item.tableNum;
                var tab_n_seat = item.seat;
                var num_of_chair = parseInt(App.getTableChairsCount(tab_n));
                all_seats = all_seats + num_of_chair;
                var percentage = Math.round(((item.seat / num_of_chair) * 100)) +"%";
                total_seat = total_seat+item.seat;
                table_html += '<tr data_table="'+ tab_n +'" data_chairs="'+Math.round(((item.seat / num_of_chair) * 100)) +'"><td>'+ tab_n +'</td><td>'+tab_n_seat+'/'+num_of_chair+'<span>('+percentage+')</span></td></tr>';

            });
            // console.log(all_seats);
            table_html +=  '</table></td></tr><tr><td><strong>Total chair</strong></td><td><strong>'+total_seat+'/'+ all_seats +'</strong><span>('+Math.round(((total_seat / (all_seats)) * 100))+'%)</span></td></tr>';
            jQuery('#guest_allocation_table_summary_tables_data').html(table_html);

            //tables

            //dietary Requriments

            var dietreq = [];
            var dietreq_blank = [];


            jQuery.each( dietArray, function( i, item ) {
                var dietId = item.id;
                var dietName = item.name;
                var dietColor = item.color;
                var dietDetailsData = [];
                var dietreq_blank_indev = [];

// console.log(guestArray);
                jQuery.each( guestArray, function( i, guest ) {
                    var guestDiet = guest.dietary;
                    // console.log(guestDiet);
                    // console.log(guest.firstName);


                    var dietary_allArray = guestDiet.replace("[","").replace("]","").split(',');
                    if(jQuery.inArray(dietId, dietary_allArray) !== -1){
                        var id= guest.id;
                        var lname = guest.lastName;
                        var table= guest.tableNumber;
                        var seat = guest.seatNumber;
                        var fname= guest.firstName;
                        dietDetailsData.push({id,fname,lname,table,seat});
                    }

                    // if(guestDiet == 'none'){

                    //     var id= guest.id;
                    //     var lname = guest.lastName;
                    //     var table= guest.tableNumber;
                    //     var seat = guest.seatNumber;
                    //     var fname= guest.firstName;
                    //     dietreq_blank_indev.push({id,fname,lname,table,seat});
                    //     console.log(fname);
                    // }

                });
                dietreq.push({dietId,dietDetailsData,dietName,dietColor});
                // dietreq_blank.push({dietreq_blank_indev});
            });

            console.log(dietreq_blank);
            // var dupes = [];
            // var uniqueGuest = [];
            // $.each(dietreq_blank, function (index, entry) {
            //     if (!dupes[entry.id]) {
            //         dupes[entry.id] = true;
            //         uniqueGuest.push(entry);
            //     }
            // });
            // console.log(uniqueGuest);



            var html_data = '';
            var html_data_blank = '';

            jQuery.each( dietreq, function( i, diet ) {

                if(diet.dietDetailsData.length > 0){
                    // console.log(diet.dietId + diet.dietName + diet.dietColor);
                    var table_html = '<div class="guest_allocation_table_summary_row_die">';
                    table_html += '<div class="title"><span class="svg_icon_radio"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="'+ diet.dietColor+'" class="" viewBox="0 0 16 16" stroke="'+ diet.dietColor+'" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>'+ diet.dietName+'<span class="chair_qty">'+diet.dietDetailsData.length+' </span></div><div class="table_details_inner"><table><tbody>';
                    jQuery.each( diet.dietDetailsData, function( i, guest ) {
                        table_html += '<tr data_id="'+ guest.id +'"><td>'+ guest.fname+ ' '+ guest.lname +'</td><td>Table '+ guest.table +'</td><td>Seat '+ guest.seat +'</td></tr>';
                    });

                    table_html += '</tbody></table></div></div>';
                    html_data += table_html;
                }
            });

            // if(uniqueGuest.length > 0){
            //     jQuery.each( uniqueGuest, function( i, data ) {
            //         console.log(data);

            //         jQuery.each( diet.dietDetailsData, function( i, guest ) {
            //         table_html += '<tr><td>'+ guest.fname+ ' '+ guest.lname +'</td><td>Table '+ guest.table +'</td><td>Seat '+ guest.seat +'</td></tr>';
            //         });

            //     });
            // }// 0
            var not_requr  = 0;

            var table_html_sing = '';
            jQuery.each( guestArray, function( i, guest ) {
                console.log(guest);
                if(guest.dietary == 'none' || guest.dietary == ''){
                    table_html_sing += '<tr data_id="'+ guest.id +'"><td>'+ guest.firstName+ ' '+ guest.lastName +'</td><td>Table '+ guest.tableNumber +'</td><td>Seat '+ guest.seatNumber +'</td></tr>';
                    not_requr++;
                }
            });

            if(not_requr > 0){
                console.log(table_html_none);
            }

            var table_html_none = '<div class="guest_allocation_table_summary_row_die"><div class="title"><span class="svg_icon_radio"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="12" fill="#ff" class="" viewBox="0 0 16 16" stroke="#000" stroke-width="2px"><path fill-rule="evenodd" d="M0 3a2 2 0 0 1 2-2h7.08a2 2 0 0 1 1.519.698l4.843 5.651a1 1 0 0 1 0 1.302L10.6 14.3a2 2 0 0 1-1.52.7H2a2 2 0 0 1-2-2V3zm9.854 2.854a.5.5 0 0 0-.708-.708L7 7.293 4.854 5.146a.5.5 0 1 0-.708.708L6.293 8l-2.147 2.146a.5.5 0 0 0 .708.708L7 8.707l2.146 2.147a.5.5 0 0 0 .708-.708L7.707 8l2.147-2.146z"></path></svg></span>Not Requried<span class="chair_qty">'+ not_requr +' </span></div><div class="table_details_inner"><table><tbody>'+ table_html_sing +'</tbody></table></div></div>';


            html_data += table_html_none;

            // console.log(html_data);
            jQuery('#guest_allocation_table_summary_diet_requriments').html(html_data);


            //Dietary Requriments

            //Seating Types
            var seating_type_html = '';

            jQuery.each(guestArray, function( i, item ) {
                //console.log(item.seatType)
                var objIndex = seatArray.findIndex((obj => obj.id == item.seatType));
                //console.log(seatArray[objIndex].name);
                seating_type_html += '<tr data_table="'+ item.tableNumber +'" data_seat_no="'+item.seatNumber+'" data_seat_type="'+seatArray[objIndex].id+'"><td>'+ item.tableNumber +'</td><td>'+item.seatNumber+'</td><td>'+seatArray[objIndex].name+' </td></tr>';
            });
            jQuery('#guest_allocation_table_summary_seating_types tbody').html(seating_type_html);

            //Seating Types

        }

        //shorting function starts
        getSorted = function(selector, attrName, has_class) {
            // console.log(has_class);
            return $(
                $(selector).toArray().sort(function(a, b){
                    if(attrName === 'data_first' || attrName === 'data_seat_type'){
                        var aVal = a.getAttribute(attrName).toLowerCase(),
                            bVal = b.getAttribute(attrName).toLowerCase();
                        if(has_class){
                            //return bVal - aVal;
                            return (bVal <  aVal) ? 1 : -1;
                        }else{
                            return (aVal <  bVal) ? 1 : -1;
                        }

                    }else{

                        var aVal = parseInt(a.getAttribute(attrName)),
                            bVal = parseInt(b.getAttribute(attrName));
                        if(has_class){
                            return bVal - aVal;
                        }else{
                            return aVal - bVal;
                        }
                    }//else
                })
            );
        };



        jQuery(document).on("click", "#guest_allocation_table_summary_tables_data th" ,function(){

            var sortType = jQuery(this).attr('data-short');
            console.log(sortType);
            var sorted_items,has_class;
            has_class = jQuery(this).hasClass('active');

            sorted_items = getSorted('#guest_allocation_table_summary_tables_data table tr', sortType, has_class).clone();

            jQuery('#guest_allocation_table_summary_tables_data table').html(sorted_items);
            jQuery("#guest_allocation_table_summary_tables_data th").removeClass('active');
            jQuery("#guest_allocation_table_summary_tables_data th").removeClass('sorted_on');
            jQuery(this).addClass('sorted_on');
            if(has_class){
                jQuery(this).removeClass('active');
            }else{
                jQuery(this).addClass('active');
            }
        });

        jQuery(document).on("click", "#guest_allocation_table_summary_seating_types th" ,function(){

            var sortType = jQuery(this).attr('data-short');
            var sorted_items,has_class;
            has_class = jQuery(this).hasClass('active');

            sorted_items = getSorted('#guest_allocation_table_summary_seating_types tbody tr', sortType, has_class).clone();

            jQuery('#guest_allocation_table_summary_seating_types tbody').html(sorted_items);
            jQuery("#guest_allocation_table_summary_seating_types th").removeClass('active');

            jQuery("#guest_allocation_table_summary_seating_types th").removeClass('sorted_on');
            jQuery(this).addClass('sorted_on');

            if(has_class){
                jQuery(this).removeClass('active');
            }else{
                jQuery(this).addClass('active');
            }
        });


        //shorting function end


    }  //SummaryDetails

    function PrintTab(){
        console.log('PrintTab function excute');
        jQuery(document).on("click", "#guest_allocation_table_print_tab" ,function(){
            jQuery(".remove_class_selected").removeClass('selected');
            jQuery("#guest_allocation_table_print").toggleClass('selected');
            jQuery("div#list_tab ul button").removeClass('selected');
            jQuery(this).toggleClass('selected');
        });

        // jQuery(document).on('click', '#guest_allocation_table_print_btn', function(){
        //     // var print_type = jQuery
        //     jQuery('#print_tab_confirm_popup').show();
        // });

        jQuery(document).on('click', '#print_tab_confirm_popup_cancel', function(){
            // var print_type = jQuery
            jQuery('#print_tab_confirm_popup').hide();
        });

        jQuery(document).on('click', '#guest_allocation_table_print_download', function(){
            // var print_type = jQuery
            print('pdf')
            jQuery('#print_tab_confirm_popup').hide();
        });

        jQuery(document).on('click', '#print_tab_confirm_popup_xls', function(){
            // var print_type = jQuery
            print('xls')
            jQuery('#print_tab_confirm_popup').hide();
        });



        function print(type){

            var dietArray_print = App.GuestDietTypes;
            var guestArray_print = App.GuestAllocUsers;
            var print_array = [];

            if(window.NewDietaryArray && window.NewDietaryArray.length > 0 || window.NewGuestArray && window.NewGuestArray.length > 0){
                var dietArray_print = window.NewDietaryArray;
                var guestArray_print = window.NewGuestArray;
            }

            //code

            jQuery.each(guestArray_print, function(i, data_single) {
                //diet
                var diet = data_single.dietary;
                var diet_name = '';
                if(diet == '' || diet == 'none' ){
                    diet_name = 'none'
                }else{
                    var  diet_array_single = diet.replace("[","").replace("]","").split(',');
                    jQuery.each(diet_array_single, function(i, item_diet) {
                        var objIndex = dietArray_print.findIndex((obj => obj.id == item_diet));
                        diet_name += dietArray_print[objIndex].name;

                        if(diet_array_single.length > i+1){
                            diet_name += ','
                        }
                    });

                }//else
                //diet

                //seat
                var objIndex = App.GuestSeatTypes.findIndex((obj => obj.id == data_single.seatType));
                var seatName = App.GuestSeatTypes[objIndex].name;
                //seat



                var diet_arr = JSON.parse('{"First Name": "'+data_single.firstName+'", "Last Name": "'+data_single.lastName+'","Seat Number": "'+data_single.seatNumber+'","Seat Type": "'+seatName+'","Table Number": "'+data_single.tableNumber+'","Dietary": "'+ diet_name +'"}');

                print_array.push(diet_arr);
            });


            //code




            var json = print_array;
            var fields = Object.keys(json[0])
            var replacer = function(key, value) { return value === null ? '' : value }
            var csv = json.map(function(row){
                return fields.map(function(fieldName){
                    return JSON.stringify(row[fieldName], replacer)
                }).join(',')
            })
            csv.unshift(fields.join(',')) // add header column
            csv = csv.join('\r\n');
            console.log(csv)
            function save(filename, data) {
                var blob = new Blob([data], {type: 'text/csv'});
                if(window.navigator.msSaveOrOpenBlob) {
                    window.navigator.msSaveBlob(blob, filename);
                }
                else{
                    var elem = window.document.createElement('a');
                    elem.href = window.URL.createObjectURL(blob);
                    elem.download = filename;
                    document.body.appendChild(elem);
                    elem.click();
                    document.body.removeChild(elem);
                }
            }
            // save('dsds.csv',csv);
            save('guest.csv',csv);


        }


    }//PrintTab

    function tableDesigner(){
        console.log('table Designer excuted');

        //number field fix

        // jQuery(document).on("click","#sidebar_right_table_design .custom_toggle_buton div", function(e) {
        //         // e.preventdefault();
        //       var button = jQuery(this);
        //       var oldValue = button.parent().parent().find("input").val();
        //       var step = parseFloat(button.parent().parent().find("input").attr('step'));
        //        if(oldValue == ''){
        //         oldValue = 0;
        //        }

        //        if(step == ''){
        //         step = 1;
        //        }

        //       if (button.hasClass('custom_toggle_buton_up') == true) {
        //           var newVal = parseFloat(oldValue) + step;
        //         } else {
        //        // Don't allow decrementing below zero
        //         if (oldValue > 0) {
        //           var newVal = parseFloat(oldValue) - step;
        //         } else {
        //           newVal = 0;
        //         }
        //       }

        //       button.parent().parent().find("input").val(newVal).delay( 800 ).trigger('change');
        //       // button.parent().parent().find("input").trigger('change');
        //       // button.trigger('click');
        //       // console.log(e);

        //     });



        //number field fix


        jQuery(document).on('change','input[name="table_design_selection_type"]',function(){
            var valu = jQuery(this).val();
            jQuery('#table_design_round_table, #table_design_rectangular_table, #table_design_oval_table').hide();
            // console.log(valu);
            jQuery('#table_design_'+valu+'_table').show()

        });

        jQuery(document).on('click', '#table_design_selection label', function(){
            var value =  jQuery(this).attr('for');
            // console.log(value);
            jQuery("input[name=table_design_selection_type][value='" + value + "']").prop("checked", true).trigger('change');

        });

        jQuery(document).on('click', '#toolbar_extras_table_design', function(){

            addUserUnit();
            jQuery(document).find('#sidebar_right_table_design').find('input[type="number"]').parent('span').css('position','relative').css('display','inline-block');
            // sidebar_right_table_design
            tableDesignDataPush();

        });

        window.addUserUnit =  function addUserUnit(){
            var user_unit = App.measureUnit;
            //console.log(user_unit);
            if(user_unit == 'BOTH'){
                user_unit = 'm';
            }
            setTimeout(function() {
                jQuery(document).find('#sidebar_right_table_design').find('i.field_unit').html(user_unit);
            }, 100);
            // setDefValue(user_unit);

            //check max and set values
            check_max_and_set('#table_design_round_table_max_chairs','#table_design_round_table_default_chairs');
            check_max_and_set('#table_design_rectangular_table_max_chair_top','#table_design_rectangular_table_default_chair_top');
            check_max_and_set('#table_design_rectangular_table_max_chair_bottom','#table_design_rectangular_table_default_chair_bottom');
            check_max_and_set('#table_design_rectangular_table_max_chair_left','#table_design_rectangular_table_default_chair_left');
            check_max_and_set('#table_design_rectangular_table_max_chair_right','#table_design_rectangular_table_default_chair_right');
            check_max_and_set('#table_design_oval_table_max_chairs','#table_design_oval_table_default_chairs');

            var valu =  jQuery('input[name="table_design_selection_type"]:checked').val();
            enable_create_button(valu);
            //check max and set values

        }

        window.setDefValue =  function setDefValue(user_unit){


        }


        jQuery(document).on('change, keyup','#sidebar_right_table_design input[type="number"]', function() {
            var currentInput = jQuery(this).val();
            var fixedInput = currentInput.replace(/[A-Za-z!>=+\?<@#/\/$%^&*[{}\]~`_()|"\\\\']/g, '');
            var fixedInput_n = fixedInput.replace(/[/-]/g, '');
            var user_unit = jQuery(this).attr('user_unit');
            jQuery(this).val(fixedInput_n);

        });

        jQuery(document).on('click', '#table_design_create',function(){
            tableDesignDataCreate();
        });

        //create data array

        window.tableDesignDataCreate = function tableDesignDataCreate(){


            var round_table_data = [];
            var reactangular_table_data = [];
            var oval_table_data = [];
            var tables_data_all = [];
            var sel_table = jQuery('input[name="table_design_selection_type"]:checked').val();

            jQuery('#table_design_round_table input').each(function(){

                var name = jQuery(this).attr('name');
                var data = jQuery(this).val();
                if(jQuery(this).attr('id') == "table_design_round_table_cabaret_style"){
                    if(jQuery('#table_design_round_table_cabaret_style:checked').length > 0){
                        data = 'yes';
                    }else{
                        data = 'no';
                    }
                }


                var table_data_field = JSON.parse('{"name": "'+name +'", "value": "'+data+'"}');
                round_table_data.push(table_data_field);
            });
            tables_data_all.round = round_table_data;



            jQuery('#table_design_rectangular_table input').each(function(){

                var name = jQuery(this).attr('name');
                var data = jQuery(this).val();


                var table_data_field = JSON.parse('{"name": "'+name +'", "value": "'+data+'"}');
                reactangular_table_data.push(table_data_field);
            });
            tables_data_all.rectangular = reactangular_table_data;

            jQuery('#table_design_oval_table input').each(function(){

                var name = jQuery(this).attr('name');
                var data = jQuery(this).val();
                if(jQuery(this).attr('id') == "table_design_oval_table_cabaret_style"){
                    if(jQuery('#table_design_oval_table_cabaret_style:checked').length > 0){
                        data = 'yes';
                    }else{
                        data = 'no';
                    }
                }


                var table_data_field = JSON.parse('{"name": "'+name +'", "value": "'+data+'"}');
                oval_table_data.push(table_data_field);
            });
            tables_data_all.oval = oval_table_data;

            tables_data_all.selected_table_view = sel_table;
            window.table_design_array = tables_data_all;
            console.log(window.table_design_array);
        }

        //create data array
        //push data
        window.tableDesignDataPush = function tableDesignDataPush(){
// console.log(window.table_design_array.table_data.length);
            if(window.table_design_array){
                console.log('existing data');

                //New code

                var round_data = window.table_design_array.round;
                var rect_data = window.table_design_array.rectangular;
                var oval_data = window.table_design_array.oval;

                jQuery.each( round_data, function( i, val ) {
                    if(val.name == 'table_design_round_table_cabaret_style'){
                        if(val.value == 'yes'){
                            jQuery('#table_design_round_table_cabaret_style').prop('checked',true)
                        }
                    }else{
                        jQuery('#'+ val.name).val(val.value)
                    }
                });

                jQuery.each( rect_data, function( i, val ) {
                    jQuery('#'+ val.name).val(val.value)
                });

                jQuery.each( oval_data, function( i, val ) {
                    if(val.name == 'table_design_oval_table_cabaret_style'){

                        if(val.value == 'yes'){
                            jQuery('#table_design_oval_table_cabaret_style').prop('checked',true)
                        }

                    }else{
                        jQuery('#'+ val.name).val(val.value)
                    }
                });

                //New code

                // var table_name  = window.table_design_array.table_name;
                // var table_data = window.table_design_array.table_data;
                // jQuery("input[name=table_design_selection_type][value=" + table_name + "]").attr('checked', 'checked').trigger('change');
                // jQuery.each( table_data, function( i, val ) {

                // jQuery('#'+ val.name).val(val.value)
                //             });



            }else{
                console.log('No existing Field data');
            }

            window.addUserUnit();


        }
        //push data


    }//tableDesigner

    function combineSidebar(){
        console.log('table combine function')
        jQuery(document).on('click', 'div#tab_select h4', function(){
            jQuery('div#tab_select h4').removeClass('active_tab');
            jQuery(this).addClass('active_tab');
            var tab_id = jQuery(this).attr('data_table');
            jQuery('.sidebar_right > div').hide();
            jQuery('.sidebar_right > div[id="'+ tab_id +'"').show();

            window.addUserUnit();

        });


        //check max chairs
        jQuery(document).on('click','div[id^="table_design_round_table_max_chairs_number_"], div[id^="table_design_round_table_default_chairs_number_"]', function(){check_max_and_set('#table_design_round_table_max_chairs','#table_design_round_table_default_chairs');
        });

        jQuery(document).on('click','div[id^="table_design_rectangular_table_max_chair_top_number_"], div[id^="table_design_rectangular_table_default_chair_top_number_"]', function(){

            check_max_and_set('#table_design_rectangular_table_max_chair_top','#table_design_rectangular_table_default_chair_top');
        });

        jQuery(document).on('click','div[id^="table_design_rectangular_table_max_chair_bottom_number_"], div[id^="table_design_rectangular_table_default_chair_bottom_number_"]', function(){
            check_max_and_set('#table_design_rectangular_table_max_chair_bottom','#table_design_rectangular_table_default_chair_bottom');
        });

        jQuery(document).on('click','div[id^="table_design_rectangular_table_max_chair_left_number_"], div[id^="table_design_rectangular_table_default_chair_left_number_"]', function(){
            check_max_and_set('#table_design_rectangular_table_max_chair_left','#table_design_rectangular_table_default_chair_left');
        });

        jQuery(document).on('click','div[id^="table_design_rectangular_table_max_chair_right_number_"], div[id^="table_design_rectangular_table_default_chair_right_number_"]', function(){
            check_max_and_set('#table_design_rectangular_table_max_chair_right','#table_design_rectangular_table_default_chair_right');
        });

        jQuery(document).on('click','div[id^="table_design_oval_table_max_chairs_number_"], div[id^="table_design_oval_table_default_chairs_number_"]', function(){
            check_max_and_set('#table_design_oval_table_max_chairs','#table_design_oval_table_default_chairs');
        });

        jQuery(document).on('change','input[name="table_design_selection_type"]',function(){
            var valu = jQuery(this).val();
            enable_create_button(valu);
        });


        jQuery(document).on('click','#sidebar_right_table_design div[id$="_number_up"],#sidebar_right_table_design div[id$="_number_down"]',function(){
            var valu =  jQuery('input[name="table_design_selection_type"]:checked').val();
            enable_create_button(valu);

        });

        window.check_max_and_set = function check_max_and_set(max,set){
            //   console.log('check_max_and_set');
            var max_c = parseInt(jQuery(max).val());
            var set_c = parseInt(jQuery(set).val());
            if(set_c > max_c){
                jQuery(set).css('border-color','red');
            }else{
                jQuery(set).css('border-color','#d7dede');
            }
            var valu =  jQuery('input[name="table_design_selection_type"]:checked').val();
            enable_create_button(valu);
        }//check max and set

        window.enable_create_button =   function enable_create_button(valu){

            var check = true;
            jQuery(document).find('#sidebar_right_table_design #table_design_'+valu+'_table input').each(function(){
                var bor = jQuery(this).css('border-color');
                // console.log(bor);
                if(bor == 'red' || bor == 'rgb(255, 0, 0)'){
                    check = false;
                }

            });

            if(check == false){
                jQuery('#sidebar_right_table_design #table_design_create').addClass('create_disable');
            }else{
                jQuery('#sidebar_right_table_design #table_design_create').removeClass('create_disable')
            }

        }
        //enable create button




    }

    window.toolbar_recheck = function toolbar_recheck(){
        console.log('Recheck ToolBar');
        if(jQuery(document).find('#Toolbar').length > 0){
            //
            if(jQuery('#Toolbar').hasClass('view_done')){
                console.log('View is done');

            }else{
                toolBarFunction();
                console.log('View is done');
                console.log('fixing view');
                setTimeout(function() {
                    toolbar_recheck();
                }, 6000);
            }
            //
        }else{

        } //else
    }

    setTimeout(function() {
        console.log('Tool Bar');
        toolBarFunction();
        toolBarExtra();
        modifyTableCust();
        initGuestAllocationData();
        SummaryDetails();
        PrintTab();
        tableDesigner();
        combineSidebar();
    }, 3000); // for 1 second delay

    setTimeout(function() {
        toolbar_recheck();
    }, 6000);






</script>

</body>
</html>

