<!DOCTYPE html>
<html>
<head>
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #eee; margin-top: 20px; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label">Name:</span>
                <span>{{ $data['firstName'] }} {{ $data['lastName'] }}</span>
            </div>
            
            <div class="field">
                <span class="label">Email:</span>
                <span>{{ $data['email'] }}</span>
            </div>
            
            <div class="field">
                <span class="label">Phone:</span>
                <span>{{ $data['phone'] ?? 'Not provided' }}</span>
            </div>
            
            <div class="field">
                <span class="label">Company:</span>
                <span>{{ $data['company'] ?? 'Not provided' }}</span>
            </div>
            
            <div class="field">
                <span class="label">Service:</span>
                <span>{{ $data['service'] ?? 'Not specified' }}</span>
            </div>
            
            <div class="field">
                <span class="label">Budget:</span>
                <span>{{ $data['budget'] ?? 'Not specified' }}</span>
            </div>
            
            <div class="field">
                <span class="label">Timeline:</span>
                <span>{{ $data['timeline'] ?? 'Not specified' }}</span>
            </div>
            
            @if(!empty($data['message']))
            <div class="field">
                <div class="label">Message:</div>
                <div>{{ $data['message'] }}</div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
