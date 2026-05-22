<div class="modal-dialog" role="document" style="top: 5%">
    <div class="modal-content">
        <div class="modal-body" style="padding: 0">
            <style>
                /* -------------------------------------
                    GLOBAL RESETS
                ------------------------------------- */
                .email-body img {
                    border: none;
                    -ms-interpolation-mode: bicubic;
                    max-width: 100%;
                }

                .email-body {
                    background-color: #f6f6f6;
                    font-family: sans-serif;
                    -webkit-font-smoothing: antialiased;
                    font-size: 14px;
                    line-height: 1.4;
                    margin: 0;
                    padding: 0;
                    -ms-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%;
                }

                .email-body table {
                    border-collapse: separate;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    width: 100%;
                }

                .email-body table td {
                    font-family: sans-serif;
                    font-size: 14px;
                    vertical-align: top;
                }

                /* -------------------------------------
                    BODY & CONTAINER
                ------------------------------------- */
                .email-body {
                    background-color: #f6f6f6;
                    width: 100%;
                }

                /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                .email-body .container {
                    display: block;
                    Margin: 0 auto !important;
                    /* makes it centered */
                    max-width: 580px;
                    padding: 10px;
                    width: 580px;
                }

                /* This should also be a block element, so that it will fill 100% of the .container */
                .email-body .content {
                    box-sizing: border-box;
                    display: block;
                    Margin: 0 auto;
                    max-width: 580px;
                    padding: 10px;
                }

                /* -------------------------------------
                    HEADER, FOOTER, MAIN
                ------------------------------------- */
                .email-body .main {
                    background: #fff;
                    border-radius: 3px;
                    width: 100%;
                }

                .email-body .wrapper {
                    box-sizing: border-box;
                    padding: 20px;
                }

                .email-body .footer {
                    clear: both;
                    padding-top: 10px;
                    text-align: center;
                    width: 100%;
                }

                .email-body .footer td,
                .email-body .footer p,
                .email-body .footer span,
                .email-body .footer a {
                    color: #999999;
                    font-size: 12px;
                    text-align: center;
                }

                /* -------------------------------------
                    TYPOGRAPHY
                ------------------------------------- */
                .email-body h1,
                .email-body h2,
                .email-body h3,
                .email-body h4 {
                    color: #000000;
                    font-family: sans-serif;
                    font-weight: 400;
                    line-height: 1.4;
                    margin: 0;
                    Margin-bottom: 30px;
                }

                .email-body h1 {
                    font-size: 35px;
                    font-weight: 300;
                    text-align: center;
                    text-transform: capitalize;
                }

                .email-body p,
                .email-body ul,
                .email-body ol {
                    font-family: sans-serif;
                    font-size: 14px;
                    font-weight: normal;
                    margin: 0;
                    Margin-bottom: 15px;
                }

                .email-body p li,
                .email-body ul li,
                .email-body ol li {
                    list-style-position: inside;
                    margin-left: 5px;
                }

                .email-body a {
                    color: #3498db;
                    text-decoration: underline;
                }

                /* -------------------------------------
                    BUTTONS
                ------------------------------------- */
                .email-body .btn {
                    box-sizing: border-box;
                    width: 100%;
                }

                .email-body .btn > tbody > tr > td {
                    padding-bottom: 15px;
                }

                .email-body .btn table {
                    width: auto;
                }

                .email-body .btn table td {
                    background-color: #ffffff;
                    border-radius: 5px;
                    text-align: center;
                }

                .email-body .btn a {
                    background-color: #ffffff;
                    border: solid 1px #3498db;
                    border-radius: 5px;
                    box-sizing: border-box;
                    color: #3498db;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 25px;
                    text-decoration: none;
                    text-transform: capitalize;
                }

                .email-body .btn-primary table td {
                    background-color: #3498db;
                }

                .email-body .btn-primary a {
                    background-color: #3498db;
                    border-color: #3498db;
                    color: #ffffff;
                }

                /* -------------------------------------
                    OTHER STYLES THAT MIGHT BE USEFUL
                ------------------------------------- */
                .email-body .last {
                    margin-bottom: 0;
                }

                .email-body .first {
                    margin-top: 0;
                }

                .email-body .align-center {
                    text-align: center;
                }

                .email-body .align-right {
                    text-align: right;
                }

                .email-body .align-left {
                    text-align: left;
                }

                .email-body .clear {
                    clear: both;
                }

                .email-body .mt0 {
                    margin-top: 0;
                }

                .email-body .mb0 {
                    margin-bottom: 0;
                }

                .email-body .preheader {
                    color: transparent;
                    display: none;
                    height: 0;
                    max-height: 0;
                    max-width: 0;
                    opacity: 0;
                    overflow: hidden;
                    mso-hide: all;
                    visibility: hidden;
                    width: 0;
                }

                .powered-by a {
                    text-decoration: none;
                }

                .email-body hr {
                    border: 0;
                    border-bottom: 1px solid #f6f6f6;
                    Margin: 20px 0;
                }

                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                @media only screen and (max-width: 620px) {
                    .email-body table[class=body] h1 {
                        font-size: 28px !important;
                        margin-bottom: 10px !important;
                    }

                    .email-body table[class=body] p,
                    .email-body table[class=body] ul,
                    .email-body table[class=body] ol,
                    .email-body table[class=body] td,
                    .email-body table[class=body] span,
                    .email-body table[class=body] a {
                        font-size: 16px !important;
                    }

                    .email-body table[class=body] .wrapper,
                    .email-body table[class=body] .article {
                        padding: 10px !important;
                    }

                    .email-body table[class=body] .content {
                        padding: 0 !important;
                    }

                    .email-body table[class=body] .container {
                        padding: 0 !important;
                        width: 100% !important;
                    }

                    .email-body table[class=body] .main {
                        border-left-width: 0 !important;
                        border-radius: 0 !important;
                        border-right-width: 0 !important;
                    }

                    .email-body table[class=body] .btn table {
                        width: 100% !important;
                    }

                    .email-body table[class=body] .btn a {
                        width: 100% !important;
                    }

                    .email-body table[class=body] .img-responsive {
                        height: auto !important;
                        max-width: 100% !important;
                        width: auto !important;
                    }
                }

                @media all {
                    .email-body .ExternalClass {
                        width: 100%;
                    }

                    .email-body .ExternalClass,
                    .email-body .ExternalClass p,
                    .email-body .ExternalClass span,
                    .email-body .ExternalClass font,
                    .email-body .ExternalClass td,
                    .email-body .ExternalClass div {
                        line-height: 100%;
                    }

                    .email-body .apple-link a {
                        color: inherit !important;
                        font-family: inherit !important;
                        font-size: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                        text-decoration: none !important;
                    }

                    .email-body .btn-primary table td:hover {
                        background-color: #34495e !important;
                    }

                    .email-body .btn-primary a:hover {
                        background-color: #34495e !important;
                        border-color: #34495e !important;
                    }
                }
            </style>
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
                                                     <!--   <p>Hi {first name},</p> -->
                                                        <p id="email-template-body"></p>

                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- END MAIN CONTENT AREA -->
                                </table>

                                <!-- START FOOTER -->
                                <div class="footer">
                                    <table border="0" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td class="content-block">
                                                <br>
                                                <a href="#">
                                                    Subscribe
                                                </a>.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="content-block powered-by">
                                                Email Preferences <a href="http://www.eventdraw.com">EventDraw</a>.
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- END FOOTER -->

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