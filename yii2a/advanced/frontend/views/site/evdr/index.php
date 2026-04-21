<?php

/* @var $this yii\web\View */

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
    // $userStencilXML =$userStencilXML .';U' . $stencils[$x]->stencilXML;
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
<?php
$momentusFw  = Yii::$app->params['momentus']['floorplanWorkflow'] ?? [];
$momentusOrg = Yii::$app->params['momentus']['orgCode'] ?? '';

// URL params passed from actionEventdraw() — default to empty/0 when opened without a Momentus link
$urlOrgCode             = isset($urlOrgCode)             ? $urlOrgCode             : '';
$urlMomentusEventId     = isset($urlMomentusEventId)     ? (int) $urlMomentusEventId : 0;
$urlSpaceCode           = isset($urlSpaceCode)           ? $urlSpaceCode           : '';
$urlEventSpaceDiagramId = isset($urlEventSpaceDiagramId) ? (int) $urlEventSpaceDiagramId : 0;
$urlTemplateId          = isset($urlTemplateId)          ? (int) $urlTemplateId     : 0;

// Resolve orgCode: URL param → client DB → global params config
$clientOrgCode = '';
$clientModel = \common\models\Client::findIdentity(Yii::$app->user->identity->clientid);
if ($clientModel) {
    $clientOrgCode = trim((string) $clientModel->momentusOrgCode);
}
$resolvedOrgCode = $urlOrgCode !== ''
    ? $urlOrgCode
    : ($clientOrgCode !== '' ? $clientOrgCode : (string) ($momentusFw['defaultOrgCode'] ?? $momentusOrg));

// Resolve momentusEventId: URL param → config default (no hardcoded fallback)
$resolvedMomentusEventId = $urlMomentusEventId > 0
    ? $urlMomentusEventId
    : (int) ($momentusFw['defaultMomentusEventId'] ?? 0);
?>
window.MomentusFloorplanWorkflow = <?= json_encode([
    'enabled'              => array_key_exists('enabled', $momentusFw) ? (bool) $momentusFw['enabled'] : true,
    'momentusEventId'      => $resolvedMomentusEventId,
    'orgCode'              => $resolvedOrgCode,
    'spaceCode'            => $urlSpaceCode,
    'eventSpaceDiagramId'  => $urlEventSpaceDiagramId,
    'templateId'           => $urlTemplateId,
], JSON_UNESCAPED_UNICODE) ?>;

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
window.variableUserAllowShare = "<?php echo Yii::$app->user->identity->getClientAllowShare() ?>" ;

window.variableUserAllowImportPDF = "<?php echo Yii::$app->user->identity->getClientAllowImportPDF() ?>" ;
window.variableUserType = "<?php echo Yii::$app->user->identity->userType ?>" ;
window.variableUserTotalSessions = "<?php echo Yii::$app->user->identity->totSession ?>" ;
window.variableUserStatus = "<?php echo Yii::$app->user->identity->status ?>" ;
window.variableUserClientID = "<?php echo Yii::$app->user->identity->clientid ?>" ;
window.variableUserAllowSaveFolder = "<?php echo Yii::$app->user->identity->getClientAllowSaveFolder() ?>" ;

window.variableUserIsExpiry = 0;
<?php if(Yii::$app->user->identity->expiry_date < strtotime("now")){?>
  window.variableUserIsExpiry = 1;
<?php } ?>



</script>

<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=5,IE=9"/><![endif]-->
<script type="text/javascript">
  window.tableNumArray = [];  
  window.guestIdLast = 0;
   window.table_design_array = [];

   window.user_template_done = 0;
   window.user_saved_layout_done = 0;
   window.client_array = [];

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
        .extra_details iframe {
            width: 590px;
            max-width: 600px;
        }
    </style>

    <script type="text/javascript">
        // Raghaw 04-03-2022

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



        //Raghaw end 04-03-2022

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
        .video-popup>.h3-heading {
            display: none;
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
            
        mxscript("js/app.min.js?no-cache=<?php echo time(); ?>");
        //mxscript("js/app_1115m.min.js?no-cache=<?php echo time(); ?>");


        // Adds basic error handling
        window.onerror = function() {
            var status = document.getElementById("geStatus");

            if (status != null) {
                status.innerHTML = "Page could not be loaded. Please try refreshing.";
            }
        };
    </script>
    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 

<!-- new js file -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xls/0.7.4-a/xls.core.min.js"></script>
    <script type="text/javascript" src="/advanced/frontend/web/site/design/test-demo_023.js?no-cache=<?php echo time(); ?>"></script>
    <!-- <script type="text/javascript" src="/frontend/web/site/design/guest_allocation.js?no-cache=<?php echo time(); ?>"></script> -->
<!--     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.8.24/jquery-ui.min.js" type="text/javascript"></script>
    <link href="http://code.jquery.com/ui/1.8.24/themes/blitzer/jquery-ui.css" rel="stylesheet" type="text/css" /> -->
<!-- <script type="text/javascript" src="/frontend/web/site/design/guided-tours/intro.js?v=3"></script> -->

    <!-- <script type="text/javascript" src="/frontend/web/site/design/guided-tours/jquery.hemiIntro.js?v=3"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="/frontend/web/site/design/guided-tours/jquery.hemiIntro.css?v=3"> -->

     <!-- <script type="text/javascript" src="/frontend/web/site/design/guided-tours/pageintro.js"></script>
<link rel="stylesheet" type="text/css" href="/frontend/web/site/design/guided-tours/pageintro.css"> -->

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->

</head>
<body class="geEditor">

<div class="popup_box" style="<?php if($userType !='0') echo 'display:none;';?>">
    <form>
        <div class="extra_details">
            <h4>To assist our team with customised training and support for you, please choose one of the below!</h4>

            <div style="    border-bottom: 5px solid;">
                <h1><sup>*</sup> VENUE / PROPERTY <label> <input type="radio" onclick="updateUserROle(1)" id="venue" class="userType" name="user_type" value="1"><span></span></label></h1>
                <p>I'm a professionals working at Hotel, Wedding Venue, Performing Arts Venues, Convention Centres, Stadiums, Casino, Theatre's, Schools, Universities, Goverment, Council or Other Venues etc</p>
            </div>

            <div>
                <h1><sup>*</sup> EVENT ORGANISER  <label><input type="radio" onclick="updateUserROle(2)" id="organiser" class="userType" name="user_type" value="2"><span></span></label></h1>
                <p>I'm an independent Event Planner AV Company / Staging Company, Festival Planner of Caterer using many different venues for my events.</p>
            </div>
            <span class="close">X</span>
        </div>
    </form>
</div>
<?php if($userType !=2){?>
<style type="text/css">
table#event-organizer-window-templates-import-plan.geTemplate_main {
   display: none!important;
}
</style>
<?php } ?>
<div class="popup_box" id="ajax_poup_show" style="display:none;">
    <div id="getting-start" style="overflow: scroll;">
        <div class="bg-purple">
            <div style="padding: 10px;"><h5 style="color:#fff;"><span id="heading"></span></h5></div>
        <img src="design/images/download-close.png" class="close-box-btn" style="width: 10px; position: absolute; right: 13px; top: 19px; cursor: pointer;">
        </div>
        <div style="background:#fff;padding-bottom: 60px;">
            <div class="image-box">
                <span id="description">
                    
                </span>
            </div>
            <button class="close">Close</button>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery('div#getting-start img.close-box-btn').click(function(){
                        jQuery('button.close').trigger('click');
                    });
                });
            </script>
        </div>
    </div>
</div>
<style type="text/css">
button#event-organizer-window-move-to-folder-btn {
    position: absolute;
    right: 432px !important;
    background-color: #a8518a;
    background-image: linear-gradient(#a8518a 0px,#a8518a 100%);
    opacity: 0.6;
    pointer-events: none;
    }

button#event-organizer-window-move-to-folder-btn.active_move_to_folder { opacity: 1;
    pointer-events: all; }

span.close {
    position: absolute;
    top: -6px;
    right: -8px;
    background: red;
    z-index: 2;
    opacity: 1;
    color: #fff;
    width: 20px;
    height: 20px;
    border-radius: 56px;
    padding: 3px;
}
.scroll_div {
    text-align: center;
    font-size: 12px;
    min-height: 350px;
    height: 300px;
    overflow: scroll;
    width: 620px;
}

.extra_details h1 {
    font-size: 20px;
    text-align: left;
}


.extra_details h1 label input {
    opacity: 0;
    position: absolute;
    top: 0;
    left: 0;
}

.extra_details h1 label {
    display: inline-block;
    width: 25px;
    padding: 5px;
    border: 2px solid;
    border-radius: 50px;
    height: 25px;
    position: relative;
    float: right;
    cursor:pointer;
}
.extra_details h1 label:hover span{
    background:#a8518a;
}
.extra_details h1 label input:checked + span {
    font-weight: bold;    background:#a8518a;
}
.extra_details h1 label span {
    border: 1px solid #aaa;
    padding: 4px;
    display: inline-block;
    width: 23px;
    height: 23px;
    border: 1px solid;
    border-radius: 32px;
    position: absolute;
    left: 1px;
    top: 1px;}

.popup_box {
    position: fixed;
    top: 0;
    left: 0;
    background: #00000038;
    width: 100%;
    height: 100%;
    /*padding: 5vh;*/
    z-index: 99999;
}

.popup_box .extra_details {
    position: absolute;
    top: 15vh;
    max-width: 600px;
    margin: 0 auto;
    left: 0;
    right: 0;
    background: #F8F9FA;
    padding: 30px;
    border-radius: 4px;
    color: #a8518a;
    box-shadow: 0 0 10px 0px #0000004a;

}
h4 span {
    border-bottom: 1px solid #a8518a;
    width: auto;
}
.image-box > p {
    margin-top: 15px !important;
    margin-bottom: -15px;
}
button.close {
    position: relative;
    right: 36px;
    padding: 5px 10px;
    float: right;
    bottom: 27px;
    background: #a8518a;
    color: #fff;
    border: 1px solid #a8518a;
    top: 10px;
    cursor: pointer;
}
#getting-start > div.bg-purple, #getting-start-template > div.bg-purple {
    border-radius: 4px 4px 0 0;
}
#getting-start > div, #getting-start-template > div {
    border-radius: 0 0 4px 4px;
    color: #fff;
    box-shadow: 0 0 10px 0px #0000004a;
}
.bg-purple{
    background-color: #a8518a;
}
div#getting-start, div#getting-start-template {
    max-width: 900px;
    margin: auto;
    text-align: center;
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 540px;


}
div#getting-start button.close, div##getting-start-template button.close {
        border-radius: 4px !important;
        min-width: 60px;
        box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
        }

div#getting-start h5, #getting-start-template h5 {
    font-size: 15px;
    margin-bottom: 5px;
        margin-top: 10px;
}

div#getting-start p, #getting-start-template p {
    margin-top: 0;
}

div#getting-start img, #getting-start-template img {
    width: 100%;
    border-radius: 10px;
}

#getting-start > div h5, #getting-start > div p, #getting-start-template > div h5, #getting-start-template > div p {
    padding: 0 0px;
    color: #000;
}

#getting-start .image-box, #getting-start-template .image-box {
    height: 470px;
    overflow: hidden;
    overflow-y: scroll;
}
.image-box iframe {
    width: 825px;
}


table#folder_names_event_back_button {
    display: inline-block;
}

table#folder_names_event_back_button .geTemplate_inner {
    color: #a8518a;
   background-color: #fff;
    font-size: 15px;
    border-radius: 5px;
    text-align: center;
    margin-top: -15px;
    box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
}

table#folder_names_event_back_button .geTemplate_inner p, .folder_names_event .geTemplate_inner p {
    position: relative;
    top: 60px;
}

table.folder_names_event,#folder_names_event_back_button {
    cursor: pointer;
}
.bulletin_popup{
    z-index: 9999999;
}
</style>
<input type="hidden" name="user_role_id" class="user_role_id" value="<?= $userType;?>">
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

    <!-- modal-dialog start -->

    <div class="modal-dialog" role="document" style="top: 5%">
        <div class="modal-content">
            <div class="modal-body" style="padding: 0">
                <style></style>
                <div class="email-body">
                    <table border="0" cellpadding="0" cellspacing="0" class="body">
                        <tr>
                            <td>&nbsp;</td>
                            <td class="container">
                                <div class="content">
                                    <table class="main">

                                        <!-- START MAIN CONTENT AREA -->
                                        <tr>
                                            <td class="wrapper">
                                                <table border="0" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td>
                                                            <h3 id="email-template-heading"></h3>
                                                            <!-- <p>Hi {first name},</p> -->
                                                            <p id="email-template-body"></p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <!-- END MAIN CONTENT AREA -->
                                    </table>


                                    <!-- END CENTERED WHITE CONTAINER -->
                                </div>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- modal-dialog end -->


   <!-- Open dialog start   -->
   

<div id="bulletinPopupContainer" style="display:none;"></div>
<div id="templateChangePopupContainer" style="display:none;"></div>



    <div class="popup_box" id="ajax_poup_show" style="display:none;">
        <div id="getting-start">
            <div class="bg-purple">
                <div style="padding: 10px;"><h5 style="color:#fff;"><span id="heading"></span></h5></div>
            <img src="design/images/download-close.png" class="close-box-btn" style="width: 10px; position: absolute; right: 13px; top: 19px; cursor: pointer;">
            </div>
            <div style="background:#fff;padding-bottom: 60px;">
                <div class="image-box">
                    <span id="description">
                        
                    </span>
                </div>
                <button class="close">Close</button>
                <script type="text/javascript">
                    jQuery(document).ready(function(){
                        jQuery('div#getting-start img.close-box-btn').click(function(){
                            jQuery('button.close').trigger('click');
                        });
                    });
                </script>
            </div>
        </div>
    </div> <!--  Open dialog end -->
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

<script type="text/javascript">

// vimeo video grid start

function toolBarFunction() {
    console.log('Tool Bar CallBack toolBarFunction');


  
jQuery(document).on('click', 'div#event-organizer-window-saved-on-my-pc', function(){    
        App.MODE_DEVICE = 'device';
     editorUI.pickFile(App.MODE_DEVICE);
    });

// Open a Plan Saved On My PC js end

jQuery(document).on('click', '#event-organizer-window-edit-list-btn', function(){

    jQuery('div#event-organizer-window').hide();
    
    editorUI.pickFileFromServer('server');

});



<?php //print_r($videoData);die;?>

<?php 
    function slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', ' ', $text);
        $text = trim($text, '-');
        $text = strtolower($text);
        if (empty($text))
            return '';
        return $text;
    }
?>


// window.UserVedioList = [
//     <?php 
//     $domData = new DOMDocument();
//     if(!empty($videoData)){foreach($videoData as $video){
//         $template_image     =   $video['template_image'];
//         $domData->loadHTML($template_image);
//         $iframeTags = $domData->getElementsByTagName('iframe');
//         foreach ($iframeTags as $iframe) {
//             $src = $iframe->getAttribute('src');?>
//             { 
//                 'videoURL': "<?= $src;?>",
//                 'videoThamb': "<?= $src;?>",
//                 'videoTitle': "<?= slugify($video['heading']);?>",
//             },
//             <?php 
//         }
//     } }?>

// ]; 
// updateVideoList(window.UserVedioList);

} //toolBarFunction



// vimeo video grid start
 


        setTimeout(function() { 
                console.log('Tool Bar');
                toolBarFunction();
                toolBarFunctionUP();
                toolBarExtra();
                modifyTableCust();
                initGuestAllocationData();
                SummaryDetails();
                PrintTab();
                tableDesigner();
                combineSidebar();
               jQuery('.toggle-btn-header').click();
               BoardFun();
               EmailTemplateChange();
               VideoData(); 
                GetGuestSeatingAllow();              
          }, 3000); // for 1 second delay    

// modify Raghaw 16-03-2022
//Recheck Toolbar
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
      // $.getScript("https://code.jquery.com/ui/1.13.2/jquery-ui.js");
  }


 setTimeout(function() {
            toolbar_recheck();
            lazyLoad()
          }, 6000);

// modify Raghaw 16-03-2022
function lazyLoad(){
    // Create one IntersectionObserver (for lazy load backgrounds)
// Create one IntersectionObserver (for lazy load backgrounds)
const bgObserver = new IntersectionObserver((entries, obs) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const el = entry.target;
      const bg = el.dataset.bg;
      if (bg) {
        el.style.backgroundImage = `url(${bg})`;
        el.classList.remove('lazy_bg');
        obs.unobserve(el); // stop observing once loaded
      }
    }
  });
});

// Function to observe a lazy_bg element
function observeLazyBg(el) {
  if (el.classList.contains('lazy_bg')) {
    bgObserver.observe(el);
  }
}

// 1️⃣ Observe all existing .lazy_bg on page load
document.querySelectorAll('.lazy_bg').forEach(observeLazyBg);

// 2️⃣ Watch for new children inside .event-organizer-window-inner
const container = document.querySelector('.event-organizer-window-inner');

if (container) {
  const mo = new MutationObserver(mutations => {
    mutations.forEach(m => {
      m.addedNodes.forEach(node => {
        if (node.nodeType === 1) { // element only
          if (node.classList.contains('lazy_bg')) {
            observeLazyBg(node);
          }
          // also check inside nested children (search results may add groups of divs)
          if (node.querySelectorAll) {
            node.querySelectorAll('.lazy_bg').forEach(observeLazyBg);
          }
        }
      });
    });
  });

  mo.observe(container, { childList: true, subtree: true });
}
}


// popup_box function executed here start

$(document).on('click','.close',function(){
    $('.popup_box').hide();
});

window.UseS3Preview = App.UseS3Preview;
// window.UseS3Preview = true;


 // window.site_url = "https://staging.eventdraw.com.au/frontend/web/site/";
 window.site_url = "https://momentusstaging.eventdrawus.com/frontend/web/site/";

   if(window.UseS3Preview == true){
    window.site_url = "https://eventdraw-public.s3.ap-southeast-2.amazonaws.com/staging_data/tmpl_images/";
   }

// popup_box function executed here end 
</script>
<!-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.debug.js"></script>    
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script> 


<script type="text/javascript">
    
    $("#guest_allocation_table_export_pdf").click(function(){    
  var doc = new jsPDF('portrait', 'pt', 'a4', true);    
    var elementHandler = {    
        '#ignorePDF': function(element, renderer) {    
            return true;    
        }    
    };    
    
    var source = document.getElementById("top-content");    
    doc.fromHTML(source, 15, 15, {    
        'width': 560,    
        'elementHandlers': elementHandler    
    });    
    
    var svg = document.querySelector('svg');    
    var canvas = document.createElement('canvas');    
    var canvasIE = document.createElement('canvas');    
    var context = canvas.getContext('2d');    
    
    
    
    
    var data = (new XMLSerializer()).serializeToString(svg);    
    canvg(canvas, data);    
    var svgBlob = new Blob([data], {    
        type: 'image/svg+xml;charset=utf-8'    
    });    
    
    var url = canvas.toDataURL(svgBlob);//DOMURL.createObjectURL(svgBlob);    
    
    var img = new Image();    
    img.onload = function() {    
        context.canvas.width = $('#testchart').find('svg').width();;    
        context.canvas.height = $('#testchart').find('svg').height();;    
        context.drawImage(img, 0, 0);    
        // freeing up the memory as image is drawn to canvas    
        //DOMURL.revokeObjectURL(url);    
    
        var dataUrl;    
        if (isIEBrowser()) { // Check of IE browser     
            var svg = $('#testchart').highcharts().container.innerHTML;    
            canvg(canvasIE, svg);    
            dataUrl = canvasIE.toDataURL('image/JPEG');    
        } else {    
            dataUrl = canvas.toDataURL('image/jpeg');    
        }    
        doc.addImage(dataUrl, 'JPEG', 20, 365, 560, 350); // 365 is top     
    
        var bottomContent = document.getElementById("bottom-content");    
        doc.fromHTML(bottomContent, 15, 750, {   //700 is bottom content top  if you increate this then you should increase above 365    
            'width': 560,    
            'elementHandlers': elementHandler    
        });    
    
        setTimeout(function() {    
            doc.save('HTML-To-PDF-Dvlpby-Bhavdip.pdf');    
        }, 2000);    
    };    
    img.src = url;    
});    
function isIEBrowser() {    
    var ieBrowser;    
    var ua = window.navigator.userAgent;    
    var msie = ua.indexOf("MSIE ");    
    
    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // Internet Explorer    
    {    
        ieBrowser = true;    
    } else //Other browser    
    {    
        console.log('Other Browser');    
        ieBrowser = false;    
    }    
    
    return ieBrowser;    
};
</script> -->

<!-- <script type="text/javascript" src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> -->
<!-- <script>
$( ".folder_names_event" ).droppable({
      drop: function( event, ui ) {
        $( this )
          .addClass( "ui-state-highlight" )
          .find( "p" )
            .html( "Dropped!" );
      }
    });
</script> -->
<!-- close main div -->


<style type="text/css">
    div#folder_structure {
    position: absolute;
    margin: 0 auto;
    left: 0;
    right: 0;
    width: 100%;
    background: white;
    top: 100px;
    z-index: 99;
    max-width: 450px;
    padding: 25px;
    box-shadow: 0 0 10px 0px #0000004a;
    border: 1px solid #ccc;
    font-size: 13px;
    font-family: 'Open Sans', sans-serif;
    border-radius: 5px;
    bottom: 0;
    max-height: 346px;
    display: none;
}
 #myUL {
  list-style-type: none;
}

#myUL {
  margin: 0;
  padding: 0;
}

.caret {
  cursor: pointer;
  -webkit-user-select: none; /* Safari 3.1+ */
  -moz-user-select: none; /* Firefox 2+ */
  -ms-user-select: none; /* IE 10+ */
  user-select: none;
}

.caret::before {
  content: "\25B6";
  color: black;
  display: inline-block;
  margin-right: 6px;
}

.caret-down::before {
  -ms-transform: rotate(90deg); /* IE 9 */
  -webkit-transform: rotate(90deg); /* Safari */'
  transform: rotate(90deg);  
}

.nested {
  display: none;
}

.active_folder {
  display: block;
}

span.selected_folder_to_move {
    background: #a8518a;
    color: #fff;
}


div#folder_structure h6 {font-size: 15px;padding: 0;margin: 0 0 5px;}


div#folder_structure .folder_name {
margin: 5px 0;
}

div#folder_structure .folder_name span {
font-weight: bold !important;
}


div#folder_structure span.remove_plan {
margin-left: 15px;
cursor: pointer;
}

div#folder_structure .plans_name >div > div {margin-bottom: 5px;}

div#folder_structure button#event-organizer-window-move-to-folder-btn-cancel {    color: #000 !important;
    border: 1px solid #d8d8d8;border: 0;outline: 0;padding: 0px 15px;cursor: pointer;font-size: 11px !important;border-radius: 3px;height: 29px;box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;}

    div#folder_structure button#event-organizer-window-move-to-folder-btn-done {background-color: #a8518a;background-image: linear-gradient(#a8518a 0px,#a8518a 100%);color: #Fff;border: 0;outline: 0;padding: 0px 15px;cursor: pointer;font-size: 11px !important;border-radius: 3px;height: 29px;margin-right: 15px;box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;}



div#folder_structure .plans_list {
margin-bottom: 10px;
}


div#folder_structure .plans_name > div {
/*    max-height: 132px;*/
    overflow: scroll}


ul#myUL li {
    list-style: none;
}

.folders_view {
    border: 1px solid #efefef;
    padding: 10px;
/*    max-height: 107px;*/
    overflow: scroll;
}

ul#myUL  span {
    padding: 2px 9px;
    display: inline-block;
}


div#new_folder_popup {
    position: absolute;
    margin: 0 auto;
    left: 0;
    right: 0;
    width: 100%;
    background: white;
    top: 100px;
    z-index: 10006;
    max-width: 450px;
    padding: 25px;
    box-shadow: 0 0 10px 0px #0000004a;
    border: 1px solid #ccc;
    font-size: 13px;
    font-family: 'Open Sans', sans-serif;
    border-radius: 5px;
    bottom: 0;
    max-height: 346px;
    display: none;
}

div#new_folder_popup > div {
    margin: 10px 0;
}

div#new_folder_popup select {
    width: 207px;
    height: 23px;
}

div#new_folder_popup label {
    width: 85px;
    display: inline-block;
}

input#new_folder_name {
    display: inline-block;
    width: 350px;
    height: 20px;
}

button#new_folder_popup_done {
    background-color: #a8518a;
    background-image: linear-gradient(#a8518a 0px,#a8518a 100%);
    border: 0px solid #d8d8d8;
    color: #fff;
    font-size: 11px !important;
    border-radius: 4px;
    font-weight: 500;
    letter-spacing: 0.25px;
    height: 29px;
    line-height: 27px;
    /* margin: 0 0 0 8px; */
    min-width: 60px;
    outline: 0;
    padding: 0 15px;
    cursor: pointer;
    font-size: 11px !important;
    box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
    margin-right: 15px;
}


button#new_folder_popup_cancel {  
  color: #fff;
  font-size: 11px !important;
  border-radius: 4px;
  font-weight: 500;
  letter-spacing: 0.25px;
  height: 29px;
  line-height: 27px;
  /* margin: 0 0 0 8px; */
  min-width: 60px;
  outline: 0;
  padding: 0 15px;
  cursor: pointer;
  font-size: 11px !important;
  color: #000 !important;
  border: 0px solid #d8d8d8;
  box-shadow: rgba(0, 0, 0, 0.19) 0px 10px 20px, rgba(0, 0, 0, 0.23) 0px 6px 6px;
}

div#event-organizer-window-user_path {
    margin-left: 46px;
}

div#custom_popup_box {
    position: absolute;
    z-index: 999999;
    background: #ffffff80;
    width: 100%;
    height: 100%;
    top: 0;
    display: none;
}

div#custom_popup_box .popup_box_content {
    background: #fff;
    border: red 1px solid;
    max-width: 400px;
    margin: 0 auto;
    top: 250px;
    position: relative;
    text-align: center;
    padding: 10px;
    box-shadow: 0 0 10px 0px #0000004a;
    border: 1px solid #ccc;
    font-size: 13px;
    font-family: 'Open Sans', sans-serif;
    border-radius: 5px;
}
div#folder_structure .plans_name > div ul li {
    list-style: auto;
    padding: 0;
}
div#folder_structure .plans_name > div ul {
    margin: 0;
    padding-left: 15px;
}

.import_new_view input[type="checkbox"]{
    visibility: visible!important;
}
</style>

<style type="text/css">
    /** folder toggle task **/
    ul#myUL ul, ul#myUL_create ul {
    padding-left: 0!important;
    }
    ul#myUL ul ul, ul#myUL_create ul ul {
        padding-left: 15px!important;
    }
    ul#myUL  ul > li.top_item_list > span.caret-down, ul#myUL_create  ul > li.top_item_list > span, .folderlistsaved ul#myUL  ul  li > span.caret-down {
        padding-left: 24px;
    }

        span.caret-down.drop_toggle i.close img{
        transform: rotate(-90deg); 
    }

    span.caret-down.drop_toggle i.drop_toggle_trigger img {max-width:12px; }
    span.caret-down.drop_toggle i.drop_toggle_trigger {
        position: absolute;
        height: 15px;
        width: 15px;
        left: 3px;
        top: 0px;
    }

    span.caret-down.drop_toggle {
      position: relative;
      padding-left: 25px!important;
    }

    div#folder_structure {
        z-index: 99999999;
        max-width: 700px;
        max-height: 440px;
    }

    .folders_view {
        max-height: 230px !important;
    }

    .plans_name {
        min-height: 100px !important;
    }

    ul#myUL span {
/*        padding: 5px 9px;*/
    }

    span.caret-down.drop_toggle i.drop_toggle_trigger {
/*        top: 4px;*/
    }
    .plans_name > div {
        max-height: 80px !important;
    }  
div#loader_refresh {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    background-color: rgba(0,0,0,0.3);
    z-index: 99999;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
    font-size: 16px;
    flex-wrap: wrap;
}


#loader_spinnner {
    width: 30px;
    height: 30px;
    border: 5px solid #FFF;
    border-bottom-color: #a8518a;
    border-radius: 50%;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
    margin-left: 0px;
    margin-bottom: 20px;
    margin-top: 0;
    position: relative;
    left: 40px;
    bottom: 0;
}

    @keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    } 

div#loader_refresh span {
    display: block;
}
</style>

<div id="custom_popup_box">
    <div class="popup_box_content">       
        <p class="notification_text"></p>
        <button class="close-btn" id="custom_popup_box-cancel-btn">Close</button>        
    </div>
</div><!-- custom_popup_box -->




<style type="text/css">
    /*** 14-02-2025 ***/
    .geSidebarContainer.geFormatContainer .geFormatSection {
    clear: both;
}

    div#getting-start {
    overflow: scroll;
}

div#getting-start-template { overflow: scroll;}

</style>

</body>
</html>

