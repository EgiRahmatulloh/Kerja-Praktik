<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $letter->letterType->nama_jenis }} - {{ $letter->no_surat }}</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {!! $renderedContent !!}
</body>
</html>
