<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .content p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
        }
        
        .code-box {
            background: #f0f0f0;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .code {
            font-size: 42px;
            font-weight: 900;
            color: #667eea;
            letter-spacing: 8px;
            margin: 0;
            font-family: 'Courier New', monospace;
        }
        
        .code-subtext {
            color: #999;
            font-size: 13px;
            margin-top: 10px;
        }
        
        .warning {
            background: #fff3cd;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
            font-size: 14px;
            color: #856404;
        }
        
        .footer {
            background: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Password Reset Code</h2>
        </div>
        
        <div class="content">
            <p>Hi {{ $name ?? 'User' }},</p>
            
            <p>We received a request to reset your password. Use the code below to proceed:</p>
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
                <div class="code-subtext">6-digit verification code</div>
            </div>
            
            <p>Enter this code in the password reset form to verify your identity.</p>
            
            <a href="{{ url('/verify-reset-code?email=' . urlencode($email ?? '')) }}" class="button">Verify Code</a>
            
            <div class="warning">
                <strong>⏰ This code expires in 15 minutes</strong><br>
                If you didn't request a password reset, please ignore this email or contact support if you're concerned about your account security.
            </div>
            
            <p><strong>For security reasons:</strong></p>
            <ul style="text-align: left; display: inline-block; color: #666;">
                <li>Never share your reset code with anyone</li>
                <li>KIPS staff will never ask for your reset code</li>
                <li>We will never ask for your password via email</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>KIPS System</strong></p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p>&copy; 2026 KIPS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
