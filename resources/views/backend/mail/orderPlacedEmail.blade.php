<!DOCTYPE HTML
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>

    <style type="text/css">
        @media only screen and (min-width: 620px) {
            .u-row {
                width: 600px !important;
            }

            .u-row .u-col {
                vertical-align: top;
            }

            .u-row .u-col-9p92 {
                width: 59.52px !important;
            }

            .u-row .u-col-11p66 {
                width: 69.96px !important;
            }

            .u-row .u-col-11p83 {
                width: 70.98px !important;
            }

            .u-row .u-col-12p17 {
                width: 73.02px !important;
            }

            .u-row .u-col-12p2 {
                width: 73.2px !important;
            }

            .u-row .u-col-23p66 {
                width: 141.96px !important;
            }

            .u-row .u-col-25p03 {
                width: 150.18px !important;
            }

            .u-row .u-col-52p34 {
                width: 314.04px !important;
            }

            .u-row .u-col-52p51 {
                width: 315.06px !important;
            }

            .u-row .u-col-52p85 {
                width: 317.1px !important;
            }

            .u-row .u-col-100 {
                width: 600px !important;
            }
        }

        @media (max-width: 620px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }

            .u-row {
                width: 100% !important;
            }

            .u-col {
                width: 100% !important;
            }

            .u-col>div {
                margin: 0 auto;
            }
        }

        body {
            margin: 0;
            padding: 0;
        }

        table,
        tr,
        td {
            vertical-align: top;
            border-collapse: collapse;
        }

        p {
            margin: 0;
        }

        .ie-container table,
        .mso-container table {
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors='true'] {
            color: inherit !important;
            text-decoration: none !important;
        }

        @media (max-width: 480px) {
            .hide-mobile {
                max-height: 0px;
                overflow: hidden;
                display: none !important;
            }
        }

        table,
        td {
            color: #000000;
        }

        #u_body a {
            color: #007aee;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            #u_column_34 .v-col-padding {
                padding: 0px 16px !important;
            }

            #u_column_36 .v-col-padding {
                padding: 0px 16px !important;
            }
        }
    </style>

    <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css">
</head>

<body class="clean-body u_body"
    style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #ffffff;color: #000000">

    @php
        $generalInfo = App\Models\GeneralInfo::where('id', 1)
            ->select(
                'logo_dark',
                'company_name',
                'contact',
                'email',
                'address',
                'facebook',
                'twitter',
                'linkedin',
                'youtube',
                'messenger',
                'whatsapp',
                'telegram',
            )
            ->first();

        $orderDetails = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('order_details.*', 'products.name as product_name')
            ->where('order_id', $orderInfo->id)
            ->get();

        $billingInfo = App\Models\BillingAddress::where('order_id', $orderInfo->id)->first();
        $shippingInfo = App\Models\ShippingInfo::where('order_id', $orderInfo->id)->first();

        function getDomain($url)
        {
            $pieces = parse_url($url);
            $domain = isset($pieces['host']) ? $pieces['host'] : '';
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
                return $regs['domain'];
            }
            return false;
        }
    @endphp

    <table id="u_body"
        style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #ffffff;width:100%"
        cellpadding="0" cellspacing="0">
        <tbody>
            <tr style="vertical-align: top">
                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">


                    {{-- Header Banner Logo --}}
                    <div class="u-row-container" style="padding: 0px;background-color: #f4faff">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div style="height: 100%;width: 100% !important;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 10px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                                border="0">
                                                                <tr>
                                                                    <td style="padding-right: 0px;padding-left: 0px;"
                                                                        align="center">
                                                                        <a href="#" target="_blank">
                                                                            <img align="center" border="0"
                                                                                @if ($generalInfo->logo_dark && file_exists(public_path($generalInfo->logo_dark))) src="{{ url($generalInfo->logo_dark) }}" @endif
                                                                                alt="" title=""
                                                                                style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 35%;max-width: 203px;"
                                                                                width="203">
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Your order has been received! with Banner --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #122257;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                                border="0">
                                                                <tr>
                                                                    <td style="padding-right: 0px;padding-left: 0px;"
                                                                        align="center">

                                                                        <img align="center" border="0"
                                                                            src="https://assets.unlayer.com/projects/193148/1698316546391-bg-email.png"
                                                                            alt="" title=""
                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 100%;max-width: 600px;"
                                                                            width="600" />

                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 20px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 24px; font-weight: 700; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><strong>Your order has
                                                                        been received!</strong></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- We are thrilled to inform => Message --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <table height="0px" align="center" border="0"
                                                                cellpadding="0" cellspacing="0" width="100%"
                                                                style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #f2f2f2;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                                            <span>&#160;</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 150%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 150%;">Dear @if ($shippingInfo && $shippingInfo->full_name)
                                                                        <strong>{{ $shippingInfo->full_name }}</strong>
                                                                    @else
                                                                        Customer
                                                                    @endif, Thanks for your order!
                                                                </p>
                                                                <p style="line-height: 150%;">&nbsp;</p>
                                                                <p style="line-height: 150%;">We are thrilled to inform
                                                                    you that your recent order with @if ($generalInfo && $generalInfo->company_name)
                                                                        <strong>{{ $generalInfo->company_name }}</strong>
                                                                    @else
                                                                        us
                                                                    @endif has been successfully
                                                                    received. We want to express our gratitude for
                                                                    choosing us for your online shopping needs.</p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Track your order Button --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f6fbff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 16px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <div align="center">
                                                                <a href="{{ env('APP_FRONTEND_URL') }}/track/order?order_no={{ $orderInfo->order_no }}"
                                                                    target="_blank" class="v-button"
                                                                    style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #0056ff; border-radius: 4px;-webkit-border-radius: 4px; -moz-border-radius: 4px; width:auto; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
                                                                    <span
                                                                        style="display:block;padding:10px 20px;line-height:120%;">
                                                                        <span style="line-height: 16.8px;">Track your
                                                                            order</span>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Summary Button --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #122257;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <h1
                                                                style="margin: 0px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word; font-size: 22px; font-weight: 400;">
                                                                <strong>Order summary</strong>
                                                            </h1>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <div>
                                                                <br>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ordered products -->
                    @php $orderSl = 1; @endphp
                    @foreach ($orderDetails as $item)
                        <div class="u-row-container" style="padding: 0px;background-color: transparent">
                            <div class="u-row"
                                style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                                <div
                                    style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                    <div class="u-col u-col-11p83"
                                        style="max-width: 320px;min-width: 70.98px;display: table-cell;vertical-align: top;">
                                        <div
                                            style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <div class="v-col-padding"
                                                style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                                <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                    cellpadding="0" cellspacing="0" width="100%" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                                align="left">
                                                                <div>
                                                                    <p></p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="u-col u-col-52p34"
                                        style="max-width: 320px;min-width: 314.04px;display: table-cell;vertical-align: top;">
                                        <div
                                            style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <div class="v-col-padding"
                                                style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                                <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                    cellpadding="0" cellspacing="0" width="100%" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                                align="left">

                                                                <div
                                                                    style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                    <p style="line-height: 140%;">
                                                                        <span
                                                                            style="color: #000000; line-height: 19.6px;">
                                                                            {{ $orderSl++ }}.
                                                                            {{ $item->product_name }}*{{ $item->qty }}
                                                                        </span>
                                                                    </p>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="u-col u-col-23p66"
                                        style="max-width: 320px;min-width: 141.96px;display: table-cell;vertical-align: top;">
                                        <div
                                            style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <div class="v-col-padding"
                                                style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                                <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                    cellpadding="0" cellspacing="0" width="100%" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                                align="left">
                                                                <div
                                                                    style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                    <p style="line-height: 140%;"><strong><span
                                                                                style="color: #000000; line-height: 19.6px;">{{ $item->total_price }}
                                                                                BDT</span></strong></p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <!--[if (!mso)&(!IE)]><!-->
                                            </div>
                                            <!--<![endif]-->
                                        </div>
                                    </div>

                                    <div class="u-col u-col-12p17"
                                        style="max-width: 320px;min-width: 73.02px;display: table-cell;vertical-align: top;">
                                        <div
                                            style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <div class="v-col-padding"
                                                style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                                <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                    cellpadding="0" cellspacing="0" width="100%" border="0">
                                                    <tbody>
                                                        <tr>
                                                            <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                                align="left">

                                                                <div>
                                                                    <p></p>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach



                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <table height="0px" align="center" border="0"
                                                                cellpadding="0" cellspacing="0" width="100%"
                                                                style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #dddddd;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top">
                                                                        <td
                                                                            style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                                                                            <span>&#160;</span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- shipping charge --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-11p83"
                                    style="max-width: 320px;min-width: 70.98px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-52p34"
                                    style="max-width: 320px;min-width: 314.04px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;">Shipping
                                                                        charge</span></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-23p66"
                                    style="max-width: 320px;min-width: 141.96px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">
                                                                    <strong><span
                                                                            style="color: #000000; line-height: 19.6px;">{{ $orderInfo->delivery_fee }}
                                                                            BDT</span></strong>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-12p17"
                                    style="max-width: 320px;min-width: 73.02px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>

                    {{-- vat tax --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-11p83"
                                    style="max-width: 320px;min-width: 70.98px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-52p34"
                                    style="max-width: 320px;min-width: 314.04px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">
                                                                    <span
                                                                        style="color: #000000; line-height: 19.6px;">VAT/TAX</span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-23p66"
                                    style="max-width: 320px;min-width: 141.96px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><strong><span
                                                                            style="color: #000000; line-height: 19.6px;">{{ $orderInfo->vat + $orderInfo->tax }}
                                                                            BDT</span></strong></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-12p17"
                                    style="max-width: 320px;min-width: 73.02px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- total order amount --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ecf8ff;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-11p66"
                                    style="max-width: 320px;min-width: 69.96px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-52p51"
                                    style="max-width: 320px;min-width: 315.06px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><strong><span
                                                                            style="color: #000000; line-height: 19.6px;">Total
                                                                            amount</span></strong></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-23p66"
                                    style="max-width: 320px;min-width: 141.96px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 18px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><strong><span
                                                                            style="color: #000000; line-height: 19.6px;">{{ $orderInfo->total }}
                                                                            BDT</span></strong></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div class="u-col u-col-12p17"
                                    style="max-width: 320px;min-width: 73.02px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <p></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>



                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <br>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #122257;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <h1
                                                                style="margin: 0px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word; font-size: 22px; font-weight: 400;">
                                                                <strong>Order details</strong>
                                                            </h1>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <br>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td><![endif]-->
                                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>




                    {{-- order no , shipping , billing --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">

                                <div class="u-col u-col-12p2"
                                    style="max-width: 320px;min-width: 73.2px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <div id="u_column_34" class="u-col u-col-52p85"
                                    style="max-width: 320px;min-width: 317.1px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            {{-- order no --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 40px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;">Order
                                                                        Number: </span></p>
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;"><strong>#{{ $orderInfo->order_no }}</strong></span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- order date --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 40px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;">Order
                                                                        date: </span></p>
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;"><strong>{{ date('d M Y, h:i a', strtotime($orderInfo->order_date)) }}</strong></span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- Billing Address --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 40px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;">Billing
                                                                        address: </span></p>
                                                                <p style="line-height: 140%;">
                                                                    <span style="color: #000000; line-height: 19.6px;">
                                                                        <span
                                                                            style="color: #000000; line-height: 19.6px;">
                                                                            @if ($billingInfo)
                                                                                <strong>{{ $billingInfo ? $billingInfo->address : '' }},
                                                                                    {{ $billingInfo ? $billingInfo->thana : '' }},
                                                                                    {{ $billingInfo ? $billingInfo->city : '' }}-{{ $billingInfo ? $billingInfo->post_code : '' }},
                                                                                    {{ $billingInfo ? $billingInfo->country : '' }}</strong>
                                                                            @endif
                                                                        </span>
                                                                    </span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- shipping address --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 40px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;"><span
                                                                        style="color: #000000; line-height: 19.6px;">Shipping
                                                                        address: </span></p>
                                                                <p style="line-height: 140%;">
                                                                    <span style="color: #000000; line-height: 19.6px;">
                                                                        <span
                                                                            style="color: #000000; line-height: 19.6px;">
                                                                            @if ($shippingInfo)
                                                                                <strong>{{ $shippingInfo ? $shippingInfo->address : '' }},
                                                                                    {{ $shippingInfo ? $shippingInfo->thana : '' }},
                                                                                    {{ $shippingInfo ? $shippingInfo->city : '' }}-{{ $shippingInfo ? $shippingInfo->post_code : '' }},
                                                                                    {{ $shippingInfo ? $shippingInfo->country : '' }}</strong>
                                                                            @endif
                                                                        </span>
                                                                    </span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>

                                <div id="u_column_36" class="u-col u-col-25p03"
                                    style="max-width: 320px;min-width: 150.18px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            {{-- order status --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">Order status:</p>
                                                                <p style="line-height: 140%;">
                                                                    <span style="color: #f9bd0b; line-height: 19.6px;">
                                                                        <strong>
                                                                            @if ($orderInfo->order_status == 0)
                                                                                <span
                                                                                    style="color: blue; line-height: 19.6px;"><strong>Pending</strong></span>
                                                                            @elseif($orderInfo->order_status == 1)
                                                                                <span
                                                                                    style="color: green; line-height: 19.6px;"><strong>Approved</strong></span>
                                                                            @elseif($orderInfo->order_status == 2)
                                                                                <span
                                                                                    style="color: green; line-height: 19.6px;"><strong>Intransit</strong></span>
                                                                            @elseif($orderInfo->order_status == 3)
                                                                                <span
                                                                                    style="color: green; line-height: 19.6px;"><strong>Delivered</strong></span>
                                                                            @else
                                                                                <span
                                                                                    style="color: red; line-height: 19.6px;"><strong>Cancelled</strong></span>
                                                                            @endif
                                                                        </strong>
                                                                    </span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- payment status --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 10px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">Payment status:</p>
                                                                <p style="line-height: 140%;">
                                                                    @if ($orderInfo->payment_status == 0)
                                                                        <span
                                                                            style="color: #e03e2d; line-height: 19.6px;"><strong>Unpaid</strong></span>
                                                                    @elseif($orderInfo->payment_status == 1)
                                                                        <span
                                                                            style="color: green; line-height: 19.6px;"><strong>Paid</strong></span>
                                                                    @else
                                                                        <span
                                                                            style="color: #e03e2d; line-height: 19.6px;"><strong>Failed</strong></span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- payment method --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 10px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">Payment method:</p>
                                                                <p style="line-height: 140%;">

                                                                    @if ($orderInfo->payment_method == null)
                                                                        <span
                                                                            style="color: red; line-height: 19.6px;"><strong>Unpaid</strong></span>
                                                                    @elseif($orderInfo->payment_method == 1)
                                                                        <span
                                                                            style="color: blue; line-height: 19.6px;"><strong>COD</strong></span>
                                                                    @elseif($orderInfo->payment_method == 2)
                                                                        <span
                                                                            style="color: green; line-height: 19.6px;"><strong>bKash</strong></span>
                                                                    @elseif($orderInfo->payment_method == 3)
                                                                        <span
                                                                            style="color: green; line-height: 19.6px;"><strong>Nagad</strong></span>
                                                                    @else
                                                                        <span
                                                                            style="color: green; line-height: 19.6px;"><strong>Card</strong></span>
                                                                    @endif

                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            {{-- delivery method --}}
                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:4px 10px 4px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">Delivery method:</p>
                                                                <p style="line-height: 140%;">
                                                                    @if ($orderInfo->delivery_method == 1)
                                                                        <span
                                                                            style="color: #34495e; line-height: 19.6px;"><strong>Home
                                                                                Delivery</strong></span>
                                                                    @endif

                                                                    @if ($orderInfo->delivery_method == 2)
                                                                        <span
                                                                            style="color: #34495e; line-height: 19.6px;"><strong>Store
                                                                                Pickup</strong></span>
                                                                    @endif
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                                <div class="u-col u-col-9p92"
                                    style="max-width: 320px;min-width: 59.52px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div>
                                                                <br>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- footer logo --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px 10px 0px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <table width="100%" cellpadding="0" cellspacing="0"
                                                                border="0">
                                                                <tr>
                                                                    <td style="padding-right: 0px;padding-left: 0px;"
                                                                        align="center">
                                                                        <a href="#" target="_blank">

                                                                            <img align="center" border="0"
                                                                                @if ($generalInfo->logo_dark && file_exists(public_path($generalInfo->logo_dark))) src="{{ url($generalInfo->logo_dark) }}" @endif
                                                                                alt="" title=""
                                                                                style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: inline-block !important;border: none;height: auto;float: none;width: 30%;max-width: 174px;"
                                                                                width="174">

                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- footer email contact address --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 15px; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">
                                                                    <span style="color: #000000; line-height: 19.6px;">
                                                                        <strong>Helpline:</strong>
                                                                        <a target="_blank"
                                                                            @if ($generalInfo->contact) href="tel:{{ explode(',', $generalInfo->contact)[0] }}" @endif
                                                                            rel="noopener">{{ $generalInfo->contact }}</a>
                                                                        <br>
                                                                        <strong>E-mail:</strong>
                                                                        <a target="_blank"
                                                                            @if ($generalInfo->email) href="mailto:{{ explode(',', $generalInfo->email)[0] }}" @endif
                                                                            rel="noopener">{{ $generalInfo->email }}</a>
                                                                        <br>
                                                                    </span>
                                                                </p>
                                                                <p style="line-height: 140%;">
                                                                    <span style="color: #000000; line-height: 19.6px;">
                                                                        <strong>Address:</strong>
                                                                        {{ $generalInfo->address }}
                                                                    </span>
                                                                </p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- social media links --}}
                    <div class="u-row-container" style="padding: 0px;background-color: transparent">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #062b52;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">
                                                            <h1
                                                                style="margin: 0px; color: #ffffff; line-height: 140%; text-align: center; word-wrap: break-word; font-size: 22px; font-weight: 400;">
                                                                <strong>Follow us</strong>
                                                            </h1>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div align="center">
                                                                <div style="display: table; max-width:258px;">


                                                                    {{-- facebook --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt; mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->facebook }}"
                                                                                        title="Facebook"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/facebook.png"
                                                                                            alt="Facebook"
                                                                                            title="Facebook"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block  !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- twitter --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->twitter }}"
                                                                                        title="X"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/x.png"
                                                                                            alt="X"
                                                                                            title="X"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none; height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- linkedin --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->linkedin }}"
                                                                                        title="LinkedIn"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/linkedin.png"
                                                                                            alt="LinkedIn"
                                                                                            title="LinkedIn"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- youtube --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->youtube }}"
                                                                                        title="YouTube"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/youtube.png"
                                                                                            alt="YouTube"
                                                                                            title="YouTube"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- messenger --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt; mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->messenger }}"
                                                                                        title="Messenger"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/messenger.png"
                                                                                            alt="Messenger"
                                                                                            title="Messenger"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- whats app --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 5px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->whatsapp }}"
                                                                                        title="WhatsApp"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/whatsapp.png"
                                                                                            alt="WhatsApp"
                                                                                            title="WhatsApp"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                    {{-- telegram --}}
                                                                    <table align="left" border="0"
                                                                        cellspacing="0" cellpadding="0"
                                                                        width="32" height="32"
                                                                        style="width: 32px !important;height: 32px !important;display: inline-block;border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;margin-right: 0px">
                                                                        <tbody>
                                                                            <tr style="vertical-align: top">
                                                                                <td align="left" valign="middle"
                                                                                    style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                                                                                    <a href="{{ $generalInfo->telegram }}"
                                                                                        title="Telegram"
                                                                                        target="_blank">
                                                                                        <img src="https://cdn.tools.unlayer.com/social/icons/circle/telegram.png"
                                                                                            alt="Telegram"
                                                                                            title="Telegram"
                                                                                            width="32"
                                                                                            style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important">
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>


                                                                </div>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- footer links --}}
                    <div class="u-row-container" style="padding: 0px;background-color: #ecf0f1">
                        <div class="u-row"
                            style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div
                                style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div class="menu" style="text-align:center">

                                                                <a href="{{ getDomain(env('APP_URL')) }}/about"
                                                                    target="_blank"
                                                                    style="padding:5px 8px;display:inline-block;color:#0068A5;font-size:14px;font-weight: 400;text-decoration:none">
                                                                    About us
                                                                </a>
                                                                <span
                                                                    style="padding:5px 8px;display:inline-block;color:#dddddd;font-size:14px;font-weight: 400;"
                                                                    class="hide-mobile">
                                                                    |
                                                                </span>

                                                                <a href="{{ getDomain(env('APP_URL')) }}/contact"
                                                                    target="_blank"
                                                                    style="padding:5px 8px;display:inline-block;color:#0068A5;font-size:14px;font-weight: 400;text-decoration:none">
                                                                    Contact us
                                                                </a>
                                                                <span
                                                                    style="padding:5px 8px;display:inline-block;color:#dddddd;font-size:14px;font-weight: 400;"
                                                                    class="hide-mobile">
                                                                    |
                                                                </span>

                                                                <a href="{{ getDomain(env('APP_URL')) }}/terms"
                                                                    target="_blank"
                                                                    style="padding:5px 8px;display:inline-block;color:#0068A5;font-size:14px;font-weight: 400;text-decoration:none">
                                                                    Terms & Condition
                                                                </a>
                                                                <span
                                                                    style="padding:5px 8px;display:inline-block;color:#dddddd;font-size:14px;font-weight: 400;"
                                                                    class="hide-mobile">
                                                                    |
                                                                </span>

                                                                <a href="{{ getDomain(env('APP_URL')) }}/privacy-policy"
                                                                    target="_blank"
                                                                    style="padding:5px 8px;display:inline-block;color:#0068A5;font-size:14px;font-weight: 400;text-decoration:none">
                                                                    Privacy policy
                                                                </a>
                                                                <span
                                                                    style="padding:5px 8px;display:inline-block;color:#dddddd;font-size:14px;font-weight: 400;"
                                                                    class="hide-mobile">
                                                                    |
                                                                </span>

                                                                <a href="{{ getDomain(env('APP_URL')) }}/refund-policy"
                                                                    target="_blank"
                                                                    style="padding:5px 8px;display:inline-block;color:#0068A5;font-size:14px;font-weight: 400;text-decoration:none">
                                                                    Refund policy
                                                                </a>

                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- <div class="u-row-container" style="padding: 0px 0px 16px;background-color: transparent">
                        <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;">
                            <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                                <div class="u-col u-col-100"
                                    style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
                                    <div
                                        style="height: 100%;width: 100% !important;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div class="v-col-padding"
                                            style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;border-radius: 0px;-webkit-border-radius: 0px; -moz-border-radius: 0px;">
                                            <!--<![endif]-->

                                            <table style="font-family:'Lato',sans-serif;" role="presentation"
                                                cellpadding="0" cellspacing="0" width="100%" border="0">
                                                <tbody>
                                                    <tr>
                                                        <td style="overflow-wrap:break-word;word-break:break-word;padding:10px;font-family:'Lato',sans-serif;"
                                                            align="left">

                                                            <div
                                                                style="font-size: 14px; line-height: 140%; text-align: center; word-wrap: break-word;">
                                                                <p style="line-height: 140%;">If you no longer wish to
                                                                    receive these emails, click on the following link:
                                                                    <a target="_blank" href="#"
                                                                        rel="noopener"><span
                                                                            style="text-decoration: underline; line-height: 19.6px;"><strong>Unsubscribe
                                                                                email</strong></span></a></p>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div> --}}

                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
