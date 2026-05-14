@php
    $body = $body ?? '';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CroWork</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6; margin: 0; padding: 24px;">
<div style="max-width: 680px; margin: 0 auto;">
    {!! nl2br(e($body)) !!}
</div>
</body>
</html>
