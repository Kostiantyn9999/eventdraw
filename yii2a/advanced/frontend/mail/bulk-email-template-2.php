<?php

/* @var $name string */
/* @var $email array */
/* @var $user string */
/* @var $isSubscribed boolean */
?>

<html>
<head>
    <title></title>
</head>
<body>
<table align="center" bgcolor="#EAECED" border="0" cellpadding="0"
       cellspacing="0" width="100%">
    <tbody>
    <tr style="font-size:0;line-height:0">
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="top">
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <div style=
                 "color:inherit;font-size:inherit;line-height:inherit;margin:inherit;padding:inherit">
            </div>
            <table width="600">
                <tbody>
                <tr>
                    <td align="center" valign="top">
                        <table bgcolor="#FFFFFF" border="0"
                               cellpadding="0" cellspacing="0" style=
                               "overflow:hidden!important;border-radius:3px"
                               width="580">
                            <tbody>
                            <tr>
                                <td align="center">
                                    <table width="85%">
                                        <tbody>
                                        <tr>
                                            <td align=
                                                "center">
                                                <h3 style=
                                                    "margin:5% 0 0 0 !important;font-family:'Open Sans',arial,sans-serif!important;font-size:28px!important;line-height:38px!important;font-weight:200!important;color:#252b33!important">
                                                    <?= $email['heading'] ?></h3>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <table border="0"
                                           cellpadding="0"
                                           cellspacing="0" width=
                                           "78%">
                                        <tbody>
                                            <tr>
                                                <td
                                                    align="center"
                                                    style="font-family:'Open Sans',arial,sans-serif!important;font-size:16px!important;line-height:30px!important;font-weight:400!important;color:#7e8890!important">
                                                    <p><i>Hi <?= $name ?>,</i></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <table border="0"
                                           cellpadding="0"
                                           cellspacing="0" width=
                                           "78%">
                                        <tbody>
                                        <?php foreach ($email['body'] as $body) : ?>
                                        <tr>
                                            <td align=
                                                "center" style=
                                                "font-family:'Open Sans',arial,sans-serif!important;font-size:16px!important;line-height:30px!important;font-weight:400!important;color:#7e8890!important">
                                                <p><?= $body ?></p>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign=
                                "top">
                                    <table border="0"
                                           cellpadding="0"
                                           cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td align=
                                                "center"
                                                valign="top">
                                                <a href="http://www.eventdraw.com"
                                                   style=
                                                   "background-color:#ff2b63;padding:14px 28px 14px 28px;border-radius:3px;line-height:18px!important;letter-spacing:0.125em;text-transform:uppercase;font-size:13px;font-family:'Open Sans',Arial,sans-serif;font-weight:400;color:#ffffff;text-decoration:none;display:inline-block;line-height:18px!important"
                                                   target=
                                                   "_blank">EventDraw</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0"
                               cellspacing="0" width="580">
                            <tbody>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" style=
                                "font-family:'Open Sans',sans-serif!important;font-weight:400!important;color:#7e8890!important;font-size:12px!important;text-transform:uppercase!important;letter-spacing:.045em!important"
                                    valign="top">EventDraw - Hospitality Event Diagramming Software
                                </td>
                            </tr>
                            <tr style=
                                "padding:0;margin:0;font-size:0;line-height:0">
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" style=
                                "font-family:'Open Sans',sans-serif!important;font-weight:400!important;color:#7e8890!important;font-size:11px!important;letter-spacing:.05em!important"
                                    valign="top"><em>Email Preferences <a href="http://www.eventdraw.com">EventDraw</a>.</em></td>
                            </tr>
                            <tr>
                                <?php if ($isSubscribed) : ?>
                                    <td align="center">
                                        <a href="<?= Yii::$app->urlManagerFrontend->createUrl([
                                            '/site/toggle-user-subscription',
                                            'email' => $user,
                                            'subscribed' => !$isSubscribed,
                                        ]) ?>">
                                            Unsubscribe
                                        </a>
                                    </td>
                                <?php else: ?>
                                    <td align="center">
                                        <a href="<?= Yii::$app->urlManagerFrontend->createUrl([
                                            '/site/toggle-user-subscription',
                                            'email' => $user,
                                            'subscribed' => $isSubscribed,
                                        ]) ?>">
                                            Subscribe
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <tr style=
                                "padding:0;margin:0;font-size:0;line-height:0">
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
