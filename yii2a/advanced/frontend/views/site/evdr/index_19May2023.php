<?php

/* @var $this yii\web\View */

use common\models\UserTemplates;
use yii\helpers\Html;
use yii\web\View;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use PHPHtmlParser\Dom;

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

$video_record      =   new Query();
$video_record->select(['*'])->from('new_user_broadcast_email_templates as T')
                          //->where('T.display_as=:display_as',['display_as'=>''])
                          ->where('T.userType=:userType',['userType'=>Yii::$app->user->identity->userType])
                          ->orderBy(['T.id' => SORT_ASC]);

$userType = Yii::$app->user->identity->userType;

$video_q   = $video_record->createCommand();
$videoData    = $video_q->queryAll(); 



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
window.variableUserAllowShare = "<?php echo Yii::$app->user->identity->getClientAllowShare() ?>" ;

window.variableUserAllowImportPDF = "<?php echo Yii::$app->user->identity->getClientAllowImportPDF() ?>" ;
window.variableUserType = "<?php echo Yii::$app->user->identity->userType ?>" ;
window.variableUserTotalSessions = "<?php echo Yii::$app->user->identity->totSession ?>" ;
window.variableUserStatus = "<?php echo Yii::$app->user->identity->status ?>" ;


window.UserVedioList = [
    <?php 
    $dom = new Dom;
    if(!empty($videoData)){foreach($videoData as $video){
        $template_image     =   $video['template_image'];
        $dom->loadStr($template_image);
        $ifram      =   $dom->find('iframe');
        
        foreach($ifram as $iframe){
            preg_match('/src="([^"]+)"/', $iframe, $match);
            $url = $match[1];?>
            { 'videoURL': "<?= $url;?>",
                'videoThamb': "<?= $url;?>",
                'videoTitle': "<?= $video['heading'];?>",
            },
        <?php }?>
    <?php } }?>

  ];  
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
        // console.log("Load Shapes Integration Loaded");

        var newurl = window.location.href;
        var reload = false;

        if (reload) {
            window.location = newurl;
        }
    </script>
    <script type="text/javascript">
        // console.log("Init Load shapes");

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
<link rel="stylesheet" type="text/css" href="js/croppie/croppie.min.css">
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

            mxscript("js/app_795m.min.js");


        // Adds basic error handling
        window.onerror = function() {
            var status = document.getElementById("geStatus");

            if (status != null) {
                status.innerHTML = "Page could not be loaded. Please try refreshing.";
            }
        };
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" src="design/test-demo.js"></script>

</head>
<body class="geEditor">
<style type="text/css">
span.close {
    position: absolute;
    top: -15px;
    right: -10px;
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
    padding: 4px;display:inline-block;width: 15px;height: 15px;border: 1px solid;border-radius: 10px;position: absolute;left: 3px;top: 3px;}

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
#getting-start > div {
    border-radius: 4px;
    color: #fff;
    box-shadow: 0 0 10px 0px #0000004a;
}
.bg-purple{
    background-color: #a8518a;
}
div#getting-start {
max-width: 890px;
    margin: 0 auto;
    text-align: center;
    margin-top: 8%;
    position: relative;
    left: 0;
    right: 0;

}

div#getting-start h5 {
    font-size: 15px;
    margin-bottom: 5px;
        margin-top: 10px;
}

div#getting-start p {
    margin-top: 0;
}

div#getting-start img {
    width: 100%;
    border-radius: 10px;
}

#getting-start > div h5, #getting-start > div p {
    padding: 0 0px;
    color: #000;
}

#getting-start .image-box {
    height: 470px;
    overflow: hidden;
    overflow-y: scroll;
}
.image-box iframe {
    width: 825px;
}
</style>
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


      <?php if(false && !empty($broadDataResult)){?>
    <div class="popup_box">
    <?php } else{?>
    <div class="popup_box" style="display: none;">
    <?php } ?>
        <div id="getting-start">
            <div class="bg-purple">
                <div style="padding: 10px;"><h5 style="color:#fff;"><?php echo $broadDataResult[0]['heading'];?></h5></div>
            <img src="design/images/download-close.png" class="close-box-btn" style="width: 10px; position: absolute; right: 13px; top: 19px; cursor: pointer;">
            </div>
            <div style="background:#fff;padding-bottom: 60px;">
                <div class="image-box">
                    <?php echo $broadDataResult[0]['template_image'];?>
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
    // console.log('Tool Bar CallBack');

  
jQuery(document).on('click', 'div#event-organizer-window-saved-on-my-pc', function(){ 
	jQuery('div#event-organizer-window').hide();   
        App.MODE_DEVICE = 'device';
     editorUI.pickFile(App.MODE_DEVICE);
    });

// Open a Plan Saved On My PC js end

jQuery(document).on('click', '#event-organizer-window-edit-list-btn', function(){

    jQuery('div#event-organizer-window').hide();
    
    editorUI.pickFileFromServer('server');

});



<?php //print_r($videoData);die;?>


updateVideoList(window.UserVedioList);

//updateUserTemplateList(window.UserTemplatesList);
//updateUserEventList(window.UserEventsList);

} //toolBarFunction


function BulletinPopUp(){
//console.log(51654645546546);
    function setCookie(key, value, expiry) {
        var expires = new Date();
        expires.setTime(expires.getTime() + expiry * 60 * 1000);
        document.cookie = key + "=" + value + ";expires=" + expires.toUTCString();
    }

    function getCookie(key) {
        var keyValue = document.cookie.match("(^|;) ?" + key + "=([^;]*)(;|$)");
        return keyValue ? keyValue[2] : null;
    }

    function eraseCookie(key) {
        var keyValue = getCookie(key);
        setCookie(key, keyValue, "-1");
    }

    jQuery('button.close').click(function(){
        jQuery('.popup_box').hide();
        setCookie("CookiePopup","1", "1");
            // console.log('Cookie saved');
        
    });

    var cPop = getCookie("CookiePopup");
    // console.log(cPop);
    if (cPop == 1) {
        //$('.popup_box').hide();
    }else{
        
    }


}
BulletinPopUp();



        setTimeout(function() { 
                // console.log('Tool Bar');
                toolBarFunction();
                toolBarFunctionUP();
                toolBarExtra();
                modifyTableCust();
                initGuestAllocationData();
                SummaryDetails();
                PrintTab();
                tableDesigner();
                combineSidebar();
            
            //set toolbar to full height
              jQuery('.toggle-btn-header').click();

          }, 3000); // for 1 second delay     

        //   setTimeout(function() {
        //     toolbar_recheck();
        //   }, 6000); 






</script>
</body>

</html>

