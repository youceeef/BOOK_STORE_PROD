<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #INV-ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 32px 36px;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #18181b;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #18181b;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #09090b;
            margin: 0 0 4px 0;
        }
        .brand-subtitle {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #71717a;
            margin: 0;
        }
        .invoice-title {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 18px;
            font-weight: 700;
            text-align: right;
            color: #09090b;
            margin: 0 0 4px 0;
        }
        .invoice-meta {
            text-align: right;
            font-size: 10px;
            color: #71717a;
            line-height: 1.5;
        }
        .invoice-meta strong {
            color: #18181b;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 3px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 24px;
        }
        .info-grid table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            color: #71717a;
            margin-bottom: 6px;
        }
        .info-box {
            background: #fafafa;
            border: 1px solid #e4e4e7;
            border-radius: 4px;
            padding: 10px 12px;
            margin-right: 8px;
            font-size: 10.5px;
            line-height: 1.5;
        }
        .info-box.seller {
            margin-right: 0;
            margin-left: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #f4f4f5;
            color: #3f3f46;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            padding: 8px 10px;
            border-top: 1px solid #e4e4e7;
            border-bottom: 1px solid #e4e4e7;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #f4f4f5;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .items-table td.text-center {
            text-align: center;
        }
        .item-title {
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 700;
            font-size: 11.5px;
            color: #09090b;
            margin-bottom: 2px;
        }
        .item-author {
            color: #71717a;
            font-size: 9.5px;
        }
        .totals-section {
            width: 100%;
            margin-bottom: 28px;
        }
        .totals-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table {
            width: 260px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 4px 0;
            font-size: 10.5px;
        }
        .totals-table td.label {
            color: #71717a;
            text-align: left;
        }
        .totals-table td.amount {
            color: #18181b;
            text-align: right;
            font-weight: 600;
        }
        .totals-table tr.discount td {
            color: #047857;
            font-weight: 600;
        }
        .totals-table tr.grand-total td {
            border-top: 1.5px solid #18181b;
            padding-top: 8px;
            margin-top: 4px;
            font-size: 13px;
            font-weight: 700;
            color: #09090b;
        }
        .totals-table tr.grand-total td.amount {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 15px;
        }
        .footer {
            border-top: 1px solid #e4e4e7;
            padding-top: 14px;
            margin-top: 20px;
        }
        .footer table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-note {
            font-size: 9.5px;
            color: #71717a;
            line-height: 1.5;
        }
        .footer-stamp {
            text-align: right;
            font-family: monospace;
            font-size: 9px;
            color: #a1a1aa;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <div class="brand-title">BookStore</div>
                    <p class="brand-subtitle">Curated Editions &bull; Fine Literature &bull; Global Fulfillment</p>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <div class="invoice-title">TAX INVOICE</div>
                    <div class="invoice-meta">
                        <strong>Invoice No:</strong> #INV-ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <strong>Date:</strong> {{ $order->created_at->format('F d, Y') }}<br>
                        <strong>Payment:</strong> <span class="status-badge">{{ strtoupper($order->status) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Billing & Fulfillment Meta --}}
    <div class="info-grid">
        <table>
            <tr>
                <td class="info-col">
                    <div class="info-label">Billed & Shipped To:</div>
                    <div class="info-box">
                        <strong>{{ $order->user?->name ?? 'Valued Customer' }}</strong><br>
                        {{ $order->user?->email }}<br>
                        @if ($order->shipping_address)
                            {{ $order->shipping_address }}<br>
                        @elseif ($order->billing_address)
                            {{ $order->billing_address }}<br>
                        @endif
                        Delivery Mode: Standard Insured Tracked
                    </div>
                </td>
                <td class="info-col">
                    <div class="info-label">Issued By:</div>
                    <div class="info-box seller">
                        <strong>BookStore Curations Ltd.</strong><br>
                        Academic & Literature Fulfillment Hub<br>
                        University Boulevard, District 7<br>
                        VAT / Tax Registration: US-TAX-9823481<br>
                        support@bookstore.example.com
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Line Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description / Title</th>
                <th class="text-center" style="width: 12%;">Qty</th>
                <th class="text-right" style="width: 18%;">Unit Price</th>
                <th class="text-right" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        <div class="item-title">{{ $item->book?->title ?? 'Book Volume' }}</div>
                        <div class="item-author">By {{ $item->book?->author ?? 'Independent Author' }} &bull; ISBN: {{ $item->book?->isbn ?? 'N/A' }}</div>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Financial Totals Breakdown --}}
    <div class="totals-section">
        <table>
            <tr>
                <td style="vertical-align: top; width: 55%;">
                    <div style="background: #fafafa; border: 1px dashed #d4d4d8; border-radius: 4px; padding: 10px 12px; margin-right: 16px; font-size: 10px; color: #71717a;">
                        <strong style="color: #18181b; display: block; margin-bottom: 4px;">Payment Authentication:</strong>
                        Gateway: Stripe Secure Payments (Level 1 PCI-DSS)<br>
                        Payment Intent: <code style="font-family: monospace; color: #3f3f46;">{{ $order->stripe_payment_intent_id ?: 'Manual/Dev Checkout' }}</code><br>
                        Settlement Status: Settled & Verified
                    </div>
                </td>
                <td style="vertical-align: top; width: 45%;">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Items Subtotal:</td>
                            <td class="amount">${{ number_format($order->total_amount + ($order->discount_amount ?? 0), 2) }}</td>
                        </tr>
                        @if ($order->discount_amount > 0)
                            <tr class="discount">
                                <td class="label">Coupon ({{ $order->coupon_code }}):</td>
                                <td class="amount">-${{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="label">Tracked Shipping:</td>
                            <td class="amount" style="color: #047857;">FREE ($0.00)</td>
                        </tr>
                        <tr>
                            <td class="label">Tax (Included):</td>
                            <td class="amount">$0.00</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="label">Total Paid:</td>
                            <td class="amount">${{ number_format($order->total_amount, 2) }} USD</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Legal & Brand Footer --}}
    <div class="footer">
        <table>
            <tr>
                <td class="footer-note" style="width: 70%;">
                    Thank you for choosing <strong>BookStore</strong>. This tax invoice serves as official proof of purchase.<br>
                    For inquiries, exchange requests, or digital reading assistance, contact support@bookstore.example.com.
                </td>
                <td class="footer-stamp" style="width: 30%;">
                    OFFICIALLY STAMPED<br>
                    DIGITAL RECEIPT &bull; {{ date('Y-m-d') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
