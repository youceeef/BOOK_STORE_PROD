<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BookStore' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f4f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #18181b;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            border: 1px solid #e4e4e7;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .header {
            padding: 32px 36px 24px;
            border-bottom: 1px solid #f4f4f5;
            text-align: left;
        }
        .brand {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 22px;
            font-weight: 700;
            color: #09090b;
            text-decoration: none;
            letter-spacing: -0.3px;
        }
        .tagline {
            font-size: 11px;
            color: #71717a;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .content {
            padding: 36px;
        }
        .title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 24px;
            font-weight: 700;
            color: #09090b;
            margin: 0 0 16px;
            line-height: 1.3;
        }
        .lead {
            font-size: 14px;
            color: #3f3f46;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .btn-wrapper {
            margin: 32px 0;
            text-align: left;
        }
        .btn {
            display: inline-block;
            background-color: #18181b;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .subtext {
            font-size: 12px;
            color: #71717a;
            margin-top: 24px;
            border-top: 1px solid #f4f4f5;
            padding-top: 18px;
            line-height: 1.5;
        }
        .subtext a {
            color: #18181b;
            word-break: break-all;
        }
        .footer {
            padding: 24px 36px;
            background-color: #fafafa;
            border-top: 1px solid #e4e4e7;
            font-size: 11px;
            color: #a1a1aa;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ config('app.url') }}" class="brand">BookStore</a>
            <div class="tagline">Fine Literature &bull; Curated Editions</div>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} BookStore Curations. All rights reserved.<br>
            University Boulevard, Academic District 7.<br>
            This is an automated communication regarding your reader account.
        </div>
    </div>
</body>
</html>
